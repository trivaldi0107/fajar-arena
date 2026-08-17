<?php

if (!function_exists('active_arena')) {
    function active_arena() {
        if (request()->filled('arena')) {
            $param = request()->query('arena');
            $arena = \App\Models\Pengaturan::where('slug', $param)
                ->orWhere('jenis_olahraga', $param)
                ->orWhere('id', $param)
                ->first();
            if ($arena) {
                session(['active_arena_slug' => $arena->slug]);
                return $arena;
            }
        }
        if (request()->filled('cabor')) {
            $param = request()->query('cabor');
            $arena = \App\Models\Pengaturan::where('slug', $param)
                ->orWhere('jenis_olahraga', $param)
                ->orWhere('id', $param)
                ->first();
            if ($arena) {
                session(['active_arena_slug' => $arena->slug]);
                return $arena;
            }
        }
        $slug = session('active_arena_slug');
        if ($slug) {
            $arena = \App\Models\Pengaturan::where('slug', $slug)->first();
            if ($arena) return $arena;
        }
        return \App\Models\Pengaturan::first() ?? new \App\Models\Pengaturan();
    }
}
