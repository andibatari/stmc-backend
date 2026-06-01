<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\JadwalMcu; 

class NotificationHeader extends Component
{
    public $unreadNotificationsCount = 0;
    public $latestNotifications = []; // Variabel baru untuk menampung daftar notifikasi

    public function mount()
    {
        $this->checkNotifications();
    }
    
    public function checkNotifications()
    {
        // Kita gunakan array agar sistem menangkap semua variasi kata status
        $statusPencarian = ['scheduled', 'Scheduled'];

        // 1. Hitung totalnya
        $this->unreadNotificationsCount = JadwalMcu::whereIn('status', $statusPencarian)->count();

        // 2. Ambil 5 data terbaru
        if ($this->unreadNotificationsCount > 0) {
            $this->latestNotifications = JadwalMcu::with('karyawan') 
                ->whereIn('status', $statusPencarian)
                ->latest('created_at') 
                ->take(5) 
                ->get();
        } else {
            $this->latestNotifications = [];
        }
    }

    public function markNotificationsAsRead()
    {
        $this->checkNotifications(); 
    }

    public function render()
    {
        return view('livewire.notification-header');
    }
}