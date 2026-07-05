@extends('layouts.app')
@section('title', 'Pengaturan & Profil — TIECO')

@section('content')
<div class="w-full space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Pengaturan</h1>
            <p class="text-xs text-slate-400">Kelola informasi profil dan keamanan akun Anda.</p>
        </div>
        <a href="{{ route('warga.dashboard') }}" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-600 hover:text-slate-800 font-bold rounded-xl text-xs transition-all flex items-center gap-1.5 border border-slate-200 shadow-sm">
            <i class="bi bi-arrow-left text-sm"></i> Kembali
        </a>
    </div>

    {{-- Profile Banner Card --}}
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col sm:flex-row items-center gap-5 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-50 rounded-full blur-3xl pointer-events-none"></div>
        
        @php
            $initials = $user->initials();
        @endphp
        
        <div class="w-16 h-16 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center font-black text-xl shrink-0 shadow-sm overflow-hidden">
            @if($user->profilePhotoUrl())
                <img src="{{ $user->profilePhotoUrl() }}" alt="Foto profil {{ $user->name }}" class="w-full h-full object-cover">
            @else
                {{ $initials }}
            @endif
        </div>
        <div class="text-center sm:text-left space-y-1">
            <h2 class="text-base font-bold text-slate-800">{{ $user->name }}</h2>
            <p class="text-xs text-slate-400 font-medium">@ {{ $user->username }} &bull; {{ $user->email }}</p>
            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-2">
                <span class="text-[9px] px-2 py-0.5 bg-emerald-50 text-emerald-700 font-bold rounded-full border border-emerald-100">
                    <i class="bi bi-person-fill"></i> Warga
                </span>
                <span class="text-[9px] px-2 py-0.5 bg-amber-50 text-amber-700 font-bold rounded-full border border-amber-100">
                    <i class="bi bi-coin"></i> {{ number_format($user->point_balance, 0, ',', '.') }} Poin
                </span>
            </div>
        </div>
    </div>

    {{-- Main Settings Forms Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        
        {{-- Settings Sidebar (Tabs Menu) --}}
        <div class="md:col-span-1 bg-white border border-slate-100 rounded-3xl p-4 shadow-sm h-fit space-y-1">
            <button onclick="switchTab('profile')" id="btn-tab-profile" class="tab-btn-settings w-full flex items-center gap-3 px-4 py-3 text-xs font-bold rounded-xl transition-all text-emerald-700 bg-emerald-50/70 border-l-4 border-emerald-500 rounded-l-none focus:outline-none">
                <i class="bi bi-person-gear text-sm shrink-0"></i>
                <span>Edit Profil</span>
            </button>
            <button onclick="switchTab('security')" id="btn-tab-security" class="tab-btn-settings w-full flex items-center gap-3 px-4 py-3 text-xs font-bold rounded-xl transition-all text-slate-500 hover:bg-slate-50 hover:text-slate-700 focus:outline-none">
                <i class="bi bi-shield-lock text-sm shrink-0"></i>
                <span>Keamanan</span>
            </button>
        </div>

        {{-- Forms Card --}}
        <div class="md:col-span-3 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl">
                    <p class="text-xs font-bold text-red-700 mb-1">Gagal menyimpan perubahan:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li class="text-[11px] text-red-600 font-medium">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('warga.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                {{-- TAB 1: EDIT PROFILE --}}
                <div id="settings-tab-profile" class="settings-content-pane space-y-6">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-2">Informasi Profil</h3>
                        <p class="text-[10px] text-slate-400 mt-1">Perbarui informasi pribadi akun TIECO Anda.</p>
                    </div>

                    {{-- Foto Profil --}}
                    <div class="flex flex-col sm:flex-row items-center gap-5 p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                        <div class="relative shrink-0">
                            <div id="profilePhotoPreview" class="w-20 h-20 rounded-full bg-emerald-50 border-2 border-emerald-100 text-emerald-600 flex items-center justify-center font-black text-2xl shadow-sm overflow-hidden">
                                <span id="profilePhotoPreviewInitials" class="{{ $user->profilePhotoUrl() ? 'hidden' : '' }}">{{ $initials }}</span>
                                <img src="{{ $user->profilePhotoUrl() ?? '' }}" alt="Foto profil" class="w-full h-full object-cover {{ $user->profilePhotoUrl() ? '' : 'hidden' }}" id="profilePhotoPreviewImg">
                            </div>
                            <label for="profilePhotoInput" class="absolute -bottom-1 -right-1 w-8 h-8 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full flex items-center justify-center cursor-pointer shadow-md transition-colors" title="Ubah foto profil">
                                <i class="bi bi-camera-fill text-xs"></i>
                            </label>
                        </div>
                        <div class="flex-1 text-center sm:text-left space-y-2">
                            <p class="text-xs font-bold text-slate-700">Foto Profil</p>
                            <p class="text-[10px] text-slate-400">Unggah foto JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
                            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                                <label for="profilePhotoInput" class="px-3 py-2 bg-white hover:bg-slate-50 text-slate-600 font-bold rounded-xl text-[10px] transition-all border border-slate-200 shadow-sm cursor-pointer">
                                    <i class="bi bi-upload mr-1"></i> Pilih Foto
                                </label>
                                @if($user->profile_photo)
                                    <label class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-xl text-[10px] transition-all border border-red-100 cursor-pointer">
                                        <input type="checkbox" name="remove_profile_photo" value="1" class="rounded border-red-300 text-red-600 focus:ring-red-500" onchange="toggleRemovePhoto(this)">
                                        Hapus Foto
                                    </label>
                                @endif
                            </div>
                            <input type="file" name="profile_photo" id="profilePhotoInput" class="hidden" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewProfilePhoto(this)">
                            @error('profile_photo')
                                <p class="text-[10px] text-red-500 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs font-semibold focus:outline-none focus:border-emerald-500 focus:bg-white transition-all" required>
                            @error('name')
                                <p class="text-[10px] text-red-500 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Username <span class="text-red-500">*</span></label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}" placeholder="Contoh: budi_santoso" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs font-semibold focus:outline-none focus:border-emerald-500 focus:bg-white transition-all" required>
                            @error('username')
                                <p class="text-[10px] text-red-500 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Alamat Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs font-semibold focus:outline-none focus:border-emerald-500 focus:bg-white transition-all" required>
                            @error('email')
                                <p class="text-[10px] text-red-500 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Nomor Handphone (Telepon)</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs font-semibold focus:outline-none focus:border-emerald-500 focus:bg-white transition-all">
                            @error('phone')
                                <p class="text-[10px] text-red-500 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Alamat Lengkap Rumah</label>
                        <textarea name="address" rows="4" placeholder="Tulis alamat rumah lengkap Anda..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs font-semibold focus:outline-none focus:border-emerald-500 focus:bg-white transition-all">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <p class="text-[10px] text-red-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- TAB 2: SECURITY / CHANGE PASSWORD --}}
                <div id="settings-tab-security" class="settings-content-pane space-y-6 hidden">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-2">Ubah Kata Sandi</h3>
                        <p class="text-[10px] text-slate-400 mt-1">Ubah kata sandi secara berkala untuk menjaga keamanan akun Anda.</p>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1.5 max-w-md">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Kata Sandi Saat Ini</label>
                            <input type="password" name="current_password" placeholder="Ketik kata sandi saat ini" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs font-semibold focus:outline-none focus:border-emerald-500 focus:bg-white transition-all">
                            @error('current_password')
                                <p class="text-[10px] text-red-500 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5 max-w-md">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Kata Sandi Baru</label>
                            <input type="password" name="new_password" placeholder="Minimal 8 karakter" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs font-semibold focus:outline-none focus:border-emerald-500 focus:bg-white transition-all">
                            @error('new_password')
                                <p class="text-[10px] text-red-500 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5 max-w-md">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="new_password_confirmation" placeholder="Ketik ulang kata sandi baru" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs font-semibold focus:outline-none focus:border-emerald-500 focus:bg-white transition-all">
                        </div>
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="border-t border-slate-100 pt-5 flex justify-end">
                    <button type="submit" class="w-full sm:w-auto px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs uppercase tracking-widest transition-all shadow-md shadow-emerald-600/10 hover:scale-[1.01] active:scale-[0.99] transform">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
    function previewProfilePhoto(input) {
        const previewImg = document.getElementById('profilePhotoPreviewImg');
        const previewInitials = document.getElementById('profilePhotoPreviewInitials');
        const removeCheckbox = document.querySelector('input[name="remove_profile_photo"]');

        if (removeCheckbox) {
            removeCheckbox.checked = false;
        }

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
                if (previewInitials) {
                    previewInitials.classList.add('hidden');
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function toggleRemovePhoto(checkbox) {
        const input = document.getElementById('profilePhotoInput');
        const previewImg = document.getElementById('profilePhotoPreviewImg');
        const previewInitials = document.getElementById('profilePhotoPreviewInitials');

        if (checkbox.checked) {
            input.value = '';
            previewImg.src = '';
            previewImg.classList.add('hidden');
            if (previewInitials) {
                previewInitials.classList.remove('hidden');
            }
        }
    }

    function switchTab(tabName) {
        // Toggle tab screens
        document.querySelectorAll('.settings-content-pane').forEach(el => {
            el.classList.add('hidden');
        });
        document.getElementById('settings-tab-' + tabName).classList.remove('hidden');

        // Toggle button styling
        document.querySelectorAll('.tab-btn-settings').forEach(btn => {
            btn.className = "tab-btn-settings w-full flex items-center gap-3 px-4 py-3 text-xs font-bold rounded-xl transition-all text-slate-500 hover:bg-slate-50 hover:text-slate-700 focus:outline-none";
        });
        
        const activeBtn = document.getElementById('btn-tab-' + tabName);
        activeBtn.className = "tab-btn-settings w-full flex items-center gap-3 px-4 py-3 text-xs font-bold rounded-xl transition-all text-emerald-700 bg-emerald-50/70 border-l-4 border-emerald-500 rounded-l-none focus:outline-none";
    }

    // Automatically switch tab if there is password/security validation error
    @if($errors->has('current_password') || $errors->has('new_password'))
        document.addEventListener('DOMContentLoaded', function() {
            switchTab('security');
        });
    @elseif($errors->has('profile_photo') || $errors->has('username') || $errors->has('name') || $errors->has('email'))
        document.addEventListener('DOMContentLoaded', function() {
            switchTab('profile');
        });
    @endif
</script>
@endsection
