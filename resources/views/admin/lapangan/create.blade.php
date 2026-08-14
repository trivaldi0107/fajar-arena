@extends('admin.layouts.app')

@section('title', 'Tambah Lapangan - Fajar Arena')

@section('content')

<!-- Header -->
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">Tambah Lapangan</h2>
    </div>
</div>

<style>
    /* CSS untuk transisi form */
    .step-section {
        display: none;
        opacity: 0;
        transform: translateY(15px);
        transition: opacity 0.4s ease, transform 0.4s ease;
    }
    
    .step-section.active {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }
    
    /* Custom Input styling focus */
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        border-color: #3b82f6; /* blue-500 */
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }
</style>

<!-- Main Wrapper -->
<div class="max-w-6xl mx-auto pb-10">

    <!-- STEPPER INDICATOR -->
    <div class="bg-white rounded-full p-3 md:p-4 md:px-8 mb-8 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border border-gray-100 flex items-center justify-between relative overflow-hidden z-10">
        
            @php
        $steps = [
            1 => 'Info Arena',
            2 => 'Lokasi & Kontak',
            3 => 'Lapangan & Harga',
            4 => 'Member',
        ];
    @endphp

        <!-- Line Background and Progress Wrapper -->
        <div id="line-wrapper" class="absolute top-[26px] md:top-[38px] left-[10%] right-[10%] h-[3px] -z-10">
            <!-- Gray background -->
            <div class="w-full h-full bg-gray-200 rounded-full"></div>
            <!-- Blue progress -->
            <div id="stepper-progress" class="absolute top-0 left-0 h-full bg-blue-600 rounded-full transition-all duration-500 ease-out" style="width: 0%;"></div>
        </div>

        @foreach($steps as $num => $label)
            <div class="step-indicator flex flex-col items-center gap-1 md:gap-2 cursor-pointer group w-[25%]" onclick="goToStep({{ $num }})" id="step-indicator-{{ $num }}">
                <!-- Circle -->
                <div class="step-circle w-8 h-8 md:w-12 md:h-12 rounded-full flex items-center justify-center text-sm md:text-lg font-bold transition-all duration-300 shadow-sm border-[3px] md:border-4 border-white relative z-10
                    {{ $num == 1 ? 'bg-blue-600 text-white shadow-blue-500/30 ring-2 md:ring-4 ring-blue-50' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                    id="step-circle-{{ $num }}">
                    
                    <span class="step-number">{{ $num }}</span>
                </div>
                <!-- Label -->
                <span class="hidden md:block text-xs md:text-sm font-semibold transition-colors duration-300 {{ $num == 1 ? 'text-blue-700' : 'text-gray-500 group-hover:text-gray-700' }}" id="step-label-{{ $num }}">
                    {{ $label }}
                </span>
            </div>
        @endforeach
    </div>

        <!-- Error Display -->
    @if ($errors->any())
        <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan saat menyimpan data:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- FORM SECTIONS -->
    <form id="lapangan-form" method="POST" action="{{ route('admin.lapangan.store') }}" enctype="multipart/form-data" novalidate>
        @csrf
        <input type="hidden" name="step" id="current_step_input" value="1">
        
        <!-- STEP 1: INFO ARENA -->
        <div class="step-section active" id="step-1">
            <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 md:p-8 mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Informasi Dasar Arena</h3>
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lapangan / Cabang <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_arena" required class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('nama_arena') }}" placeholder="Contoh: Fajar Arena Cabang 2...">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Cabang Olahraga (Cabor) <span class="text-red-500">*</span></label>
                        <input type="text" name="jenis_olahraga" required class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('jenis_olahraga') }}" placeholder="Ketik Cabang Olahraga (contoh: Badminton, Futsal, Basket...)">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Singkat (Teks Card) <span class="text-red-500">*</span></label>
                        <textarea name="deskripsi" required rows="1" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white resize-none" placeholder="Contoh: Nikmati fasilitas olahraga terbaik...">{{ old('deskripsi', $pengaturan->deskripsi) }}</textarea>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Gambar Arena (Muncul di Halaman Pemesanan) <span class="text-red-500">*</span></label>
                    <input type="file" name="gambar_utama" required class="form-input w-full px-4 py-3 rounded-xl border border-gray-700 transition-all bg-gray-50 focus:bg-white cursor-pointer" accept="image/*">
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 md:p-8 mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Fasilitas</h3>
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Fasilitas Utama</label>
                    <div class="flex flex-wrap gap-3">
                        @php 
                            $fasilitas = ['Kipas', 'Parkiran', 'Kantin', 'Wifi', 'Musholla', 'Toilet', 'Locker Room']; 
                            $checked = json_decode($pengaturan->fasilitas ?? '[]', true) ?: [];
                        @endphp
                        @foreach($fasilitas as $f)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="fasilitas[]" value="{{ $f }}" class="peer sr-only" {{ in_array($f, $checked) ? 'checked' : '' }}>
                            <div class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-medium text-sm transition-all peer-checked:bg-blue-50 peer-checked:border-blue-500 peer-checked:text-blue-700 hover:bg-gray-50 shadow-sm">
                                {{ $f }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Fasilitas Tambahan (Opsional)</label>
                    <textarea name="fasilitas_tambahan" rows="3" class="form-textarea w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 placeholder-gray-400 transition-all bg-gray-50 focus:bg-white resize-none" placeholder="Tuliskan fasilitas lain yang belum ada di daftar atas...">{{ old('fasilitas_tambahan', $pengaturan->fasilitas_tambahan) }}</textarea>
                </div>
            </div>
        </div>

        <!-- STEP 2: LOKASI & KONTAK -->
        <div class="step-section" id="step-2">
            <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 md:p-8 mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Lokasi & Informasi Kontak</h3>
                
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap <span class="text-red-500">*</span></label>
                    <textarea name="alamat" required rows="3" class="form-textarea w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white resize-none">{{ old('alamat', $pengaturan->alamat) }}</textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kode HTML Iframe Peta (Google Maps) <span class="text-red-500">*</span></label>
                    <textarea name="link_maps" required rows="2" class="form-textarea w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white resize-none" placeholder="Tempelkan Kode HTML Iframe di sini (Contoh: <iframe src=... ></iframe>)">{{ old('link_maps', $pengaturan->link_maps ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kota <span class="text-red-500">*</span></label>
                        <input type="text" name="kota" required class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('kota', $pengaturan->kota) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Provinsi <span class="text-red-500">*</span></label>
                        <input type="text" name="provinsi" required class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('provinsi', $pengaturan->provinsi) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Pos <span class="text-red-500">*</span></label>
                        <input type="text" name="kodepos" required class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('kodepos', $pengaturan->kodepos) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">No Telepon <span class="text-red-500">*</span></label>
                        <input type="text" name="no_telp" required class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('no_telp', $pengaturan->no_telp) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('email', $pengaturan->email) }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 3: LAPANGAN & HARGA -->
        <div class="step-section" id="step-3">
            <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 md:p-8 mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Konfigurasi Lapangan & Harga</h3>
                
                @php
                    $bukaDefault = old('jam_buka', $pengaturan->jam_buka ?? '08:00:00');
                    $tutupDefault = old('jam_tutup', $pengaturan->jam_tutup ?? '23:00:00');
                    
                    $bukaHour = (int) substr($bukaDefault, 0, 2);
                    $tutupHour = (int) substr($tutupDefault, 0, 2);
                    if ($tutupHour == 0 && $tutupDefault == '00:00:00') $tutupHour = 24;

                    if ($tutupHour <= $bukaHour) {
                        $tutupDefault = '23:00:00';
                    }

                    $jb_opts = [];
                    for($i=0; $i<=23; $i++) { 
                        $t = sprintf('%02d:00:00', $i); 
                        $jb_opts[] = ['value' => $t, 'label' => sprintf('%02d:00', $i)]; 
                    }

                    $jt_opts = [];
                    for($i = $bukaHour + 1; $i <= 24; $i++) { 
                        $valHour = ($i == 24) ? 0 : $i;
                        $t = sprintf('%02d:00:00', $valHour); 
                        $label = sprintf('%02d:00', $valHour);
                        $jt_opts[] = ['value' => $t, 'label' => $label]; 
                    }
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Awalan Nama <span class="text-red-500">*</span></label>
                        <input type="text" name="prefix_lapangan" required placeholder="Contoh: Lapangan, Court, Meja" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('prefix_lapangan') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jumlah Lapangan <span class="text-red-500">*</span></label>
                        <input type="number" min="1" max="100" name="jumlah_lapangan" required placeholder="Masukkan jumlah lapangan" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('jumlah_lapangan') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jam Buka <span class="text-red-500">*</span></label>
                        <x-custom-select name="jam_buka" :default="$bukaDefault" :options="$jb_opts" placeholder="Jam Buka"/>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jam Tutup <span class="text-red-500">*</span></label>
                        <x-custom-select name="jam_tutup" :default="$tutupDefault" :options="$jt_opts" placeholder="Jam Tutup"/>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Harga Per Jam (Non Member) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 text-gray-500 font-medium">Rp</span>
                            <input type="text" name="harga_per_jam" required placeholder="Masukkan harga per jam" class="form-input w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('harga_per_jam') ? number_format((float)preg_replace('/[^0-9]/', '', old('harga_per_jam')), 0, ',', '.') : '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 5: KONFIGURASI MEMBER -->
        <div class="step-section" id="step-4">
            <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 md:p-8 mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Konfigurasi Member</h3>
                
                <label class="flex items-center gap-4 bg-blue-50/50 border border-blue-100 p-4 rounded-xl mb-6 cursor-pointer hover:bg-blue-50 transition-colors">
                    <div class="relative flex items-center justify-center">
                        <input type="checkbox" name="is_member_active" id="is_member_active_checkbox" onchange="toggleMemberFields()" class="peer appearance-none w-6 h-6 border-2 border-blue-400 rounded bg-white checked:bg-blue-600 checked:border-blue-600 transition-all cursor-pointer" {{ old('is_member_active') ? 'checked' : '' }}>
                        <svg class="absolute w-4 h-4 text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span class="font-bold text-gray-800">Aktifkan Fitur Member</span>
                </label>

                <div id="member-fields-container" class="transition-all duration-300">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jumlah Pekan <span class="member-asterisk text-red-500 hidden">*</span></label>
                            <div class="flex items-center w-full">
                                <input type="number" min="1" max="52" name="member_jumlah_pekan" id="input_member_jumlah_pekan" placeholder="Contoh: 4" class="form-input flex-1 min-w-0 px-4 py-3 rounded-l-xl border border-r-0 border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white font-medium text-sm" value="{{ old('member_jumlah_pekan') }}">
                                <span class="px-4 py-3 bg-gray-100 border border-gray-200 text-gray-600 font-semibold text-sm rounded-r-xl select-none flex items-center justify-center whitespace-nowrap">
                                    Pekan
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jam per Pertemuan <span class="member-asterisk text-red-500 hidden">*</span></label>
                            <div class="flex items-center w-full">
                                <input type="number" min="1" max="24" name="member_jam_per_pertemuan" id="input_member_jam_per_pertemuan" placeholder="Contoh: 2" class="form-input flex-1 min-w-0 px-4 py-3 rounded-l-xl border border-r-0 border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white font-medium text-sm" value="{{ old('member_jam_per_pertemuan') }}">
                                <span class="px-4 py-3 bg-gray-100 border border-gray-200 text-gray-600 font-semibold text-sm rounded-r-xl select-none flex items-center justify-center whitespace-nowrap">
                                    Jam
                                </span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Harga (Paket Member) <span class="member-asterisk text-red-500 hidden">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 text-gray-500 font-medium">Rp</span>
                            <input type="text" name="member_harga" id="input_member_harga" placeholder="Masukkan harga paket member" class="form-input w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('member_harga') ? number_format((float)preg_replace('/[^0-9]/', '', old('member_harga')), 0, ',', '.') : '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="mt-8 flex items-center justify-end gap-3 bg-white p-6 rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100">
            <a href="{{ route('admin.lapangan.index') }}" class="px-6 py-2.5 rounded-xl font-bold text-gray-700 bg-white border border-gray-300 shadow-sm hover:bg-gray-50 hover:text-gray-900 transition-all">
                Batal
            </a>
            <button type="submit" id="btn-simpan" class="px-8 py-2.5 rounded-xl font-bold text-white bg-blue-600 shadow-[0_4px_12px_rgba(37,99,235,0.3)] hover:bg-blue-700 hover:shadow-[0_6px_15px_rgba(37,99,235,0.4)] hover:-translate-y-0.5 transition-all">
                Simpan Perubahan
            </button>
        </div>

    </form>

</div>

<!-- JAVASCRIPT FOR MULTI-STEP LOGIC -->
<script>
    function editSlide(id, judul, tagline, deskripsi) {
        document.querySelector('input[name="slider_judul"]').value = judul;
        document.querySelector('input[name="slider_tagline"]').value = tagline;
        document.querySelector('textarea[name="slider_deskripsi"]').value = deskripsi;
        
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
        btn.formAction = "{{ route('admin.sliders.update') }}";
        
        document.getElementById('cancel-edit-btn').classList.remove('hidden');
        
        document.getElementById('slider-form-container').scrollIntoView({behavior: 'smooth', block: 'center'});
    }

    function cancelEditSlide() {
        document.querySelector('input[name="slider_judul"]').value = '';
        document.querySelector('input[name="slider_tagline"]').value = '';
        document.querySelector('textarea[name="slider_deskripsi"]').value = '';
        document.querySelector('input[name="slider_gambar"]').value = '';
        
        let idInput = document.getElementById('slider_edit_id');
        if(idInput) idInput.remove();

        document.getElementById('slider-form-title').innerText = 'Tambah Slide Baru';
        document.getElementById('slider-image-label').innerText = 'Upload Gambar (Wajib, Max 2MB)';
        
        const btn = document.getElementById('submit-slider-btn');
        btn.innerHTML = '+ Tambah Slide';
        btn.formAction = "{{ route('admin.sliders.store') }}";
        
        document.getElementById('cancel-edit-btn').classList.add('hidden');
    }

    let currentStep = {{ old('step', session('step', 1)) }};
    const totalSteps = 4;

    function goToStep(step) {
        if(step === currentStep) return;
        // Opsional: Validasi saat pindah step
        currentStep = step;
        document.getElementById('current_step_input').value = currentStep;
        updateUI();
    }

    function nextStep() {
        if(currentStep < totalSteps) {
            currentStep++;
            document.getElementById('current_step_input').value = currentStep;
            updateUI();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function prevStep() {
        if(currentStep > 1) {
            currentStep--;
            document.getElementById('current_step_input').value = currentStep;
            updateUI();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function updateLinePosition() {
        const circle1 = document.getElementById('step-circle-1');
        const circleLast = document.getElementById('step-circle-' + totalSteps);
        const lineWrapper = document.getElementById('line-wrapper');
        const container = lineWrapper ? lineWrapper.parentElement : null;
        
        if (circle1 && circleLast && container && lineWrapper) {
            const containerRect = container.getBoundingClientRect();
            const c1Rect = circle1.getBoundingClientRect();
            const cLastRect = circleLast.getBoundingClientRect();
            
            // Calculate distance from container's left to circle1's center
            const leftOffset = (c1Rect.left + c1Rect.width / 2) - containerRect.left;
            // Calculate distance from container's right to circleLast's center
            const rightOffset = containerRect.right - (cLastRect.left + cLastRect.width / 2);
            
            lineWrapper.style.left = leftOffset + 'px';
            lineWrapper.style.right = rightOffset + 'px';
        }
    }
    
    function toggleMemberFields() {
        const cb = document.getElementById('is_member_active_checkbox');
        const container = document.getElementById('member-fields-container');
        if (!container) return;

        const asterisks = container.querySelectorAll('.member-asterisk');
        const inputs = container.querySelectorAll('input');

        if (cb && cb.checked) {
            container.classList.remove('opacity-40', 'pointer-events-none');
            container.classList.add('opacity-100');
            asterisks.forEach(el => el.classList.remove('hidden'));
            inputs.forEach(input => {
                input.disabled = false;
                input.setAttribute('required', 'required');
            });
        } else {
            container.classList.remove('opacity-100');
            container.classList.add('opacity-40', 'pointer-events-none');
            asterisks.forEach(el => el.classList.add('hidden'));
            inputs.forEach(input => {
                input.disabled = true;
                input.removeAttribute('required');
                input.classList.remove('border-red-500', 'ring-4', 'ring-red-100', 'bg-red-50/20');
                const errText = input.parentNode.querySelector('.field-error-msg');
                if (errText) errText.remove();
            });
        }
    }

    // Run on resize and load
    window.addEventListener('resize', updateLinePosition);
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('current_step_input').value = currentStep;
        updateLinePosition();
        updateUI();
        toggleMemberFields();

        // Switch to step containing first error if validation failed from server
        @if($errors->any())
            const errorFields = @json($errors->keys());
            if (errorFields.length > 0) {
                for (let fieldName of errorFields) {
                    const el = document.querySelector(`[name="${fieldName}"]`) || document.querySelector(`[name="${fieldName}[]"]`);
                    if (el) {
                        const stepSec = el.closest('.step-section');
                        if (stepSec) {
                            const stepId = parseInt(stepSec.id.replace('step-', ''));
                            if (stepId) {
                                goToStep(stepId);
                                setTimeout(() => el.focus(), 100);
                                break;
                            }
                        }
                    }
                }
            }
        @endif

        // Clear error highlights on input
        const form = document.getElementById('lapangan-form');
        if (form) {
            form.querySelectorAll('input, textarea, select').forEach(input => {
                input.addEventListener('input', () => {
                    input.classList.remove('border-red-500', 'ring-4', 'ring-red-100', 'bg-red-50/20');
                    const errText = input.parentNode.querySelector('.field-error-msg');
                    if (errText) errText.remove();
                });
            });

            form.addEventListener('submit', function(e) {
                // Clear existing inline error messages
                form.querySelectorAll('.field-error-msg').forEach(el => el.remove());
                form.querySelectorAll('.border-red-500').forEach(el => {
                    el.classList.remove('border-red-500', 'ring-4', 'ring-red-100', 'bg-red-50/20');
                });

                const requiredInputs = form.querySelectorAll('[required]');
                let firstInvalid = null;
                let invalidCount = 0;

                requiredInputs.forEach(input => {
                    if (!input.value || !input.value.trim()) {
                        invalidCount++;
                        if (!firstInvalid) firstInvalid = input;

                        input.classList.add('border-red-500', 'ring-4', 'ring-red-100', 'bg-red-50/20');

                        // Add modern inline error text below input
                        const errMsg = document.createElement('p');
                        errMsg.className = 'text-xs text-red-500 font-semibold mt-1.5 flex items-center gap-1 field-error-msg';
                        errMsg.innerHTML = `<svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Kolom ini wajib diisi!`;
                        input.parentNode.appendChild(errMsg);
                    }
                });

                if (firstInvalid) {
                    e.preventDefault();
                    
                    // Auto switch to step containing the first invalid input
                    const stepSec = firstInvalid.closest('.step-section');
                    if (stepSec) {
                        const stepId = parseInt(stepSec.id.replace('step-', ''));
                        if (stepId) {
                            goToStep(stepId);
                        }
                    }

                    setTimeout(() => {
                        firstInvalid.focus();
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 100);

                    // Show modern SweetAlert2 warning modal
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: '<span class="text-xl font-bold text-gray-800">Kolom Wajib Belum Diisi</span>',
                            html: `<p class="text-sm text-gray-600">Terdapat <b class="text-red-600">${invalidCount} kolom wajib</b> yang masih kosong. Mohon lengkapi seluruh kolom yang bertanda bintang merah (<b class="text-red-500">*</b>).</p>`,
                            confirmButtonText: 'Lengkapi Sekarang',
                            confirmButtonColor: '#2563eb',
                            customClass: {
                                popup: 'rounded-3xl shadow-2xl border border-gray-100 p-6',
                                confirmButton: 'px-6 py-2.5 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all'
                            }
                        });
                    }
                    return false;
                }
            });
        }
    });
    
    function updateUI() {
        updateLinePosition();
        // 1. Update Sections (Smooth fade transition)
        for(let i=1; i<=totalSteps; i++) {
            const section = document.getElementById('step-' + i);
            if(i === currentStep) {
                // Show new section
                section.style.display = 'block';
                // slight delay for animation
                setTimeout(() => {
                    section.classList.add('active');
                }, 20);
            } else {
                // Hide other sections
                section.classList.remove('active');
                setTimeout(() => {
                    if(i !== currentStep) section.style.display = 'none';
                }, 400); // Wait for transition out
            }
        }

        // 2. Update Stepper Progress Line
        const progressPercentage = ((currentStep - 1) / (totalSteps - 1)) * 100;
        document.getElementById('stepper-progress').style.width = progressPercentage + '%';

        // 3. Update Stepper Circles & Labels
        for(let i=1; i<=totalSteps; i++) {
            const circle = document.getElementById('step-circle-' + i);
            const label = document.getElementById('step-label-' + i);
            
            // Because it's a tab interface, we don't use 'checkmarks' for completed steps.
            // We just highlight the active one.
            if (i === currentStep) {
                circle.className = "step-circle w-8 h-8 md:w-12 md:h-12 rounded-full flex items-center justify-center text-sm md:text-lg font-bold transition-all duration-300 shadow-sm border-[3px] md:border-4 border-white relative z-10 bg-blue-600 text-white shadow-blue-500/30 ring-2 md:ring-4 ring-blue-50";
                label.className = "hidden md:block text-xs md:text-sm font-semibold transition-colors duration-300 text-blue-700";
            } else {
                circle.className = "step-circle w-8 h-8 md:w-12 md:h-12 rounded-full flex items-center justify-center text-sm md:text-lg font-bold transition-all duration-300 shadow-sm border-[3px] md:border-4 border-white relative z-10 bg-gray-100 text-gray-600 hover:bg-gray-200 hover:scale-105 cursor-pointer";
                label.className = "hidden md:block text-xs md:text-sm font-semibold transition-colors duration-300 text-gray-500 hover:text-gray-700 cursor-pointer";
            }
        }
    }
</script>
<form id="form-hapus-pengumuman" action="{{ route('admin.lapangan.hapus_pengumuman') }}" method="POST" style="display: none;">
    @csrf
</form>

@endsection
