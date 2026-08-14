<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jadwal;
use Illuminate\Support\Facades\DB;

class CleanDuplicates extends Command
{
    protected $signature = 'clean:duplicates';
    protected $description = 'Clean duplicate jadwal records';

    public function handle()
    {
        $this->info('Finding duplicates...');
        $duplicates = Jadwal::select('tanggal', 'lapangan_id', 'jam_mulai')
            ->groupBy('tanggal', 'lapangan_id', 'jam_mulai')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $deleted = 0;
        foreach ($duplicates as $dup) {
            $records = Jadwal::where('tanggal', $dup->tanggal)
                ->where('lapangan_id', $dup->lapangan_id)
                ->where('jam_mulai', $dup->jam_mulai)
                ->orderBy('id', 'asc') // Keep the oldest by default if all are tersedia
                ->get();

            $booked = $records->where('status', '!=', 'tersedia');
            
            if ($booked->count() > 0) {
                // If there's a booked record, keep the booked one, delete the rest
                $keepId = $booked->first()->id;
                foreach ($records as $r) {
                    if ($r->id != $keepId) {
                        $r->delete();
                        $deleted++;
                    }
                }
            } else {
                // All are tersedia, keep the first one
                $keepId = $records->first()->id;
                foreach ($records as $r) {
                    if ($r->id != $keepId) {
                        $r->delete();
                        $deleted++;
                    }
                }
            }
        }

        $this->info("Deleted $deleted duplicate records.");
    }
}
