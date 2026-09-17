<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUnitUsahaRequest;
use App\Models\UnitUsahaLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UnitUsahaController extends Controller
{
    public function edit(): View
    {
        $link = UnitUsahaLink::first() ?? UnitUsahaLink::create(['is_active' => false]);

        return view('admin.unit-usaha.edit', compact('link'));
    }

    public function update(UpdateUnitUsahaRequest $request): RedirectResponse
    {
        $link = UnitUsahaLink::first() ?? UnitUsahaLink::create(['is_active' => false]);

        $link->update(array_merge(
            $request->validated(),
            ['updated_by' => $request->user()->id]
        ));

        return redirect()
            ->route('admin.unit-usaha.edit')
            ->with('success', 'Unit Usaha berhasil diperbarui.');
    }
}