<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\UnitUsahaLink;
use Illuminate\View\View;

class UnitUsahaController extends Controller
{
    public function __invoke(): View
    {
        $unitUsaha = UnitUsahaLink::firstOrCreate([], ['is_active' => false]);

        return view('public.unit-usaha.index', [
            'unitUsaha' => $unitUsaha,
        ]);
    }
}