<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFaqRequest;
use App\Http\Requests\Admin\UpdateFaqRequest;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('is_active');

        $faqs = Faq::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('question', 'like', "%{$search}%")
                      ->orWhere('answer', 'like', "%{$search}%");
                });
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('is_active', filter_var($status, FILTER_VALIDATE_BOOLEAN));
            })
            ->orderBy('sort_order')
            ->orderBy('question')
            ->paginate(15)
            ->withQueryString();

        return view('admin.faq.index', [
            'faqs'   => $faqs,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.faq.create', [
            'faq' => new Faq(),
        ]);
    }

    public function store(StoreFaqRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use (&$data) {
            if (! isset($data['sort_order']) || $data['sort_order'] === null) {
                $data['sort_order'] = (Faq::max('sort_order') ?? 0) + 1;
            }

            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();

            Faq::create($data);
        });

        return redirect()
            ->route('admin.faq.index')
            ->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function edit(Faq $faq): View
    {
        return view('admin.faq.edit', [
            'faq' => $faq,
        ]);
    }

    public function update(UpdateFaqRequest $request, Faq $faq): RedirectResponse
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $faq->update($data);

        return redirect()
            ->route('admin.faq.index')
            ->with('success', 'FAQ berhasil diperbarui.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()
            ->route('admin.faq.index')
            ->with('success', 'FAQ berhasil dihapus.');
    }
}