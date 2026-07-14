<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Nilai;
use App\Models\User;

class NilaiPolicy
{
    /**
     * Admin dapat melakukan apa saja.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isGuru();
    }

    public function view(User $user, Nilai $nilai): bool
    {
        return $this->mengampuKelasMapel($user, $nilai->kelas_id, $nilai->mapel_id);
    }

    public function create(User $user): bool
    {
        return $user->isGuru();
    }

    public function update(User $user, Nilai $nilai): bool
    {
        return $this->mengampuKelasMapel($user, $nilai->kelas_id, $nilai->mapel_id);
    }

    public function delete(User $user, Nilai $nilai): bool
    {
        return $this->mengampuKelasMapel($user, $nilai->kelas_id, $nilai->mapel_id);
    }

    /**
     * Guru boleh menyentuh nilai jika ia mengampu mapel di kelas tersebut
     * (punya jadwal), atau menjadi wali kelas tersebut.
     */
    private function mengampuKelasMapel(User $user, int $kelasId, int $mapelId): bool
    {
        $guru = $user->guru;

        if ($guru === null) {
            return false;
        }

        $mengampu = $guru->jadwal()
            ->where('kelas_id', $kelasId)
            ->where('mapel_id', $mapelId)
            ->exists();

        $waliKelas = $guru->kelasWali()
            ->where('id', $kelasId)
            ->exists();

        return $mengampu || $waliKelas;
    }
}
