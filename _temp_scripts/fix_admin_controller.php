<?php
$file = 'app/Http/Controllers/AdminController.php';
$c = file_get_contents($file);

$search = "return view('admin.lapangan.create', compact('pengaturan', 'pusat'));";
$replace = "\$sliders = collect();\n        return view('admin.lapangan.create', compact('pengaturan', 'pusat', 'sliders'));";

$c = str_replace($search, $replace, $c);
file_put_contents($file, $c);
echo "Fixed AdminController";
