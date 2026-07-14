<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Absensi;
use App\Models\JadwalPelajaran;
use App\Models\User;

class AbsensiPolicy
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

    public function view(User $user, Absensi $absensi): bool
    {
        return $this->mengampuJadwal($user, $absensi->jadwal_id);
    }

    public function create(User $user): bool
    {
        return $user->isGuru();
    }

    public function update(User $user, Absensi $absensi): bool
    {
        return $this->mengampuJadwal($user, $absensi->jadwal_id);
    }

    public function delete(User $user, Absensi $absensi): bool
    {
        return $this->mengampuJadwal($user, $absensi->jadwal_id);
    }

    /**
     * Guru boleh menyentuh absensi jika ia pengampu jadwal tersebut,
     * atau wali kelas dari kelas pada jadwal itu.
     */
    public function mengampuJadwal(User $user, int $jadwalId): bool
    {
        $guru = $user->guru;

        if ($guru === null) {
            return false;
        }

        $jadwal = JadwalPelajaran::find($jadwalId);

        if ($jadwal === null) {
            return false;
        }

        if ($jadwal->guru_id === $guru->id) {
            return true;
        }

        return $guru->kelasWali()
            ->where('id', $jadwal->kelas_id)
            ->exists();
    }
}
