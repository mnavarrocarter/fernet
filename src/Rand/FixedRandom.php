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

use function strlen;
use function substr;

/**
 * FixedRandom is a Random implementation that provides deterministic
 * reading of random bytes.
 *
 * This is very useful for testing.
 *
 * @see SpecComplianceAcceptanceTest
 */
final class FixedRandom implements Random
{
    private string $random;

    public function __construct(string $random)
    {
        $this->random = $random;
    }

    public static function fromUint8Array(array $arr): FixedRandom
    {
        return new self(implode('', array_map('chr', $arr)));
    }

    /**
     * {@inheritDoc}
     */
    public function read(int $bytes): string
    {
        if ($bytes > strlen($this->random)) {
            throw new RandomError('Not enough bytes to read');
        }

        return substr($this->random, 0, $bytes);
    }
}
