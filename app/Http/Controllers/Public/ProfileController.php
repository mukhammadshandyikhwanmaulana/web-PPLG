<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\IndustryPartner;
use App\Models\ProfileContent;
use App\Models\StaffMember;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __invoke(): View
    {
        $profile = ProfileContent::firstOrCreate([], []);

        // Ambil data Guru & Staf yang aktif
        $staffMembers = StaffMember::active()
            ->with('photo')
            ->orderByPositionHierarchy()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $partners = IndustryPartner::with('logo')
            ->published()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('public.profile', [
            'profile'      => $profile,
            'staffMembers' => $staffMembers,
            'partners'     => $partners,
        ]);
    }
}