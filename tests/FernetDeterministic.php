<?php
declare(strict_types=1);

namespace MNC\Fernet\Tests;

use DateTime;
use DateTimeInterface;
use MNC\Fernet\KeyInterface;
use MNC\Fernet\Version\Vx80Version;

/**
 *
 * @package MNC\Fernet\Tests
 */
class FernetDeterministic extends Vx80Version
{
    /**
     * @var DateTimeInterface
     */
    private $time;
    /**
     * @var array
     */
    private $iv;

    /**
     * FernetDeterministic constructor.
     * @param KeyInterface $key
     * @param DateTimeInterface $time
     * @param array $iv
     */
    public function __construct(KeyInterface $key, DateTimeInterface $time, array $iv)
    {
        parent::__construct($key);
        $this->key = $key;
        $this->time = $time;
        $this->iv = $iv;
    }

    /**
     * @return string
     */
    protected function iv(): string
    {
        return implode(array_map('chr', $this->iv));
    }

    protected function getBinaryTime(): string
    {
        return pack('NN', 0, $this->time->getTimestamp());
    }
}