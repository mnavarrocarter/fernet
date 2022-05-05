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

use ChrisHarrison\Clock\FrozenClock;
use DateTimeImmutable;
use MNC\Fernet;
use MNC\Rand\FixedRandom;

/**
 * Class SpecComplianceAcceptanceTest.
 *
 * Tests Fernet implementation by using deterministic values provided by
 * the Fernet Spec
 *
 * @see https://github.com/fernet/spec
 *
 * @internal
 * @coversDefaultClass
 */
class SpecComplianceTest extends AcceptanceTestCase
{
    /**
     * @dataProvider getGenerateData
     *
     * @throws UnexpectedError
     */
    public function testGenerate(Fernet $fernet, string $message, string $expected): void
    {
        $encoded = $fernet->encode($message);
        self::assertSame($expected, $encoded);
    }

    /**
     * @dataProvider getInvalidData
     *
     * @throws DecodingError
     */
    public function testInvalid(Fernet $fernet, int $ttl, string $message): void
    {
        $this->expectException(DecodingError::class);
        $fernet->decode($message, $ttl);
    }

    /**
     * @dataProvider getVerifyData
     *
     * @throws DecodingError
     */
    public function testVerify(Fernet $fernet, int $ttl, string $message, string $expected): void
    {
        $decoded = $fernet->decode($message, $ttl);
        self::assertSame($expected, $decoded);
    }

    /**
     * @throws UnexpectedError
     */
    public function getGenerateData(): array
    {
        $data = [];
        foreach ($this->readTestData('generate') as $test) {
            $data[] = [
                Fernet::create(
                    $test['secret'] ?? '',
                    FixedRandom::fromUint8Array($test['iv'] ?? []),
                    new FrozenClock(DateTimeImmutable::createFromFormat(DATE_ATOM, $test['now']))
                ),
                $test['src'] ?? '',
                $test['token'] ?? '',
            ];
        }

        return $data;
    }

    /**
     * @throws UnexpectedError
     */
    public function getVerifyData(): array
    {
        $data = [];
        foreach ($this->readTestData('verify') as $test) {
            $data[] = [
                Fernet::create(
                    $test['secret'] ?? '',
                    FixedRandom::fromUint8Array($test['iv'] ?? []),
                    new FrozenClock(DateTimeImmutable::createFromFormat(DATE_ATOM, $test['now']))
                ),
                $test['ttl_sec'] ?? 0,
                $test['token'] ?? '',
                $test['src'] ?? '',
            ];
        }

        return $data;
    }

    /**
     * @throws UnexpectedError
     */
    public function getInvalidData(): array
    {
        $data = [];
        foreach ($this->readTestData('invalid') as $test) {
            $data[$test['desc']] = [
                Fernet::create(
                    $test['secret'] ?? '',
                    FixedRandom::fromUint8Array($test['iv'] ?? []),
                    new FrozenClock(DateTimeImmutable::createFromFormat(DATE_ATOM, $test['now']))
                ),
                $test['ttl_sec'] ?? 0,
                $test['token'] ?? '',
            ];
        }

        return $data;
    }
}
