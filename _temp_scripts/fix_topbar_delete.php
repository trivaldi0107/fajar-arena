<?php
$file = 'resources/views/admin/partials/topbar.blade.php';
$content = file_get_contents($file);

// Replace the foreach block in the dropdown
$oldForeach = <<<EOF
                @foreach(\$arenas as \$arena)
                    <a href="{{ route('admin.set_arena', \$arena->slug) }}" class="block px-4 py-3 hover:bg-slate-50 transition-colors {{ \$activeSlug === \$arena->slug ? 'bg-blue-50' : '' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold {{ \$activeSlug === \$arena->slug ? 'text-blue-700' : 'text-slate-800' }}">{{ \$arena->nama_arena }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ \$arena->jenis_olahraga }}</p>
                            </div>
                            @if(\$activeSlug === \$arena->slug)
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @endif
                        </div>
                    </a>
                @endforeach
EOF;

$newForeach = <<<EOF
                @foreach(\$arenas as \$arena)
                    <div class="relative group/item flex items-center border-b border-gray-50 last:border-0 hover:bg-slate-50 transition-colors {{ \$activeSlug === \$arena->slug ? 'bg-blue-50/50' : '' }}">
                        <a href="{{ route('admin.set_arena', \$arena->slug) }}" class="flex-1 block px-4 py-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-bold {{ \$activeSlug === \$arena->slug ? 'text-blue-700' : 'text-slate-800' }}">{{ \$arena->nama_arena }}</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ \$arena->jenis_olahraga }}</p>
                                </div>
                                @if(\$activeSlug === \$arena->slug)
                                    <svg class="w-5 h-5 text-blue-600 mr-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @endif
                            </div>
                        </a>
                        @if(count(\$arenas) > 1)
                        <button type="button" onclick="confirmDeleteTopbar({{ \$arena->id }})" class="absolute right-3 p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors z-10" title="Hapus Cabang">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                        @endif
                    </div>
                @endforeach
EOF;

$content = str_replace($oldForeach, $newForeach, $content);

// Add the JS and Form at the end of the file
$scriptForm = <<<EOF

<form id="form-hapus-topbar" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
<script>
function confirmDeleteTopbar(id) {
    if(typeof Swal === 'undefined') {
        if(confirm('Hapus cabang ini? Semua data terkait akan terhapus permanen!')) {
            let form = document.getElementById('form-hapus-topbar');
            form.action = "/admin/lapangan/destroy/" + id;
            form.submit();
        }
        return;
    }
    Swal.fire({
        title: 'Hapus Cabang Ini?',
        text: "Semua pengaturan dan jadwal terkait cabang ini akan ikut terhapus dan tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            let form = document.getElementById('form-hapus-topbar');
            form.action = "/admin/lapangan/destroy/" + id;
            form.submit();
        }
    })
}
</script>
EOF;

if (strpos($content, 'confirmDeleteTopbar') === false) {
    $content .= $scriptForm;
}

file_put_contents($file, $content);
echo "Topbar updated with delete buttons in dropdown.\n";
