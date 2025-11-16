<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\JadwalMcu; // Pastikan model Jadwal Anda sudah ada

class NotificationHeader extends Component
{
    /**
     * Properti publik untuk menyimpan jumlah notifikasi yang belum dibaca.
     * Nilai ini akan diakses oleh livewire/notification-header.blade.php.
     */
    public $unreadNotificationsCount = 0;

    /**
     * Lifecycle hook Livewire: dijalankan saat komponen dimuat.
     */
    public function mount()
    {
        $this->checkNotifications();
    }
    
    /**
     * Metode untuk menghitung jumlah jadwal pasien baru yang statusnya pending.
     */
    public function checkNotifications()
    {
        // Cari di database (asumsi ada kolom 'status' di tabel 'jadwal')
        $this->unreadNotificationsCount = JadwalMcu::where('status', 'pending')->count();
    }

    /**
     * Metode ini dipanggil saat tombol lonceng diklik.
     * Tujuannya bisa untuk mereset tampilan badge.
     * Dalam kasus ini, kita hanya akan memanggil checkNotifications() lagi untuk memastikan
     * badge merefleksikan data terkini, namun tidak mereset data di DB.
     */
    public function markNotificationsAsRead()
    {
        // Logika sederhana: Muat ulang hitungan dari database.
        // Jika Anda ingin badge hilang setelah diklik, Anda perlu menambahkan 
        // kolom 'is_read_by_admin' pada tabel Jadwal dan mengupdatenya di sini.
        
        $this->checkNotifications(); 

        // Contoh jika ingin badge langsung hilang:
        // $this->unreadNotificationsCount = 0;
        
        // Catatan: Karena notifikasi ini mewakili data live (pending schedules),
        // badge akan muncul kembali saat halaman di-refresh jika masih ada data 'pending'.
    }

    /**
     * Metode render Livewire.
     */
    public function render()
    {
        return view('livewire.notification-header');
    }
}
