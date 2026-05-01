<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Anonymization\Rule;

use App\Domain\Anonymization\Rule\BirthDateRule;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Tests\Support\AnonymizeDtoFactory;

final class BirthDateRuleTest extends TestCase
{
    public function testItReducesBirthDateToYear(): void
    {
        $rule = new BirthDateRule();

        $result = $rule->apply(AnonymizeDtoFactory::make([
            'birthDate' => '2010-04-12',
        ]));

        self::assertSame('2010', $result);
    }

    public function testItReturnsNullWhenBirthDateIsMissing(): void
    {
        $rule = new BirthDateRule();

        $result = $rule->apply(AnonymizeDtoFactory::make([
            'birthDate' => null,
        ]));

        self::assertNull($result);
    }

    public function testItThrowsExceptionForInvalidDateFormat(): void
    {
        $rule = new BirthDateRule();

        $this->expectException(InvalidArgumentException::class);

        $rule->apply(AnonymizeDtoFactory::make([
            'birthDate' => '12.04.2010',
        ]));
    }

    public function testItThrowsExceptionForInvalidCalendarDate(): void
    {
        $rule = new BirthDateRule();

        $this->expectException(InvalidArgumentException::class);

        $rule->apply(AnonymizeDtoFactory::make([
            'birthDate' => '2010-99-99',
        ]));
    }
}