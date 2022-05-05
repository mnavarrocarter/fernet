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

namespace MNC\Fernet;

use function MNC\Fernet\UrlBase64\decode;
use function MNC\Fernet\UrlBase64\encode;
use MNC\Rand\PHPSecureRandom;
use MNC\Rand\Random;
use MNC\Rand\RandomError;

class Key
{
    private const FLAGS = OPENSSL_ZERO_PADDING + OPENSSL_RAW_DATA;

    private string $signingKey;
    private string $encryptionKey;

    /**
     * Key constructor.
     */
    private function __construct(string $signingKey, string $encryptionKey)
    {
        $this->signingKey = $signingKey;
        $this->encryptionKey = $encryptionKey;
    }

    /**
     * Decodes a Fernet key from its base64 representation.
     *
     * @throws UnexpectedError if the key cannot be decoded or is invalid
     */
    public static function decode(string $key): Key
    {
        $bytes = decode($key);
        if (32 !== \strlen($bytes)) {
            throw new UnexpectedError('Invalid key length: must be 32 bytes');
        }

        [$signingKey, $encryptionKey] = \str_split($bytes, 16);

        return new self($signingKey, $encryptionKey);
    }

    /**
     * @throws RandomError if there is an error reading from the random source
     */
    public static function random(Random $random = null): Key
    {
        $random = $random ?? PHPSecureRandom::instance();

        return new self($random->read(16), $random->read(16));
    }

    /**
     * @throws UnexpectedError if data cannot be encrypted
     */
    public function encrypt(string $data, string $iv): string
    {
        $cipher = \openssl_encrypt($data, 'aes-128-cbc', $this->encryptionKey, self::FLAGS, $iv);
        if (false === $cipher) {
            throw new UnexpectedError('Could not encrypt data');
        }

        return $cipher;
    }

    /**
     * @throws UnexpectedError if decryption fails
     */
    public function decrypt(string $cipher, string $iv): string
    {
        $message = \openssl_decrypt($cipher, 'aes-128-cbc', $this->encryptionKey, self::FLAGS, $iv);
        if (false === $message) {
            throw new UnexpectedError('Decryption failed');
        }

        return $message;
    }

    public function sign(string $data): string
    {
        return \hash_hmac('sha256', $data, $this->signingKey, true);
    }

    /**
     * Encodes a Fernet key into its base64 representation.
     */
    public function encode(): string
    {
        return encode($this->signingKey.$this->encryptionKey);
    }
}
