<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function __invoke(Request $request): View
    {
        $search = trim((string) $request->input('q', ''));

        $faqs = Faq::active()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('question', 'like', "%{$search}%")
                      ->orWhere('answer', 'like', "%{$search}%");
                });
            })
            ->get();

        return view('public.faq.index', [
            'faqs'   => $faqs,
            'search' => $search,
        ]);
    }
}