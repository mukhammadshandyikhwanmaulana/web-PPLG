@extends('layouts.admin.app')

@section('title', 'Manajemen FAQ')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">FAQ (Pertanyaan Umum)</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola daftar pertanyaan dan jawaban yang sering ditanyakan pengunjung.</p>
        </div>
        <div>
            <a href="{{ route('admin.faq.create') }}"
               class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah FAQ
            </a>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-xs border border-slate-300">
        <form id="search-form" method="GET" action="{{ route('admin.faq.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            {{-- Input Search --}}
            <div class="relative w-full md:flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" id="search-input" value="{{ request('search') }}"
                       placeholder="Cari pertanyaan / jawaban..."
                       autocomplete="off"
                       oninput="handleAutoSearch()"
                       class="w-full pl-10 {{ request('search') ? 'pr-10' : 'pr-3.5' }} py-2.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition bg-white">

                @if(request('search'))
                    <a href="{{ route('admin.faq.index', request()->except('search')) }}" 
                       title="Hapus kata kunci pencarian"
                       class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </div>

            {{-- Dropdown Status --}}
            <div class="w-full md:w-48">
                <select name="is_active" onchange="document.getElementById('search-form').submit()" class="w-full py-2.5 px-3.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition bg-white cursor-pointer font-medium text-slate-700">
                    <option value="">Semua Status</option>
                    <option value="1" @selected(request('is_active') === '1')>Aktif</option>
                    <option value="0" @selected(request('is_active') === '0')>Nonaktif</option>
                </select>
            </div>

            {{-- Tombol Reset --}}
            @php $hasFilter = request()->anyFilled(['search', 'is_active']); @endphp
            @if($hasFilter)
                <a href="{{ route('admin.faq.index') }}" 
                   class="flex items-center justify-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 text-sm font-medium px-4 py-2.5 rounded-xl transition duration-150 shrink-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Reset</span>
                </a>
            @endif
        </form>
    </div>

    <!-- TAMPILAN HP / MOBILE -->
    <div class="block md:hidden space-y-3">
        @forelse ($faqs as $faq)
            <div class="bg-white rounded-2xl border border-slate-300 p-4 shadow-xs flex flex-col gap-3">
                <div class="flex items-start justify-between gap-2">
                    <h2 class="text-sm font-semibold text-slate-900 leading-snug">{{ $faq->question }}</h2>
                    @if($faq->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">Aktif</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 shrink-0">Nonaktif</span>
                    @endif
                </div>

                <p class="text-xs text-slate-600 line-clamp-3 bg-slate-50 p-3 rounded-xl border border-slate-200/60 leading-relaxed">
                    {{ $faq->answer }}
                </p>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                    <div class="text-xs text-slate-500 font-medium">
                        Urutan: <span class="font-mono font-semibold text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">#{{ $faq->sort_order }}</span>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('admin.faq.edit', $faq) }}"
                           class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-900 transition leading-none">
                            Edit
                        </a>
                        <form action="{{ route('admin.faq.destroy', $faq) }}" method="POST" class="inline-flex items-center m-0 p-0"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus FAQ ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center text-xs font-semibold text-rose-600 hover:text-rose-800 transition leading-none p-0 border-0 bg-transparent cursor-pointer">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-300 p-8 text-center text-slate-500 text-sm shadow-xs">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Belum ada data FAQ yang ditemukan.
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN LAPTOP / DESKTOP -->
    <div class="hidden md:block bg-white rounded-2xl shadow-xs border border-slate-300 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                    <th class="py-3.5 px-4">Pertanyaan</th>
                    <th class="py-3.5 px-4">Jawaban</th>
                    <th class="py-3.5 px-4 w-24 text-center">Urutan</th>
                    <th class="py-3.5 px-4 w-28 text-center">Status</th>
                    <th class="py-3.5 px-4 w-32 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse ($faqs as $faq)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-3 px-4 text-center text-slate-400 font-medium">
                            {{ method_exists($faqs, 'firstItem') && $faqs->firstItem() ? $faqs->firstItem() + $loop->index : $loop->iteration }}
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-900 max-w-xs">
                            <div class="line-clamp-2" title="{{ $faq->question }}">{{ $faq->question }}</div>
                        </td>
                        <td class="py-3 px-4 text-slate-600 max-w-md">
                            <div class="line-clamp-2" title="{{ $faq->answer }}">{{ $faq->answer }}</div>
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center justify-center min-w-[2rem] px-1.5 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-600 font-mono">
                                #{{ $faq->sort_order }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if($faq->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.faq.edit', $faq) }}"
                                   class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-900 transition leading-none">
                                    Edit
                                </a>
                                <form action="{{ route('admin.faq.destroy', $faq) }}" method="POST" class="inline-flex items-center m-0 p-0"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus FAQ ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center text-xs font-semibold text-rose-600 hover:text-rose-800 transition leading-none p-0 border-0 bg-transparent cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 px-4 text-center text-slate-500">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Belum ada data FAQ yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if (method_exists($faqs, 'hasPages') && $faqs->hasPages())
            <div class="px-4 py-3 bg-slate-50 border-t border-slate-200">
                {{ $faqs->links() }}
            </div>
        @endif
    </div>

    @if (method_exists($faqs, 'hasPages') && $faqs->hasPages())
        <div class="mt-3 block md:hidden">
            {{ $faqs->links() }}
        </div>
    @endif
</div>

<script>
    let searchTimer;

    function handleAutoSearch() {
        clearTimeout(searchTimer);

        const input = document.getElementById('search-input');
        const urlParams = new URLSearchParams(window.location.search);
        const hasSearchParam = urlParams.has('search') && urlParams.get('search') !== '';

        if (input.value.trim() === '' && !hasSearchParam) {
            return;
        }

        searchTimer = setTimeout(() => {
            sessionStorage.setItem('faq_search_focus', 'true');
            document.getElementById('search-form').submit();
        }, 500);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('search-input');
        
        if (searchInput && sessionStorage.getItem('faq_search_focus') === 'true') {
            searchInput.focus();
            const textLen = searchInput.value.length;
            searchInput.setSelectionRange(textLen, textLen);
            sessionStorage.removeItem('faq_search_focus');
        }
    });
</script>
@endsection