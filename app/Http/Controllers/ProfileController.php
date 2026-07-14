<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('profile.edit', ['user' => request()->user()]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->nama = $validated['nama'];
        $user->email = $validated['email'];
        $user->no_hp = $validated['no_hp'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Sinkronkan nama pada profil guru bila ada.
        $user->guru?->update(['nama_lengkap' => $validated['nama']]);

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
