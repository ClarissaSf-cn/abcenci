<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sesi Berakhir | PT Indofood</title>
    <!-- Auto-redirect langsung via HTTP-EQUIV (Fallback tanpa JS) -->
    <meta http-equiv="refresh" content="2;url={{ route('login') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            background: linear-gradient(135deg, #0b192c 0%, #1e3a8a 50%, #0b192c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: #ffffff;
            cursor: pointer;
        }
        .card {
            background: rgba(255, 255, 255, 0.98);
            color: #1e293b;
            max-width: 440px;
            width: 100%;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);
            padding: 2.75rem 2rem 2.25rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-top: 6px solid #004a99;
            animation: cardFadeIn 0.4s ease-out;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .card:hover {
            transform: translateY(-2px);
        }
        @keyframes cardFadeIn {
            from { opacity: 0; transform: translateY(12px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .icon-circle {
            width: 76px;
            height: 76px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            background: #eff6ff;
            color: #004a99;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.25rem;
            border: 2px solid #bfdbfe;
            position: relative;
        }
        .icon-circle i {
            animation: rotateClock 2s linear infinite;
        }
        @keyframes rotateClock {
            from { transform: rotate(0deg); }
            to { transform: rotate(-360deg); }
        }
        .badge {
            display: inline-block;
            background: #fee2e2;
            color: #dc2626;
            font-weight: 800;
            font-size: 0.725rem;
            padding: 0.3rem 0.85rem;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 0.85rem;
        }
        h1 {
            font-size: 1.45rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.65rem;
            letter-spacing: -0.02em;
        }
        p {
            font-size: 0.875rem;
            color: #64748b;
            line-height: 1.55;
            margin-bottom: 1.75rem;
        }
        .progress-container {
            width: 100%;
            height: 6px;
            background: #e2e8f0;
            border-radius: 9999px;
            overflow: hidden;
            margin-bottom: 1rem;
            position: relative;
        }
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #004a99, #38bdf8);
            width: 100%;
            border-radius: 9999px;
            animation: progressCountdown 2s linear forwards;
        }
        @keyframes progressCountdown {
            from { width: 100%; }
            to { width: 0%; }
        }
        .redirect-status {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.825rem;
            font-weight: 600;
            color: #475569;
        }
        .redirect-status .seconds {
            color: #004a99;
            font-weight: 800;
        }
    </style>
</head>
<body onclick="redirectToLogin()">
    <div class="card" onclick="redirectToLogin()">
        <div class="icon-circle">
            <i class="fas fa-clock-rotate-left"></i>
        </div>
        <div class="badge">Sesi Berakhir &bull; 419</div>
        <h1>Sesi Halaman Berakhir</h1>
        <p>Sesi Anda telah habis. Anda akan dialihkan ke halaman login secara otomatis.</p>
        
        {{-- Progress bar timer --}}
        <div class="progress-container">
            <div class="progress-bar"></div>
        </div>

        <div class="redirect-status">
            <i class="fas fa-arrow-right-to-bracket text-xs text-blue-600"></i>
            <span>Mengalihkan ke Login dalam <b id="countdown" class="seconds">2</b> detik...</span>
        </div>
        <div style="font-size: 11px; color: #94a3b8; margin-top: 10px; font-weight: 600;">(Klik layar untuk langsung login)</div>
    </div>

    <script>
        const loginUrl = "{{ route('login') }}";
        let isRedirecting = false;

        function redirectToLogin() {
            if (isRedirecting) return;
            isRedirecting = true;
            window.location.replace(loginUrl);
            setTimeout(() => { window.location.href = loginUrl; }, 100);
        }

        let seconds = 2;
        const countdownEl = document.getElementById('countdown');

        const timer = setInterval(function() {
            seconds--;
            if (countdownEl && seconds >= 0) {
                countdownEl.innerText = seconds;
            }
            if (seconds <= 0) {
                clearInterval(timer);
                redirectToLogin();
            }
        }, 1000);

        document.addEventListener('keydown', redirectToLogin);
    </script>
</body>
</html>
