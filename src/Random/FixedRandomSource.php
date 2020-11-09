<?php

/*
 * This file is part of the MNC\Fernet project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Fernet\Random;

/**
 * Class FixedRandomSource.
 */
final class FixedRandomSource implements RandomSource
{
    /**
     * @var string
     */
    private $random;

    /**
     * @param array $array
     *
     * @return FixedRandomSource
     */
    public static function fromUint8Array(array $array): FixedRandomSource
    {
        return new self(implode('', array_map('chr', $array)));
    }

    /**
     * FixedRandomSource constructor.
     *
     * @param string $random
     */
    public function __construct(string $random)
    {
        $this->random = $random;
    }

    /**
     * {@inheritdoc}
     */
    public function read(int $bytes): string
    {
        if ($bytes !== strlen($this->random)) {
            throw new EntropyError('Not the same random length');
        }

        return $this->random;
    }
}
