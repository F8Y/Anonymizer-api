<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Anonymization\Rule;

use App\Domain\Anonymization\Rule\LoginRule;
use PHPUnit\Framework\TestCase;
use Tests\Support\AnonymizeDtoFactory;

final class LoginRuleTest extends TestCase
{
    public function testItGeneratesStableLoginPseudonym(): void
    {
        $rule = new LoginRule('test-secret');

        $first = $rule->apply(AnonymizeDtoFactory::make([
            'login' => 'ivanov_ii',
        ]));

        $second = $rule->apply(AnonymizeDtoFactory::make([
            'login' => 'ivanov_ii',
        ]));

        self::assertSame($first, $second);
        self::assertMatchesRegularExpression('/^LOGIN-[A-F0-9]{12}$/', $first);
    }

    public function testDifferentLoginsProduceDifferentPseudonyms(): void
    {
        $rule = new LoginRule('test-secret');

        $first = $rule->apply(AnonymizeDtoFactory::make([
            'login' => 'ivanov_ii',
        ]));

        $second = $rule->apply(AnonymizeDtoFactory::make([
            'login' => 'petrov_pp',
        ]));

        self::assertNotSame($first, $second);
    }

    public function testDifferentSecretsProduceDifferentLoginPseudonyms(): void
    {
        $firstRule = new LoginRule('first-secret');
        $secondRule = new LoginRule('second-secret');

        $input = AnonymizeDtoFactory::make([
            'login' => 'ivanov_ii',
        ]);

        self::assertNotSame(
            $firstRule->apply($input),
            $secondRule->apply($input)
        );
    }

    public function testItDoesNotExposeOriginalLogin(): void
    {
        $rule = new LoginRule('test-secret');

        $result = $rule->apply(AnonymizeDtoFactory::make([
            'login' => 'ivanov_ii',
        ]));

        self::assertStringNotContainsString('ivanov', $result);
        self::assertStringNotContainsString('ivanov_ii', $result);
    }
}