@php $act = $activity ?? null; @endphp

<div class="space-y-6">
    <!-- Grid Judul & Tanggal -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Judul Kegiatan <span class="text-red-500">*</span></label>
            <input type="text"
                   name="title"
                   value="{{ old('title', $act->title ?? '') }}"
                   placeholder="Masukkan judul kegiatan..."
                   class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition @error('title') border-red-500 @enderror">
            @error('title')
                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
            @error
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Kegiatan <span class="text-red-500">*</span></label>
            <input type="date"
                   name="event_date"
                   value="{{ old('event_date', isset($act) ? $act->event_date->format('Y-m-d') : '') }}"
                   class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition @error('event_date') border-red-500 @enderror">
            @error('event_date')
                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Konten -->
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Konten / Deskripsi</label>
        <textarea name="content"
                  rows="5"
                  placeholder="Tuliskan deskripsi lengkap mengenai kegiatan ini..."
                  class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition @error('content') border-red-500 @enderror">{{ old('content', $act->content ?? '') }}</textarea>
        @error('content')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Status Publikasi -->
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status Publikasi <span class="text-red-500">*</span></label>
        <select name="status" class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-white @error('status') border-red-500 @enderror">
            @foreach (\App\Enums\PublishStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(old('status', $act->status->value ?? 'draft') === $status->value)>
                    {{ ucfirst($status->value) }}
                </option>
            @endforeach
        </select>
        @error('status')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Cover -->
    <div class="pt-4 border-t border-gray-100">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Cover Kegiatan</label>
        <p class="text-xs text-gray-500 mb-2">Opsional (Maksimal 2MB, Format: JPEG, PNG, WEBP)</p>

        @if (isset($act) && $act->cover)
            <div class="mb-3">
                <span class="block text-xs font-medium text-gray-500 mb-1">Cover Saat Ini:</span>
                <img src="{{ Storage::url($act->cover->file_path) }}" alt="Cover Saat Ini" class="w-44 h-28 object-cover rounded-lg border border-gray-200 shadow-sm">
            </div>
        @endif

        <input type="file"
               name="cover"
               accept="image/jpeg,image/png,image/webp"
               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-lg cursor-pointer bg-gray-50/50 focus:outline-none">
        @error('cover')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Gambar Galeri -->
    <div class="pt-4 border-t border-gray-100">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Tambah Gambar Galeri</label>
        <p class="text-xs text-gray-500 mb-2">Bisa memilih beberapa file sekaligus (Maksimal total 8 gambar, @2MB, JPEG/PNG/WEBP)</p>

        <input type="file"
               name="images[]"
               multiple
               accept="image/jpeg,image/png,image/webp"
               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-lg cursor-pointer bg-gray-50/50 focus:outline-none">
        @error('images')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
        @error('images.*')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>
</div>