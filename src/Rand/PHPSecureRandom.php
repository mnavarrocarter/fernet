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

use Exception;
use function random_bytes;

final class PHPSecureRandom implements Random
{
    private static ?PHPSecureRandom $instance = null;

    public static function instance(): PHPSecureRandom
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * {@inheritDoc}
     */
    public function read(int $bytes): string
    {
        try {
            return random_bytes($bytes);
        } catch (Exception $e) {
            throw new RandomError('PHP random_bytes error', 0, $e);
        }
    }
}
