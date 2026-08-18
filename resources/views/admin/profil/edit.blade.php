@extends('layouts.admin.app')
@section('title', 'Kelola Konten Profil')
@section('content')
    <h1 class="text-xl font-semibold mb-6">Kelola Konten Profil</h1>

    <form method="POST" action="{{ route('admin.profil.update') }}" class="bg-white rounded-lg border p-6 max-w-2xl space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="history_content" class="block text-sm font-medium mb-1">Sejarah (RPL &rarr; PPLG)</label>
            <textarea name="history_content" id="history_content" rows="6"
                      class="w-full border rounded px-3 py-2 text-sm">{{ old('history_content', $profile->history_content) }}</textarea>
            @error('history_content')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="vision_mission_content" class="block text-sm font-medium mb-1">Visi &amp; Misi</label>
            <textarea name="vision_mission_content" id="vision_mission_content" rows="6"
                      class="w-full border rounded px-3 py-2 text-sm">{{ old('vision_mission_content', $profile->vision_mission_content) }}</textarea>
            @error('vision_mission_content')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="about_excerpt" class="block text-sm font-medium mb-1">Ringkasan "Tentang PPLG" (Beranda)</label>
            <textarea name="about_excerpt" id="about_excerpt" rows="3"
                      class="w-full border rounded px-3 py-2 text-sm">{{ old('about_excerpt', $profile->about_excerpt) }}</textarea>
            @error('about_excerpt')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="meta_title" class="block text-sm font-medium mb-1">Meta Title <span class="text-gray-400 text-xs">(maks. 60 karakter)</span></label>
            <input type="text" name="meta_title" id="meta_title" maxlength="60"
                   value="{{ old('meta_title', $profile->meta_title) }}"
                   class="w-full border rounded px-3 py-2 text-sm">
            @error('meta_title')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="meta_description" class="block text-sm font-medium mb-1">Meta Description <span class="text-gray-400 text-xs">(maks. 160 karakter)</span></label>
            <textarea name="meta_description" id="meta_description" rows="2" maxlength="160"
                      class="w-full border rounded px-3 py-2 text-sm">{{ old('meta_description', $profile->meta_description) }}</textarea>
            @error('meta_description')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-2">
            <button type="submit" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-800">Simpan Perubahan</button>
        </div>
    </form>
@endsection