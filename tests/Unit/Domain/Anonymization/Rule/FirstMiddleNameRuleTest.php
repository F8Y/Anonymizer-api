<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Anonymization\Rule;

use App\Domain\Anonymization\Rule\FirstMiddleNameRule;
use PHPUnit\Framework\TestCase;
use Tests\Support\AnonymizeDtoFactory;

final class FirstMiddleNameRuleTest extends TestCase
{
    public function testItRemovesFirstMiddleName(): void
    {
        $rule = new FirstMiddleNameRule();

        $result = $rule->apply(AnonymizeDtoFactory::make([
            'firstMiddleName' => 'Иван Иванович',
        ]));

        self::assertSame('[обезличено]', $result);
    }

    public function testItDoesNotExposeOriginalNameParts(): void
    {
        $rule = new FirstMiddleNameRule();

        $result = $rule->apply(AnonymizeDtoFactory::make([
            'firstMiddleName' => 'Иван Иванович',
        ]));

        self::assertStringNotContainsString('Иван', $result);
        self::assertStringNotContainsString('Иванович', $result);
    }
}