<?php

declare(strict_types=1);

namespace Test\Unit\Http;

use App\Http\JsonResponse;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \App\Http\JsonResponse
 */
class JsonResponseTest extends TestCase
{
    /**
     * @dataProvider getCases
     * @var mixed $source
     * @var mixed $expect
     * @throws \JsonException
     */
    public function testResponse($source, $expect, int $status = 200): void
    {
        $response = new JsonResponse($source, $status);

        $this->assertEquals($expect, (string)$response->getBody());
        $this->assertEquals($status, $response->getStatusCode());
    }

    public function getCases(): array
    {
        $obj = new stdClass();
        $obj->str = 'value';
        $obj->int = 1;
        $obj->none = null;

        return [
            'int' => [1, '1'],
            'string' => ['1', '"1"'],
            'anyWithStatus' =>  ['1', '"1"', 201],
            'array' => [
                ['str' => 'value', 'int' => 1, 'none' => null],
                '{"str":"value","int":1,"none":null}'
            ],
            'object' => [
                $obj,
                '{"str":"value","int":1,"none":null}'
            ]
        ];
    }
}
