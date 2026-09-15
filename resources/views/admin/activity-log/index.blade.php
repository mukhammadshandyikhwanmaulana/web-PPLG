@extends('layouts.admin.app')

@section('title', 'Log Aktivitas Sistem')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Log Aktivitas Sistem</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Rekam jejak audit trail pengguna dan aksi sistem yang bersifat permanen (*read-only*).</p>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-xs border border-slate-300">
        <form id="search-form" method="GET" action="{{ route('admin.activity-log.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            
            <!-- Search Input -->
            <div class="relative w-full md:flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" id="search-input" value="{{ request('search') }}"
                       placeholder="Cari deskripsi, aksi, atau nama pengguna..."
                       autocomplete="off"
                       oninput="handleAutoSearch()"
                       class="w-full pl-10 {{ request('search') ? 'pr-10' : 'pr-3.5' }} py-2.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition bg-white">

                @if(request('search'))
                    <a href="{{ route('admin.activity-log.index', request()->except('search')) }}" 
                       title="Hapus pencarian"
                       class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </div>

            <!-- Filter Dropdown Aksi -->
            <div class="w-full md:w-56 shrink-0">
                <select name="action" onchange="document.getElementById('search-form').submit()" 
                        class="w-full py-2.5 px-3.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition bg-white cursor-pointer font-medium text-slate-700">
                    <option value="">Semua Aksi</option>
                    @if(isset($actions) && is_iterable($actions))
                        @foreach($actions as $act)
                            <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                                {{ ucfirst($act) }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Filter Dropdown User -->
            <div class="w-full md:w-56 shrink-0">
                <select name="user_id" onchange="document.getElementById('search-form').submit()" 
                        class="w-full py-2.5 px-3.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition bg-white cursor-pointer font-medium text-slate-700">
                    <option value="">Semua Pengguna</option>
                    @if(isset($users) && is_iterable($users))
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            @php $hasFilter = request()->anyFilled(['search', 'action', 'user_id', 'start_date', 'end_date']); @endphp
            @if($hasFilter)
                <a href="{{ route('admin.activity-log.index') }}" 
                   class="flex items-center justify-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 text-sm font-medium px-4 py-2.5 rounded-xl transition duration-150 shrink-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Reset</span>
                </a>
            @endif
        </form>
    </div>

    <!-- TAMPILAN MOBILE / HP -->
    <div class="block md:hidden space-y-3">
        @forelse ($logs as $log)
            @php
                $actLower = strtolower($log->action ?? '');
                $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                if ($actLower === 'login') {
                    $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                } elseif ($actLower === 'logout') {
                    $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                } elseif (in_array($actLower, ['delete', 'destroy', 'remove'])) {
                    $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                } elseif (in_array($actLower, ['create', 'store', 'add'])) {
                    $badgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                } elseif (in_array($actLower, ['update', 'edit', 'modify'])) {
                    $badgeClass = 'bg-sky-50 text-sky-700 border-sky-200';
                }
            @endphp
            <div class="bg-white rounded-2xl border border-slate-300 p-3.5 shadow-xs flex flex-col gap-2">
                <div class="flex items-center justify-between gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold tracking-wide uppercase border {{ $badgeClass }}">
                        {{ strtoupper($log->action ?? 'AKSI') }}
                    </span>
                    <span class="text-[10px] text-slate-400 font-mono">{{ $log->created_at?->format('d M Y, H:i') ?? '—' }}</span>
                </div>

                <p class="text-xs font-semibold text-slate-900 leading-snug">{{ $log->description ?? '-' }}</p>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-[11px]">
                    <div class="min-w-0 flex-1 pr-2">
                        <span class="font-semibold text-slate-700 block truncate">{{ $log->user?->name ?? 'Sistem / Tamu' }}</span>
                    </div>
                    <a href="{{ route('admin.activity-log.show', $log) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-900 transition shrink-0">
                        Detail →
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-300 p-8 text-center text-slate-500 text-sm shadow-xs">
                Belum ada rekam aktivitas sistem yang ditemukan.
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN DESKTOP / LAPTOP -->
    <div class="hidden md:block bg-white rounded-2xl shadow-xs border border-slate-300 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                        <th class="py-3.5 px-4 w-16">ID</th>
                        <th class="py-3.5 px-4 w-28">Aksi</th>
                        <th class="py-3.5 px-4">Deskripsi</th>
                        <th class="py-3.5 px-4 w-44">Pengguna</th>
                        <th class="py-3.5 px-4 w-32">IP Address</th>
                        <th class="py-3.5 px-4 w-40">Waktu</th>
                        <th class="py-3.5 px-4 w-20 text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($logs as $log)
                        @php
                            $actLower = strtolower($log->action ?? '');
                            $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                            if ($actLower === 'login') {
                                $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            } elseif ($actLower === 'logout') {
                                $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                            } elseif (in_array($actLower, ['delete', 'destroy', 'remove'])) {
                                $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                            } elseif (in_array($actLower, ['create', 'store', 'add'])) {
                                $badgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                            } elseif (in_array($actLower, ['update', 'edit', 'modify'])) {
                                $badgeClass = 'bg-sky-50 text-sky-700 border-sky-200';
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4 font-mono text-xs text-slate-400">#{{ $log->id }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold tracking-wide uppercase border {{ $badgeClass }}">
                                    {{ strtoupper($log->action ?? 'AKSI') }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-800 font-medium text-xs max-w-xs">
                                <div class="truncate" title="{{ $log->description ?? '-' }}">{{ $log->description ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 text-xs">
                                @if($log->user)
                                    <span class="font-semibold text-slate-900 block truncate">{{ $log->user->name }}</span>
                                    <span class="text-slate-400 text-[11px] block truncate">{{ $log->user->email }}</span>
                                @else
                                    <span class="text-slate-400 italic">Sistem / Tamu</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono text-xs text-slate-600 whitespace-nowrap">
                                {{ $log->ip_address ?? '—' }}
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-500 whitespace-nowrap">
                                {{ $log->created_at?->format('d M Y, H:i:s') ?? '—' }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.activity-log.show', $log) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs transition">
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 px-4 text-center text-slate-500 text-sm">
                                Belum ada rekam aktivitas sistem yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if (method_exists($logs, 'hasPages') && $logs->hasPages())
        <div class="bg-white px-4 py-3 rounded-2xl border border-slate-300 shadow-xs">
            {{ $logs->links() }}
        </div>
    @endif

</div>

<script>
    let searchTimer;

    function handleAutoSearch() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            sessionStorage.setItem('activity_log_search_focus', 'true');
            const form = document.getElementById('search-form');
            if (form) form.submit();
        }, 500);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('search-input');
        if (searchInput && sessionStorage.getItem('activity_log_search_focus') === 'true') {
            searchInput.focus();
            const textLen = searchInput.value.length;
            searchInput.setSelectionRange(textLen, textLen);
            sessionStorage.removeItem('activity_log_search_focus');
        }
    });
</script>
@endsection