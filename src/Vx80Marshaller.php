<?php

/*
 * This file is part of the MNC\Fernet project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Fernet;

use Lcobucci\Clock\Clock;
use Lcobucci\Clock\SystemClock;
use MNC\Fernet\Random\PhpRandomSource;
use MNC\Fernet\Random\RandomSource;
use function MNC\Fernet\Str\pad;
use function MNC\Fernet\Str\unpad;
use function MNC\Fernet\UrlBase64\decode;
use function MNC\Fernet\UrlBase64\encode;

/**
 * Class Vx80Marshaller.
 */
final class Vx80Marshaller implements Marshaller
{
    private const VERSION = "\x80";
    private const MIN_LENGTH = 73;
    private const MAX_CLOCK_SKEW = 60;
    private const IV_SIZE = 16;

    /**
     * @var Vx80Key
     */
    private $key;
    /**
     * @var Clock
     */
    private $clock;
    /**
     * @var RandomSource
     */
    private $randomSource;

    /**
     * Vx80Marshaller constructor.
     *
     * @param Vx80Key           $key
     * @param Clock|null        $clock
     * @param RandomSource|null $randomSource
     */
    public function __construct(Vx80Key $key, Clock $clock = null, RandomSource $randomSource = null)
    {
        $this->key = $key;
        $this->clock = $clock ?? SystemClock::fromUTC();
        $this->randomSource = $randomSource ?? new PhpRandomSource();
    }

    /**
     * @param string $message
     *
     * @return string
     *
     * @throws Random\EntropyError
     */
    public function encode(string $message): string
    {
        $time = pack('J', $this->clock->now()->getTimestamp());
        $iv = $this->randomSource->read(self::IV_SIZE);
        $cipher = $this->key->encrypt(pad($message), $iv);
        $base = self::VERSION.$time.$iv.$cipher;
        $hmac = $this->key->sign($base);

        return encode($base.$hmac);
    }

    /**
     * @param string   $token
     * @param int|null $ttl
     *
     * @return string
     *
     * @throws FernetException
     */
    public function decode(string $token, int $ttl = null): string
    {
        // We base64 decode the token
        try {
            $decoded = decode($token);
        } catch (\InvalidArgumentException $exception) {
            throw FernetException::invalidBase64();
        }

        $length = strlen($decoded);

        if ($length < self::MIN_LENGTH) {
            throw FernetException::tooShort();
        }

        $base = substr($decoded, 0, -32);
        $version = $base[0];
        $tokenTime = unpack('J', substr($base, 1, 8))[1];

        // We ensure the first byte is 0x80
        if ($version !== self::VERSION) {
            throw FernetException::unsupportedVersion($version);
        }

        // We extract the time and do future and expiration checks
        $currentTime = $this->clock->now()->getTimestamp();
        $timeDiff = $currentTime - $tokenTime;

        if ($ttl > 0 && $timeDiff > $ttl) {
            throw FernetException::expiredTTL();
        }

        if ($tokenTime > ($currentTime + self::MAX_CLOCK_SKEW)) {
            throw FernetException::farFuture();
        }

        // We recompute the HMAC and ensure matched the token one
        $hmac = substr($decoded, -32);
        $recomputedHmac = $this->key->sign($base);
        if (!hash_equals($hmac, $recomputedHmac)) {
            throw FernetException::incorrectMac();
        }

        // Decrypt the cipher with the iv
        $iv = substr($base, 9, 16);
        $cipher = substr($base, 25);
        $message = $this->key->decrypt($cipher, $iv);

        // Unpad decrypted, returning original message
        try {
            return unpad($message);
        } catch (\InvalidArgumentException $exception) {
            throw FernetException::payloadPaddingError();
        }
    }
}
