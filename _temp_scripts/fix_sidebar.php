<?php
$file = 'resources/views/admin/partials/sidebar.blade.php';
$content = file_get_contents($file);

$kelolaLapanganHtml = <<<EOF
            <a href="{{ route('admin.lapangan.index') }}"
               class="flex items-center gap-4 px-5 py-4 rounded-xl mt-1 transition
               {{ request()->routeIs('admin.lapangan.index')
                       ? 'bg-blue-50 text-blue-600 font-semibold shadow-sm border border-blue-100/50'
                       : 'text-gray-500 font-medium hover:bg-slate-50 hover:text-blue-600' }}">

                <svg class="w-5 h-5 {{ request()->routeIs('admin.lapangan.index') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>

                Kelola Lapangan
            </a>
EOF;

// Since request()->routeIs('admin.lapangan.*') was used, let's fix that as well so it only highlights for index
$oldKelolaLapanganRegex = '/<a href="\{\{\s*route\(\'admin\.lapangan\.index\'\)\s*\}\}".*?Kelola Lapangan\s*<\/a>/s';

$tambahLapanganHtml = <<<EOF
            <a href="{{ route('admin.lapangan.create') }}"
               class="flex items-center gap-4 px-5 py-4 rounded-xl mt-1 transition
               {{ request()->routeIs('admin.lapangan.create')
                       ? 'bg-blue-50 text-blue-600 font-semibold shadow-sm border border-blue-100/50'
                       : 'text-gray-500 font-medium hover:bg-slate-50 hover:text-blue-600' }}">

                <svg class="w-5 h-5 {{ request()->routeIs('admin.lapangan.create') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>

                Tambah Lapangan
            </a>
EOF;

$content = preg_replace($oldKelolaLapanganRegex, $kelolaLapanganHtml . "\n\n" . $tambahLapanganHtml, $content);
file_put_contents($file, $content);
echo "Sidebar updated.\n";
