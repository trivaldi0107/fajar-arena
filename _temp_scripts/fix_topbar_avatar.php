<?php
$file = 'resources/views/admin/partials/topbar.blade.php';
$content = file_get_contents($file);

$oldAvatar = <<<EOF
        <!-- Avatar -->

        <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg">

            {{ strtoupper(substr(Auth::user()->name,0,1)) }}

        </div>
EOF;

$newAvatar = <<<EOF
        <!-- Avatar -->

        <a href="{{ route('profile.edit') }}" class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg cursor-pointer hover:bg-blue-700 hover:scale-105 transition-all shadow-sm">

            {{ strtoupper(substr(Auth::user()->name,0,1)) }}

        </a>
EOF;

$content = str_replace($oldAvatar, $newAvatar, $content);
file_put_contents($file, $content);
echo "Topbar avatar updated.\n";
