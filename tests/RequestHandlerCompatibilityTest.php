<?php

declare(strict_types=1);

namespace Solo\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Solo\RequestHandler\Exceptions\ValidationException;
use Solo\RequestHandler\Request;
use Solo\RequestHandler\RequestHandler;
use Solo\Validator\InvalidRuleException;
use Solo\Validator\Tests\Fixtures\BatchRequest;
use Solo\Validator\Tests\Fixtures\OrderRequest;
use Solo\Validator\Tests\Fixtures\SignupRequest;
use Solo\Validator\Tests\Fixtures\TypoRequest;
use Solo\Validator\Validator;

/**
 * The validator behind solophp/request-handler 4: the same rule-string grammar,
 * presence rules for absent fields, and error paths of nested items.
 */
final class RequestHandlerCompatibilityTest extends TestCase
{
    public function testRuleStringsAreReadTheSameWay(): void
    {
        $this->assertSame(
            ['email' => [['rule' => 'email']]],
            $this->errors(SignupRequest::class, ['email' => 'not-an-email']),
        );
    }

    public function testAbsentFieldWithRequiredIfIsValidated(): void
    {
        $this->assertSame(
            ['company' => [['rule' => 'required_if', 'params' => ['type', 'company']]]],
            $this->errors(SignupRequest::class, ['email' => 'user@example.com', 'type' => 'company']),
        );
    }

    public function testAbsentFieldWithPresentIsValidated(): void
    {
        $this->assertSame([['rule' => 'present']], $this->errors(BatchRequest::class, [])['ids'] ?? null);
    }

    public function testValidRequestIsHydrated(): void
    {
        $dto = $this->handler()->handleArray(SignupRequest::class, ['email' => 'user@example.com']);

        $this->assertSame('user@example.com', $dto->email);
        $this->assertSame('individual', $dto->type);
        $this->assertNull($dto->company);
    }

    public function testItemErrorsKeepTheirPaths(): void
    {
        $this->assertSame(
            ['lines.1.qty' => [['rule' => 'min_value', 'params' => ['1']]]],
            $this->errors(OrderRequest::class, ['lines' => [
                ['sku' => 'A-1', 'qty' => '2'],
                ['sku' => 'B-2', 'qty' => '0'],
            ]]),
        );
    }

    public function testUnknownRuleIsAConfigurationError(): void
    {
        $this->expectException(InvalidRuleException::class);

        $this->handler()->handleArray(TypoRequest::class, ['name' => 'John']);
    }

    public function testUnknownRuleOfAnAbsentFieldIsAConfigurationErrorToo(): void
    {
        $this->expectException(InvalidRuleException::class);

        $this->handler()->handleArray(TypoRequest::class, []);
    }

    private function handler(): RequestHandler
    {
        return new RequestHandler(new Validator());
    }

    /**
     * @param class-string<Request> $class
     * @param array<string, mixed>  $data
     * @return array<string, list<array{rule: string, params?: string[]}>>
     */
    private function errors(string $class, array $data): array
    {
        try {
            $this->handler()->handleArray($class, $data);
        } catch (ValidationException $e) {
            return $e->getErrors();
        }

        $this->fail('Expected a ValidationException');
    }
}
