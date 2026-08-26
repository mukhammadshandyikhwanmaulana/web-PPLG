@php $p = $partner ?? null; @endphp

<div class="space-y-5">
    <div>
        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
            Nama Mitra <span class="text-red-500">*</span>
        </label>
        <input type="text" name="name" id="name" value="{{ old('name', $p->name ?? '') }}" required
               placeholder="Nama perusahaan/mitra industri..."
               class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition @error('name') border-red-500 @enderror">
        @error('name') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="website_url" class="block text-sm font-semibold text-gray-700 mb-1.5">Website URL</label>
        <input type="url" name="website_url" id="website_url" value="{{ old('website_url', $p->website_url ?? '') }}"
               placeholder="https://example.com"
               class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition @error('website_url') border-red-500 @enderror">
        @error('website_url') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="logo" class="block text-sm font-semibold text-gray-700 mb-1">Logo Mitra</label>
        <p class="text-xs text-gray-500 mb-2.5">Opsional (Maksimal 5MB, format JPEG, PNG, WEBP)</p>

        <div class="flex items-start gap-4 mb-3">
            <div id="preview-container" class="{{ (isset($p) && $p->logo) ? 'block' : 'hidden' }}">
                <span class="block text-xs font-medium text-gray-500 mb-1">Pratinjau Logo:</span>
                <img id="logo-preview" 
                     src="{{ (isset($p) && $p->logo) ? $p->logo->url : '#' }}" 
                     alt="Preview Logo" 
                     class="w-32 h-24 object-contain bg-gray-50 rounded-lg border border-gray-200 shadow-sm p-1">
            </div>
        </div>

        <input type="file" name="logo" id="logo" accept="image/jpeg,image/png,image/webp"
               onchange="previewImage(event)"
               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-lg cursor-pointer bg-gray-50/50 focus:outline-none @error('logo') border-red-500 @enderror">
        @error('logo') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="sort_order" class="block text-sm font-semibold text-gray-700 mb-1">Urutan Tampil</label>
        <p class="text-xs text-gray-500 mb-1.5">Angka urutan posisi tampilan (misal: 0, 1, 2)</p>
        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $p->sort_order ?? 0) }}" min="0"
               class="w-full sm:w-48 text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition @error('sort_order') border-red-500 @enderror">
        @error('sort_order') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="status" class="block text-sm font-semibold text-gray-700 mb-1.5">Status Publikasi <span class="text-red-500">*</span></label>
        <select name="status" id="status" required
                class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-white @error('status') border-red-500 @enderror">
            @foreach (\App\Enums\PublishStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(old('status', $p?->status?->value ?? 'draft') === $status->value)>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
        @error('status') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
    </div>
</div>

<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('logo-preview');
        const container = document.getElementById('preview-container');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>