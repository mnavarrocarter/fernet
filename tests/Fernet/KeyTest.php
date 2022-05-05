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

use MNC\Rand\RandomError;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \MNC\Fernet\Key
 */
class KeyTest extends TestCase
{
    public function testItEncodesAndDecodes(): void
    {
        $encoded = 'cw_0x689RpI-jtRR7oE8h_eQsKImvJapLeSbXpwF4e4=';
        $key = Key::decode($encoded);
        self::assertSame($encoded, $key->encode());
    }

    /**
     * @dataProvider getDecodeError
     *
     * @throws UnexpectedError
     */
    public function testItFailsDecoding(string $encoded, string $message): void
    {
        $this->expectErrorMessage($message);
        $this->expectException(UnexpectedError::class);
        Key::decode($encoded);
    }

    /**
     * @throws RandomError
     */
    public function testItGeneratesRandom(): void
    {
        $a = Key::random()->encode();
        $b = Key::random()->encode();
        self::assertNotSame($a, $b);
    }

    public function getDecodeError(): array
    {
        return [
            'not base 64' => ['ldksjD2412%£', 'Invalid url encoded base64 string provided'],
            'too short' => ['cw_0x689RpI-jtRR7oE8h_eQsK', 'Invalid key length: must be 32 bytes'],
            'malformed base 64' => ['cw_0x689RpI-jtRR7oE8h_eQsKImvapLeSbXpwF4e4=', 'Invalid url encoded base64 string provided'],
        ];
    }
}
