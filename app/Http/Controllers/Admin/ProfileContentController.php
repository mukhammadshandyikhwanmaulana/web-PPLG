<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProfileContentRequest;
use App\Models\ProfileContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileContentController extends Controller
{
    public function edit(): View
    {
        $profile = ProfileContent::first() ?? ProfileContent::create([]);

        return view('admin.profil.edit', [
            'profile' => $profile,
        ]);
    }

    public function update(UpdateProfileContentRequest $request): RedirectResponse
    {
        $profile = ProfileContent::first() ?? ProfileContent::create([]);

        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $profile->update($data);

        return redirect()
            ->route('admin.profil.edit')
            ->with('success', 'Konten Profil berhasil diperbarui.');
    }
}