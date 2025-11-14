<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NouveauProduitPublie extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $produitId,
        public string $titre,
        public ?string $imageUrl,
        public int $vendeurId
    ) {}

    public function via($notifiable): array
    {
        return ['database']; 
    }

    public function toDatabase($notifiable): array
    {
        return [
            'produitId' => $this->produitId,
            'titre'     => $this->titre,
            'imageUrl'  => $this->imageUrl,
            'vendeurId' => $this->vendeurId,
            'published_at' => now(),
        ];
    }
}