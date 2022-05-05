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

use PHPUnit\Framework\TestCase;

abstract class AcceptanceTestCase extends TestCase
{
    protected function readTestData(string $name): array
    {
        $file = __DIR__.'/testdata/'.$name.'.json';
        $json = file_get_contents($file);
        if (!is_string($json)) {
            throw new \RuntimeException('Could not read file '.$file);
        }

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException(sprintf('File %s contains invalid json', $file), 0, $e);
        }

        return $data;
    }
}
