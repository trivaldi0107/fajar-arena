<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_passes_middleware(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $request = Request::create('/admin/dashboard', 'GET');
        $middleware = new AdminMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return response('Access Granted');
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Access Granted', $response->getContent());
    }

    public function test_regular_user_is_forbidden_with_403(): void
    {
        $this->expectException(HttpException::class);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user);

        $request = Request::create('/admin/dashboard', 'GET');
        $middleware = new AdminMiddleware();

        $middleware->handle($request, function ($req) {
            return response('Access Granted');
        });
    }

    public function test_guest_is_forbidden_with_403(): void
    {
        $this->expectException(HttpException::class);

        $request = Request::create('/admin/dashboard', 'GET');
        $middleware = new AdminMiddleware();

        $middleware->handle($request, function ($req) {
            return response('Access Granted');
        });
    }
}
