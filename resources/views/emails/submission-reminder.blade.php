<x-mail::message>
# Pengingat Pengajuan Jadwal Medical Check-up (MCU)

Yth. Bapak/Ibu **{{ $nama }}** dari Departemen **{{ $departemen }}**,

Data kami menunjukkan bahwa Anda belum mengajukan jadwal Medical Check-up (MCU) tahunan Anda. 

Periode pengajuan jadwal untuk departemen Anda sangat direkomendasikan sebelum atau sekitar tanggal **{{ $tanggal_ref }}**.

Mohon segera ajukan jadwal MCU Anda melalui Aplikasi Mobile STMC Health untuk memastikan Anda mendapatkan slot waktu yang tersedia.

<x-mail::button :url="url('/mobile')">
Ajukan Jadwal MCU Sekarang
</x-mail::button>

Terima kasih atas perhatiannya.

Salam,
Tim STMC Health

</x-mail::message>