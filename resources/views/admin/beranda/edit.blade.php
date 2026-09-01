<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@extends('admin.layouts.app')

@section('title', 'Pengaturan Beranda - Fajar Arena')

@section('content')

<!-- Header -->
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">Pengaturan Beranda</h2>
    </div>
</div>

<!-- Main Wrapper -->
<div class="max-w-6xl mx-auto pb-10">

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
    <form id="lapangan-form" method="POST" action="{{ route('admin.beranda.update', $pengaturan->id) }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="step" id="current_step_input" value="1">
        
        <!-- STEP 1: INFO ARENA -->
        <div>
            <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 md:p-8">
                <h3 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Informasi Dasar Arena</h3>
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Navbar Utama</label>
                    <input type="text" name="navbar_name" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('navbar_name', $pengaturan->navbar_name) }}" placeholder="Fajar Arena">
                    <p class="text-xs text-gray-500 mt-1">Nama ini akan muncul di ujung kiri menu bar (navbar) pengguna.</p>
                </div>

                <!-- CATATAN PRICELIST & KEBIJAKAN PEMESANAN MEMBER -->
                <div class="mb-6 p-5 bg-gradient-to-br from-blue-50/70 to-indigo-50/50 rounded-2xl border border-blue-200/80">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-800">Catatan Pricelist & Kebijakan Pemesanan Member</label>
                            <p class="text-xs text-gray-500">Teks ini akan muncul saat pengunjung mengklik icon info/pricelist di sudut kanan atas halaman beranda.</p>
                        </div>
                    </div>
                    <textarea name="catatan_member" rows="6" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-white focus:ring-2 focus:ring-blue-500 text-sm leading-relaxed" placeholder="Contoh:
