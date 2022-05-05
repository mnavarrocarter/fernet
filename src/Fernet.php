<?php

declare(strict_types=1);

/**
 * @project MNC Fernet
 * @link https://github.com/mnavarrocarter/fernet
 * @project mnavarrocarter/fernet
 * @author Matias Navarro-Carter mnavarrocarter@gmail.com
 * @license MIT
 * @copyright 2022 Matias Navarro-Carter
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC;

use ChrisHarrison\Clock\Clock;
use ChrisHarrison\Clock\SystemClock;
use function MNC\Fernet\Str\pad;
use function MNC\Fernet\Str\unpad;
use function MNC\Fernet\UrlBase64\decode;
use function MNC\Fernet\UrlBase64\encode;
use MNC\Rand\PHPSecureRandom;
use MNC\Rand\Random;

/**
 * Fernet is an implementation of the Fernet messaging specification.
 *
 * @see https://github.com/fernet/spec/blob/master/Spec.md
 *
 * Fernet messages are both encrypted and authenticated, with different keys.
 * This means you can safely encode any message using Fernet and you have strong guarantees
 * that the original message has not been exposed and that has not been tampered with.
 *
 * Fernet messages are safe to transfer over the web as they are base64 url encoded.
 *
 * Common use cases are for short-lived messages stored client side but then retrieved
 * for validation in the same server that issued the message.
 *
 * Fernet messages embed inside them their creation timestamp, so their expiry time can be
 * defined by the consumer upon decoding if necessary.
 */
class Fernet
{
    private const VERSION = "\x80";
    private const MIN_LENGTH = 73;
    private const MAX_CLOCK_SKEW = 60;
    private const IV_SIZE = 16;

    private Fernet\Key $key;
    private Random $random;
    private Clock $clock;

    public function __construct(Fernet\Key $key, Random $random, Clock $clock)
    {
        $this->key = $key;
        $this->random = $random;
        $this->clock = $clock;
    }

    /**
     * @throws Fernet\UnexpectedError if the key cannot be decoded
     */
    public static function create(string $key, Random $random = null, Clock $clock = null): Fernet
    {
        return new self(
            Fernet\Key::decode($key),
            $random ?? PHPSecureRandom::instance(),
            $clock ?? new SystemClock(),
        );
    }

    /**
     * Encodes a message according to the Fernet specification.
     *
     * @throws Fernet\UnexpectedError if there is an error in the random source
     */
    public function encode(string $message): string
    {
        $time = pack('J', $this->clock->now()->getTimestamp());

        try {
            $iv = $this->random->read(self::IV_SIZE);
        } catch (Rand\RandomError $e) {
            throw new Fernet\UnexpectedError('Could not read random bytes', 0, $e);
        }
        $cipher = $this->key->encrypt(pad($message), $iv);
        $base = self::VERSION.$time.$iv.$cipher;
        $hmac = $this->key->sign($base);

        return encode($base.$hmac);
    }

    /**
     * @throws Fernet\DecodingError
     */
    public function decode(string $message, int $ttl = 0): string
    {
        // We base64 decode the token
        try {
            $decoded = decode($message);
        } catch (Fernet\UnexpectedError $e) {
            throw new Fernet\DecodingError('Error while decoding the message', 0, $e);
        }

        $length = \strlen($decoded);

        if ($length < self::MIN_LENGTH) {
            throw new Fernet\DecodingError('The message is too short');
        }

        $base = \substr($decoded, 0, -32);
        $version = $base[0];
        $tokenTime = \unpack('J', \substr($base, 1, 8))[1];

        // We ensure the first byte is 0x80
        if (self::VERSION !== $version) {
            throw new Fernet\DecodingError('Version mismatch');
        }

        // We extract the time and do future and expiration checks
        $currentTime = $this->clock->now()->getTimestamp();
        $timeDiff = $currentTime - $tokenTime;

        if ($ttl > 0 && $timeDiff > $ttl) {
            throw new Fernet\ExpiredMessage('The message has expired');
        }

        if ($tokenTime > ($currentTime + self::MAX_CLOCK_SKEW)) {
            throw new Fernet\DecodingError('The message has been created too far in the future. Possibly out of sync clock.');
        }

        // We recompute the HMAC and ensure matched the token one
        $hmac = \substr($decoded, -32);
        $recomputedHmac = $this->key->sign($base);
        if (!\hash_equals($hmac, $recomputedHmac)) {
            throw new Fernet\DecodingError('The message has a wrong hmac');
        }

        // Decrypt the cipher with the iv
        $iv = \substr($base, 9, 16);
        $cipher = \substr($base, 25);

        try {
            $message = $this->key->decrypt($cipher, $iv);
        } catch (Fernet\UnexpectedError $e) {
            throw new Fernet\DecodingError('The message could not be decrypted', 0, $e);
        }

        // Un-pad decrypted message, returning original message
        try {
            return unpad($message);
        } catch (Fernet\UnexpectedError $e) {
            throw new Fernet\DecodingError('The message has wrong padding', 0, $e);
        }
    }
}
