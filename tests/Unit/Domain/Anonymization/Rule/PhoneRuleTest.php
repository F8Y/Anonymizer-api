<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Anonymization\Rule;

use App\Domain\Anonymization\DTO\AnonymizeRequestDto;
use App\Domain\Anonymization\Rule\PhoneRule;
use PHPUnit\Framework\TestCase;

final class PhoneRuleTest extends TestCase
{
    private function makeDto(string $phone): AnonymizeRequestDto
    {
        return new AnonymizeRequestDto(
            fullName: 'Иванов Иван Иванович',
            email: 'ivanov@example.com',
            phone: $phone,
            birthDate: '2010-04-12',
        );
    }

    public function testItMasksRussianPhoneWithPlusSeven(): void
    {
        $rule = new PhoneRule();

        $result = $rule->apply($this->makeDto('+79991234567'));

        self::assertSame('+7********67', $result);
    }

    public function testItNormalizesRussianPhoneStartingWithEight(): void
    {
        $rule = new PhoneRule();

        $result = $rule->apply($this->makeDto('89991234567'));

        self::assertSame('+7********67', $result);
    }

    public function testItMasksPhoneWithCountryCodeOne(): void
    {
        $rule = new PhoneRule();

        $result = $rule->apply($this->makeDto('+12345678901'));

        self::assertSame('+1********01', $result);
    }

    public function testItIgnoresFormattingCharacters(): void
    {
        $rule = new PhoneRule();

        $result = $rule->apply($this->makeDto('+7 (999) 123-45-67'));

        self::assertSame('+7********67', $result);
    }

    public function testItDoesNotExposeMiddleDigits(): void
    {
        $rule = new PhoneRule();

        $result = $rule->apply($this->makeDto('+79991234567'));

        self::assertStringStartsWith('+7', $result);
        self::assertStringEndsWith('67', $result);
        self::assertStringNotContainsString('99912345', $result);
    }
}