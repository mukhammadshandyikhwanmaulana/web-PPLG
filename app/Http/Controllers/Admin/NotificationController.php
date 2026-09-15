<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Tandai 1 notifikasi spesifik sebagai sudah dibaca, lalu redirect ke URL tujuan jika ada.
     */
    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $targetUrl = $notification->data['url'] ?? null;

        if ($targetUrl) {
            return redirect()->to($targetUrl);
        }

        return redirect()->back()->with('success', 'Notifikasi ditandai telah dibaca.');
    }

    /**
     * Tandai SEMUA notifikasi sebagai sudah dibaca.
     */
    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}