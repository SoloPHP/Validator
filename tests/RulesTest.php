<?php

declare(strict_types=1);

namespace Solo\Validator\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Solo\Validator\Validator;

final class RulesTest extends TestCase
{
    /**
     * @param array{rule: string, params?: list<string>}|null $error
     */
    #[DataProvider('values')]
    public function testRule(string $rules, mixed $value, ?array $error): void
    {
        $errors = (new Validator())->validate(['field' => $value], ['field' => $rules]);

        $this->assertSame($error === null ? [] : ['field' => [$error]], $errors);
    }

    /**
     * @return iterable<string, array{string, mixed, array{rule: string, params?: list<string>}|null}>
     */
    public static function values(): iterable
    {
        $stringable = new class {
            public function __toString(): string
            {
                return 'abc';
            }
        };

        yield 'required: value' => ['required', 'John', null];
        yield 'required: zero string' => ['required', '0', null];
        yield 'required: false' => ['required', false, null];
        yield 'required: empty string' => ['required', '', ['rule' => 'required']];
        yield 'required: whitespace' => ['required', '   ', ['rule' => 'required']];
        yield 'required: null' => ['required', null, ['rule' => 'required']];
        yield 'required: empty array' => ['required', [], ['rule' => 'required']];

        yield 'present: empty array' => ['present', [], null];

        yield 'filled: value' => ['filled', 'x', null];
        yield 'filled: whitespace' => ['filled', ' ', ['rule' => 'filled']];
        yield 'filled: null' => ['filled', null, ['rule' => 'filled']];

        yield 'string: string' => ['string', 'abc', null];
        yield 'string: int' => ['string', 123, ['rule' => 'string']];
        yield 'string: null is no value' => ['string', null, null];

        yield 'integer: int' => ['integer', 25, null];
        yield 'integer: numeric string' => ['integer', '-42', null];
        yield 'integer: whole float' => ['integer', 5.0, null];
        yield 'integer: fraction' => ['integer', 1.5, ['rule' => 'integer']];
        yield 'integer: fraction string' => ['integer', '1.5', ['rule' => 'integer']];
        yield 'integer: trailing letters' => ['integer', '12abc', ['rule' => 'integer']];
        yield 'integer: float beyond exact range' => ['integer', 1e20, ['rule' => 'integer']];
        yield 'integer: true' => ['integer', true, ['rule' => 'integer']];

        yield 'numeric: float' => ['numeric', 99.99, null];
        yield 'numeric: decimal string' => ['numeric', '9.99', null];
        yield 'numeric: exponent string' => ['numeric', '1e3', null];
        yield 'numeric: letters' => ['numeric', 'abc', ['rule' => 'numeric']];
        yield 'numeric: overflow to INF' => ['numeric', '1e400', ['rule' => 'numeric']];
        yield 'numeric: NAN' => ['numeric', NAN, ['rule' => 'numeric']];
        yield 'numeric: true' => ['numeric', true, ['rule' => 'numeric']];

        yield 'boolean: true' => ['boolean', true, null];
        yield 'boolean: 0' => ['boolean', 0, null];
        yield 'boolean: "off"' => ['boolean', 'off', null];
        yield 'boolean: "TRUE"' => ['boolean', 'TRUE', null];
        yield 'boolean: 2' => ['boolean', 2, ['rule' => 'boolean']];
        yield 'boolean: 1.0' => ['boolean', 1.0, ['rule' => 'boolean']];
        yield 'boolean: "maybe"' => ['boolean', 'maybe', ['rule' => 'boolean']];

        yield 'array: list' => ['array', ['a'], null];
        yield 'array: string' => ['array', 'a', ['rule' => 'array']];
        yield 'array: allowed keys' => ['array:name,email', ['name' => 'John'], null];
        yield 'array: list keys' => ['array:0,1', ['a', 'b'], null];
        yield 'array: other key' => [
            'array:name,email',
            ['name' => 'John', 'password' => 'secret'],
            ['rule' => 'array_keys', 'params' => ['name', 'email']],
        ];

        yield 'email: valid' => ['email', 'user@example.com', null];
        yield 'email: invalid' => ['email', 'user@', ['rule' => 'email']];
        yield 'email: array' => ['email', ['user@example.com'], ['rule' => 'email']];

        yield 'uuid: valid' => ['uuid', '550e8400-e29b-41d4-a716-446655440000', null];
        yield 'uuid: upper case' => ['uuid', '550E8400-E29B-41D4-A716-446655440000', null];
        yield 'uuid: invalid' => ['uuid', 'not-a-uuid', ['rule' => 'uuid']];
        yield 'uuid: int' => ['uuid', 12345, ['rule' => 'uuid']];

        yield 'phone: international' => ['phone', '+14155552671', null];
        yield 'phone: national without region' => ['phone', '4155552671', ['rule' => 'phone']];
        yield 'phone: national with region' => ['phone:US', '(415) 555-2671', null];
        yield 'phone: lower-case region' => ['phone:us', '(415) 555-2671', null];
        yield 'phone: too short' => ['phone:US', '+14155', ['rule' => 'phone', 'params' => ['US']]];
        yield 'phone: unknown country code' => ['phone:US', '+999 1234567', ['rule' => 'phone', 'params' => ['US']]];
        yield 'phone: int' => ['phone:US', 14155552671, ['rule' => 'phone', 'params' => ['US']]];

        yield 'date: ISO date' => ['date', '2024-01-15', null];
        yield 'date: relative' => ['date', 'next monday', null];
        yield 'date: timestamp' => ['date', 1705334400, null];
        yield 'date: timestamp string' => ['date', '1705334400', null];
        yield 'date: compact date' => ['date', '20240115', null];
        yield 'date: impossible date' => ['date', '2024-02-31', ['rule' => 'date']];
        yield 'date: digits read as a time' => ['date', '1230', ['rule' => 'date']];
        yield 'date: empty string' => ['date', '', ['rule' => 'date']];
        yield 'date: float' => ['date', 1.5, ['rule' => 'date']];
        yield 'date: garbage' => ['date', 'not-a-date', ['rule' => 'date']];
        yield 'date format: match' => ['date:Y-m-d', '2024-01-15', null];
        yield 'date format: other order' => ['date:Y-m-d', '15-01-2024', ['rule' => 'date_format', 'params' => ['Y-m-d']]];
        yield 'date format: impossible' => ['date:Y-m-d', '2024-02-31', ['rule' => 'date_format', 'params' => ['Y-m-d']]];
        yield 'date format: not written exactly' => ['date:Y-m-d', '2024-1-5', ['rule' => 'date_format', 'params' => ['Y-m-d']]];
        yield 'date format: unpadded format' => ['date:Y-n-j', '2024-1-5', null];
        yield 'date format: escaped literal' => ['date:Y-m-d\TH:i:sP', '2024-01-15T10:30:00+00:00', null];
        yield 'date format: parse-only reset' => ['date:!Y-m-d', '2024-01-15', null];
        yield 'date format: int' => ['date:Ymd', 20240115, null];
        yield 'date format: with a comma' => ['date:D, d M Y', 'Mon, 15 Jan 2024', null];
        yield 'date format: comma kept in params' => [
            'date:D, d M Y',
            'nope',
            ['rule' => 'date_format', 'params' => ['D, d M Y']],
        ];

        yield 'regex: match' => ['regex:/^[a-z0-9_]+$/', 'user_1', null];
        yield 'regex: no match' => ['regex:/^[a-z0-9_]+$/', 'user@name', ['rule' => 'regex']];
        yield 'regex: comma in pattern' => ['regex:/^[a,b]+$/', 'a,b', null];
        yield 'regex: quantifier range' => ['regex:/^\\d{2,4}$/', '123', null];
        yield 'regex: int' => ['regex:/^42$/', 42, null];
        yield 'regex: array' => ['regex:/a/', ['a'], ['rule' => 'regex']];
        yield 'regex: object' => ['regex:/a/', new \stdClass(), ['rule' => 'regex']];
        yield 'regex: stringable' => ['regex:/^abc$/', $stringable, null];

        yield 'in: listed' => ['in:active,inactive', 'active', null];
        yield 'in: not listed' => ['in:active,inactive', 'pending', ['rule' => 'in', 'params' => ['active', 'inactive']]];
        yield 'in: int' => ['in:1,2,3', 2, null];
        yield 'in: array, all listed' => ['in:php,js', ['php', 'js'], null];
        yield 'in: array, one not listed' => ['in:php,js', ['php', 'ruby'], ['rule' => 'in', 'params' => ['php', 'js']]];
        yield 'in: nested array' => ['in:php,js', [['php']], ['rule' => 'in', 'params' => ['php', 'js']]];
        yield 'in: bool' => ['in:1,0', true, ['rule' => 'in', 'params' => ['1', '0']]];
        yield 'in: null is no value' => ['in:1,0', null, null];
        yield 'in: object' => ['in:a,b', new \stdClass(), ['rule' => 'in', 'params' => ['a', 'b']]];
        yield 'in: stringable' => ['in:abc,def', $stringable, null];

        yield 'min: long enough' => ['min:3', 'abc', null];
        yield 'min: too short' => ['min:3', 'ab', ['rule' => 'min', 'params' => ['3']]];
        yield 'min: multibyte' => ['min:3', 'Ёж', ['rule' => 'min', 'params' => ['3']]];
        yield 'min: array count' => ['min:2', ['a', 'b'], null];
        yield 'min: array too small' => ['min:2', ['a'], ['rule' => 'min', 'params' => ['2']]];
        yield 'min: object' => ['min:1', new \stdClass(), ['rule' => 'min', 'params' => ['1']]];
        yield 'min: bool' => ['min:1', true, ['rule' => 'min', 'params' => ['1']]];
        yield 'min: null is no value' => ['min:1', null, null];
        yield 'min: empty string has no characters' => ['min:1', '', ['rule' => 'min', 'params' => ['1']]];
        yield 'min: stringable' => ['min:3', $stringable, null];
        yield 'max: null is no value' => ['max:3', null, null];
        yield 'max: empty string has no characters' => ['max:0', '', null];
        yield 'max: bool' => ['max:3', true, ['rule' => 'max', 'params' => ['3']]];
        yield 'max: object' => ['max:3', new \stdClass(), ['rule' => 'max', 'params' => ['3']]];
        yield 'max: stringable' => ['max:3', $stringable, null];
        yield 'max: short enough' => ['max:3', 'abc', null];
        yield 'max: too long' => ['max:3', 'abcd', ['rule' => 'max', 'params' => ['3']]];
        yield 'max: int digits' => ['max:5', 12345, null];
        yield 'max: array too big' => ['max:1', ['a', 'b'], ['rule' => 'max', 'params' => ['1']]];
        yield 'length: exact' => ['length:4', '1234', null];
        yield 'length: wrong' => ['length:4', '123', ['rule' => 'length', 'params' => ['4']]];
        yield 'length: multibyte' => ['length:2', 'Ёж', null];
        yield 'length: array count' => ['length:3', ['a', 'b', 'c'], null];
        yield 'length: stringable' => ['length:3', $stringable, null];
        yield 'length: object' => ['length:3', new \stdClass(), ['rule' => 'length', 'params' => ['3']]];
        yield 'length: null is no value' => ['length:3', null, null];
        yield 'length: empty string is zero' => ['length:0', '', null];

        yield 'min_value: above' => ['min_value:18', 25, null];
        yield 'min_value: numeric string' => ['min_value:18', '25', null];
        yield 'min_value: below' => ['min_value:18', 15, ['rule' => 'min_value', 'params' => ['18']]];
        yield 'min_value: not a number' => ['min_value:18', 'abc', ['rule' => 'min_value', 'params' => ['18']]];
        yield 'min_value: overflow to INF' => ['min_value:1', '1e400', ['rule' => 'min_value', 'params' => ['1']]];
        yield 'max_value: INF' => ['max_value:1', -INF, ['rule' => 'max_value', 'params' => ['1']]];
        yield 'max_value: below' => ['max_value:100', 99, null];
        yield 'max_value: above' => ['max_value:100', '100.5', ['rule' => 'max_value', 'params' => ['100']]];
    }

