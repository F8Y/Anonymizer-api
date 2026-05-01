<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Anonymization\Rule;

use App\Domain\Anonymization\DTO\AnonymizeRequestDto;
use App\Domain\Anonymization\Rule\EmailRule;
use PHPUnit\Framework\TestCase;

final class EmailRuleTest extends TestCase
{
    private function makeDto(string $email): AnonymizeRequestDto
    {
        return new AnonymizeRequestDto(
            fullName: 'Иванов Иван Иванович',
            email: $email,
            phone: '+79991234567',
            birthDate: '2010-04-12',
        );
    }

    public function testItMasksRegularEmail(): void
    {
        $rule = new EmailRule();

        $result = $rule->apply($this->makeDto('ivanov@example.com'));

        self::assertSame('i****v@***.com', $result);
    }

    public function testItMasksOneCharacterLocalPart(): void
    {
        $rule = new EmailRule();

        $result = $rule->apply($this->makeDto('a@example.com'));

        self::assertSame('*@***.com', $result);
    }

    public function testItMasksTwoCharacterLocalPart(): void
    {
        $rule = new EmailRule();

        $result = $rule->apply($this->makeDto('ab@example.com'));

        self::assertSame('a*@***.com', $result);
    }

    public function testItNormalizesEmailCase(): void
    {
        $rule = new EmailRule();

        $result = $rule->apply($this->makeDto('Ivanov@Example.COM'));

        self::assertSame('i****v@***.com', $result);
    }

    public function testItDoesNotExposeOriginalDomainName(): void
    {
        $rule = new EmailRule();

        $result = $rule->apply($this->makeDto('ivanov@sirius27.ru'));

        self::assertSame('i****v@***.ru', $result);
        self::assertStringNotContainsString('sirius27', $result);
    }
}