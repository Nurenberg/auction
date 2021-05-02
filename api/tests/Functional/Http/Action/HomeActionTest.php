<?php

declare(strict_types=1);

namespace Test\Functional\Http\Action;

use Test\Functional\Http\WebTestCase;

/**
 * @coversNothing
 */
class HomeActionTest extends WebTestCase
{
    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('GET', '/'));

        $this->assertEquals(json_encode(['pool' => 'imARich']), (string)$response->getBody());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUnsupportedMethod(): void
    {
        $response = $this->app()->handle(self::json('POST', '/'));

        $this->assertEquals(405, $response->getStatusCode());
    }
}
