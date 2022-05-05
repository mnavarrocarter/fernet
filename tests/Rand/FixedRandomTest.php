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

namespace MNC\Rand;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \MNC\Rand\FixedRandom
 */
class FixedRandomTest extends TestCase
{
    /**
     * @dataProvider getReadData
     *
     * @throws RandomError
     */
    public function testItReads(array $uint8Arr, int $length, string $expectedHex): void
    {
        $random = FixedRandom::fromUint8Array($uint8Arr);
        $bytes = $random->read($length);
        self::assertSame(hex2bin($expectedHex), $bytes);
    }

    public function testItFailsToRead(): void
    {
        $random = FixedRandom::fromUint8Array([]);
        $this->expectException(RandomError::class);
        $random->read(2);
    }

    public function getReadData(): array
    {
        return [
            'zero' => [
                [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
                0,
                '',
            ],
            'half' => [
                [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
                8,
                '0001020304050607',
            ],
            'full' => [
                [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
                16,
                '000102030405060708090A0B0C0D0E0F',
            ],
        ];
    }
}
