<?php
$file = 'app/Http/Controllers/AdminController.php';
$content = file_get_contents($file);

$oldCode = <<<'EOD'
            $jadwals = \App\Models\Jadwal::where('tanggal', $tanggal)->get();
            
            foreach ($jadwals as $jadwal) {
                $jadwal->update(['status' => $status]);
            }
EOD;

$newCode = <<<'EOD'
            $lapanganIds = $lapangan->pluck('id');
            $jadwals = \App\Models\Jadwal::where('tanggal', $tanggal)->whereIn('lapangan_id', $lapanganIds)->get();
            
            foreach ($jadwals as $jadwal) {
                $jadwal->update(['status' => $status]);
            }
EOD;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Fixed jadwalUpdateAll scope.";
