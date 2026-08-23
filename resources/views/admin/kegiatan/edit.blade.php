@extends('layouts.admin.app')

@section('title', 'Edit Kegiatan')

@section('content')
<div class="max-w-3xl mx-auto py-4 sm:py-6 px-3 sm:px-6">
    <div class="mb-3">
        <a href="{{ route('admin.kegiatan.index') }}"
           class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-gray-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Kegiatan
        </a>
    </div>

    <div class="mb-5">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Edit Kegiatan</h1>
        <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Perbarui informasi, status, dan dokumentasi kegiatan ini.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <form method="POST" action="{{ route('admin.kegiatan.update', $activity) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('admin.kegiatan._form')
        </form>
    </div>
</div>
@endsection