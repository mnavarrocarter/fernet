<?php

/*
 * This file is part of the MNC\Fernet project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Fernet;

/**
 * A Marshaller is responsible fot managing encoding/decoding of
 * tokens according to the Fernet Spec.
 */
interface Marshaller
{
    /**
     * Encodes a message into a fernet token.
     *
     * @param string $message
     *
     * @return string
     */
    public function encode(string $message): string;

    /**
     * Decodes a fernet token into the original message.
     *
     * @param string   $token
     * @param int|null $ttl
     *
     * @return string
     */
    public function decode(string $token, int $ttl = null): string;
}
