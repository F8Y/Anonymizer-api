<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Anonymization\Rule;

use App\Domain\Anonymization\Rule\LastNameRule;
use PHPUnit\Framework\TestCase;
use Tests\Support\AnonymizeDtoFactory;

final class LastNameRuleTest extends TestCase
{
    public function testItRemovesLastName(): void
    {
        $rule = new LastNameRule();

        $result = $rule->apply(AnonymizeDtoFactory::make([
            'lastName' => 'Иванов',
        ]));

        self::assertSame('[обезличено]', $result);
    }

    public function testItDoesNotExposeOriginalLastName(): void
    {
        $rule = new LastNameRule();

        $result = $rule->apply(AnonymizeDtoFactory::make([
            'lastName' => 'Иванов',
        ]));

        self::assertStringNotContainsString('Иванов', $result);
    }
}