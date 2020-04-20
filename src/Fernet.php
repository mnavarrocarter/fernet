<?php

declare(strict_types=1);

/*
 * This file is part of the MNC\Fernet project.
 * (c) Matías Navarro-Carter <mnavarrocarter@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MNC\Fernet;

use MNC\Fernet\Version\Vx80Key;
use MNC\Fernet\Version\Vx80Version;

/**
 * Class Fernet.
 */
class Fernet implements EncoderInterface, DecoderInterface
{
    /**
     * @var EncoderInterface
     */
    private $encoder;
    /**
     * @var DecoderInterface
     */
    private $decoder;

    /**
     * @param string $key
     *
     * @return Fernet
     *
     * @throws FernetException
     */
    public static function vx80(string $key): Fernet
    {
        $fernet = new Vx80Version(Vx80Key::fromString($key));

        return new self($fernet, $fernet);
    }

    /**
     * Fernet constructor.
     *
     * @param EncoderInterface $encoder
     * @param DecoderInterface $decoder
     */
    public function __construct(EncoderInterface $encoder, DecoderInterface $decoder)
    {
        $this->encoder = $encoder;
        $this->decoder = $decoder;
    }

    /**
     * {@inheritdoc}
     */
    public function decode(string $token, int $ttl = null): string
    {
        return $this->decoder->decode($token);
    }

    /**
     * {@inheritdoc}
     */
    public function encode(string $message): string
    {
        return $this->encoder->encode($message);
    }
}
