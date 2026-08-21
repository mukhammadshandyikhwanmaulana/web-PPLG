@extends('layouts.admin.app')

@section('title', 'Tambah Karya Siswa')

@section('content')
<div class="max-w-4xl mx-auto py-4 sm:py-6 px-4 sm:px-6 lg:px-8 space-y-5">

    {{-- Header & Navigasi Kembali --}}
    <div class="flex items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.karya-siswa.index') }}" 
               class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-gray-500 hover:text-indigo-600 font-medium mb-1 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar Karya
            </a>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Tambah Karya Siswa</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Lengkapi formulir di bawah untuk menambahkan publikasi karya siswa baru.</p>
        </div>
    </div>

    {{-- Form Container Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6 lg:p-8">
        <form method="POST" action="{{ route('admin.karya-siswa.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Include Form Input Utama --}}
            @include('admin.karya-siswa._form', ['studentWork' => null])

            {{-- Tombol Aksi --}}
            <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.karya-siswa.index') }}" 
                   class="w-full sm:w-auto px-4 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 text-center transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-medium shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Karya</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection