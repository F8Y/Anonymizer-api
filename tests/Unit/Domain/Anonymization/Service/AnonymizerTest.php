<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Anonymization\Service;

use App\Domain\Anonymization\DTO\AnonymizeRequestDto;
use App\Domain\Anonymization\Service\Anonymizer;
use PHPUnit\Framework\TestCase;

final class AnonymizerTest extends TestCase
{
    public function testItAnonymizesPersonalData(): void
    {
        $anonymizer = new Anonymizer();

        $input = new AnonymizeRequestDto(
            fullName: 'Иванов Иван Иванович',
            email: 'ivanov@example.com',
            phone: '+79991234567',
            birthDate: '2010-04-12',
        );

        $result = $anonymizer->anonymize($input);

        self::assertMatchesRegularExpression('/^USER-[A-F0-9]{10}$/', $result->fullName);
        self::assertSame('i***@example.com', $result->email);
        self::assertSame('+7*******67', $result->phone);
        self::assertSame('2010', $result->birthDate);
    }

    public function testFullNamePseudonymIsStable(): void
    {
        $anonymizer = new Anonymizer();

        $firstInput = new AnonymizeRequestDto(
            fullName: 'Иванов Иван Иванович',
            email: 'ivanov@example.com',
            phone: '+79991234567',
            birthDate: '2010-04-12',
        );

        $secondInput = new AnonymizeRequestDto(
            fullName: 'Иванов Иван Иванович',
            email: 'other@example.com',
            phone: '+79990000000',
            birthDate: '2011-01-01',
        );

        $firstResult = $anonymizer->anonymize($firstInput);
        $secondResult = $anonymizer->anonymize($secondInput);

        self::assertSame($firstResult->fullName, $secondResult->fullName);
    }

    public function testFullNamePseudonymDoesNotContainOriginalName(): void
    {
        $anonymizer = new Anonymizer();

        $input = new AnonymizeRequestDto(
            fullName: 'Иванов Иван Иванович',
            email: 'ivanov@example.com',
            phone: '+79991234567',
            birthDate: '2010-04-12',
        );

        $result = $anonymizer->anonymize($input);

        self::assertStringStartsWith('USER-', $result->fullName);
        self::assertStringNotContainsString('Иванов', $result->fullName);
        self::assertStringNotContainsString('Иван', $result->fullName);
    }
}