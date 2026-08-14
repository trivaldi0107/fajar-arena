<?php
$file = 'resources/views/admin/lapangan/edit.blade.php';
$content = file_get_contents($file);

// Replace the ACTION BUTTONS div
$oldButtons = <<<EOF
        <!-- ACTION BUTTONS -->
        <div class="mt-8 flex items-center justify-end gap-3 bg-white p-6 rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100">
            <a href="{{ route('admin.lapangan.index') }}" class="px-6 py-2.5 rounded-xl font-bold text-gray-700 bg-white border border-gray-300 shadow-sm hover:bg-gray-50 hover:text-gray-900 transition-all">
                Batal
            </a>
            <button type="submit" id="btn-simpan" class="px-8 py-2.5 rounded-xl font-bold text-white bg-blue-600 shadow-[0_4px_12px_rgba(37,99,235,0.3)] hover:bg-blue-700 hover:shadow-[0_6px_15px_rgba(37,99,235,0.4)] hover:-translate-y-0.5 transition-all">
                Simpan Perubahan
            </button>
        </div>
EOF;

$newButtons = <<<EOF
        <!-- ACTION BUTTONS -->
        <div class="mt-8 flex items-center justify-between gap-3 bg-white p-6 rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100">
            <div>
                @if(isset(\$pengaturan->id) && \App\Models\Pengaturan::count() > 1)
                <button type="button" onclick="confirmDeleteCabang()" class="px-5 py-2.5 rounded-xl font-bold text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-700 transition-all border border-red-100">
                    <svg class="w-5 h-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    Hapus Cabang
                </button>
                @endif
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.lapangan.index') }}" class="px-6 py-2.5 rounded-xl font-bold text-gray-700 bg-white border border-gray-300 shadow-sm hover:bg-gray-50 hover:text-gray-900 transition-all">
                    Batal
                </a>
                <button type="submit" id="btn-simpan" class="px-8 py-2.5 rounded-xl font-bold text-white bg-blue-600 shadow-[0_4px_12px_rgba(37,99,235,0.3)] hover:bg-blue-700 hover:shadow-[0_6px_15px_rgba(37,99,235,0.4)] hover:-translate-y-0.5 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </div>
EOF;

$content = str_replace($oldButtons, $newButtons, $content);

// Add the delete form and script at the bottom
$deleteForm = <<<EOF
@if(isset(\$pengaturan->id))
<form id="form-hapus-cabang" action="{{ route('admin.lapangan.destroy', \$pengaturan->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
<script>
function confirmDeleteCabang() {
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
            document.getElementById('form-hapus-cabang').submit();
        }
    })
}
</script>
@endif
EOF;

$content = str_replace('@endsection', $deleteForm . "\n@endsection", $content);

file_put_contents($file, $content);
echo "Added hapus button and form to edit.blade.php\n";
