<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Enterprise Attendance And stationery Control</title>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        /* 1. Base Latar Belakang */
        body, html {
            min-height: 100vh;
            width: 100vw;
            margin: 0;
            padding: 0;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background-color: #f4f6f9; /* Abu-abu kertas yang sangat bersih */
            overflow: hidden;
            position: relative;
            z-index: 0;
        }

        .full-screen-wrapper {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        /* 2. Nuansa Indofood: Gelembung Warna Korporat (Lebih Pekat) */
        .bubble-bg {
            content: "";
            position: absolute;
            border-radius: 50%;
            filter: blur(120px); /* Blur dinaikkan agar percampuran warnanya halus */
            z-index: -2;
            pointer-events: none;
            animation: float 15s infinite alternate ease-in-out;
        }

        /* Biru Indofood (Dominan di Kiri & Atas) */
        .bubble-blue {
            width: 800px;
            height: 800px;
            background-color: rgba(0, 74, 153, 0.45); /* Warna Biru Logo */
            top: -200px;
            left: -200px;
        }

        /* Merah Indofood (Kanan Bawah) */
        .bubble-red {
            width: 600px;
            height: 600px;
            background-color: rgba(237, 28, 36, 0.35); /* Warna Merah Logo */
            bottom: -100px;
            right: -150px;
            animation-duration: 12s;
            animation-direction: alternate-reverse;
        }

        /* Kuning Indofood/Indomie (Kiri Bawah - Sentuhan Halus) */
        .bubble-yellow {
            width: 500px;
            height: 500px;
            background-color: rgba(245, 158, 11, 0.25); /* Warna Emas/Kuning */
            bottom: -200px;
            left: 10%;
            animation-duration: 18s;
            animation-delay: 2s;
        }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(40px, 40px) scale(1.05); }
        }

        /* 3. Pola Sirkuit Digital */
        .login-pattern {
            position: absolute;
            inset: 0;
            background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgdmlld0JveD0iMCAwIDQwIDQwIj48ZyBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiMwMDQ5OTkiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTAgMGg0MHY0MEgwVjB6bTIwIDIwaDIwdjIwSDIWMjB6TTAgMjBoMjB2MjBIMFYyMHoyMCAwaDIwdjIwSDIwVjB6Ii8+PC9nPjwvZz48L3N2Zz4=');
            z-index: -1;
            opacity: 1; /* Diperjelas sedikit */
            pointer-events: none;
            width: 100%;
            height: 100%;
        }

        /* 4. Efek Kaca (Glassmorphism) */
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 25px 50px -12px rgba(0, 74, 153, 0.15), 0 0 0 1px rgba(255,255,255,0.3) inset;
        }

        /* Warna Khusus Kelas Tailwind */
        .bg-indofood-blue { background-color: #004a99; }
        .text-indofood-blue { color: #004a99; }
        .bg-indofood-red { background-color: #ed1c24; }
        .text-indofood-red { color: #ed1c24; }

        /* Custom Input Transition */
        .modern-input {
            background-color: rgba(249, 250, 251, 0.9);
            border: 1px solid rgba(229, 231, 235, 1);
            transition: all 0.3s ease;
        }
        .modern-input:focus {
            background-color: #ffffff;
            border-color: #004a99;
            box-shadow: 0 0 0 4px rgba(0, 74, 153, 0.15);
            outline: none;
        }

        /* Dekorasi Lingkaran di Kolom Biru */
        .circle-decoration {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 100%);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="antialiased text-gray-800">

    <div class="login-pattern"></div>
    <div class="bubble-bg bubble-blue"></div>
    <div class="bubble-bg bubble-red"></div>
    <div class="bubble-bg bubble-yellow"></div>

    <div class="full-screen-wrapper">
        <div class="glass-card flex flex-col md:flex-row w-full max-w-5xl rounded-3xl overflow-hidden h-auto md:h-[600px] relative z-10 transition-all duration-500">

            <div class="hidden md:flex md:w-5/12 bg-indofood-blue relative p-12 flex-col justify-center gap-8 overflow-hidden shadow-[15px_0_30px_rgba(0,74,153,0.15)] z-10">
                <div class="circle-decoration w-64 h-64 -top-20 -right-20 border border-white border-opacity-20"></div>
                <div class="circle-decoration w-80 h-80 -bottom-32 -left-20 bg-blue-400 mix-blend-overlay opacity-50"></div>

                <div class="relative z-10 w-full flex justify-start -ml-12 -mt-20">
                    <img src="{{ asset('images/logo-baru.png') }}" alt="Logo Indofood CBP GWS" class="w-64 lg:w-[280px] h-auto object-contain object-left drop-shadow-lg">
                </div>

                <div class="relative z-10">
                    <div class="w-12 h-1 bg-white mb-6 rounded-full shadow-lg opacity-90"></div>
                    <h2 class="text-3xl font-black text-white leading-tight mb-4 drop-shadow-md">
                        Enterprise<br>Attendance<br>And stationery<br>Control.
                    </h2>
                    <p class="text-blue-100 text-sm leading-relaxed font-medium">
                        Sistem Manajemen Absensi dan Stationery Terpadu untuk Efisiensi, Validasi, dan Akurasi Data Kehadiran serta Pengelolaan Inventaris ATK Secara Real-Time
                    </p>
                </div>
            </div>

            <div class="w-full md:w-7/12 p-8 md:p-14 lg:p-16 flex flex-col justify-center relative bg-white bg-opacity-30">

                <div class="md:hidden flex justify-center mb-8">
                    <img src="{{ asset('images/logo-baru.png') }}" alt="Logo Indofood CBP GWS" class="w-48 sm:w-60 h-auto object-contain">
                </div>

                <div class="mb-10">
                    <h2 class="text-2xl font-black text-indofood-blue tracking-tight">Selamat Datang</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Silakan masukkan kredensial akun  Anda.</p>
                </div>

                @if (session('status'))
                    <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-sm font-semibold text-green-700 flex items-center">
                        <i class="fas fa-check-circle mr-3"></i> {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-[11px] font-black uppercase tracking-widest text-indofood-blue mb-2">Alamat Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400 text-sm"></i>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="modern-input block w-full pl-11 pr-4 py-3.5 rounded-xl text-sm font-semibold text-gray-800 placeholder-gray-400"
                                placeholder="admin@indofood.com">
                        </div>
                        @error('email')
                            <p class="text-red-500 text-[11px] mt-2 font-semibold flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-[11px] font-black uppercase tracking-widest text-indofood-blue">Kata Sandi</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-[11px] font-black text-indofood-red hover:text-red-800 transition-colors">
                                    Lupa Sandi?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 text-sm"></i>
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="modern-input block w-full pl-11 pr-4 py-3.5 rounded-xl text-sm font-semibold text-gray-800 placeholder-gray-400"
                                placeholder="••••••••">
                        </div>
                        @error('password')
                            <p class="text-red-500 text-[11px] mt-2 font-semibold flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center mt-2">
                        <input id="remember_me" type="checkbox" class="w-4 h-4 text-indofood-blue bg-gray-100 border-gray-300 rounded focus:ring-indofood-blue focus:ring-2 cursor-pointer" name="remember">
                        <label for="remember_me" class="ml-2 text-xs font-semibold text-gray-600 cursor-pointer">
                            Ingat sesi saya di perangkat ini
                        </label>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-indofood-blue hover:bg-blue-800 text-white font-black py-4 rounded-xl transition-all uppercase text-xs tracking-[0.2em] shadow-lg shadow-blue-900/30 active:scale-[0.98] flex items-center justify-center gap-2">
                            Masuk Sistem <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>

                <div class="mt-6 flex items-center justify-center">
                    <div class="border-t border-gray-300 w-full"></div>
                    <span class="text-gray-400 text-[10px] font-black uppercase tracking-widest px-4">Atau</span>
                    <div class="border-t border-gray-300 w-full"></div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('auth.google') }}" class="w-full bg-white border border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-700 font-bold py-3.5 rounded-xl transition-all flex items-center justify-center gap-3 shadow-sm active:scale-[0.98]">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google Logo" class="w-5 h-5">
                        <span class="text-xs uppercase tracking-widest">Masuk dengan Google</span>
                    </a>
                </div>

                <div class="mt-auto pt-8">
                    <p class="text-[10px] font-bold text-gray-400 text-center md:text-left">
                        &copy; 2026 PT INDOFOOD CBP SUCCESS MAKMUR TBK.
                    </p>
                </div>

            </div>
        </div>
    </div>

</body>
</html>
