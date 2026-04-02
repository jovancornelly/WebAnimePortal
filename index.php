<?php
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>AnimePortal - Anime & Manga Terbaik</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://i.pinimg.com/originals/7e/6b/c0/7e6bc0bf0a9d0b1d9e2a0a0b0a0b0a0b.jpg');
            background-size: cover;
            background-position: center;
            opacity: 0.15;
            z-index: 0;
            pointer-events: none;
        }

        .header {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            color: white;
            padding: clamp(10px, 2vw, 15px) 0;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 100;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 clamp(15px, 4vw, 20px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: clamp(10px, 2vw, 15px);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: clamp(5px, 2vw, 10px);
            font-size: clamp(18px, 5vw, 24px);
            font-weight: 700;
        }

        .logo span {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            gap: clamp(12px, 3vw, 25px);
            flex-wrap: wrap;
            justify-content: center;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            transition: 0.3s;
            font-weight: 500;
            font-size: clamp(12px, 3.5vw, 16px);
        }

        .nav-links a:hover {
            color: #764ba2;
        }

        .main-container {
            position: relative;
            z-index: 2;
            min-height: calc(100vh - 200px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: clamp(80px, 15vh, 120px) clamp(15px, 4vw, 20px) clamp(40px, 8vh, 60px);
        }

        .hero {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }

        .hero h1 {
            font-size: clamp(28px, 8vw, 48px);
            margin-bottom: 15px;
        }

        .hero p {
            font-size: clamp(14px, 4vw, 18px);
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        .button-group {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .btn-outline {
            border: 2px solid white;
            color: white;
        }

        .btn-outline:hover {
            background: white;
            color: #764ba2;
        }

        .feature-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1000px;
            margin-top: 60px;
        }

        .feature-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            transition: 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            background: rgba(255,255,255,0.2);
        }

        .feature-card i {
            font-size: 40px;
            margin-bottom: 15px;
            color: #ffc107;
        }

        .feature-card h3 {
            color: white;
            margin-bottom: 10px;
        }

        .feature-card p {
            color: rgba(255,255,255,0.8);
            font-size: 14px;
        }

        .footer {
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
            color: white;
            padding: clamp(20px, 5vw, 30px) 0 clamp(15px, 4vw, 20px);
            margin-top: auto;
            position: relative;
            z-index: 100;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 clamp(15px, 4vw, 20px);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: clamp(20px, 4vw, 30px);
        }

        .footer-section h3 {
            margin-bottom: clamp(10px, 2vw, 15px);
            font-size: clamp(14px, 4vw, 18px);
        }

        .footer-section p {
            color: #aaa;
            font-size: clamp(11px, 3vw, 14px);
            line-height: 1.6;
        }

        .footer-section .social-links {
            display: flex;
            gap: clamp(10px, 3vw, 15px);
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .footer-section .social-links a {
            color: white;
            font-size: clamp(16px, 4.5vw, 20px);
            transition: 0.3s;
        }

        .footer-section .social-links a:hover {
            color: #764ba2;
        }

        .footer-bottom {
            text-align: center;
            padding-top: clamp(15px, 3vw, 20px);
            margin-top: clamp(15px, 3vw, 20px);
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: clamp(10px, 2.5vw, 12px);
            color: #888;
        }

        @keyframes fall {
            0% { transform: translateY(-10vh) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
        }

        .sakura {
            position: fixed;
            top: -10vh;
            font-size: clamp(1rem, 4vw, 1.5rem);
            user-select: none;
            pointer-events: none;
            z-index: 1;
            animation: fall linear forwards;
        }

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                text-align: center;
            }
            .nav-links {
                justify-content: center;
            }
            .footer-container {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .footer-section .social-links {
                justify-content: center;
            }
            .button-group {
                gap: 15px;
            }
            .btn {
                padding: 10px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <div class="logo">
                🎌 <span>AnimePortal</span>
            </div>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="login.php">Login</a>
                <a href="register.php">Daftar</a>
                <a href="#">Community</a>
            </div>
        </div>
    </header>

    <div class="main-container">
        <div class="hero">
            <h1>Selamat Datang di AnimePortal 🎌</h1>
            <p>Tempat terbaik untuk menemukan anime dan manga favoritmu. Streaming anime dan baca manga kapan saja, di mana saja.</p>
            <div class="button-group" style="margin-top: 30px;">
                <a href="login.php" class="btn btn-primary">Login</a>
                <a href="register.php" class="btn btn-outline">Daftar Sekarang</a>
            </div>
        </div>

        <div class="feature-section">
            <div class="feature-card">
                <i class="fas fa-tv"></i>
                <h3>Streaming Anime</h3>
                <p>Tonton anime favoritmu dalam kualitas HD dengan subtitle Indonesia.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-book"></i>
                <h3>Baca Manga</h3>
                <p>Nikmati ribuan chapter manga terupdate dari berbagai genre.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-users"></i>
                <h3>Komunitas Aktif</h3>
                <p>Diskusikan anime dan manga favoritmu dengan sesama otaku.</p>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>🎌 AnimePortal</h3>
                <p>Portal anime dan manga terbaik untuk para otaku sejati.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <p>Trending Anime<br>Top Rating Manga<br>Coming Soon</p>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-discord"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2024 AnimePortal. All rights reserved. | Made with ❤️ for Otaku
        </div>
    </footer>

    <script>
        function createSakura() {
            const sakura = document.createElement('div');
            sakura.classList.add('sakura');
            sakura.innerHTML = ['🌸', '🌸', '🌸', '🌸', '🌸'][Math.floor(Math.random() * 5)];
            sakura.style.left = Math.random() * 100 + '%';
            sakura.style.fontSize = (Math.random() * 20 + 10) + 'px';
            sakura.style.animationDuration = (Math.random() * 5 + 5) + 's';
            sakura.style.animationDelay = Math.random() * 5 + 's';
            document.body.appendChild(sakura);
            setTimeout(() => sakura.remove(), 10000);
        }
        setInterval(createSakura, 300);
    </script>
</body>
</html>