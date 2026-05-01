<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Anonymization\Rule;

use App\Domain\Anonymization\DTO\AnonymizeRequestDto;
use App\Domain\Anonymization\Rule\FullNameRule;
use PHPUnit\Framework\TestCase;

final class FullNameRuleTest extends TestCase
{
    private function makeDto(string $fullName): AnonymizeRequestDto
    {
        return new AnonymizeRequestDto(
            fullName: $fullName,
            email: 'ivanov@example.com',
            phone: '+79991234567',
            birthDate: '2010-04-12',
        );
    }

    public function testItGeneratesStablePseudonymForSameFullName(): void
    {
        $rule = new FullNameRule('test-secret');

        $first = $rule->apply($this->makeDto('Иванов Иван Иванович'));
        $second = $rule->apply($this->makeDto('Иванов Иван Иванович'));

        self::assertSame($first, $second);
        self::assertMatchesRegularExpression('/^USER-[A-F0-9]{12}$/', $first);
    }

    public function testItNormalizesCaseSpacesAndYoLetter(): void
    {
        $rule = new FullNameRule('test-secret');

        $first = $rule->apply($this->makeDto('Семёнов   Артём   Сергеевич'));
        $second = $rule->apply($this->makeDto('  СЕМЕНОВ АРТЕМ СЕРГЕЕВИЧ  '));

        self::assertSame($first, $second);
    }

    public function testDifferentFullNamesProduceDifferentPseudonyms(): void
    {
        $rule = new FullNameRule('test-secret');

        $first = $rule->apply($this->makeDto('Иванов Иван Иванович'));
        $second = $rule->apply($this->makeDto('Петров Петр Петрович'));

        self::assertNotSame($first, $second);
    }

    public function testDifferentSecretsProduceDifferentPseudonyms(): void
    {
        $firstRule = new FullNameRule('first-secret');
        $secondRule = new FullNameRule('second-secret');

        $input = $this->makeDto('Иванов Иван Иванович');

        self::assertNotSame(
            $firstRule->apply($input),
            $secondRule->apply($input)
        );
    }

    public function testItDoesNotUsePlainSha256HashDirectly(): void
    {
        $rule = new FullNameRule('test-secret');

        $normalized = 'иванов иван иванович';
        $plainHashBasedId = 'USER-' . strtoupper(substr(hash('sha256', $normalized), 0, 12));

        $result = $rule->apply($this->makeDto('Иванов Иван Иванович'));

        self::assertNotSame($plainHashBasedId, $result);
    }

    public function testPseudonymDoesNotContainOriginalFullNameParts(): void
    {
        $rule = new FullNameRule('test-secret');

        $result = $rule->apply($this->makeDto('Иванов Иван Иванович'));

        self::assertStringNotContainsString('Иванов', $result);
        self::assertStringNotContainsString('Иван', $result);
        self::assertStringNotContainsString('Иванович', $result);
    }
}