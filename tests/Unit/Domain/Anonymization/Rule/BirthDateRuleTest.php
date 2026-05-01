<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Anonymization\Rule;

use App\Domain\Anonymization\DTO\AnonymizeRequestDto;
use App\Domain\Anonymization\Rule\BirthDateRule;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class BirthDateRuleTest extends TestCase
{
    private function makeDto(string $birthDate): AnonymizeRequestDto
    {
        return new AnonymizeRequestDto(
            fullName: 'Иванов Иван Иванович',
            email: 'ivanov@example.com',
            phone: '+79991234567',
            birthDate: $birthDate,
        );
    }

    public function testItReducesBirthDateToYear(): void
    {
        $rule = new BirthDateRule();

        $result = $rule->apply($this->makeDto('2010-04-12'));

        self::assertSame('2010', $result);
    }

    public function testItThrowsExceptionForInvalidDateFormat(): void
    {
        $rule = new BirthDateRule();

        $this->expectException(InvalidArgumentException::class);

        $rule->apply($this->makeDto('12.04.2010'));
    }

    public function testItThrowsExceptionForInvalidCalendarDate(): void
    {
        $rule = new BirthDateRule();

        $this->expectException(InvalidArgumentException::class);

        $rule->apply($this->makeDto('2010-99-99'));
    }
}