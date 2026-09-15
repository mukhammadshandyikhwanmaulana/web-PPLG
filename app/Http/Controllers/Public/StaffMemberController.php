<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\StaffMember;
use Illuminate\View\View;

class StaffMemberController extends Controller
{
    public function __invoke(): View
    {
        // Ambil seluruh data staf aktif tanpa pencarian agar struktur bagan utuh
        $allStaff = StaffMember::with('photo')
            ->active()
            ->orderByPositionHierarchy()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // 1. Level 1: Ketua Program / Kaprog (Atas)
        $ketua = $allStaff->first(function ($s) {
            $pos = strtolower($s->position ?? '');
            return str_contains($pos, 'ketua') || str_contains($pos, 'kaprog') || str_contains($pos, 'kajur') || str_contains($pos, 'kepala');
        });

        // Fallback jika tidak ditemukan kata kunci 'ketua', ambil data pertama
        if (!$ketua && $allStaff->isNotEmpty()) {
            $ketua = $allStaff->first();
        }

        // 2. Level 2: Guru-Guru / Pengajar (Tengah)
        $gurus = $allStaff->filter(function ($s) use ($ketua) {
            if ($ketua && $s->id === $ketua->id) {
                return false;
            }
            $pos = strtolower($s->position ?? '');
            return str_contains($pos, 'guru') || str_contains($pos, 'pengajar') || str_contains($pos, 'produktif');
        });

        // 3. Level 3: Staf / Laboran / Admin (Bawah)
        $staffs = $allStaff->filter(function ($s) use ($ketua, $gurus) {
            if ($ketua && $s->id === $ketua->id) {
                return false;
            }
            return ! $gurus->contains('id', $s->id);
        });

        return view('public.staff.index', [
            'ketua'    => $ketua,
            'gurus'    => $gurus,
            'staffs'   => $staffs,
            'allStaff' => $allStaff,
        ]);
    }
}