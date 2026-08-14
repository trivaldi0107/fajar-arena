<?php

// 1. Fix Sidebar Logic
$sidebarFile = 'resources/views/admin/partials/sidebar.blade.php';
$sidebarContent = file_get_contents($sidebarFile);

// Replace for Kelola Lapangan a tag
$sidebarContent = str_replace(
    "request()->routeIs('admin.lapangan.index')",
    "(request()->routeIs('admin.lapangan.index') || request()->routeIs('admin.lapangan.edit'))",
    $sidebarContent
);
file_put_contents($sidebarFile, $sidebarContent);


// 2. Fix AdminController
$controllerFile = 'app/Http/Controllers/AdminController.php';
$controllerContent = file_get_contents($controllerFile);

// Add prefix_lapangan to fillable array inside savePengaturanData
if (strpos($controllerContent, "'prefix_lapangan'") === false || strpos($controllerContent, "'slug', 'nama_arena', 'jenis_olahraga'") !== false) {
    $controllerContent = str_replace(
        "'slug', 'nama_arena', 'jenis_olahraga',",
        "'slug', 'nama_arena', 'jenis_olahraga', 'prefix_lapangan',",
        $controllerContent
    );
    file_put_contents($controllerFile, $controllerContent);
}

echo "Fixed sidebar and controller.\n";
