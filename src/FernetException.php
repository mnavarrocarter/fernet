<?php

declare(strict_types=1);

/*
 * This file is part of the MNC\Fernet project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Fernet;

use Exception;

/**
 * Class FernetException.
 */
class FernetException extends Exception
{
    public static function unsupportedVersion(string $version): FernetException
    {
        return new self(sprintf('Unsupported version 0x%s', bin2hex($version)));
    }

    public static function invalidBase64(): FernetException
    {
        return new self('Invalid base64');
    }

    public static function incorrectMac(): FernetException
    {
        return new self('Token has incorrect mac');
    }

    public static function tooShort(): FernetException
    {
        return new self('Token is too short');
    }

    public static function payloadNotMultipleOfBlock(): FernetException
    {
        return new self('Payload size not multiple of block size');
    }

    public static function payloadPaddingError(): FernetException
    {
        return new self('Payload padding error');
    }

    public static function farFuture(): FernetException
    {
        return new self('Token timestamp is in the future');
    }

    public static function expiredTTL(): FernetException
    {
        return new self('Token has expired');
    }
}
