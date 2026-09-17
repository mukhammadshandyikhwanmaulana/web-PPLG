<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\UnitUsahaLink;
use Illuminate\View\View;

class UnitUsahaController extends Controller
{
    public function __invoke(): View
    {
        // Temukan record pertama tanpa memicu pembuatan data baru otomatis saat GET request
        $unitUsaha = UnitUsahaLink::first() ?? new UnitUsahaLink(['is_active' => false]);

        return view('public.unit-usaha.index', [
            'unitUsaha' => $unitUsaha,
        ]);
    }
}