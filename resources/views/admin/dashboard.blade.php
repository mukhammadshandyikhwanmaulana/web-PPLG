@extends('layouts.admin.app')
@section('title', 'Dashboard')
@section('content')
    <h1 class="text-xl font-semibold mb-6">Dashboard</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg border p-4">
            <div class="text-sm text-gray-500">Jumlah Guru</div>
            <div class="text-2xl font-semibold mt-1">{{ $guruCount }}</div>
        </div>
        <div class="bg-white rounded-lg border p-4 opacity-50">
            <div class="text-sm text-gray-500">Modul Konten</div>
            <div class="text-sm mt-1">Belum tersedia</div>
        </div>
    </div>
@endsection