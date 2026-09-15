@php
    $faq = $faq ?? new \App\Models\Faq();
@endphp

@csrf

<div class="space-y-5">
    {{-- Pertanyaan --}}
    <div>
        <label for="question" class="block text-sm font-semibold text-slate-900 mb-1.5">
            Pertanyaan <span class="text-rose-500">*</span>
        </label>
        <input
            type="text"
            name="question"
            id="question"
            value="{{ old('question', $faq->question ?? '') }}"
            required
            placeholder="Masukkan pertanyaan FAQ..."
            class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('question') border-rose-300 bg-rose-50/30 @enderror"
        >
        @error('question')
            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    {{-- Jawaban --}}
    <div>
        <label for="answer" class="block text-sm font-semibold text-slate-900 mb-1.5">
            Jawaban <span class="text-rose-500">*</span>
        </label>
        <textarea
            name="answer"
            id="answer"
            rows="5"
            required
            placeholder="Tuliskan jawaban lengkap..."
            class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition resize-none @error('answer') border-rose-300 bg-rose-50/30 @enderror"
        >{{ old('answer', $faq->answer ?? '') }}</textarea>
        @error('answer')
            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    {{-- Urutan Tampil --}}
    <div>
        <label for="sort_order" class="block text-sm font-semibold text-slate-900 mb-1">Urutan Tampil</label>
        <p class="text-xs text-slate-400 mb-1.5">Angka posisi urutan tampilan (kosongkan untuk urutan otomatis di posisi terakhir)</p>
        <input
            type="number"
            name="sort_order"
            id="sort_order"
            min="0"
            placeholder="Otomatis (urutan terakhir)"
            value="{{ old('sort_order', $faq->sort_order ?? '') }}"
            class="w-full sm:w-64 text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('sort_order') border-rose-300 bg-rose-50/30 @enderror"
        >
        @error('sort_order')
            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    {{-- Status Aktif --}}
    <div class="pt-2">
        <label for="is_active" class="inline-flex items-center cursor-pointer gap-2.5 select-none">
            {{-- Hidden input memastikan nilai '0' terkirim jika checkbox tidak dicentang --}}
            <input type="hidden" name="is_active" value="0">
            <input
                type="checkbox"
                name="is_active"
                id="is_active"
                value="1"
                @checked(old('is_active', isset($faq) && $faq->exists ? $faq->is_active : true))
                class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
            >
            <span class="text-sm font-semibold text-slate-900">Aktifkan FAQ ini (ditampilkan ke publik)</span>
        </label>
    </div>
</div>