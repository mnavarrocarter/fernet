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

namespace MNC;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \MNC\Fernet
 */
class FernetTest extends TestCase
{
    public function testItEncodesAndDecodes(): void
    {
        $msg = 'Hello';
        $fernet = Fernet::create('cw_0x689RpI-jtRR7oE8h_eQsKImvJapLeSbXpwF4e4=');

        $a = $fernet->encode($msg);
        $b = $fernet->encode($msg);

        // Assert that two encoded messages will never be the same
        self::assertNotSame($a, $b);
        // Assert we can decode the original back
        self::assertSame($msg, $fernet->decode($a));
    }
}
