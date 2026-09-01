<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/**
 * Notifikasi in-app generik (Core System). Modul lain tinggal panggil
 * $user->notify(new GenericNotification(...)) — tidak perlu bikin class
 * notifikasi baru untuk kasus sederhana "judul + isi + link opsional".
 */
class GenericNotification extends Notification
{
    public function __construct(
        public string $title,
        public string $body,
        public ?string $url = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
        ];
    }
}
