<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePrincipalWelcomeRequest;
use App\Models\PrincipalWelcome;
use App\Models\StaffMember;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PrincipalWelcomeController extends Controller
{
    public function edit(): View
    {
        $welcome = PrincipalWelcome::with(['staffMember.photo'])->first();

        if (! $welcome) {
            $firstStaff = StaffMember::active()->first();
            
            $welcome = PrincipalWelcome::create([
                'staff_member_id' => $firstStaff?->id,
                'content'         => 'Selamat datang di website resmi jurusan kami.',
            ]);
        }

        $staffMembers = StaffMember::active()
            ->orderBy('name')
            ->get();

        return view('admin.sambutan.edit', compact('welcome', 'staffMembers'));
    }

    public function update(UpdatePrincipalWelcomeRequest $request): RedirectResponse
    {
        $welcome = PrincipalWelcome::first() ?? new PrincipalWelcome();
        $data = $request->validated();

        $welcome->fill([
            'staff_member_id' => $data['staff_member_id'],
            'content'         => $data['content'],
            'updated_by'      => auth()->id(),
        ]);
        
        $welcome->save();

        if (class_exists(ActivityLogger::class)) {
            app(ActivityLogger::class)->log(
                action: 'update_principal_welcome',
                description: 'Memperbarui isi Sambutan Ketua Jurusan.',
                subject: $welcome
            );
        }

        return redirect()
            ->route('admin.sambutan.edit')
            ->with('success', 'Sambutan Ketua Jurusan berhasil diperbarui.');
    }
}