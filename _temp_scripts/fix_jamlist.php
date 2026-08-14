<?php
$file = 'app/Http/Controllers/AdminController.php';
$content = file_get_contents($file);

$oldJamList = <<<EOF
        \$jamList = [
            '08:00', '09:00', '10:00', '11:00', '12:00', '13:00',
            '14:00', '15:00', '16:00', '17:00', '18:00', '19:00',
            '20:00', '21:00', '22:00'
        ];
EOF;

$newJamList = <<<EOF
        \$jamBuka = (int) (active_arena()->jam_buka ?? 8);
        \$jamTutup = (int) (active_arena()->jam_tutup ?? 22);
        \$jamList = [];
        for (\$i = \$jamBuka; \$i < \$jamTutup; \$i++) {
            \$jamList[] = str_pad(\$i, 2, '0', STR_PAD_LEFT) . ':00';
        }
EOF;

$content = str_replace($oldJamList, $newJamList, $content);
file_put_contents($file, $content);
echo "AdminController.php fixed.\n";
