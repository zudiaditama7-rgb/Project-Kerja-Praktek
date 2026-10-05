<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_siswa' => 'required|string|max:255',
            'nis' => 'nullable|string|unique:siswas,nis,' . ($this->route('siswa') ? $this->route('siswa')->id : 'NULL'),
            'kelas' => 'required|string',
            'tahun_ajaran' => 'required|string',
            'status' => 'required|in:aktif,lulus,pindah',
        ];
    }
}
