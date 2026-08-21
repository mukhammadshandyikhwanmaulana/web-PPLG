@extends('layouts.admin.app')

@section('title', 'Karya Siswa')

@section('content')
<div class="max-w-7xl mx-auto py-4 sm:py-6 px-4 sm:px-6 lg:px-8 space-y-5">

    {{-- Header: Judul & Tombol Tambah --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Karya Siswa</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Kelola publikasi dan galeri hasil karya siswa.</p>
        </div>
        <a href="{{ route('admin.karya-siswa.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-xs sm:text-sm font-medium shadow-sm transition-all shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Karya Siswa</span>
        </a>
    </div>

    {{-- Box Form Search & Filter Responsif --}}
    <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-gray-200 shadow-sm">
        <form method="GET" action="{{ route('admin.karya-siswa.index') }}" class="flex flex-col md:flex-row gap-2.5">
            {{-- Input Search --}}
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari judul atau kontributor..."
                       class="pl-9 w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2">
            </div>

            {{-- Dropdown Filters --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:flex gap-2">
                <select name="status" onchange="this.form.submit()"
                        class="w-full text-xs sm:text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 py-2 bg-white cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="published" @selected(strtolower(request('status')) === 'published')>Published</option>
                    <option value="draft" @selected(strtolower(request('status')) === 'draft')>Draft</option>
                </select>

                <select name="pembimbing_id" onchange="this.form.submit()"
                        class="w-full text-xs sm:text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 py-2 bg-white cursor-pointer">
                    <option value="">Semua Pembimbing</option>
                    @foreach ($supervisors ?? $pembimbings ?? [] as $pembimbing)
                        <option value="{{ $pembimbing->id }}" @selected(request('pembimbing_id') == $pembimbing->id || request('supervisor_id') == $pembimbing->id)>
                            {{ $pembimbing->name }}
                        </option>
                    @endforeach
                </select>

                <a href="{{ route('admin.karya-siswa.index') }}"
                   class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 transition-colors col-span-2 sm:col-span-1">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @php
        $items = $studentWorks ?? $karyaSiswas ?? $works ?? [];
    @endphp

    {{-- 1. TAMPILAN MOBILE & TABLET KECIL (< md): Grid Card View --}}
    <div class="grid grid-cols-1 gap-3 md:hidden">
        @forelse ($items as $karya)
            @php
                $statusVal = strtolower(is_object($karya->status ?? null) ? $karya->status->value : ($karya->status ?? 'draft'));
                $imgCount = $karya->galleries ? $karya->galleries->count() : ($karya->images_count ?? 0);
                
                $supervisorId = $karya->supervisor_id ?? $karya->pembimbing_id ?? $karya->supervisor?->id;
                $supervisorName = $karya->supervisor?->name ?? $karya->supervisor_name ?? $karya->pembimbing?->name;
            @endphp
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h2 class="font-semibold text-gray-900 text-sm leading-snug break-words">
                            {{ $karya->title }}
                        </h2>
                        @if (!empty($karya->demo_url))
                            <a href="{{ $karya->demo_url }}" target="_blank" rel="noopener noreferrer" 
                               class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:underline mt-0.5 font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                <span>Lihat Demo</span>
                            </a>
                        @endif
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold shrink-0 uppercase tracking-wider
                        {{ $statusVal === 'published' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                        {{ $statusVal }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                    <div>
                        <span class="block text-gray-400">Kontributor</span>
                        <span class="font-medium text-gray-800 break-words">{{ $karya->contributor_name ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400">Pembimbing</span>
                        @if ($supervisorName && $supervisorId)
                            <a href="{{ route('admin.karya-siswa.index', array_merge(request()->query(), ['pembimbing_id' => $supervisorId])) }}" 
                               class="font-medium text-indigo-600 hover:text-indigo-800 hover:underline break-words">
                                {{ $supervisorName }}
                            </a>
                        @else
                            <span class="font-medium text-gray-800 break-words">{{ $supervisorName ?? '—' }}</span>
                        @endif
                    </div>
                    <div class="mt-1">
                        <span class="block text-gray-400">Gambar</span>
                        <span class="font-medium text-gray-800">📷 {{ $imgCount }}/5</span>
                    </div>
                    <div class="mt-1">
                        <span class="block text-gray-400">Featured</span>
                        <span class="font-medium text-gray-800">{{ $karya->is_featured ? '⭐ Ya' : '—' }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100 text-xs font-semibold">
                    <a href="{{ route('admin.karya-siswa.edit', $karya) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                    <form method="POST" action="{{ route('admin.karya-siswa.destroy', $karya) }}" class="inline" onsubmit="return confirm('Hapus karya siswa ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-rose-600 hover:text-rose-800">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 p-6 text-center text-gray-400 text-xs">
                Belum ada data karya siswa.
            </div>
        @endforelse
    </div>

    {{-- 2. TAMPILAN DESKTOP & TABLET BESAR (>= md): Table View --}}
    <div class="hidden md:block bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50/75 border-b border-gray-200 text-gray-500 text-xs font-semibold uppercase tracking-wider">
                        <th class="py-3.5 px-4 min-w-[220px]">Judul</th>
                        <th class="py-3.5 px-4 min-w-[140px]">Kontributor</th>
                        <th class="py-3.5 px-4 min-w-[140px]">Pembimbing</th>
                        <th class="py-3.5 px-4 text-center min-w-[90px]">Gambar</th>
                        <th class="py-3.5 px-4 text-center min-w-[110px]">Status</th>
                        <th class="py-3.5 px-4 text-center min-w-[90px]">Featured</th>
                        <th class="py-3.5 px-4 text-right min-w-[120px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse ($items as $karya)
                        @php
                            $statusVal = strtolower(is_object($karya->status ?? null) ? $karya->status->value : ($karya->status ?? 'draft'));
                            $imgCount = $karya->galleries ? $karya->galleries->count() : ($karya->images_count ?? 0);

                            $supervisorId = $karya->supervisor_id ?? $karya->pembimbing_id ?? $karya->supervisor?->id;
                            $supervisorName = $karya->supervisor?->name ?? $karya->supervisor_name ?? $karya->pembimbing?->name;
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-medium text-gray-900">
                                <div>{{ $karya->title }}</div>
                                @if (!empty($karya->demo_url))
                                    <a href="{{ $karya->demo_url }}" target="_blank" rel="noopener noreferrer" 
                                       class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 hover:underline mt-0.5 font-normal">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                        <span>Lihat Demo</span>
                                    </a>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-gray-600">{{ $karya->contributor_name ?? '—' }}</td>
                            <td class="py-3.5 px-4 text-gray-600">
                                @if ($supervisorName && $supervisorId)
                                    <a href="{{ route('admin.karya-siswa.index', array_merge(request()->query(), ['pembimbing_id' => $supervisorId])) }}" 
                                       class="text-indigo-600 hover:text-indigo-800 hover:underline font-medium transition-colors">
                                        {{ $supervisorName }}
                                    </a>
                                @else
                                    <span>{{ $supervisorName ?? '—' }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center text-gray-600 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700 font-medium">
                                    📷 {{ $imgCount }}/5
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider
                                    {{ $statusVal === 'published' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    {{ $statusVal }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($karya->is_featured)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-amber-50 text-amber-700 border border-amber-200 font-medium">⭐ Ya</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap font-medium space-x-2">
                                <a href="{{ route('admin.karya-siswa.edit', $karya) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors">Edit</a>
                                <form method="POST" action="{{ route('admin.karya-siswa.destroy', $karya) }}" class="inline" onsubmit="return confirm('Hapus karya siswa ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-900 transition-colors">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400">Belum ada data karya siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination Preserved --}}
    <div>
        @if (is_object($items) && method_exists($items, 'withQueryString'))
            {{ $items->withQueryString()->links() }}
        @elseif (is_object($items) && method_exists($items, 'links'))
            {{ $items->links() }}
        @endif
    </div>
</div>
@endsection