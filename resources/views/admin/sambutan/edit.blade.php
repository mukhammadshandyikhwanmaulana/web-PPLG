@extends('layouts.admin.app')

@section('title', 'Sambutan Ketua Kompetensi Keahlian')

@section('content')
@php
    $staffList = $staffMembers ?? collect([]);
    $mappedStaffs = $staffList->map(function ($s) {
        $photoRel = $s->photo ?? null;
        $photoPath = $photoRel?->file_path ?? $photoRel?->path ?? $s->avatar ?? null;
        $photoUrl = null;

        if ($photoPath) {
            if (filter_var($photoPath, FILTER_VALIDATE_URL)) {
                $photoUrl = $photoPath;
            } else {
                $photoDisk = $photoRel?->disk ?? 'public';
                $photoUrl = \Illuminate\Support\Facades\Storage::disk($photoDisk)->url(ltrim($photoPath, '/'));
            }
        }

        return [
            'id' => $s->id,
            'name' => $s->name,
            'position' => $s->position ?? 'Ketua Kompetensi Keahlian',
            'photo' => $photoUrl,
        ];
    });
@endphp

<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4"
     x-data="{
         selectedStaffId: '{{ old('staff_member_id', $welcome?->staff_member_id ?? '') }}',
         staffs: @js($mappedStaffs),
         get activeStaff() {
             return this.staffs.find(s => s.id == this.selectedStaffId) || null;
         }
     }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Sambutan Ketua Kompetensi Keahlian</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola data Ketua Kompetensi Keahlian dan isi kata sambutan untuk halaman publik.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-4 sm:p-6">
        <form action="{{ route('admin.sambutan.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Header Informasi -->
            <div class="border-b border-slate-200 pb-3.5 flex items-center gap-2.5">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Konfigurasi Sambutan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih profil pengampu jurusan dan tuliskan pesan sambutan resmi.</p>
                </div>
            </div>

            <!-- Pilihan Ketua Kompetensi Keahlian -->
            <div>
                <label for="staff_member_id" class="block text-sm font-semibold text-slate-900 mb-1.5">
                    Ketua Kompetensi Keahlian <span class="text-rose-500 ml-1">*</span>
                </label>
                <select name="staff_member_id" id="staff_member_id" required 
                        x-model="selectedStaffId"
                        class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition bg-white @error('staff_member_id') border-rose-300 bg-rose-50/30 @enderror">
                    <option value="">-- Pilih Guru / Staf --</option>
                    @foreach ($staffList as $staff)
                        <option value="{{ $staff->id }}">
                            {{ $staff->name }} {{ $staff->position ? "({$staff->position})" : '' }}
                        </option>
                    @endforeach
                </select>
                @error('staff_member_id')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
                <p class="text-xs text-slate-400 mt-1.5">
                    * Foto dan nama otomatis diambil dari profil Guru/Staf yang dipilih. Kelola data guru di <a href="{{ route('admin.guru.index') }}" class="text-indigo-600 hover:underline font-medium">Manajemen Guru</a>.
                </p>
            </div>

            <!-- Kartu Preview Informasi Terpilih (Dynamic via Alpine.js) -->
            <div x-show="activeStaff" x-cloak x-transition
                 class="p-4 border border-indigo-100 rounded-xl bg-indigo-50/40 flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl overflow-hidden bg-white shrink-0 border border-indigo-200 shadow-xs flex items-center justify-center">
                    <template x-if="activeStaff && activeStaff.photo">
                        <img :src="activeStaff.photo" :alt="activeStaff.name" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!activeStaff || !activeStaff.photo">
                        <span class="text-[10px] font-bold text-slate-400 uppercase text-center px-1">NO PHOTO</span>
                    </template>
                </div>
                <div class="min-w-0">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-100 text-indigo-700 border border-indigo-200 mb-0.5">
                        Ketua Terpilih
                    </span>
                    <h4 class="text-sm font-bold text-slate-900 truncate" x-text="activeStaff ? activeStaff.name : ''"></h4>
                    <p class="text-xs text-slate-500 truncate" x-text="activeStaff ? activeStaff.position : 'Ketua Kompetensi Keahlian'"></p>
                </div>
            </div>

            <!-- Teks Sambutan -->
            <div>
                <label for="content" class="block text-sm font-semibold text-slate-900 mb-1.5">
                    Isi Sambutan <span class="text-rose-500 ml-1">*</span>
                </label>
                <textarea name="content" id="content" rows="8" required
                          placeholder="Tuliskan isi kata sambutan Ketua Kompetensi Keahlian di sini..."
                          class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition resize-none @error('content') border-rose-300 bg-rose-50/30 @enderror">{{ old('content', $welcome?->content ?? '') }}</textarea>
                @error('content')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Area Tombol CTA -->
            <div class="pt-5 border-t border-slate-200 flex items-center justify-between sm:justify-end gap-3">
                <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition text-center shrink-0">Batal</a>
                <button type="submit" class="inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm cursor-pointer whitespace-nowrap shrink-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection