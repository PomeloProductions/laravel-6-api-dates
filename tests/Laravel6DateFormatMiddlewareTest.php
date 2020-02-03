<?php
declare(strict_types=1);

namespace PomeloProductions\Laravel6Middleware\Tests;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use PomeloProductions\Laravel6Middleware\Laravel6DateFormatMiddleware;

/**
 * Class Laravel6DateFormatMiddlewareTest
 * @package PomeloProductions\Laravel6Middleware\Tests
 */
class Laravel6DateFormatMiddlewareTest extends TestCase
{
    public function testFormatString()
    {
        $middleware = new Laravel6DateFormatMiddleware();

        $this->assertEquals('hi', $middleware->formatString('hi'));
        $this->assertEquals('2019-12-02 20:01:00', $middleware->formatString('2019-12-02T20:01:00.283041Z'));
        $this->assertEquals('2019-12-02 20:01:00', $middleware->formatString('2019-12-02 20:01:00'));
    }

    public function testFormatData()
    {
        $middleware = new Laravel6DateFormatMiddleware();

        $this->assertEquals([
            'created_at' => '2019-12-02 20:01:00',
            'id' => 124,
            'name' => 'Stan',
        ], $middleware->formatData([
            'created_at' => '2019-12-02T20:01:00.283041Z',
            'id' => 124,
            'name' => 'Stan',
        ]));

        $this->assertEquals([
            'created_at' => '2019-12-02 20:01:00',
            'id' => 124,
            'name' => 'Stan',
            'children' => [
                [
                    'name' => 'John',
                    'birthday' => '2019-01-02 20:01:00'
                ],
            ],
        ], $middleware->formatData([
            'created_at' => '2019-12-02T20:01:00.283041Z',
            'id' => 124,
            'name' => 'Stan',
            'children' => [
                [
                    'name' => 'John',
                    'birthday' => '2019-01-02T20:01:00.283041Z'
                ],
            ],
        ]));
    }

    public function testHandle()
    {
        $middleware = new Laravel6DateFormatMiddleware();
        $request = new Request();
        $next = function($request) {
            return new JsonResponse([
                'created_at' => '2019-12-02T20:01:00.283041Z',
                'id' => 124,
                'name' => 'Stan',
                'children' => [
                    [
                        'name' => 'John',
                        'birthday' => '2019-01-02T20:01:00.283041Z'
                    ],
                ],
            ]);
        };

        /** @var JsonResponse $response */
        $response = $middleware->handle($request, $next);

        $this->assertEquals([
            'created_at' => '2019-12-02 20:01:00',
            'id' => 124,
            'name' => 'Stan',
            'children' => [
                [
                    'name' => 'John',
                    'birthday' => '2019-01-02 20:01:00'
                ],
            ],
        ], $response->getData(true));
    }
}