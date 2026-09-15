@extends('layouts.public')

@section('title', 'Unit Usaha Sekolah - ' . config('app.name', 'Website Sekolah'))

@section('content')
    <!-- Banner Header -->
    <section class="bg-blue-600 py-16 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Unit Usaha Sekolah</h1>
            <p class="mt-2 text-blue-100 text-sm sm:text-base">Pusat kewirausahaan, teaching factory, dan layanan bisnis sekolah</p>
        </div>
    </section>

    <!-- Section Content Unit Usaha -->
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($unitUsahaList->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($unitUsahaList as $unit)
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col justify-between hover:shadow-md transition">
                            <div class="p-6">
                                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center font-bold text-xl mb-4">
                                    {{ strtoupper(substr($unit->name ?? 'U', 0, 1)) }}
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 mb-2">
                                    {{ $unit->name }}
                                </h3>
                                <p class="text-sm text-slate-600 leading-relaxed line-clamp-4">
                                    {{ $unit->description ?? 'Layanan unit usaha aktif sekolah.' }}
                                </p>
                            </div>

                            @if(!empty($unit->link) || !empty($unit->url))
                                <div class="p-6 pt-0">
                                    <a href="{{ $unit->link ?? $unit->url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-xs font-bold text-blue-600 hover:text-blue-700 transition">
                                        Kunjungi Link Unit Usaha &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center bg-white rounded-xl border border-slate-200 text-slate-500 max-w-xl mx-auto">
                    <p class="text-lg font-medium">Belum ada data unit usaha yang ditambahkan.</p>
                </div>
            @endif
        </div>
    </section>
@endsection