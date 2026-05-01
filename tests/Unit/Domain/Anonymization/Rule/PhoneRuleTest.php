<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Anonymization\Rule;

use App\Domain\Anonymization\Rule\PhoneRule;
use PHPUnit\Framework\TestCase;
use Tests\Support\AnonymizeDtoFactory;

final class PhoneRuleTest extends TestCase
{
    public function testItMasksRussianPhoneWithPlusSeven(): void
    {
        $rule = new PhoneRule();

        $result = $rule->apply(AnonymizeDtoFactory::make([
            'phone' => '+79991234567',
        ]));

        self::assertSame('+7********67', $result);
    }

    public function testItNormalizesRussianPhoneStartingWithEight(): void
    {
        $rule = new PhoneRule();

        $result = $rule->apply(AnonymizeDtoFactory::make([
            'phone' => '89991234567',
        ]));

        self::assertSame('+7********67', $result);
    }

    public function testItMasksPhoneWithCountryCodeOne(): void
    {
        $rule = new PhoneRule();

        $result = $rule->apply(AnonymizeDtoFactory::make([
            'phone' => '+12345678901',
        ]));

        self::assertSame('+1********01', $result);
    }

    public function testItIgnoresFormattingCharacters(): void
    {
        $rule = new PhoneRule();

        $result = $rule->apply(AnonymizeDtoFactory::make([
            'phone' => '+7 (999) 123-45-67',
        ]));

        self::assertSame('+7********67', $result);
    }

    public function testItReturnsNullWhenPhoneIsMissing(): void
    {
        $rule = new PhoneRule();

        $result = $rule->apply(AnonymizeDtoFactory::make([
            'phone' => null,
        ]));

        self::assertNull($result);
    }

    public function testItDoesNotExposeMiddleDigits(): void
    {
        $rule = new PhoneRule();

        $result = $rule->apply(AnonymizeDtoFactory::make([
            'phone' => '+79991234567',
        ]));

        self::assertStringStartsWith('+7', $result);
        self::assertStringEndsWith('67', $result);
        self::assertStringNotContainsString('99912345', $result);
    }
}