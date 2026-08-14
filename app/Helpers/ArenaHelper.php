<?php

if (!function_exists('active_arena')) {
    function active_arena() {
        $slug = session('active_arena_slug');
        if ($slug) {
            $arena = \App\Models\Pengaturan::where('slug', $slug)->first();
            if ($arena) return $arena;
        }
        return \App\Models\Pengaturan::first() ?? new \App\Models\Pengaturan();
    }
}
