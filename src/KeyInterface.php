<?php

declare(strict_types=1);

/*
 * This file is part of the MNC\Fernet project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Fernet;

/**
 * Interface KeyInterface.
 */
interface KeyInterface
{
    /**
     * @param string $data
     *
     * @return string
     */
    public function sign(string $data): string;

    /**
     * @param string $data
     * @param string $iv
     *
     * @return string
     */
    public function encrypt(string $data, string $iv): string;

    /**
     * @param string $cipher
     * @param string $iv
     *
     * @return string
     */
    public function decrypt(string $cipher, string $iv): string;
}