1. Paket Member berlaku untuk 4 pekan berturut-turut pada hari & jam yang sama.
2. Pembayaran wajib diselesaikan maksimal 10 menit setelah checkout.
3. Reschedule jadwal member wajib dikonfirmasi H-2 sebelum jadwal bermain.
4. Harap menunjukkan e-tiket dengan QR code kepada petugas saat kedatangan.">{{ old('catatan_member', $pengaturan->catatan_member) }}</textarea>
                </div>

                <div class="mb-6 p-5 bg-slate-50/70 rounded-2xl border border-slate-200/80" x-data="{
                    removed: false,
                    confirmRemove() {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Hapus Gambar Background?',
                                text: 'Apakah Anda yakin ingin menghapus gambar background ini?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#ef4444',
                                cancelButtonColor: '#9ca3af',
                                confirmButtonText: 'Ya, Hapus!',
                                cancelButtonText: 'Batal',
                                customClass: { popup: 'rounded-2xl' }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    this.removed = true;
                                }
                            });
                        } else {
                            if (confirm('Apakah Anda yakin ingin menghapus gambar background ini?')) {
                                this.removed = true;
                            }
                        }
                    }
                }">
                    <input type="hidden" name="remove_auth_bg" :value="removed ? '1' : '0'">
                    
                    <div class="flex flex-wrap sm:flex-nowrap items-center justify-between gap-3 mb-3">
                        <label class="block text-sm font-bold text-gray-800">Gambar Background Halaman Login / Register (Opsional)</label>
                        @if(!empty($pengaturan->auth_bg_image))
                            <button type="button" x-show="!removed" @click="confirmRemove()" class="text-xs font-bold text-red-500 hover:text-red-700 flex items-center gap-1.5 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-xl border border-red-200 transition-colors cursor-pointer whitespace-nowrap shrink-0" title="Hapus Gambar Background">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus Background
                            </button>
                        @endif
                    </div>
                    
                    @if(!empty($pengaturan->auth_bg_image))
                        <div x-show="!removed" class="mb-4">
                            <img src="{{ asset('storage/' . $pengaturan->auth_bg_image) }}" alt="Auth Background" class="w-40 h-24 object-cover rounded-xl border border-gray-200 shadow-sm">
                        </div>
                    @endif

                    <input type="file" name="auth_bg_image" class="form-input w-full text-sm text-gray-700 border border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 cursor-pointer bg-white" accept="image/*">
                </div>

                <div class="mb-6">
                @php
                    $promosData = json_decode($pengaturan->pengumuman, true);
                    if (!is_array($promosData)) {
                        if (!empty($pengaturan->pengumuman)) {
                            $promosData = [[
                                'gambar' => $pengaturan->gambar_pengumuman ?? '',
                                'judul' => $pengaturan->promo_judul ?? 'Jangan Lewatkan Kesempatan Ini!',
                                'label' => $pengaturan->promo_label ?? '',
                                'deskripsi' => $pengaturan->pengumuman ?? ''
                            ]];
                        } else {
                            $promosData = [];
                        }
                    }
                @endphp

                <div class="mb-6" x-data="{
                    promos: {{ json_encode(array_values($promosData)) }},
                    addPromo() {
                        this.promos.push({ gambar: '', judul: '', label: '', deskripsi: '' });
                    },
                    removePromo(index) {
                        this.promos.splice(index, 1);
                    },
                    confirmRemovePromo(index) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Hapus Pengumuman?',
                                text: 'Pengumuman ini akan dihapus dari beranda.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#ef4444',
                                cancelButtonColor: '#9ca3af',
                                confirmButtonText: 'Ya, hapus!',
                                cancelButtonText: 'Batal',
                                customClass: { popup: 'rounded-2xl' }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    this.removePromo(index);
                                }
                            });
                        } else {
                            if (confirm('Hapus Pengumuman ini?')) {
                                this.removePromo(index);
                            }
                        }
                    }
                }">
                    <div class="flex items-center gap-2 mb-3">
                        <label class="block text-sm font-semibold text-gray-700">Pengumuman Beranda / Event</label>
                        <span class="px-2 py-0.5 text-[10px] uppercase font-bold bg-amber-100 text-amber-700 rounded-md">Baru</span>
                    </div>

                    <div class="space-y-6">
                        <template x-for="(promo, index) in promos" :key="index">
                            <div class="bg-amber-50/30 p-5 rounded-2xl border border-amber-200 relative group">
                                <div class="flex justify-between items-center mb-4 pb-3 border-b border-amber-200/60">
                                    <span class="text-xs font-bold text-amber-800 uppercase tracking-wider" x-text="'Event / Promo #' + (index + 1)"></span>
                                    <button type="button" @click="confirmRemovePromo(index)" class="text-xs font-bold text-red-500 hover:text-red-700 flex items-center gap-1 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded-lg border border-red-200 transition-colors cursor-pointer" title="Hapus Pengumuman Ini">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        Hapus Pengumuman
                                    </button>
                                </div>

                                <input type="hidden" :name="'promo_old_gambars[' + index + ']'" x-model="promo.gambar">
                                <input type="hidden" :name="'promo_base64_gambars[' + index + ']'" :value="promo.preview || ''">

                                <div class="mb-4">
                                    <label class="block text-xs font-semibold text-gray-600 mb-2">Poster / Gambar Event (Opsional)</label>
                                    <template x-if="promo.gambar">
                                        <div class="mb-3 flex items-center gap-3">
                                            <img :src="promo.gambar.startsWith('http') ? promo.gambar : ('/storage/' + promo.gambar)" alt="Poster" class="w-36 h-24 object-cover rounded-xl border border-amber-300 shadow-sm">
                                            <button type="button" @click="promo.gambar = ''" class="text-xs font-bold text-red-600 hover:text-red-800 bg-red-100 px-2.5 py-1 rounded-lg transition-colors">Hapus Gambar</button>
                                        </div>
                                    </template>
                                    <input type="file" :name="'promo_gambars[' + index + ']'" @change="
                                        const file = $event.target.files[0];
                                        if (file) {
                                            const reader = new FileReader();
                                            reader.onload = (e) => {
                                                promo.preview = e.target.result;
                                            };
                                            reader.readAsDataURL(file);
                                        }
                                    " class="form-input w-full text-sm text-gray-700 border-gray-200 rounded-lg focus:ring-amber-500 focus:border-amber-500 cursor-pointer bg-white" accept="image/*">
                                    <template x-if="promo.preview">
                                        <div class="mt-2.5">
                                            <p class="text-[11px] font-semibold text-blue-600 mb-1">Pratinjau Gambar Baru yang Dipilih:</p>
                                            <img :src="promo.preview" class="w-36 h-24 object-cover rounded-xl border-2 border-blue-400 shadow-sm">
                                        </div>
                                    </template>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-2">Judul Besar Promo</label>
                                        <input type="text" :name="'promo_juduls[' + index + ']'" x-model="promo.judul" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white focus:ring-amber-500 focus:border-amber-500" placeholder="Contoh: Turnamen Badminton Fajar Arena!">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-2">Teks Label</label>
                                        <input type="text" :name="'promo_labels[' + index + ']'" x-model="promo.label" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white focus:ring-amber-500 focus:border-amber-500" placeholder="Contoh: Promo Terbatas">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-2">Teks Utama / Deskripsi Detail (Opsional)</label>
                                    <textarea :name="'promo_deskripsis[' + index + ']'" x-model="promo.deskripsi" rows="3" class="form-textarea w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white focus:ring-amber-500 focus:border-amber-500 resize-none" placeholder="Tuliskan info event, turnamen, atau diskon khusus..."></textarea>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addPromo()" class="mt-4 h-10 inline-flex items-center gap-2 px-4 text-sm font-semibold text-amber-800 hover:text-amber-900 bg-amber-100 hover:bg-amber-200 rounded-xl border border-amber-300 transition-colors shadow-sm cursor-pointer w-auto">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Pengumuman
                    </button>
                </div>

                <!-- BAGIAN BERITA & HIGHLIGHT OLAHRAGA -->
                <div class="mb-6 border-t border-gray-100 pt-6 mt-6">
                    @php
                        $beritaData = old('berita_list', is_array($pengaturan->berita_list) && !empty($pengaturan->berita_list) ? $pengaturan->berita_list : []);
                    @endphp

                    <div x-data="{
                        beritas: {{ json_encode(array_values($beritaData)) }},
                        addBerita() {
                            this.beritas.push({ is_headline: false, gambar: '', judul: '', sumber: 'Olahraga', penulis: 'Redaksi', link: '', tanggal: '{{ date('d F Y, H:i') }} WIB', caption: '', ringkasan: '', isi: '' });
                        },
                        removeBerita(index) {
                            this.beritas.splice(index, 1);
                        },
                        confirmRemoveBerita(index) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'Hapus Berita?',
                                    text: 'Berita ini akan dihapus dari beranda.',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#ef4444',
                                    cancelButtonColor: '#9ca3af',
                                    confirmButtonText: 'Ya, hapus!',
                                    cancelButtonText: 'Batal',
                                    customClass: { popup: 'rounded-2xl' }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        this.removeBerita(index);
                                    }
                                });
                            } else {
                                if (confirm('Hapus Berita ini?')) {
                                    this.removeBerita(index);
                                }
                            }
                        }
                    }">
                        <div class="flex flex-wrap sm:flex-nowrap items-center justify-between gap-3 mb-3">
                            <div class="flex items-center gap-2">
                                <label class="block text-sm font-semibold text-gray-700">Berita</label>
                                <span class="px-2 py-0.5 text-[10px] uppercase font-bold bg-blue-100 text-blue-700 rounded-md shrink-0">Berita</span>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <template x-for="(berita, index) in beritas" :key="index">
                                <div class="bg-blue-50/40 p-5 rounded-2xl border border-blue-100 relative group">
                                    <div class="flex flex-wrap justify-between items-center mb-4 pb-3 border-b border-blue-200/60 gap-2">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-bold text-blue-800 uppercase tracking-wider" x-text="'Berita #' + (index + 1)"></span>
                                            <label class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200 cursor-pointer">
                                                <input type="checkbox" :name="'berita_headlines[' + index + ']'" value="1" x-model="berita.is_headline" class="rounded text-amber-600 focus:ring-amber-500">
                                                <span>Headline Utama</span>
                                            </label>
                                        </div>
                                        <button type="button" @click="confirmRemoveBerita(index)" class="text-xs font-bold text-red-500 hover:text-red-700 flex items-center gap-1 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded-lg border border-red-200 transition-colors cursor-pointer" title="Hapus Berita Ini">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            Hapus Berita
                                        </button>
                                    </div>

                                    <input type="hidden" :name="'berita_old_gambars[' + index + ']'" x-model="berita.gambar">
                                    <input type="hidden" :name="'berita_cropped_gambars[' + index + ']'" x-model="berita.cropped_gambar">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-2">Gambar Poster / Thumbnail Utama</label>
                                            
                                            <div class="mb-3">
                                                <template x-if="berita.cropped_gambar">
                                                    <div class="relative inline-block">
                                                        <img :src="berita.cropped_gambar" alt="Cropped Preview" class="w-48 h-24 object-cover rounded-xl shadow-md border-2 border-blue-500">
                                                    </div>
                                                </template>
                                                <template x-if="!berita.cropped_gambar && berita.gambar">
                                                    <img :src="'/storage/' + berita.gambar" alt="Thumbnail" class="w-48 h-24 object-cover rounded-xl shadow-sm border border-gray-200">
                                                </template>
                                            </div>

                                            <input type="file" :name="'berita_gambars[' + index + ']'" @change="triggerBeritaCrop($event, index, berita)" class="form-input w-full text-sm text-gray-700 border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 cursor-pointer bg-white" accept="image/*">
                                            <p class="text-[11px] text-gray-500 mt-1">Bingkai pemotong (crop) akan terbuka otomatis saat Anda memilih foto.</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-2">Keterangan / Caption Foto (Opsional)</label>
                                            <input type="text" :name="'berita_captions[' + index + ']'" x-model="berita.caption" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Foto liputan suasana pertandingan (Foto: ANTARA)">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-semibold text-gray-600 mb-2">Judul Berita</label>
                                            <input type="text" :name="'berita_juduls[' + index + ']'" x-model="berita.judul" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Tim Badminton Indonesia Tembus Final All England 2026">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-2">Kategori Berita</label>
                                            <input type="text" :name="'berita_sumbers[' + index + ']'" x-model="berita.sumber" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Olahraga, Badminton, News">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-2">Penulis / Editor</label>
                                            <input type="text" :name="'berita_penuliss[' + index + ']'" x-model="berita.penulis" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Rachmawati Editor">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-2">Tanggal & Waktu Berita</label>
                                            <input type="text" :name="'berita_tanggals[' + index + ']'" x-model="berita.tanggal" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: 11 Agustus 2026, 17:45 WIB">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-2">Link Sumber Asli External (Opsional)</label>
                                            <input type="url" :name="'berita_links[' + index + ']'" x-model="berita.link" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white focus:ring-blue-500 focus:border-blue-500" placeholder="https://www.kompas.com/...">
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-xs font-semibold text-gray-600 mb-2">Ringkasan Pendek (Tampil pada Kartu Beranda)</label>
                                        <input type="text" :name="'berita_ringkasans[' + index + ']'" x-model="berita.ringkasan" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white focus:ring-blue-500 focus:border-blue-500" placeholder="Ringkasan singkat 1-2 kalimat...">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-2">Isi Artikel Berita Lengkap (Tampil saat Berita Diklik)</label>
                                        <textarea :name="'berita_isis[' + index + ']'" x-model="berita.isi" rows="6" class="form-textarea w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white focus:ring-blue-500 focus:border-blue-500" placeholder="Tuliskan isi berita lengkap multi-paragraf..."></textarea>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="addBerita()" class="mt-4 h-10 inline-flex items-center gap-2 px-4 text-sm font-semibold text-blue-700 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 rounded-xl border border-blue-200 transition-colors shadow-sm cursor-pointer w-auto">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah Berita / Artikel
                        </button>
                    </div>
                </div>

                @php
                    $ytData = json_decode($pengaturan->youtube_link, true);
                    if (!is_array($ytData)) {
                        $oldLinks = explode("\n", str_replace("\r", "", $pengaturan->youtube_link));
                        $oldLinks = array_filter(array_map('trim', $oldLinks));
                        $ytData = [];
                        foreach($oldLinks as $ol) {
                            $ytData[] = ['title' => '', 'url' => $ol];
                        }
                    }
                    if (empty($ytData)) {
                        $ytData = [['title' => '', 'url' => '']];
                    }
                @endphp
                <div class="mb-6 border-t border-gray-100 pt-6 mt-6" x-data="{
                    videos: {{ json_encode($ytData) }},
                    addVideo() { this.videos.push({title:'', url:''}); },
                    removeVideo(index) { this.videos.splice(index, 1); },
                    confirmRemoveVideo(index) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Hapus Video?',
                                text: 'Anda yakin ingin menghapus baris video ini?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#ef4444',
                                cancelButtonColor: '#9ca3af',
                                confirmButtonText: 'Ya, hapus!',
                                cancelButtonText: 'Batal',
                                customClass: { popup: 'rounded-2xl' }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    this.removeVideo(index);
                                }
                            });
                        } else {
                            if (confirm('Hapus Video ini?')) {
                                this.removeVideo(index);
                            }
                        }
                    }
                }">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Daftar Video YouTube (Opsional)</label>
                    <p class="text-xs text-gray-500 mb-3">Tambahkan judul dan link video YouTube yang ingin ditampilkan di beranda.</p>
                    
                    <div class="space-y-4">
                        <template x-for="(video, index) in videos" :key="index">
                            <div class="flex flex-col md:flex-row gap-3 items-start md:items-center bg-gray-50 p-4 rounded-xl border border-gray-200">
                                <div class="flex-1 w-full">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Video</label>
                                    <input type="text" x-model="video.title" name="youtube_titles[]" class="form-input w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-800 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: Keseruan Fajar Arena">
                                </div>
                                <div class="flex-1 w-full">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Link YouTube</label>
                                    <input type="url" x-model="video.url" name="youtube_urls[]" class="form-input w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-800 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="https://youtube.com/...">
                                </div>
                                <div class="md:mt-5 self-end md:self-auto">
                                    <button type="button" @click="confirmRemoveVideo(index)" class="text-red-500 hover:text-red-700 p-2 bg-red-50 hover:bg-red-100 rounded-lg transition-colors cursor-pointer" title="Hapus Video">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <button type="button" @click="addVideo()" class="mt-4 h-10 inline-flex items-center gap-2 px-4 text-sm font-semibold text-blue-700 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 rounded-xl border border-blue-200 transition-colors shadow-sm cursor-pointer w-auto">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Video
                    </button>
                </div>

                <div class="mb-6 border-t border-gray-100 pt-6 mt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-4">Bagian "Mengapa memilih kami?"</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Judul Bagian</label>
                            <input type="text" name="fitur_judul" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white focus:ring-blue-500 focus:border-blue-500" value="{{ old('fitur_judul', $pengaturan->fitur_judul ?? 'Mengapa memilih kami?') }}">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Sub-judul / Deskripsi Singkat</label>
                            <input type="text" name="fitur_deskripsi" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white focus:ring-blue-500 focus:border-blue-500" value="{{ old('fitur_deskripsi', $pengaturan->fitur_deskripsi ?? 'Sistem reservasi cepat, aman, dan modern.') }}">
                        </div>
                    </div>

                    @php
                        $defaultCards = [
                            ['ikon' => '⚡', 'judul' => 'Cepat', 'deskripsi' => 'Booking hanya beberapa langkah.'],
                            ['ikon' => '📅', 'judul' => 'Real-time', 'deskripsi' => 'Jadwal selalu update.'],
                            ['ikon' => '🔒', 'judul' => 'Aman', 'deskripsi' => 'Data terlindungi.'],
                            ['ikon' => '🏟️', 'judul' => 'Modern', 'deskripsi' => 'Multi olahraga.']
                        ];
                        $cards = old('fitur_cards', is_array($pengaturan->fitur_cards) && !empty($pengaturan->fitur_cards) ? $pengaturan->fitur_cards : $defaultCards);
                    @endphp

                    <div x-data="{
                        cards: {{ json_encode(array_values($cards)) }},
                        addCard() {
                            this.cards.push({ ikon: '⭐', judul: '', deskripsi: '' });
                        },
                        removeCard(index) {
                            this.cards.splice(index, 1);
                        },
                        confirmRemoveCard(index) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'Hapus Fitur?',
                                    text: 'Anda yakin ingin menghapus kartu fitur ini?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#ef4444',
                                    cancelButtonColor: '#9ca3af',
                                    confirmButtonText: 'Ya, hapus!',
                                    cancelButtonText: 'Batal',
                                    customClass: { popup: 'rounded-2xl' }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        this.removeCard(index);
                                    }
                                });
                            } else {
                                if (confirm('Hapus Fitur ini?')) {
                                    this.removeCard(index);
                                }
                            }
                        }
                    }">
                        <label class="block text-xs font-semibold text-gray-600 mb-3">Kartu Fitur (Ikon Emoji, Judul, Deskripsi)</label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <template x-for="(card, index) in cards" :key="index">
                                <div class="bg-gray-50 p-3.5 sm:p-4 rounded-xl border border-gray-200 relative group">
                                    <div class="flex items-center gap-2 mb-2">
                                        <input type="text" :name="'fitur_cards[' + index + '][ikon]'" x-model="card.ikon" class="form-input w-12 sm:w-14 shrink-0 px-1 py-2 text-center rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-white text-base shadow-sm" placeholder="Ikon">
                                        <input type="text" :name="'fitur_cards[' + index + '][judul]'" x-model="card.judul" class="form-input flex-1 min-w-0 px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-blue-500 font-semibold bg-white text-sm shadow-sm" placeholder="Judul Fitur">
                                        <button type="button" @click="confirmRemoveCard(index)" class="shrink-0 w-9 h-9 sm:w-10 sm:h-10 text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 border border-red-100 rounded-lg transition-colors cursor-pointer flex items-center justify-center shadow-sm" title="Hapus Fitur">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                    <input type="text" :name="'fitur_cards[' + index + '][deskripsi]'" x-model="card.deskripsi" class="form-input w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm text-gray-600 bg-white shadow-sm" placeholder="Deskripsi pendek...">
                                </div>
                            </template>
                        </div>

                        <button type="button" @click="addCard()" class="mt-4 h-10 inline-flex items-center gap-2 px-4 text-sm font-semibold text-blue-700 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 rounded-xl border border-blue-200 transition-colors shadow-sm cursor-pointer w-auto">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah Kartu Fitur
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- STEP 2: MEDIA & SLIDER -->
        <div class="mt-8">
            <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 md:p-8 mb-6">
                
                <h3 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Media & Slider Beranda</h3>
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-4">Slider Beranda</label>
                    <p class="text-sm text-gray-500 mb-6">Kelola semua gambar slide dan teks yang akan muncul secara bergantian di halaman depan website Anda.</p>
                    <!-- Tampilkan slider yang sudah ada -->
                    @if(isset($sliders) && $sliders->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                            @foreach($sliders as $slider)
                                <div class="border border-gray-200 rounded-xl p-4 flex flex-col justify-between bg-white shadow-sm">
                                    <div>
                                        <img src="{{ asset('storage/' . $slider->gambar) }}" class="h-40 w-full object-cover rounded-lg mb-3">
                                        <h4 class="font-bold text-gray-800">{{ $slider->judul ?: 'Tanpa Judul' }}</h4>
                                        <p class="text-xs text-gray-500 mt-1 font-medium">{{ $slider->tagline }}</p>
                                        <p class="text-sm text-gray-600 mt-2">{{ $slider->deskripsi }}</p>
                                    </div>
                                    <div class="mt-4 flex gap-2">
                                        <button type="button" onclick="editSlide({{ $slider->id }}, '{{ addslashes($slider->judul) }}', '{{ addslashes($slider->tagline) }}', '{{ addslashes($slider->deskripsi) }}')" class="text-blue-600 text-sm font-semibold hover:text-blue-700 text-center bg-blue-50 hover:bg-blue-100 rounded-lg px-4 py-2 transition-colors border border-blue-200 w-1/2 cursor-pointer">
                                            Edit
                                        </button>
                                        <button type="submit" formaction="{{ route('admin.sliders.destroy', $slider->id) }}" formmethod="POST" class="text-red-600 text-sm font-semibold hover:text-red-700 text-center bg-red-50 hover:bg-red-100 rounded-lg px-4 py-2 transition-colors border border-red-200 w-1/2 cursor-pointer" onclick="confirmDeleteSlider(event, this)">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Form Tambah/Edit Slide -->
                    <div id="slider-form-container" class="bg-gray-50 p-6 rounded-2xl border border-gray-200">
                        <h4 id="slider-form-title" class="font-bold text-gray-800 mb-4">Tambah Slide Baru</h4>
                        <div class="space-y-4">
                            <input type="hidden" name="pengaturan_id" value="{{ $pengaturan->id }}">
                            <input type="text" name="slider_judul" placeholder="Judul (Contoh: Fajar Arena Futsal)" value="{{ old('slider_judul') }}" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <input type="text" name="slider_tagline" placeholder="Tagline (Contoh: Booking Futsal Tanpa Ribet)" value="{{ old('slider_tagline') }}" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <textarea name="slider_deskripsi" placeholder="Deskripsi Singkat" rows="2" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('slider_deskripsi') }}</textarea>
                            <div>
                                <label id="slider-image-label" class="block text-xs font-semibold text-gray-500 mb-1">Upload Gambar (Wajib, Max 2MB)</label>
                                <input type="file" name="slider_gambar" accept="image/*" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 bg-white">
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" id="submit-slider-btn" formaction="{{ route('admin.sliders.store') }}" formmethod="POST" class="bg-blue-600 text-white px-6 py-3 rounded-xl text-sm font-semibold hover:bg-blue-700 transition-colors shadow-sm cursor-pointer">
                                    + Tambah Slide
                                </button>
                                <button type="button" id="cancel-edit-btn" onclick="cancelEditSlide()" class="hidden bg-gray-200 text-gray-700 px-6 py-3 rounded-xl text-sm font-semibold hover:bg-gray-300 transition-colors shadow-sm cursor-pointer">
                                    Batal Edit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-3 bg-white p-6 rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100">
                <a href="{{ route('admin.lapangan.index') }}" class="px-6 py-2.5 rounded-xl font-bold text-gray-700 bg-white border border-gray-300 shadow-sm hover:bg-gray-50 hover:text-gray-900 transition-all">
                    Batal
                </a>
                <button type="submit" id="btn-simpan" class="px-8 py-2.5 rounded-xl font-bold text-white bg-blue-600 shadow-[0_4px_12px_rgba(37,99,235,0.3)] hover:bg-blue-700 hover:shadow-[0_6px_15px_rgba(37,99,235,0.4)] hover:-translate-y-0.5 transition-all cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </div>

    </form>

</div>

<form id="form-hapus-pengumuman" action="{{ route('admin.lapangan.hapus_pengumuman') }}" method="POST" style="display: none;">
    @csrf
</form>

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

    function confirmDeleteSlider(e, btn) {
        e.preventDefault();
        Swal.fire({
            title: 'Hapus Slide?',
            text: "Slide ini akan dihapus secara permanen dari beranda.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = btn.getAttribute('formaction');
                form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function confirmDeletePromo() {
        Swal.fire({
            title: 'Hapus Pengumuman?',
            text: "Pengumuman ini akan dihapus dari beranda.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-hapus-pengumuman').submit();
            }
        });
    }
</script>

<style>
    .cropper-bg {
        background-image: none !important;
        background-color: #0f172a !important;
    }
</style>

<!-- MODAL CROPPER FOTO BERITA & MEDIA -->
<div id="cropperModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-md transition-opacity">
    <div class="bg-white rounded-3xl shadow-2xl max-w-3xl w-full p-6 relative flex flex-col max-h-[90vh]">
        
        <!-- Modal Header -->
        <div class="flex justify-between items-center pb-4 border-b border-gray-100 mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">
                    Potong & Atur Posisi Foto Berita
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Geser & sesuaikan bingkai agar bagian penting (seperti kepala pemain) tidak terpotong saat tampil di beranda.</p>
            </div>
            <button type="button" onclick="closeCropperModal()" class="text-gray-400 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Aspect Ratio Selection Buttons -->
        <div class="flex flex-wrap gap-2 mb-4 bg-gray-50 p-2.5 rounded-2xl border border-gray-100 items-center">
            <span class="text-xs font-semibold text-gray-700 mr-1">Bingkai Ukuran:</span>
            <button type="button" id="btnRatioHeadline" onclick="setCropRatio(21/9, this)" class="ratio-btn px-4 py-2 rounded-xl text-xs font-semibold bg-blue-600 text-white shadow-sm transition-all cursor-pointer">
                Banner Headline (21:9)
            </button>
            <button type="button" id="btnRatioKartu" onclick="setCropRatio(16/10, this)" class="ratio-btn px-4 py-2 rounded-xl text-xs font-semibold bg-white text-gray-700 hover:bg-gray-100 border border-gray-200 transition-all cursor-pointer">
                Kartu Berita (16:10)
            </button>
        </div>

        <!-- Canvas Image Container -->
        <div class="relative bg-slate-950 rounded-2xl overflow-hidden flex-1 max-h-[420px] flex items-center justify-center p-2 border border-slate-800">
            <img id="cropperImageSrc" class="max-w-full max-h-full block">
        </div>

        <!-- Modal Action Buttons -->
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-4">
            <button type="button" onclick="closeCropperModal()" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold text-xs hover:bg-gray-50 transition-colors cursor-pointer">
                Batal
            </button>
            <button type="button" onclick="applyCrop()" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow transition-all cursor-pointer">
                Terapkan Hasil Crop
            </button>
        </div>

    </div>
</div>

<script>
    let currentCropper = null;
    let activeBeritaObj = null;

    function triggerBeritaCrop(event, index, beritaObj) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const file = input.files[0];
            activeBeritaObj = beritaObj;

            const reader = new FileReader();
            reader.onload = function (e) {
                const imgElement = document.getElementById('cropperImageSrc');
                imgElement.src = e.target.result;

                document.getElementById('cropperModal').classList.remove('hidden');

                if (currentCropper) {
                    currentCropper.destroy();
                }

                // Default ratio 21:9 for perfect banner headline frame
                const defaultRatio = 21 / 9;

                currentCropper = new Cropper(imgElement, {
                    aspectRatio: defaultRatio,
                    viewMode: 1,
                    autoCropArea: 0.95,
                    movable: true,
                    zoomable: true,
                    rotatable: false,
                    scalable: false
                });
            };
            reader.readAsDataURL(file);
        }
    }

    function setCropRatio(ratio, btnElement) {
        if (currentCropper) {
            currentCropper.setAspectRatio(ratio);

            document.querySelectorAll('.ratio-btn').forEach(b => {
                b.className = 'ratio-btn px-4 py-2 rounded-xl text-xs font-semibold bg-white text-gray-700 hover:bg-gray-100 border border-gray-200 transition-all cursor-pointer';
            });
            btnElement.className = 'ratio-btn px-4 py-2 rounded-xl text-xs font-semibold bg-blue-600 text-white shadow-sm transition-all cursor-pointer';
        }
    }

    function closeCropperModal() {
        document.getElementById('cropperModal').classList.add('hidden');
        if (currentCropper) {
            currentCropper.destroy();
            currentCropper = null;
        }
    }

    function applyCrop() {
        if (currentCropper && activeBeritaObj) {
            const canvas = currentCropper.getCroppedCanvas({
                width: 1200,
                height: 514,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });

            const base64Image = canvas.toDataURL('image/jpeg', 0.9);

            // Set cropped image in Alpine.js berita object
            activeBeritaObj.cropped_gambar = base64Image;

            closeCropperModal();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Foto berhasil dipotong & disesuaikan!',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        }
    }
</script>

@endsection
