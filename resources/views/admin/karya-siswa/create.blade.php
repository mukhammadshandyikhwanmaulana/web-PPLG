@extends('layouts.admin.app')

@section('title', 'Tambah Karya Siswa')

@section('content')
<div class="max-w-3xl mx-auto py-4 sm:py-6 px-3 sm:px-6">
    <!-- Link Navigasi Kembali -->
    <div class="mb-3">
        <a href="{{ route('admin.karya-siswa.index') }}"
           class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-gray-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Karya Siswa
        </a>
    </div>

    <!-- Header Section -->
    <div class="mb-5">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Tambah Karya Siswa</h1>
        <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Isi formulir di bawah ini untuk menambahkan karya siswa baru.</p>
    </div>

    <!-- Form Card Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <form method="POST" action="{{ route('admin.karya-siswa.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            @include('admin.karya-siswa._form')

            <!-- Submit Button -->
            <div class="pt-5 border-t border-gray-200 flex justify-end">
                <button type="submit" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition duration-150 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Karya Siswa
                </button>
            </div>
        </form>
    </div>
</div>
@endsection