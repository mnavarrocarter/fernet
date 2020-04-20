<?php

declare(strict_types=1);

/*
 * This file is part of the MNC\Fernet project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Fernet\Version;

use MNC\Fernet\DecoderInterface;
use MNC\Fernet\EncoderInterface;
use MNC\Fernet\FernetException;
use MNC\Fernet\KeyInterface;

/**
 * Class AbstractVersion.
 */
abstract class AbstractVersion implements DecoderInterface, EncoderInterface
{
    /**
     * @var KeyInterface
     */
    protected $key;

    /**
     * AbstractVersion constructor.
     *
     * @param KeyInterface $key
     */
    public function __construct(KeyInterface $key)
    {
        $this->key = $key;
        $this->guard();
    }

    /**
     * Generates random 16 bytes.
     *
     * @return string
     */
    protected function iv(): string
    {
        try {
            return random_bytes(16);
        } catch (\Exception $e) {
            throw new \RuntimeException('Not enough entropy for IV');
        }
    }

    /**
     * Pads a message to a multiple of 16 bytes.
     *
     * @param string $message
     *
     * @return string
     */
    protected function pad(string $message): string
    {
        $pad = 16 - (strlen($message) % 16);
        $message .= str_repeat(chr($pad), $pad);

        return $message;
    }

    /**
     * Removed the padding of a message.
     *
     * @param string $paddedMessage
     *
     * @return string
     *
     * @throws FernetException
     */
    protected function unpad(string $paddedMessage): string
    {
        $pad = ord($paddedMessage[strlen($paddedMessage) - 1]);
        if ($pad !== substr_count(substr($paddedMessage, -$pad), chr($pad))) {
            throw  FernetException::payloadPaddingError();
        }

        return substr($paddedMessage, 0, -$pad);
    }

    /**
     * Gets the time as a 64 bit unsigned big endian.
     *
     * @return string
     */
    protected function getBinaryTime(): string
    {
        return pack('NN', 0, time());
    }

    abstract protected function guard(): void;
}
