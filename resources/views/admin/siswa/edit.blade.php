<x-app-layout>
    <x-slot name="header">Edit Data Siswa</x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-50">
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('admin.siswa.index') }}" class="p-2 bg-gray-50 text-gray-400 hover:text-gray-600 rounded-xl transition">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h3 class="text-xl font-bold text-gray-900">Edit Siswa: {{ $siswa->nama_siswa }}</h3>
            </div>

            <form action="{{ route('admin.siswa.update', $siswa->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap Siswa</label>
                        <input type="text" name="nama_siswa" required value="{{ old('nama_siswa', $siswa->nama_siswa) }}" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">NIS (Nomor Induk Siswa)</label>
                        <input type="text" name="nis" value="{{ old('nis', $siswa->nis) }}" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kelas</label>
                        <select name="kelas" required class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                            @foreach($globalKelasList as $kls)
                                <option value="{{ $kls->nama_kelas }}" {{ old('kelas', $siswa->kelas) == $kls->nama_kelas ? 'selected' : '' }}>Kelas {{ $kls->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tahun Ajaran</label>
                        <select name="tahun_ajaran" required class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->nama_tahun }}" {{ old('tahun_ajaran', $siswa->tahun_ajaran) == $ta->nama_tahun ? 'selected' : '' }}>{{ $ta->nama_tahun }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                        <select name="status" required class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                            <option value="aktif" {{ old('status', $siswa->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="lulus" {{ old('status', $siswa->status) == 'lulus' ? 'selected' : '' }}>Lulus</option>
                            <option value="pindah" {{ old('status', $siswa->status) == 'pindah' ? 'selected' : '' }}>Pindah</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-[#065F46] text-white rounded-2xl font-bold hover:bg-emerald-800 shadow-xl shadow-emerald-100 transition flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        Update Data Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
