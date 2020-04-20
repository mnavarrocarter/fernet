<?php

declare(strict_types=1);

/*
 * This file is part of the MNC\Fernet project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Fernet;

/**
 * Interface EncoderInterface.
 */
interface EncoderInterface
{
    /**
     * Encodes a Fernet token from a message.
     *
     * @param string $message The message to encode
     *
     * @return string The encoded fernet token
     */
    public function encode(string $message): string;
}
