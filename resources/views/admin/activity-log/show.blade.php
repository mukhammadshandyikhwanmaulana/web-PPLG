@extends('layouts.admin.app')

@section('title', 'Detail Log Aktivitas #' . $log->id)

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="mb-2">
                <a href="{{ route('admin.activity-log.index') }}"
                   class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-slate-500 hover:text-indigo-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Log
                </a>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Rincian Log Aktivitas #{{ $log->id }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Informasi detail audit trail transaksi sistem.</p>
        </div>
    </div>

    <!-- Container Card Utama -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-4 sm:p-6 space-y-6">

        <!-- Deskripsi Utama -->
        <div>
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Deskripsi Aktivitas</span>
            <p class="text-base sm:text-lg font-semibold text-slate-900 mt-1 leading-relaxed">{{ $log->description ?? '-' }}</p>
        </div>

        <!-- Grid Informasi Detail -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-4 border-t border-slate-100">
            <div>
                <span class="text-xs font-semibold text-slate-500 block">Jenis Aksi</span>
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
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold tracking-wide uppercase mt-1 border {{ $badgeClass }}">
                    {{ strtoupper($log->action ?? 'AKSI') }}
                </span>
            </div>

            <div>
                <span class="text-xs font-semibold text-slate-500 block">Pengguna</span>
                <p class="text-sm font-semibold text-slate-900 mt-1">{{ $log->user?->name ?? 'Sistem / Tamu' }}</p>
                @if($log->user?->email)
                    <p class="text-xs text-slate-400 font-mono">{{ $log->user->email }}</p>
                @endif
            </div>

            <div>
                <span class="text-xs font-semibold text-slate-500 block">IP Address</span>
                <p class="text-xs font-mono font-semibold text-slate-800 mt-1">{{ $log->ip_address ?? '—' }}</p>
            </div>

            <div>
                <span class="text-xs font-semibold text-slate-500 block">Waktu Kejadian</span>
                <p class="text-xs font-mono font-medium text-slate-800 mt-1">
                    {{ $log->created_at?->format('d F Y, H:i:s') ?? '—' }}
                </p>
            </div>
        </div>

        <!-- User Agent -->
        <div class="pt-4 border-t border-slate-100">
            <span class="text-xs font-semibold text-slate-500 block mb-1">User Agent</span>
            <p class="text-xs font-mono text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-200 break-all leading-relaxed">
                {{ $log->user_agent ?? '—' }}
            </p>
        </div>

        <!-- JSON Metadata / Properties -->
        <div class="pt-4 border-t border-slate-100">
            <span class="text-xs font-semibold text-slate-500 block mb-2">Metadata Tambahan</span>
            @php
                $propertiesData = $log->properties;
                if (is_string($propertiesData)) {
                    $propertiesData = json_decode($propertiesData, true);
                }
            @endphp

            @if(!empty($propertiesData))
                <pre class="bg-slate-50 text-slate-800 text-xs font-mono p-4 rounded-xl border border-slate-300 overflow-x-auto leading-relaxed no-scrollbar">{{ json_encode($propertiesData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
            @else
                <p class="text-xs text-slate-400 italic">Tidak ada metadata tambahan untuk entri ini.</p>
            @endif
        </div>

        <!-- Bottom Action -->
        <div class="pt-4 border-t border-slate-200 flex items-center justify-end">
            <a href="{{ route('admin.activity-log.index') }}" 
               class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition text-center shrink-0">
                Kembali
            </a>
        </div>
    </div>

</div>
@endsection