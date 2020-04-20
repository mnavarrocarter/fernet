<?php

declare(strict_types=1);

/*
 * This file is part of the MNC\Fernet project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Fernet\Version;

use InvalidArgumentException;
use MNC\Fernet\FernetException;
use MNC\Fernet\UrlSafeBase64;

/**
 * Class Vx80Fernet.
 */
class Vx80Version extends AbstractVersion
{
    private const VERSION = "\x80";
    private const MIN_LENGTH = 73;
    private const MAX_CLOCK_SKEW = 60;

    /**
     * @param string $message
     *
     * @return string
     */
    public function encode(string $message): string
    {
        $time = $this->getBinaryTime();
        $iv = $this->iv();
        $cipher = $this->key->encrypt($this->pad($message), $iv);
        $base = self::VERSION.$time.$iv.$cipher;
        $hmac = $this->key->sign($base);

        return UrlSafeBase64::encode($base.$hmac);
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
        $decoded = UrlSafeBase64::decode($token);

        $length = strlen($decoded);

        if ($length < self::MIN_LENGTH) {
            throw FernetException::tooShort();
        }

        $base = substr($decoded, 0, -32);
        $version = $base[0];
        $tokenTime = unpack('N/N', substr($base, 1, 8))[1];

        // We ensure the first byte is 0x80
        if ($version !== self::VERSION) {
            throw FernetException::unsupportedVersion($version);
        }

        // We extract the time and do future and expiration checks
        $currentTime = unpack('N/N', $this->getBinaryTime())[1];
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
        if ($hmac !== $recomputedHmac) {
            throw FernetException::incorrectMac();
        }

        // Decrypt the cipher with the iv
        $iv = substr($base, 9, 16);
        $cipher = substr($base, 25);
        $message = $this->key->decrypt($cipher, $iv);

        // Unpad decrypted, returning original message
        return $this->unpad($message);
    }

    final protected function guard(): void
    {
        if (!$this->key instanceof Vx80Key) {
            throw new InvalidArgumentException('Invalid key for Fernet 0x80 version');
        }
    }
}
