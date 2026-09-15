@extends('layouts.admin.app')

@section('title', 'Tambah FAQ')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="mb-2">
                <a href="{{ route('admin.faq.index') }}"
                   class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-slate-500 hover:text-indigo-600 transition duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar FAQ
                </a>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Tambah FAQ Baru</h1>
            <p class="mt-0.5 text-xs sm:text-sm text-slate-500">Isi formulir di bawah ini untuk menambahkan pertanyaan dan jawaban baru.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-4 sm:p-6">
        <form method="POST" action="{{ route('admin.faq.store') }}" class="space-y-6">
            @include('admin.faq._form', ['faq' => new \App\Models\Faq()])

            <div class="pt-5 border-t border-slate-200 flex items-center justify-between sm:justify-end gap-3">
                <a href="{{ route('admin.faq.index') }}" 
                   class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition text-center shrink-0">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm cursor-pointer whitespace-nowrap shrink-0">
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