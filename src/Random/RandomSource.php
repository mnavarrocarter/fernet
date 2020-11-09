<?php

/*
 * This file is part of the MNC\Fernet project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Fernet\Random;

/**
 * Interface RandomSource.
 */
interface RandomSource
{
    /**
     * Read random bytes from somewhere.
     *
     * @param int $bytes
     *
     * @return string
     *
     * @throws EntropyError if not enough entropy can be gathered for randomness
     */
    public function read(int $bytes): string;
}
