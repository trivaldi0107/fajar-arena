<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BerandaAdminController extends Controller
{
    public function index()
    {
        $pengaturan = Pengaturan::first();
        if (!$pengaturan) {
            return redirect()->route('admin.lapangan.index')->with('error', 'Buat arena terlebih dahulu.');
        }
        return redirect()->route('admin.beranda.edit', $pengaturan->id);
    }

    public function edit($id)
    {
        $pengaturan = Pengaturan::findOrFail($id);
        $sliders = Slider::where('pengaturan_id', $id)->orderBy('urutan', 'asc')->get();
        return view('admin.beranda.edit', compact('pengaturan', 'sliders'));
    }

    public function update(Request $request, $id)
    {
        $pengaturan = Pengaturan::findOrFail($id);
        
        $request->validate([
            'gambar_utama' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'gambar_pengumuman' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'auth_bg_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5048',
        ]);

        $data = $request->only([
            'tagline', 'deskripsi',
            'pengumuman', 'promo_label', 'promo_judul', 'promo_teks_tombol',
            'fitur_judul', 'fitur_deskripsi', 'fitur_cards', 
            'beranda_alamat', 'beranda_kota', 'beranda_link_maps', 'beranda_no_telp', 'beranda_email',
            'navbar_name', 'catatan_member'
        ]);

        $youtubeData = [];
        if ($request->has('youtube_titles') && $request->has('youtube_urls')) {
            $titles = $request->youtube_titles;
            $urls = $request->youtube_urls;
            for ($i = 0; $i < count($urls); $i++) {
                if (!empty(trim($urls[$i]))) {
                    $youtubeData[] = [
                        'title' => isset($titles[$i]) ? trim($titles[$i]) : '',
                        'url' => trim($urls[$i])
                    ];
                }
            }
        }
        $data['youtube_link'] = !empty($youtubeData) ? json_encode($youtubeData) : null;

        // Process Dynamic Promos / Events
        if ($request->has('promo_juduls')) {
            $juduls = $request->promo_juduls;
            $labels = $request->promo_labels ?? [];
            $deskripsis = $request->promo_deskripsis ?? [];
            $oldGambars = $request->promo_old_gambars ?? [];
            $promosList = [];

            $base64Promos = $request->promo_base64_gambars ?? [];
            for ($i = 0; $i < count($juduls); $i++) {
                if (!empty(trim($juduls[$i])) || !empty(trim($deskripsis[$i]))) {
                    $gambarPath = $oldGambars[$i] ?? null;

                    // Priority 1: Base64 Direct Upload (Guaranteed & Fast)
                    if (!empty($base64Promos[$i]) && str_starts_with($base64Promos[$i], 'data:image')) {
                        $base64Data = $base64Promos[$i];
                        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                            $imgData = substr($base64Data, strpos($base64Data, ',') + 1);
                            $ext = strtolower($type[1]);
                            if ($ext === 'jpeg') $ext = 'jpg';
                            $decodedImage = base64_decode($imgData);
                            $fileName = 'pengaturan/promo_' . uniqid() . '.' . $ext;
                            Storage::disk('public')->put($fileName, $decodedImage);
                            $gambarPath = $fileName;
                        }
                    }
                    // Priority 2: Standard Multipart File Upload
                    elseif ($request->hasFile("promo_gambars.{$i}")) {
                        $gambarPath = $request->file("promo_gambars.{$i}")->store('pengaturan', 'public');
                    } elseif ($request->hasFile('promo_gambars') && isset($request->file('promo_gambars')[$i])) {
                        $gambarPath = $request->file('promo_gambars')[$i]->store('pengaturan', 'public');
                    }

                    $promosList[] = [
                        'gambar' => $gambarPath,
                        'judul' => trim($juduls[$i]),
                        'label' => isset($labels[$i]) ? trim($labels[$i]) : '',
                        'deskripsi' => isset($deskripsis[$i]) ? trim($deskripsis[$i]) : ''
                    ];
                }
            }

            if (!empty($promosList)) {
                $data['pengumuman'] = json_encode($promosList);
                $data['promo_judul'] = $promosList[0]['judul'] ?? null;
                $data['promo_label'] = $promosList[0]['label'] ?? null;
                $data['gambar_pengumuman'] = $promosList[0]['gambar'] ?? null;
            } else {
                $data['pengumuman'] = null;
                $data['promo_judul'] = null;
                $data['promo_label'] = null;
                $data['gambar_pengumuman'] = null;
            }
        } else {
            $data['pengumuman'] = null;
            $data['promo_judul'] = null;
            $data['promo_label'] = null;
            $data['gambar_pengumuman'] = null;
        }

        // Process Dynamic Berita & Artikel Olahraga (Headline & Detail Portal News)
        if ($request->has('berita_juduls')) {
            $beritaJuduls = $request->berita_juduls;
            $beritaHeadlines = $request->berita_headlines ?? [];
            $beritaSumbers = $request->berita_sumbers ?? [];
            $beritaPenuliss = $request->berita_penuliss ?? [];
            $beritaLinks = $request->berita_links ?? [];
            $beritaTanggals = $request->berita_tanggals ?? [];
            $beritaCaptions = $request->berita_captions ?? [];
            $beritaRingkasans = $request->berita_ringkasans ?? [];
            $beritaIsis = $request->berita_isis ?? [];
            $beritaOldGambars = $request->berita_old_gambars ?? [];
            $beritaCroppedGambars = $request->berita_cropped_gambars ?? [];
            $beritaList = [];

            for ($i = 0; $i < count($beritaJuduls); $i++) {
                if (!empty(trim($beritaJuduls[$i]))) {
                    $gambarPath = $beritaOldGambars[$i] ?? null;

                    // Priority 1: Cropped Base64 Image
                    if (!empty($beritaCroppedGambars[$i]) && str_starts_with($beritaCroppedGambars[$i], 'data:image')) {
                        $base64Data = $beritaCroppedGambars[$i];
                        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                            $imgData = substr($base64Data, strpos($base64Data, ',') + 1);
                            $ext = strtolower($type[1]);
                            if ($ext === 'jpeg') $ext = 'jpg';
                            $decodedImage = base64_decode($imgData);
                            $fileName = 'pengaturan/cropped_' . uniqid() . '.' . $ext;
                            Storage::disk('public')->put($fileName, $decodedImage);
                            $gambarPath = $fileName;
                        }
                    }
                    // Priority 2: Raw File Upload
                    elseif ($request->hasFile("berita_gambars.{$i}")) {
                        $gambarPath = $request->file("berita_gambars.{$i}")->store('pengaturan', 'public');
                    }

                    $beritaList[] = [
                        'is_headline' => isset($beritaHeadlines[$i]) && ($beritaHeadlines[$i] == '1' || $beritaHeadlines[$i] == 'true' || $beritaHeadlines[$i] == 1),
                        'gambar' => $gambarPath,
                        'judul' => trim($beritaJuduls[$i]),
                        'sumber' => !empty(trim($beritaSumbers[$i] ?? '')) ? trim($beritaSumbers[$i]) : 'CNN Indonesia',
                        'penulis' => !empty(trim($beritaPenuliss[$i] ?? '')) ? trim($beritaPenuliss[$i]) : 'Redaksi',
                        'link' => isset($beritaLinks[$i]) ? trim($beritaLinks[$i]) : '',
                        'tanggal' => !empty(trim($beritaTanggals[$i] ?? '')) ? trim($beritaTanggals[$i]) : date('d F Y, H:i') . ' WIB',
                        'caption' => isset($beritaCaptions[$i]) ? trim($beritaCaptions[$i]) : '',
                        'ringkasan' => isset($beritaRingkasans[$i]) ? trim($beritaRingkasans[$i]) : '',
                        'isi' => isset($beritaIsis[$i]) ? trim($beritaIsis[$i]) : ''
                    ];
                }
            }
            $data['berita_list'] = $beritaList;
        } else {
            $data['berita_list'] = [];
        }

        $pengaturan->fill($data);

        if ($request->hasFile('gambar_utama')) {
            if ($pengaturan->gambar_utama) {
                Storage::disk('public')->delete($pengaturan->gambar_utama);
            }
            $pengaturan->gambar_utama = $request->file('gambar_utama')->store('pengaturan', 'public');
        }

        if ($request->hasFile('gambar_pengumuman') && !$request->has('promo_juduls')) {
            if ($pengaturan->gambar_pengumuman) {
                Storage::disk('public')->delete($pengaturan->gambar_pengumuman);
            }
            $pengaturan->gambar_pengumuman = $request->file('gambar_pengumuman')->store('pengaturan', 'public');
        }

        if ($request->hasFile('auth_bg_image')) {
            if ($pengaturan->auth_bg_image) {
                Storage::disk('public')->delete($pengaturan->auth_bg_image);
            }
            $data['auth_bg_image'] = $request->file('auth_bg_image')->store('pengaturan', 'public');
        } elseif ($request->has('remove_auth_bg') && $request->remove_auth_bg == '1') {
            if ($pengaturan->auth_bg_image) {
                Storage::disk('public')->delete($pengaturan->auth_bg_image);
            }
            $data['auth_bg_image'] = null;
        }

        $pengaturan->update($data);
        $pengaturan->save();

        return redirect()->route('admin.beranda.edit', $pengaturan->id)->with('success', 'Pengaturan beranda berhasil diperbarui.')->with('step', $request->step ?? 1);
    }
}
