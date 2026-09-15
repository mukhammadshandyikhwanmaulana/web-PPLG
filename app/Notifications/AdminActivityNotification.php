<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class AdminActivityNotification extends Notification
{
    use Queueable;

    protected mixed $actor;        // User (Guru/Staf) yang melakukan aksi
    protected string $action;      // Aksi: 'created', 'updated', atau 'deleted'
    protected string $subjectType; // Jenis data, contoh: 'Kegiatan', 'Prestasi', 'Fasilitas'
    protected string $subjectName; // Judul/Nama data
    protected ?string $subjectUrl; // Link URL untuk membuka detail/edit data

    /**
     * Inisialisasi data notifikasi saat dipanggil.
     */
    public function __construct(mixed $actor, string $action, string $subjectType, string $subjectName, ?string $subjectUrl = null)
    {
        $this->actor = $actor;
        $this->action = $action;
        $this->subjectType = $subjectType;
        $this->subjectName = $subjectName;
        $this->subjectUrl = $subjectUrl;
    }

    /**
     * Tentukan saluran pengiriman notifikasi (gunakan database).
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Tentukan struktur data JSON yang disimpan di kolom `data` tabel `notifications`.
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }

    /**
     * Data array pengganti untuk kompatibilitas database/broadcast.
     */
    public function toArray(object $notifiable): array
    {
        $actionText = match ($this->action) {
            'created' => 'menambahkan',
            'updated' => 'memperbarui',
            'deleted' => 'menghapus',
            default   => $this->action,
        };

        $actorAvatar = null;
        if ($this->actor) {
            if (isset($this->actor->avatar_url)) {
                $actorAvatar = $this->actor->avatar_url;
            } elseif (isset($this->actor->avatar) && $this->actor->avatar) {
                $actorAvatar = Storage::disk('public')->url($this->actor->avatar);
            }
        }

        $message = sprintf(
            "%s telah %s %s '%s'.",
            $this->actor?->name ?? 'Pengguna',
            $actionText,
            $this->subjectType,
            $this->subjectName
        );

        return [
            'actor_id'     => $this->actor?->id,
            'actor_name'   => $this->actor?->name ?? 'Pengguna',
            'actor_avatar' => $actorAvatar,
            'action'       => $this->action,
            'subject_type' => $this->subjectType,
            'subject_name' => $this->subjectName,
            'message'      => $message,
            'url'          => $this->subjectUrl,
        ];
    }
}