    /**
     * @param array<string, mixed>                                              $data
     * @param array<string, string>                                             $rules
     * @param array<string, list<array{rule: string, params?: list<string>}>> $errors
     */
    #[DataProvider('payloads')]
    public function testCrossFieldRule(array $data, array $rules, array $errors): void
    {
        $this->assertSame($errors, (new Validator())->validate($data, $rules));
    }

    /**
     * @return iterable<string, array{array<string, mixed>, array<string, string>, array<string, mixed>}>
     */
    public static function payloads(): iterable
    {
        $requiredIf = ['name' => 'required_if:type,company'];
        $error = static fn (string $rule, string ...$params): array => ['rule' => $rule, 'params' => $params];

        yield 'required_if: condition met' => [['type' => 'company'], $requiredIf, [
            'name' => [$error('required_if', 'type', 'company')],
        ]];
        yield 'required_if: condition not met' => [['type' => 'person'], $requiredIf, []];
        yield 'required_if: filled' => [['type' => 'company', 'name' => 'Acme'], $requiredIf, []];
        yield 'required_if: any of several values' => [['type' => 'b'], ['name' => 'required_if:type,a,b'], [
            'name' => [$error('required_if', 'type', 'a', 'b')],
        ]];
        yield 'required_if: true spelled true' => [['vip' => true], ['note' => 'required_if:vip,true'], [
            'note' => [$error('required_if', 'vip', 'true')],
        ]];
        yield 'required_if: true spelled 1' => [['vip' => true], ['note' => 'required_if:vip,1'], [
            'note' => [$error('required_if', 'vip', '1')],
        ]];
        yield 'required_if: false is not true' => [['vip' => false], ['note' => 'required_if:vip,true'], []];
        yield 'required_if: checkbox "on" is true' => [['vip' => 'on'], ['note' => 'required_if:vip,true'], [
            'note' => [$error('required_if', 'vip', 'true')],
        ]];
        yield 'required_if: form "1" is true' => [['vip' => '1'], ['note' => 'required_if:vip,true'], [
            'note' => [$error('required_if', 'vip', 'true')],
        ]];
        yield 'required_if: form "off" is false' => [['vip' => 'off'], ['note' => 'required_if:vip,false'], [
            'note' => [$error('required_if', 'vip', 'false')],
        ]];
        yield 'required_if: "off" is not true' => [['vip' => 'off'], ['note' => 'required_if:vip,true'], []];
        yield 'required_if: absent is null' => [[], ['name' => 'required_if:type,null'], [
            'name' => [$error('required_if', 'type', 'null')],
        ]];
        yield 'required_if: runs under nullable' => [
            ['type' => 'company', 'name' => ''],
            ['name' => 'nullable|required_if:type,company'],
            ['name' => [$error('required_if', 'type', 'company')]],
        ];
        yield 'required_unless: not excepted' => [['type' => 'person'], ['name' => 'required_unless:type,company'], [
            'name' => [$error('required_unless', 'type', 'company')],
        ]];
        yield 'required_unless: excepted' => [['type' => 'company'], ['name' => 'required_unless:type,company'], []];

        yield 'required_with: one is filled' => [['phone' => '123'], ['email' => 'required_with:phone,fax'], [
            'email' => [$error('required_with', 'phone', 'fax')],
        ]];
        yield 'required_with: none is filled' => [['phone' => ''], ['email' => 'required_with:phone,fax'], []];
        yield 'required_with_all: all are filled' => [
            ['phone' => '1', 'fax' => '2'],
            ['email' => 'required_with_all:phone,fax'],
            ['email' => [$error('required_with_all', 'phone', 'fax')]],
        ];
        yield 'required_with_all: one is missing' => [['phone' => '1'], ['email' => 'required_with_all:phone,fax'], []];
        yield 'required_without: one is missing' => [['phone' => '1'], ['email' => 'required_without:phone,fax'], [
            'email' => [$error('required_without', 'phone', 'fax')],
        ]];
        yield 'required_without: none is missing' => [
            ['phone' => '1', 'fax' => '2'],
            ['email' => 'required_without:phone,fax'],
            [],
        ];
        yield 'required_without_all: all are missing' => [[], ['email' => 'required_without_all:phone,fax'], [
            'email' => [$error('required_without_all', 'phone', 'fax')],
        ]];
        yield 'required_without_all: one is filled' => [['fax' => '2'], ['email' => 'required_without_all:phone,fax'], []];

        yield 'filled: absent' => [[], ['bio' => 'filled|min:3'], []];
        yield 'filled: present and blank' => [['bio' => ''], ['bio' => 'filled|min:3'], ['bio' => [['rule' => 'filled']]]];

        yield 'present: absent' => [[], ['ids' => 'present|array'], ['ids' => [['rule' => 'present']]]];
        yield 'present: null under nullable' => [['note' => null], ['note' => 'nullable|present|string'], []];

        $prohibitedIf = ['company' => 'prohibited_if:type,person'];
        yield 'prohibited_if: condition met and filled' => [['type' => 'person', 'company' => 'Acme'], $prohibitedIf, [
            'company' => [$error('prohibited_if', 'type', 'person')],
        ]];
        yield 'prohibited_if: condition met and blank' => [['type' => 'person', 'company' => ''], $prohibitedIf, []];
        yield 'prohibited_if: whitespace is not filled' => [['type' => 'person', 'company' => '  '], $prohibitedIf, []];
        yield 'prohibited_if: condition met and absent' => [['type' => 'person'], $prohibitedIf, []];
        yield 'prohibited_if: condition not met' => [['type' => 'company', 'company' => 'Acme'], $prohibitedIf, []];
        yield 'prohibited_if: false is a value' => [['type' => 'person', 'company' => false], $prohibitedIf, [
            'company' => [$error('prohibited_if', 'type', 'person')],
        ]];
        yield 'prohibited_if: checkbox "on" is true' => [
            ['anonymous' => 'on', 'name' => 'John'],
            ['name' => 'prohibited_if:anonymous,true'],
            ['name' => [$error('prohibited_if', 'anonymous', 'true')]],
        ];
        yield 'prohibited_unless: not excepted' => [
            ['type' => 'person', 'company' => 'Acme'],
            ['company' => 'prohibited_unless:type,company'],
            ['company' => [$error('prohibited_unless', 'type', 'company')]],
        ];
        yield 'prohibited_unless: excepted' => [
            ['type' => 'company', 'company' => 'Acme'],
            ['company' => 'prohibited_unless:type,company'],
            [],
        ];

        $exclusive = ['name' => 'nullable|string|required_if:type,company|prohibited_if:type,person'];
        yield 'required_if with prohibited_if: required' => [['type' => 'company', 'name' => ''], $exclusive, [
            'name' => [$error('required_if', 'type', 'company')],
        ]];
        yield 'required_if with prohibited_if: prohibited' => [['type' => 'person', 'name' => 'Acme'], $exclusive, [
            'name' => [$error('prohibited_if', 'type', 'person')],
        ]];
        yield 'required_if with prohibited_if: neither' => [['type' => 'person'], $exclusive, []];
    }
}
