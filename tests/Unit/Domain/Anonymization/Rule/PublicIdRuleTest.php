<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Anonymization\Rule;

use App\Domain\Anonymization\Rule\PublicIdRule;
use PHPUnit\Framework\TestCase;
use Tests\Support\AnonymizeDtoFactory;

final class PublicIdRuleTest extends TestCase
{
    public function testItGeneratesStablePublicId(): void
    {
        $rule = new PublicIdRule('test-secret');

        $first = $rule->apply(AnonymizeDtoFactory::make());
        $second = $rule->apply(AnonymizeDtoFactory::make());

        self::assertSame($first, $second);
        self::assertMatchesRegularExpression('/^USER-[A-F0-9]{12}$/', $first);
    }

    public function testDifferentUsersProduceDifferentPublicIds(): void
    {
        $rule = new PublicIdRule('test-secret');

        $first = $rule->apply(AnonymizeDtoFactory::make());
        $second = $rule->apply(AnonymizeDtoFactory::make([
            'login' => 'petrov_pp',
            'firstMiddleName' => 'Петр Петрович',
            'lastName' => 'Петров',
            'email' => 'petrov@example.com',
        ]));

        self::assertNotSame($first, $second);
    }

    public function testDifferentSecretsProduceDifferentPublicIds(): void
    {
        $firstRule = new PublicIdRule('first-secret');
        $secondRule = new PublicIdRule('second-secret');

        $input = AnonymizeDtoFactory::make();

        self::assertNotSame(
            $firstRule->apply($input),
            $secondRule->apply($input)
        );
    }

    public function testItNormalizesCaseSpacesAndYoLetter(): void
    {
        $rule = new PublicIdRule('test-secret');

        $first = $rule->apply(AnonymizeDtoFactory::make([
            'login' => 'semenov_as',
            'firstMiddleName' => 'Артём   Сергеевич',
            'lastName' => 'Семёнов',
        ]));

        $second = $rule->apply(AnonymizeDtoFactory::make([
            'login' => 'semenov_as',
            'firstMiddleName' => '  АРТЕМ СЕРГЕЕВИЧ  ',
            'lastName' => 'СЕМЕНОВ',
        ]));

        self::assertSame($first, $second);
    }
}