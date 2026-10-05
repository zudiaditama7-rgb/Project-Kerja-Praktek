<x-app-layout>
    <x-slot name="header">Tambah Siswa Baru</x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-50">
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('admin.siswa.index') }}" class="p-2 bg-gray-50 text-gray-400 hover:text-gray-600 rounded-xl transition">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h3 class="text-xl font-bold text-gray-900">Formulir Data Siswa</h3>
            </div>

            <form action="{{ route('admin.siswa.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap Siswa</label>
                        <input type="text" name="nama_siswa" required value="{{ old('nama_siswa') }}" placeholder="Contoh: Ahmad Fauzi" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 @error('nama_siswa') border-red-500 @enderror">
                        @error('nama_siswa') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">NIS (Nomor Induk Siswa)</label>
                        <input type="text" name="nis" value="{{ old('nis') }}" placeholder="Contoh: 001" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 @error('nis') border-red-500 @enderror">
                        @error('nis') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kelas</label>
                        <select name="kelas" required class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Kelas</option>
                            @foreach($globalKelasList as $kls)
                                <option value="{{ $kls->nama_kelas }}" {{ old('kelas') == $kls->nama_kelas ? 'selected' : '' }}>Kelas {{ $kls->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tahun Ajaran</label>
                        <select name="tahun_ajaran" required class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->nama_tahun }}" {{ (old('tahun_ajaran') == $ta->nama_tahun || $ta->is_active) ? 'selected' : '' }}>{{ $ta->nama_tahun }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                        <select name="status" required class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="lulus" {{ old('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                            <option value="pindah" {{ old('status') == 'pindah' ? 'selected' : '' }}>Pindah</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-[#065F46] text-white rounded-2xl font-bold hover:bg-emerald-800 shadow-xl shadow-emerald-100 transition flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        Simpan Data Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
