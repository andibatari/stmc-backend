<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login STMC</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Inter (opsional, bisa diganti sesuai font di gambar) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            /* Menambahkan gambar latar belakang */
            background-image: url('{{asset('images/bgSemenTonasa.png')}}'); /* Ganti dengan URL gambar asli PT Semen Tonasa */
            background-size: cover;
            background-position: center;
            background-attachment: fixed; /* Membuat background tidak bergerak saat di-scroll */
            background-repeat: no-repeat; /* Mencegah gambar diulang */
            
        }
          
         /* Styling untuk tombol merah gradasi */
        .btn-red-gradient {
            background: linear-gradient(to right, #B91C1C, #991B1B); /* Gradasi dari merah terang ke gelap */
            box-shadow: 0 4px 6px rgba(185, 28, 28, 0.3); /* Bayangan untuk efek 3D */
            transition: all 0.2s ease-in-out;
        }
        .btn-red-gradient:hover {
            background: linear-gradient(to right, #991B1B, #B91C1C); /* Balik gradasi saat hover */
            box-shadow: 0 6px 10px rgba(185, 28, 28, 0.4);
        }
        /* Styling untuk input field dengan border merah muda */
        .input-red-border {
            border-color: #FECACA; /* bg-red-200 */
        }
        .input-red-border:focus {
            border-color: #EF4444; /* text-red-500 */
            box-shadow: 0 0 0 1px #EF4444;
        }
        
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class=" w-full max-w-4xl bg-white rounded-xl shadow-lg border border-red-300 overflow-hidden" style="height: 500px;">
        <div class="flex h-full">
            <!-- Bagian Kiri (Gambar Ilustrasi) -->
            <div class="w-1/2 bg-red-50 p-8 flex flex-col items-center justify-center relative">
                <div class="absolute top-6 left-6 flex items-center">
                    <img src="{{ asset('images/LogoStmc.png') }}" alt="STMC Logo" class="h-11 w-11 mr-2 rounded">
                    <div class="flex flex-col">
                        <span class="font-bold text-lg text-red-800">STMC</span>
                        <span class="text-xs text-red-600">Semen Tonasa Medical Centre</span>
                    </div>
                </div>
                <img src="{{ asset('images/ilustrasi_dokter.png')}}" alt="Ilustrasi Dokter" class="max-w-full h-auto">
                <!-- Anda bisa mengganti placeholder image dengan ilustrasi yang sesuai -->
            </div>

            <!-- Bagian Kanan (Form Login) -->
            <div class="w-1/2 p-8 flex flex-col justify-center">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800">Login</h1>
                    <p class="mt-2 text-sm text-gray-600">Masuk untuk melanjutkan ke dashboard</p>
                </div>
                
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="no_sap" class="block text-sm font-medium text-gray-700 mb-1">No. SAP</label>
                        <input type="text" id="no_sap" name="no_sap" value="{{ old('no_sap') }}" required autofocus
                               class="w-full px-4 py-2 border rounded-md shadow-sm input-red-border focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out sm:text-sm"
                               placeholder="Masukkan No. SAP">
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-2 border rounded-md shadow-sm input-red-border focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out sm:text-sm"
                               placeholder="Masukkan Password">
                    </div>

                    <div>
                        <button type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white btn-red-gradient focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
