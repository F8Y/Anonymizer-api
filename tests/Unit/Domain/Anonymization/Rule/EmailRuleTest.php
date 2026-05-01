<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Anonymization\Rule;

use App\Domain\Anonymization\Rule\EmailRule;
use PHPUnit\Framework\TestCase;
use Tests\Support\AnonymizeDtoFactory;

final class EmailRuleTest extends TestCase
{
    public function testItMasksRegularEmail(): void
    {
        $rule = new EmailRule();

        $result = $rule->apply(AnonymizeDtoFactory::make([
            'email' => 'ivanov@example.com',
        ]));

        self::assertSame('i****v@***.com', $result);
    }

    public function testItMasksOneCharacterLocalPart(): void
    {
        $rule = new EmailRule();

        $result = $rule->apply(AnonymizeDtoFactory::make([
            'email' => 'a@example.com',
        ]));

        self::assertSame('*@***.com', $result);
    }

    public function testItMasksTwoCharacterLocalPart(): void
    {
        $rule = new EmailRule();

        $result = $rule->apply(AnonymizeDtoFactory::make([
            'email' => 'ab@example.com',
        ]));

        self::assertSame('a*@***.com', $result);
    }

    public function testItNormalizesEmailCase(): void
    {
        $rule = new EmailRule();

        $result = $rule->apply(AnonymizeDtoFactory::make([
            'email' => 'Ivanov@Example.COM',
        ]));

        self::assertSame('i****v@***.com', $result);
    }

    public function testItDoesNotExposeOriginalDomainName(): void
    {
        $rule = new EmailRule();

        $result = $rule->apply(AnonymizeDtoFactory::make([
            'email' => 'ivanov@sirius27.ru',
        ]));

        self::assertSame('i****v@***.ru', $result);
        self::assertStringNotContainsString('sirius27', $result);
    }
}