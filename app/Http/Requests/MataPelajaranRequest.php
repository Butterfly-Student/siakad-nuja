<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MataPelajaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $mapelId = $this->route('mataPelajaran')?->id;

        return [
            'kode_mapel' => ['required', 'string', 'max:20', Rule::unique('mata_pelajaran', 'kode_mapel')->ignore($mapelId)],
            'nama_mapel' => ['required', 'string', 'max:100'],
            'jenjang' => ['required', Rule::in(['SD', 'SMP', 'SMA', 'SMK', 'MI', 'MTs', 'MA'])],
            'kkm' => ['nullable', 'integer', 'min:0', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'kode_mapel' => 'kode mapel',
            'nama_mapel' => 'nama mapel',
            'kkm' => 'KKM',
        ];
    }
}
