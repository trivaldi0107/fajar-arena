<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    public function up(): void
    {
        $possibleImages = [
            'pengaturan/0lQR3oZeGKAqwd60RiGtQbsauk63hrbVsYK5IbQy.jpg',
            'pengaturan/ClScPq4sMCeh4RmpjogadnZQ4r4LcsC25nj2qxqn.jpg',
            'pengaturan/jG3nTKu0Kj4avjxNEM3WkPM8iR7uc5Nmvmxk1ZEZ.jpg',
            'pengaturan/mkWu9UqwP3Lz2ZY7SS82JgDfOmHMDNBEm0730JAq.jpg'
        ];

        $foundImage = null;
        foreach ($possibleImages as $img) {
            if (File::exists(storage_path('app/public/' . $img)) || File::exists(public_path('storage/' . $img))) {
                $foundImage = $img;
                break;
            }
        }

        if (!$foundImage) {
            $foundImage = 'pengaturan/0lQR3oZeGKAqwd60RiGtQbsauk63hrbVsYK5IbQy.jpg';
        }

        DB::table('pengaturan')
            ->where('id', 1)
            ->update([
                'auth_bg_image' => $foundImage
            ]);
    }

    public function down(): void
    {
    }
};
