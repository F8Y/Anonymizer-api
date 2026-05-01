<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Anonymization\Service;

use App\Domain\Anonymization\Rule\BirthDateRule;
use App\Domain\Anonymization\Rule\EmailRule;
use App\Domain\Anonymization\Rule\FirstMiddleNameRule;
use App\Domain\Anonymization\Rule\LastNameRule;
use App\Domain\Anonymization\Rule\LoginRule;
use App\Domain\Anonymization\Rule\PhoneRule;
use App\Domain\Anonymization\Rule\PublicIdRule;
use App\Domain\Anonymization\Service\Anonymizer;
use PHPUnit\Framework\TestCase;
use Tests\Support\AnonymizeDtoFactory;

final class AnonymizerTest extends TestCase
{
    private function createAnonymizer(): Anonymizer
    {
        return new Anonymizer(
            publicIdRule: new PublicIdRule('test-secret'),
            loginRule: new LoginRule('test-secret'),
            firstMiddleNameRule: new FirstMiddleNameRule(),
            lastNameRule: new LastNameRule(),
            emailRule: new EmailRule(),
            phoneRule: new PhoneRule(),
            birthDateRule: new BirthDateRule(),
        );
    }

    public function testItAnonymizesPersonalData(): void
    {
        $anonymizer = $this->createAnonymizer();

        $result = $anonymizer->anonymize(AnonymizeDtoFactory::make());

        self::assertMatchesRegularExpression('/^USER-[A-F0-9]{12}$/', $result->publicId);
        self::assertMatchesRegularExpression('/^LOGIN-[A-F0-9]{12}$/', $result->login);
        self::assertSame('[обезличено]', $result->firstMiddleName);
        self::assertSame('[обезличено]', $result->lastName);
        self::assertSame('i****v@***.com', $result->email);
        self::assertSame('+7********67', $result->phone);
        self::assertSame('2010', $result->birthDate);
    }

    public function testPublicIdIsStableForSameInput(): void
    {
        $anonymizer = $this->createAnonymizer();

        $first = $anonymizer->anonymize(AnonymizeDtoFactory::make());
        $second = $anonymizer->anonymize(AnonymizeDtoFactory::make());

        self::assertSame($first->publicId, $second->publicId);
    }

    public function testItSupportsMissingOptionalFields(): void
    {
        $anonymizer = $this->createAnonymizer();

        $result = $anonymizer->anonymize(AnonymizeDtoFactory::make([
            'phone' => null,
            'birthDate' => null,
        ]));

        self::assertNull($result->phone);
        self::assertNull($result->birthDate);
    }
}