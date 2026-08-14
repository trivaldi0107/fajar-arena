<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Pengaturan;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        Pengaturan::create([
            'slug' => 'fajar-arena-utama',
            'nama_arena' => 'Fajar Arena Utama',
            'jenis_olahraga' => 'Badminton',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
