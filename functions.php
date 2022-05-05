<?php

namespace MNC\Fernet\UrlBase64;

use MNC\Fernet\UnexpectedError;

/**
 * @param string $data
 * @return string
 */
function encode(string $data): string
{
    return str_replace(['+', '/'], ['-', '_'], base64_encode($data));
}

/**
 * @param string $base64
 * @return string
 *
 * @throws UnexpectedError if there is a problem decoding the string
 */
function decode(string $base64): string
{
    $result = base64_decode(str_replace(['-', '_'], ['+', '/'], $base64), true);
    if (!is_string($result)) {
        throw new UnexpectedError('Invalid url encoded base64 string provided');
    }

    return $result;
}


namespace MNC\Fernet\Str;

use Exception;
use InvalidArgumentException;
use MNC\Fernet\UnexpectedError;

/**
 * Pads a message to a multiple of 16 bytes.
 *
 * @param string $message
 *
 * @return string
 */
function pad(string $message): string
{
    $pad = 16 - (strlen($message) % 16);
    $message .= str_repeat(chr($pad), $pad);
    return $message;
}

/**
 * Removes the padding of a message.
 *
 * @param string $paddedMessage
 *
 * @return string
 * @throws InvalidArgumentException on padding error
 * @throws UnexpectedError if un-padding fails
 */
function unpad(string $paddedMessage): string
{
    $pad = ord($paddedMessage[strlen($paddedMessage) - 1]);
    if ($pad !== substr_count(substr($paddedMessage, -$pad), chr($pad))) {
        throw new UnexpectedError('Padding error');
    }

    return substr($paddedMessage, 0, -$pad);
}