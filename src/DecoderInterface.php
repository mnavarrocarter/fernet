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
 * Interface TokenVerifier.
 */
interface DecoderInterface
{
    /**
     * @param string   $token
     * @param int|null $ttl
     *
     * @return string
     *
     * @throws FernetException on token validation error
     */
    public function decode(string $token, int $ttl = null): string;
}
