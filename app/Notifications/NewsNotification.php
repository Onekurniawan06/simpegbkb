<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Berita; // Import model Berita Anda

class NewsNotification extends Notification
{
    use Queueable;

    protected $berita;

    /**
     * Create a new notification instance.
     */
    public function __construct(Berita $berita)
    {
        $this->berita = $berita;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Kita pakai channel database saja
    }

    /**
     * Get the array representation of the notification.
     * Metode ini menyimpan data ke kolom 'data' di tabel notifications
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'id_pengumuman' => $this->berita->id_pengumuman,
            'judul' => $this->berita->judul,
            'deskripsi_singkat' => $this->berita->deskripsi_singkat,
            'url' => url('/berita/' . $this->berita->id_pengumuman),
        ];
    }
}
