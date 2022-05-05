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
 * @covers \MNC\Rand\PHPSecureRandom
 */
class PHPSecureRandomTest extends TestCase
{
    public function testItReads(): void
    {
        $a = PHPSecureRandom::instance()->read(16);
        $b = PHPSecureRandom::instance()->read(16);
        self::assertNotSame($a, $b);
    }
}
