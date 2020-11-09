<?php
declare(strict_types=1);

namespace MNC\Fernet\Tests;

use DateTimeImmutable;
use Lcobucci\Clock\FrozenClock;
use MNC\Fernet\FernetException;
use MNC\Fernet\Random\FixedRandomSource;
use MNC\Fernet\Vx80Key;
use MNC\Fernet\Vx80Marshaller;
use PHPUnit\Framework\TestCase;

/**
 * Class SpecComplianceAcceptanceTest
 *
 * Tests Fernet implementation by using deterministic values provided by
 * the Fernet Spec
 *
 * @see https://github.com/fernet/spec
 *
 * @package MNC\Fernet\Tests
 */
class SpecComplianceAcceptanceTest extends TestCase
{
    public function testDeterministicGeneration(): void
    {
        $tests = $this->readJson(__DIR__ . '/generate.json');
        foreach ($tests as $test) {
            $key = Vx80Key::fromString($test['secret']);
            $clock = new FrozenClock(DateTimeImmutable::createFromFormat(DATE_ATOM, $test['now']));
            $random = FixedRandomSource::fromUint8Array($test['iv']);
            $marshaller = new Vx80Marshaller($key, $clock, $random);

            $token = $marshaller->encode($test['src']);
            self::assertSame($test['token'], $token);
        }
    }

    public function testDeterministicInvalid(): void
    {
        $tests = $this->readJson(__DIR__ . '/invalid.json');
        foreach ($tests as $test) {
            $key = Vx80Key::fromString($test['secret']);
            $clock = new FrozenClock(DateTimeImmutable::createFromFormat(DATE_ATOM, $test['now']));
            $random = FixedRandomSource::fromUint8Array($test['iv'] ?? []);
            $marshaller = new Vx80Marshaller($key, $clock, $random);

            try {
                $marshaller->decode($test['token'], $test['ttl_sec']);
            } catch (FernetException $e) {
                self::assertInstanceOf(FernetException::class, $e);
            }
        }
    }

    public function testDeterministicVerify(): void
    {
        $tests = $this->readJson(__DIR__ . '/verify.json');
        foreach ($tests as $test) {
            $key = Vx80Key::fromString($test['secret']);
            $clock = new FrozenClock(DateTimeImmutable::createFromFormat(DATE_ATOM, $test['now']));
            $random = FixedRandomSource::fromUint8Array($test['iv'] ?? []);
            $marshaller = new Vx80Marshaller($key, $clock, $random);

            $message = $marshaller->decode($test['token'], $test['ttl_sec']);
            self::assertSame($test['src'], $message);
        }
    }

    protected function readJson(string $filename): array
    {
        return json_decode(file_get_contents($filename), true);
    }
}
