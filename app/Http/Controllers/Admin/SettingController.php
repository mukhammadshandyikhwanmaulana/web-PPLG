<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Models\Setting;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $setting = Setting::current();

        return view('admin.pengaturan.edit', [
            'setting' => $setting,
        ]);
    }

    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        $setting = Setting::current();

        $setting->update($request->validated());

        if (class_exists(ActivityLogger::class)) {
            app(ActivityLogger::class)->log(
                action: 'update',
                description: 'Memperbarui pengaturan website',
                subject: $setting,
                properties: $request->validated()
            );
        }

        return redirect()
            ->route('admin.pengaturan.edit')
            ->with('success', 'Pengaturan website berhasil disimpan.');
    }
}