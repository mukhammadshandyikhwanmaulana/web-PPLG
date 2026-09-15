@php
    $m = $media ?? $m ?? null;
@endphp

<div class="space-y-4">
    <div>
        <label for="alt_text" class="block text-sm font-semibold text-slate-900 mb-1.5">
            Deskripsi Berkas / Karya <span class="text-xs font-normal text-slate-400 ml-1">(Opsional)</span>
        </label>
        <textarea name="alt_text"
                  id="alt_text"
                  rows="4"
                  placeholder="Masukkan deskripsi berkas atau keterangan karya siswa..."
                  class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition resize-none @error('alt_text') border-rose-300 bg-rose-50/30 @enderror">{{ old('alt_text', $m?->alt_text ?? '') }}</textarea>
        
        @error('alt_text')
            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
        @enderror
    </div>
</div>