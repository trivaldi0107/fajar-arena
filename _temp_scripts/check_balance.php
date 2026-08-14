<?php
$c = file_get_contents('resources/views/reservasi/index.blade.php');
preg_match_all('/@(if|endif)\b/', $c, $m, PREG_OFFSET_CAPTURE);
$stack = [];
foreach($m[1] as $t) {
    if ($t[0] == 'if') {
        $stack[] = $t[1];
    } else {
        if (empty($stack)) {
            echo "Unexpected endif at offset " . $t[1] . "\n";
            $line = substr_count(substr($c, 0, $t[1]), "\n") + 1;
            echo "Line: " . $line . "\n";
        } else {
            array_pop($stack);
        }
    }
}
if (!empty($stack)) {
    echo "Unmatched if(s) at offsets: " . print_r($stack, true) . "\n";
}
