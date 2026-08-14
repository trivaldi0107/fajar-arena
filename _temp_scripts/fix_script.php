<?php
$file = 'resources/views/admin/beranda/edit.blade.php';
$c = file_get_contents($file);

if (strpos($c, 'function editSlide') === false) {
    $script = "
<script>
    function editSlide(id, judul, tagline, deskripsi) {
        document.querySelector('input[name=\"slider_judul\"]').value = judul;
        document.querySelector('input[name=\"slider_tagline\"]').value = tagline;
        document.querySelector('textarea[name=\"slider_deskripsi\"]').value = deskripsi;
        
        let idInput = document.getElementById('slider_edit_id');
        if(!idInput) {
            idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'slider_id';
            idInput.id = 'slider_edit_id';
            document.getElementById('slider-form-container').appendChild(idInput);
        }
        idInput.value = id;

        document.getElementById('slider-form-title').innerText = 'Edit Slide';
        document.getElementById('slider-image-label').innerText = 'Upload Gambar (Opsional jika tidak ingin mengganti, Max 2MB)';
        
        const btn = document.getElementById('submit-slider-btn');
        btn.innerHTML = 'Simpan Perubahan';
        btn.formAction = \"{{ route('admin.sliders.update') }}\";
        
        document.getElementById('cancel-edit-btn').classList.remove('hidden');
        
        document.getElementById('slider-form-container').scrollIntoView({behavior: 'smooth', block: 'center'});
    }

    function cancelEditSlide() {
        document.querySelector('input[name=\"slider_judul\"]').value = '';
        document.querySelector('input[name=\"slider_tagline\"]').value = '';
        document.querySelector('textarea[name=\"slider_deskripsi\"]').value = '';
        document.querySelector('input[name=\"slider_gambar\"]').value = '';
        
        let idInput = document.getElementById('slider_edit_id');
        if(idInput) idInput.remove();

        document.getElementById('slider-form-title').innerText = 'Tambah Slide Baru';
        document.getElementById('slider-image-label').innerText = 'Upload Gambar (Wajib, Max 2MB)';
        
        const btn = document.getElementById('submit-slider-btn');
        btn.innerHTML = '+ Tambah Slide';
        btn.formAction = \"{{ route('admin.sliders.store') }}\";
        
        document.getElementById('cancel-edit-btn').classList.add('hidden');
    }
</script>
";
    $c = str_replace('@endsection', $script . "\n@endsection", $c);
    file_put_contents($file, $c);
    echo "Script added successfully!";
} else {
    echo "Script already exists.";
}
