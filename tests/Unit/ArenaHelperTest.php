<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArenaHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_arena_returns_arena_based_on_session_slug(): void
    {
        $arena1 = Pengaturan::create([
            'slug' => 'fajar-utama',
            'nama_arena' => 'Fajar Arena Utama',
            'jenis_olahraga' => 'Badminton',
        ]);

        $arena2 = Pengaturan::create([
            'slug' => 'fajar-cabang',
            'nama_arena' => 'Fajar Arena Cabang',
            'jenis_olahraga' => 'Badminton',
        ]);

        session(['active_arena_slug' => 'fajar-cabang']);

        $active = active_arena();

        $this->assertEquals('fajar-cabang', $active->slug);
        $this->assertEquals('Fajar Arena Cabang', $active->nama_arena);
    }

    public function test_active_arena_falls_back_to_first_arena_when_no_session(): void
    {
        $arena = Pengaturan::create([
            'slug' => 'fajar-default',
            'nama_arena' => 'Fajar Arena Default',
            'jenis_olahraga' => 'Badminton',
        ]);

        session()->forget('active_arena_slug');

        $active = active_arena();

        $this->assertEquals('fajar-default', $active->slug);
    }

    public function test_active_arena_returns_new_model_when_table_empty(): void
    {
        session()->forget('active_arena_slug');

        $active = active_arena();

        $this->assertInstanceOf(Pengaturan::class, $active);
        $this->assertNull($active->id);
    }
}
