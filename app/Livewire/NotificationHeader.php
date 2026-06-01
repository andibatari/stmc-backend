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
        // 1. Hitung totalnya untuk angka di Lonceng (Badge)
        $this->unreadNotificationsCount = JadwalMcu::where('status', 'Pending')->count();

        // 2. Ambil 5 data terbaru beserta relasi karyawannya untuk ditampilkan di dropdown
        if ($this->unreadNotificationsCount > 0) {
            $this->latestNotifications = JadwalMcu::with('karyawan') // Pastikan relasinya benar
                ->where('status', 'Pending')
                ->latest('created_at') // Urutkan dari yang paling baru
                ->take(5) // Ambil 5 saja agar dropdown tidak kepanjangan
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