<?php

/*
 * This file is part of the MNC\Fernet project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Fernet\Random;

/**
 * Class PhpRandomSource.
 */
final class PhpRandomSource implements RandomSource
{
    /**
     * {@inheritdoc}
     */
    public function read(int $bytes): string
    {
        try {
            return random_bytes($bytes);
        } catch (\Exception $e) {
            throw new EntropyError('Not enough entropy', 0, $e);
        }
    }
}
