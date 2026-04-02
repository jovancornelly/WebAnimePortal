<?php
session_start();

// ============ FILE DATA USER (PERMANEN) ============
$dataFile = __DIR__ . '/users.json';

function loadUsers() {
    global $dataFile;
    if (file_exists($dataFile)) {
        $content = file_get_contents($dataFile);
        return json_decode($content, true);
    }
    return [];
}

function saveUsers($users) {
    global $dataFile;
    file_put_contents($dataFile, json_encode($users, JSON_PRETTY_PRINT));
}

// Load data user dari file
if (!file_exists($dataFile)) {
    $defaultUsers = [
        'admin' => [
            'password' => 'admin123',
            'role' => 'admin',
            'email' => 'admin@animeportal.com',
            'registered_at' => date('Y-m-d H:i:s')
        ]
    ];
    saveUsers($defaultUsers);
}

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

// Proses Registrasi
if (isset($_POST['register'])) {
    $reg_username = trim($_POST['reg_username'] ?? '');
    $reg_email = trim($_POST['reg_email'] ?? '');
    $reg_password = $_POST['reg_password'] ?? '';
    $reg_confirm = $_POST['reg_confirm'] ?? '';
    
    $users = loadUsers();
    
    if (empty($reg_username) || empty($reg_password)) {
        $error = 'Username dan password harus diisi!';
    } elseif (strlen($reg_username) < 3) {
        $error = 'Username minimal 3 karakter!';
    } elseif (strlen($reg_password) < 3) {
        $error = 'Password minimal 3 karakter!';
    } elseif ($reg_password !== $reg_confirm) {
        $error = 'Konfirmasi password tidak cocok!';
    } elseif (isset($users[$reg_username])) {
        $error = 'Username sudah terdaftar! Silakan gunakan username lain.';
    } elseif (!empty($reg_email) && !filter_var($reg_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } else {
        $users[$reg_username] = [
            'password' => $reg_password,
            'role' => 'user',
            'email' => $reg_email,
            'registered_at' => date('Y-m-d H:i:s')
        ];
        saveUsers($users);
        $success = 'Registrasi berhasil! Silakan <a href="login.php">login</a>.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Daftar - AnimePortal</title>
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
            justify-content: center;
            align-items: center;
            padding: clamp(60px, 10vh, 100px) clamp(15px, 4vw, 20px) clamp(40px, 8vh, 60px);
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: clamp(15px, 4vw, 20px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: clamp(20px, 5vw, 40px);
            width: 100%;
            max-width: min(450px, 90%);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .top-gif {
            margin-bottom: clamp(15px, 3vw, 20px);
            display: flex;
            justify-content: center;
        }
        
        .top-gif img {
            max-width: min(180px, 40vw);
            width: 100%;
            height: auto;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: clamp(5px, 1.5vw, 10px);
            font-size: clamp(22px, 6vw, 28px);
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: clamp(20px, 4vw, 30px);
            font-size: clamp(12px, 3vw, 14px);
        }

        .input-group {
            margin-bottom: clamp(15px, 3vw, 20px);
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: clamp(13px, 3vw, 16px);
        }

        .input-group label .optional {
            font-weight: normal;
            font-size: clamp(10px, 2.5vw, 12px);
            color: #888;
        }

        .input-group input {
            width: 100%;
            padding: clamp(10px, 2.5vw, 12px) clamp(12px, 3vw, 15px);
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: clamp(13px, 3.5vw, 16px);
            transition: all 0.3s ease;
            background: white;
        }

        .input-group input:focus {
            outline: none;
            border-color: #764ba2;
            box-shadow: 0 0 0 3px rgba(118, 75, 162, 0.1);
        }

        button {
            width: 100%;
            padding: clamp(10px, 2.5vw, 12px);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: clamp(14px, 3.5vw, 16px);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .error-message {
            background: #fee;
            color: #c33;
            padding: clamp(8px, 2vw, 10px);
            border-radius: 10px;
            margin-bottom: clamp(15px, 3vw, 20px);
            text-align: center;
            font-size: clamp(12px, 3vw, 14px);
        }

        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: clamp(8px, 2vw, 10px);
            border-radius: 10px;
            margin-bottom: clamp(15px, 3vw, 20px);
            text-align: center;
            font-size: clamp(12px, 3vw, 14px);
        }

        .success-message a {
            color: #2e7d32;
            font-weight: 600;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: clamp(12px, 3vw, 14px);
            color: #666;
        }

        .login-link a {
            color: #764ba2;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
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
        }

        @media (max-width: 480px) {
            .card {
                padding: 20px;
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
                <a href="javascript:void(0)" onclick="alert('🔒 Silakan login terlebih dahulu')">Anime</a>
                <a href="javascript:void(0)" onclick="alert('🔒 Silakan login terlebih dahulu')">Manga</a>
                <a href="#">Community</a>
            </div>
        </div>
    </header>

    <div class="main-container">
        <div class="card">
            <div class="top-gif">
                <img src="https://media1.tenor.com/m/KfNO_aGGpgwAAAAd/genshin-impact-genshin-meme.gif" alt="Genshin Impact GIF">
            </div>
            
            <h2>Daftar Akun 📝</h2>
            <p class="subtitle">Buat akun baru untuk bergabung</p>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="reg_username" required placeholder="Minimal 3 karakter">
                </div>
                
                <div class="input-group">
                    <label>Email <span class="optional">(Opsional)</span></label>
                    <input type="email" name="reg_email" placeholder="contoh@email.com">
                </div>
                
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="reg_password" required placeholder="Minimal 3 karakter">
                </div>
                
                <div class="input-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="reg_confirm" required placeholder="Ulangi password">
                </div>
                
                <button type="submit" name="register">Daftar ✨</button>
            </form>
            
            <div class="login-link">
                Sudah punya akun? <a href="login.php">Login sekarang</a>
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