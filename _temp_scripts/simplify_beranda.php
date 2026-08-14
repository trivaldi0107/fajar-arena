<?php
$c = file_get_contents('resources/views/admin/beranda/edit.blade.php');
$c = preg_replace('#<style>.*?</style>#s', '', $c);
$c = preg_replace('#<!-- STEPPER INDICATOR -->.*?</div>\s*</div>\s*</div>#s', '', $c);
$c = str_replace('<div class="step-section active" id="step-1">', '<div>', $c);
$c = str_replace('<div class="step-section" id="step-2">', '<div class="mt-8">', $c);
$c = preg_replace('#<!-- JAVASCRIPT FOR MULTI-STEP LOGIC -->.*?</script>#s', '', $c);
file_put_contents('resources/views/admin/beranda/edit.blade.php', $c);
echo "Beranda simplified!";
