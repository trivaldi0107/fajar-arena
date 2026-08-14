<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaturan;

class PortalController extends Controller
{
    public function index()
    {
        $arenas = Pengaturan::withCount("lapangan")->get();
        
        // Jika hanya ada 1 cabang (atau 0), langsung ke reservasi
        if ($arenas->count() <= 1) {
            $arena = $arenas->first() ?? new Pengaturan();
            if ($arena->slug) {
                session(["active_arena_slug" => $arena->slug]);
            }
            return redirect()->route('reservasi');
        }
        
        return view("portal", compact("arenas"));
    }

    public function setArena($slug)
    {
        session(["active_arena_slug" => $slug]);
        return redirect()->route("reservasi");
    }
}
