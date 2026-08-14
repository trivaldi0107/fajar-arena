<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jadwal;
use Illuminate\Support\Facades\DB;

class CleanDuplicatesFast extends Command
{
    protected $signature = 'clean:duplicates-fast';
    protected $description = 'Clean duplicate jadwal records fast';

    public function handle()
    {
        $this->info('Finding duplicates fast...');
        $duplicates = DB::select("
            SELECT tanggal, lapangan_id, jam_mulai, COUNT(*) as c
            FROM jadwal
            GROUP BY tanggal, lapangan_id, jam_mulai
            HAVING c > 1
        ");

        $deleted = 0;
        foreach ($duplicates as $dup) {
            $records = DB::select("
                SELECT id, status 
                FROM jadwal 
                WHERE tanggal = ? AND lapangan_id = ? AND jam_mulai = ?
                ORDER BY id ASC
            ", [$dup->tanggal, $dup->lapangan_id, $dup->jam_mulai]);

            $booked = array_filter($records, function($r) { return $r->status != 'tersedia'; });
            
            if (count($booked) > 0) {
                // If there's a booked record, keep the booked one, delete the rest
                $keepId = reset($booked)->id;
            } else {
                // All are tersedia, keep the first one
                $keepId = $records[0]->id;
            }

            $idsToDelete = [];
            foreach ($records as $r) {
                if ($r->id != $keepId) {
                    $idsToDelete[] = $r->id;
                }
            }

            if (!empty($idsToDelete)) {
                DB::table('jadwal')->whereIn('id', $idsToDelete)->delete();
                $deleted += count($idsToDelete);
            }
        }

        $this->info("Deleted $deleted duplicate records fast.");
    }
}
