<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Informasi Profil
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Perbarui informasi profil dan alamat email akun Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form id="delete-photo-form" method="post" action="{{ route('profile.photo.destroy') }}" class="hidden">
        @csrf
        @method('delete')
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Profile Photo -->
        <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
            <!-- Profile Photo File Input -->
            <input type="file" name="photo" id="photo" class="hidden"
                   x-ref="photo"
                   x-on:change="
                        photoName = $refs.photo.files[0].name;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            photoPreview = e.target.result;
                        };
                        reader.readAsDataURL($refs.photo.files[0]);
                   " />

            <x-input-label for="photo" value="Foto Profil" />

            <div class="flex items-center gap-4 mt-2">
                <!-- Current Profile Photo -->
                <div x-show="! photoPreview">
                    @if ($user->profile_photo_path)
                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="rounded-full h-20 w-20 object-cover border border-gray-200 shadow-sm">
                    @else
                        <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-extrabold text-2xl border border-gray-200 shadow-sm">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <!-- New Profile Photo Preview -->
                <div x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center border border-gray-200 shadow-sm"
                          x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <div class="flex flex-col gap-2">
                    <x-secondary-button type="button" x-on:click.prevent="$refs.photo.click()">
                        Pilih Foto Baru
                    </x-secondary-button>

                    @if ($user->profile_photo_path)
                        <button type="button" class="text-xs text-red-600 hover:text-red-900 font-semibold text-left cursor-pointer focus:outline-none" onclick="document.getElementById('delete-photo-form').submit();">
                            Hapus Foto
                        </button>
                    @endif
                </div>
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('photo')" />
        </div>

        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        Alamat email Anda belum terverifikasi.

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            Tautan verifikasi baru telah dikirimkan ke alamat email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Simpan Perubahan</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >Tersimpan.</p>
            @endif
        </div>
    </form>
</section>
