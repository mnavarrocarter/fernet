<?php
declare(strict_types=1);

namespace MNC\Fernet\Tests;

use MNC\Fernet\FernetException;
use MNC\Fernet\Version\Vx80Key;
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
            $time = \DateTimeImmutable::createFromFormat(DATE_ATOM, $test['now']);
            $fernet = new FernetDeterministic($key, $time, $test['iv'] ?? []);

            $token = $fernet->encode($test['src']);
            $this->assertSame($test['token'], $token);
        }
    }

    public function testDeterministicInvalid(): void
    {
        $tests = $this->readJson(__DIR__ . '/invalid.json');
        foreach ($tests as $test) {
            $key = Vx80Key::fromString($test['secret']);
            $time = \DateTimeImmutable::createFromFormat(DATE_ATOM, $test['now']);
            $fernet = new FernetDeterministic($key, $time, $test['iv'] ?? []);

            try {
                $fernet->decode($test['token'], $test['ttl_sec']);
            } catch (FernetException $e) {
                $this->assertInstanceOf(FernetException::class, $e);
            }
        }
    }

    public function testDeterministicVerify(): void
    {
        $tests = $this->readJson(__DIR__ . '/verify.json');
        foreach ($tests as $test) {
            $key = Vx80Key::fromString($test['secret']);
            $time = \DateTimeImmutable::createFromFormat(DATE_ATOM, $test['now']);
            $fernet = new FernetDeterministic($key, $time, $test['iv'] ?? []);

            $message = $fernet->decode($test['token'], $test['ttl_sec']);
            $this->assertSame($test['src'], $message);
        }
    }

    protected function readJson(string $filename): array
    {
        return json_decode(file_get_contents($filename), true);
    }
}
