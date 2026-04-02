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

// Cek apakah sudah login
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Refresh data dari file ke session
$_SESSION['users'] = loadUsers();

// Proses Edit User (Hanya untuk admin)
if ($_SESSION['role'] == 'admin' && isset($_POST['edit_user'])) {
    $old_username = $_POST['old_username'] ?? '';
    $new_username = trim($_POST['new_username'] ?? '');
    $new_role = $_POST['new_role'] ?? 'user';
    $new_password = $_POST['new_password'] ?? '';
    
    $users = loadUsers();
    
    if (isset($users[$old_username]) && !empty($new_username)) {
        $user_data = $users[$old_username];
        
        if ($old_username != $new_username && !isset($users[$new_username])) {
            $users[$new_username] = $user_data;
            unset($users[$old_username]);
            if ($_SESSION['username'] == $old_username) {
                $_SESSION['username'] = $new_username;
            }
        } elseif ($old_username != $new_username && isset($users[$new_username])) {
            $_SESSION['edit_error'] = 'Username sudah digunakan!';
            header('Location: dashboard.php?page=users');
            exit();
        }
        
        $users[$new_username]['role'] = $new_role;
        if (!empty($new_password) && strlen($new_password) >= 3) {
            $users[$new_username]['password'] = $new_password;
        }
        
        saveUsers($users);
        $_SESSION['users'] = $users;
        $_SESSION['edit_success'] = 'User berhasil diupdate!';
    }
    header('Location: dashboard.php?page=users');
    exit();
}

// Proses Hapus User
if ($_SESSION['role'] == 'admin' && isset($_GET['delete_user'])) {
    $delete_username = $_GET['delete_user'];
    $users = loadUsers();
    
    if ($delete_username != $_SESSION['username'] && isset($users[$delete_username])) {
        unset($users[$delete_username]);
        saveUsers($users);
        $_SESSION['users'] = $users;
        $_SESSION['delete_success'] = 'User berhasil dihapus!';
    } else {
        $_SESSION['delete_error'] = 'Tidak dapat menghapus user sendiri!';
    }
    header('Location: dashboard.php?page=users');
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'] ?? 'user';
$login_time = date('H:i:s', $_SESSION['login_time']);

// Hitung total users terdaftar dari file
$all_users = loadUsers();
$total_users = count($all_users);

// Data Anime
$anime_list = [
    [
        'title' => 'Naruto Shippuden',
        'genre' => 'Action, Adventure',
        'rating' => 8.5,
        'episodes' => 500,
        'status' => 'Completed',
        'image' => 'https://cdn.myanimelist.net/images/anime/1506/117431.jpg'
    ],
    [
        'title' => 'Attack on Titan',
        'genre' => 'Action, Dark Fantasy',
        'rating' => 9.0,
        'episodes' => 87,
        'status' => 'Completed',
        'image' => 'https://cdn.myanimelist.net/images/anime/10/47347.jpg'
    ],
    [
        'title' => 'Demon Slayer',
        'genre' => 'Action, Supernatural',
        'rating' => 8.8,
        'episodes' => 44,
        'status' => 'Ongoing',
        'image' => 'https://cdn.myanimelist.net/images/anime/1286/99889.jpg'
    ],
    [
        'title' => 'One Piece',
        'genre' => 'Action, Adventure',
        'rating' => 8.9,
        'episodes' => 1000,
        'status' => 'Ongoing',
        'image' => 'https://cdn.myanimelist.net/images/anime/6/73245.jpg'
    ],
    [
        'title' => 'Jujutsu Kaisen',
        'genre' => 'Action, Supernatural',
        'rating' => 8.7,
        'episodes' => 47,
        'status' => 'Ongoing',
        'image' => 'https://cdn.myanimelist.net/images/anime/1171/109222.jpg'
    ],
    [
        'title' => 'My Hero Academia',
        'genre' => 'Action, Superhero',
        'rating' => 8.3,
        'episodes' => 138,
        'status' => 'Ongoing',
        'image' => 'https://cdn.myanimelist.net/images/anime/10/78745.jpg'
    ]
];

// Data Manga
$manga_list = [
    [
        'title' => 'One Piece',
        'genre' => 'Action, Adventure',
        'rating' => 9.1,
        'chapters' => 1080,
        'status' => 'Ongoing',
        'image' => 'https://cdn.myanimelist.net/images/manga/2/253146.jpg'
    ],
    [
        'title' => 'Berserk',
        'genre' => 'Action, Dark Fantasy',
        'rating' => 9.2,
        'chapters' => 370,
        'status' => 'Ongoing',
        'image' => 'https://cdn.myanimelist.net/images/manga/1/157897.jpg'
    ],
    [
        'title' => 'Vagabond',
        'genre' => 'Action, Historical',
        'rating' => 9.1,
        'chapters' => 327,
        'status' => 'Hiatus',
        'image' => 'https://cdn.myanimelist.net/images/manga/1/181234.jpg'
    ],
    [
        'title' => 'Attack on Titan',
        'genre' => 'Action, Dark Fantasy',
        'rating' => 8.9,
        'chapters' => 139,
        'status' => 'Completed',
        'image' => 'https://cdn.myanimelist.net/images/manga/2/169917.jpg'
    ],
    [
        'title' => 'Jujutsu Kaisen',
        'genre' => 'Action, Supernatural',
        'rating' => 8.8,
        'chapters' => 240,
        'status' => 'Ongoing',
        'image' => 'https://cdn.myanimelist.net/images/manga/3/210112.jpg'
    ],
    [
        'title' => 'Demon Slayer',
        'genre' => 'Action, Supernatural',
        'rating' => 8.7,
        'chapters' => 205,
        'status' => 'Completed',
        'image' => 'https://cdn.myanimelist.net/images/manga/3/179884.jpg'
    ]
];

$admin_stats = [
    'total_visitors' => 1523,
    'total_anime' => count($anime_list),
    'total_manga' => count($manga_list),
    'total_users' => $total_users,
    'pending_reviews' => 12
];

$edit_success = $_SESSION['edit_success'] ?? '';
$edit_error = $_SESSION['edit_error'] ?? '';
$delete_success = $_SESSION['delete_success'] ?? '';
$delete_error = $_SESSION['delete_error'] ?? '';
unset($_SESSION['edit_success'], $_SESSION['edit_error'], $_SESSION['delete_success'], $_SESSION['delete_error']);
$active_tab = 'anime';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anime Dashboard - Portal Otaku</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow-x: hidden;
            min-height: 100vh;
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

        /* HEADER */
        .header {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            color: white;
            padding: 15px 0;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 100;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
        }

        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 24px;
            font-weight: 700;
        }

        .logo span {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-menu a {
            color: #ff4757;
            text-decoration: none;
        }

        .role-badge {
            background: rgba(118, 75, 162, 0.3);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .role-badge.admin {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
        }

        .role-badge.user {
            background: rgba(102, 126, 234, 0.3);
            color: #667eea;
        }

        /* LAYOUT WRAPPER */
        .app-wrapper {
            display: flex;
            flex: 1;
            padding-top: 70px;
        }

        /* SIDEBAR */
        .sidebar {
            width: 280px;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255,255,255,0.1);
            display: flex;
            flex-direction: column;
            height: auto;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            padding: 12px 25px;
            margin: 5px 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: #ccc;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            cursor: pointer;
        }

        .menu-item i {
            width: 24px;
            font-size: 18px;
        }

        .menu-item:hover {
            background: rgba(118, 75, 162, 0.3);
            color: white;
            transform: translateX(5px);
        }

        .menu-item.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .menu-divider {
            height: 1px;
            background: rgba(255,255,255,0.1);
            margin: 15px 20px;
        }

        .menu-title {
            padding: 10px 25px;
            color: #888;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* SIDEBAR FOOTER (MENYATU DENGAN SIDEBAR) */
        .sidebar-footer {
            padding: 20px 25px;
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: auto;
        }

        .sidebar-footer .social-links {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 15px;
        }

        .sidebar-footer .social-links a {
            color: #888;
            font-size: 18px;
            transition: 0.3s;
        }

        .sidebar-footer .social-links a:hover {
            color: #764ba2;
        }

        .sidebar-footer p {
            color: #666;
            font-size: 11px;
            text-align: center;
            line-height: 1.5;
        }

        .sidebar-footer .copyright {
            margin-top: 10px;
            color: #555;
            font-size: 10px;
        }

        /* MAIN CONTENT */
        .main-content {
            flex: 1;
            padding: 20px 30px;
            position: relative;
            z-index: 2;
        }

        /* ANIMATION SAKURA */
        @keyframes fall {
            0% { transform: translateY(-10vh) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
        }

        .sakura {
            position: fixed;
            top: -10vh;
            font-size: 1.5rem;
            user-select: none;
            pointer-events: none;
            z-index: 1;
            animation: fall linear forwards;
        }

        /* DASHBOARD CONTAINER */
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 20px;
            margin-bottom: 30px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
            flex-wrap: wrap;
            gap: 15px;
        }

        .dashboard-header h1 {
            color: #333;
            font-size: 24px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .user-avatar {
            font-size: 2rem;
        }

        .logout-btn {
            padding: 8px 20px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            text-decoration: none;
            color: white;
            border-radius: 10px;
            transition: all 0.3s ease;
            display: inline-block;
            border: none;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
        }

        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .welcome-section h2 {
            color: white;
            margin-bottom: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #764ba2;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }

        /* Section Tabs */
        .section-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .section-tab {
            padding: 10px 20px;
            background: #e0e0e0;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .section-tab.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        /* Card Grid */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .content-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .content-card:hover {
            transform: translateY(-5px);
        }

        .card-image {
            height: 180px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .card-rating {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.7);
            color: #ffc107;
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 12px;
        }

        .card-status {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 10px;
        }

        .card-info {
            padding: 15px;
        }

        .card-info h3 {
            color: #333;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .card-info p {
            color: #666;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .card-detail {
            display: flex;
            justify-content: space-between;
            color: #888;
            font-size: 12px;
        }

        /* Profile Card */
        .profile-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .profile-avatar {
            font-size: 80px;
            margin-bottom: 15px;
        }

        .profile-name {
            font-size: 22px;
            font-weight: 600;
            color: #333;
        }

        .profile-info {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .profile-info p {
            margin: 8px 0;
            color: #666;
        }

        .admin-badge {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal.active {
            display: flex;
        }
        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
        }
        .modal-content input, .modal-content select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 2px solid #ddd;
            border-radius: 10px;
        }
        .modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .modal-buttons button {
            flex: 1;
            padding: 10px;
        }
        .btn-cancel {
            background: #ddd;
            color: #333;
        }
        .btn-save {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .alert-error {
            background: #fee;
            color: #c33;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .app-wrapper {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid rgba(255,255,255,0.1);
            }
            .sidebar-footer {
                display: none;
            }
            .main-content {
                padding: 20px;
            }
            .dashboard-header {
                flex-direction: column;
                text-align: center;
            }
            .header-container {
                flex-direction: column;
                text-align: center;
            }
            .section-tabs {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                🎌 <span>AnimePortal</span>
            </div>
            <div class="user-menu">
                <span>👤 <?php echo htmlspecialchars($username); ?></span>
                <?php if ($role == 'admin'): ?>
                    <span class="role-badge admin"><i class="fas fa-crown"></i> Admin</span>
                <?php else: ?>
                    <span class="role-badge user"><i class="fas fa-user"></i> User</span>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>

    <!-- APP WRAPPER -->
    <div class="app-wrapper">
        <!-- SIDEBAR KIRI -->
        <div class="sidebar">
            <div class="sidebar-menu">
                <div class="menu-title">MAIN MENU</div>
                <a href="#" class="menu-item active" onclick="showPage('dashboard', this)">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="menu-item" onclick="showPage('anime', this)">
                    <i class="fas fa-tv"></i>
                    <span>Anime</span>
                </a>
                <a href="#" class="menu-item" onclick="showPage('manga', this)">
                    <i class="fas fa-book"></i>
                    <span>Manga</span>
                </a>
                <a href="#" class="menu-item" onclick="showPage('akun', this)">
                    <i class="fas fa-user-circle"></i>
                    <span>Akun Saya</span>
                </a>
                
                <div class="menu-divider"></div>
                
                <div class="menu-title">ANIME</div>
                <a href="#" class="menu-item" onclick="showPage('ongoing', this)">
                    <i class="fas fa-play-circle"></i>
                    <span>Ongoing</span>
                </a>
                <a href="#" class="menu-item" onclick="showPage('completed', this)">
                    <i class="fas fa-check-circle"></i>
                    <span>Completed</span>
                </a>
                
                <?php if ($role == 'admin'): ?>
                <div class="menu-divider"></div>
                <div class="menu-title">ADMIN PANEL</div>
                <a href="#" class="menu-item" onclick="showPage('admin', this)">
                    <i class="fas fa-chart-line"></i>
                    <span>Statistik</span>
                </a>
                <a href="#" class="menu-item" onclick="showPage('users', this)">
                    <i class="fas fa-users"></i>
                    <span>Kelola User</span>
                </a>
                <?php endif; ?>
                
                <div class="menu-divider"></div>
                
                <div class="menu-title">LAINNYA</div>
                <a href="#" class="menu-item" onclick="showPage('about', this)">
                    <i class="fas fa-info-circle"></i>
                    <span>Tentang</span>
                </a>
                <a href="logout.php" class="menu-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
            
            <!-- FOOTER DI DALAM SIDEBAR (MENYATU) -->
            <div class="sidebar-footer">
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-discord"></i></a>
                </div>
                <p>AnimePortal - Portal Otaku Terbaik</p>
                <p class="copyright">© 2024 AnimePortal<br>Made with ❤️ for Otaku</p>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <!-- Halaman Dashboard -->
            <div id="dashboard-page" class="dashboard-container">
                <div class="dashboard-header">
                    <h1>Anime Dashboard</h1>
                    <div class="user-info">
                        <div class="user-avatar">👤</div>
                        <span>Welcome, <strong><?php echo htmlspecialchars($username); ?></strong>!</span>
                        <?php if ($role == 'admin'): ?>
                            <span class="admin-badge"><i class="fas fa-crown"></i> Administrator</span>
                        <?php endif; ?>
                        <a href="logout.php" class="logout-btn">Logout 🚪</a>
                    </div>
                </div>
                
                <div class="welcome-section">
                    <h2>Selamat datang di Portal Anime, <?php echo htmlspecialchars($username); ?>! <?php echo $role == 'admin' ? '👑' : '🎌'; ?></h2>
                    <p>Login time: <?php echo $login_time; ?> | Anda login sebagai <strong><?php echo $role == 'admin' ? 'Administrator' : 'User Biasa'; ?></strong></p>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">📺</div>
                        <div class="stat-number"><?php echo count($anime_list); ?></div>
                        <div class="stat-label">Total Anime</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">📖</div>
                        <div class="stat-number"><?php echo count($manga_list); ?></div>
                        <div class="stat-label">Total Manga</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">👥</div>
                        <div class="stat-number"><?php echo $total_users; ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">⭐</div>
                        <div class="stat-number">4.8</div>
                        <div class="stat-label">Average Rating</div>
                    </div>
                </div>
            </div>

            <!-- Halaman Anime -->
            <div id="anime-page" class="dashboard-container" style="display: none;">
                <div class="dashboard-header">
                    <h1><i class="fas fa-tv"></i> Daftar Anime</h1>
                    <div class="user-info">
                        <span>Total: <?php echo count($anime_list); ?> Anime</span>
                    </div>
                </div>
                <div class="card-grid">
                    <?php foreach ($anime_list as $anime): ?>
                    <div class="content-card" onclick="alert('🎬 Menonton: <?php echo $anime['title']; ?>\nFitur sedang dalam pengembangan!')">
                        <div class="card-image" style="background-image: url('<?php echo $anime['image']; ?>');">
                            <div class="card-rating">⭐ <?php echo $anime['rating']; ?></div>
                            <div class="card-status"><?php echo $anime['status']; ?></div>
                        </div>
                        <div class="card-info">
                            <h3><?php echo htmlspecialchars($anime['title']); ?></h3>
                            <p><?php echo htmlspecialchars($anime['genre']); ?></p>
                            <div class="card-detail">
                                <span><i class="fas fa-video"></i> <?php echo $anime['episodes']; ?> Episode</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Halaman Manga -->
            <div id="manga-page" class="dashboard-container" style="display: none;">
                <div class="dashboard-header">
                    <h1><i class="fas fa-book"></i> Daftar Manga</h1>
                    <div class="user-info">
                        <span>Total: <?php echo count($manga_list); ?> Manga</span>
                    </div>
                </div>
                <div class="card-grid">
                    <?php foreach ($manga_list as $manga): ?>
                    <div class="content-card" onclick="alert('📖 Membaca: <?php echo $manga['title']; ?>\nFitur sedang dalam pengembangan!')">
                        <div class="card-image" style="background-image: url('<?php echo $manga['image']; ?>'); background-size: cover;">
                            <div class="card-rating">⭐ <?php echo $manga['rating']; ?></div>
                            <div class="card-status"><?php echo $manga['status']; ?></div>
                        </div>
                        <div class="card-info">
                            <h3><?php echo htmlspecialchars($manga['title']); ?></h3>
                            <p><?php echo htmlspecialchars($manga['genre']); ?></p>
                            <div class="card-detail">
                                <span><i class="fas fa-book"></i> <?php echo $manga['chapters']; ?> Chapter</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Halaman Akun Saya -->
            <div id="akun-page" class="dashboard-container" style="display: none;">
                <div class="dashboard-header">
                    <h1><i class="fas fa-user-circle"></i> Akun Saya</h1>
                    <div class="user-info">
                        <div class="user-avatar">👤</div>
                        <span><strong><?php echo htmlspecialchars($username); ?></strong></span>
                    </div>
                </div>
                
                <div class="profile-card">
                    <div class="profile-avatar">
                        <?php
                        $avatar = ['🐱', '🐶', '🐼', '🦊', '🐸', '🐙', '🦄', '🐧'];
                        echo $avatar[array_rand($avatar)];
                        ?>
                    </div>
                    <div class="profile-name"><?php echo htmlspecialchars($username); ?></div>
                    <div class="profile-info">
                        <p><i class="fas fa-tag"></i> Role / Level: 
                            <?php if ($role == 'admin'): ?>
                                <strong style="color: #f5576c;">👑 Administrator</strong>
                            <?php else: ?>
                                <strong style="color: #667eea;">👤 User Biasa</strong>
                            <?php endif; ?>
                        </p>
                        <p><i class="fas fa-calendar-alt"></i> Bergabung: <?php echo date('d F Y'); ?></p>
                        <p><i class="fas fa-clock"></i> Login Terakhir: <?php echo $login_time; ?></p>
                        <p><i class="fas fa-envelope"></i> Email: <?php echo htmlspecialchars($username); ?>@animeportal.com</p>
                    </div>
                </div>
            </div>

            <!-- Halaman Ongoing -->
            <div id="ongoing-page" class="dashboard-container" style="display: none;">
                <div class="dashboard-header">
                    <h1><i class="fas fa-play-circle"></i> Ongoing Anime</h1>
                </div>
                <div class="card-grid">
                    <?php 
                    $ongoing_anime = array_filter($anime_list, function($a) { return $a['status'] == 'Ongoing'; });
                    foreach ($ongoing_anime as $anime): ?>
                    <div class="content-card" onclick="alert('🎬 Menonton: <?php echo $anime['title']; ?>')">
                        <div class="card-image" style="background-image: url('<?php echo $anime['image']; ?>');">
                            <div class="card-rating">⭐ <?php echo $anime['rating']; ?></div>
                            <div class="card-status"><?php echo $anime['status']; ?></div>
                        </div>
                        <div class="card-info">
                            <h3><?php echo htmlspecialchars($anime['title']); ?></h3>
                            <p><?php echo htmlspecialchars($anime['genre']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Halaman Completed -->
            <div id="completed-page" class="dashboard-container" style="display: none;">
                <div class="dashboard-header">
                    <h1><i class="fas fa-check-circle"></i> Completed Anime</h1>
                </div>
                <div class="card-grid">
                    <?php 
                    $completed_anime = array_filter($anime_list, function($a) { return $a['status'] == 'Completed'; });
                    foreach ($completed_anime as $anime): ?>
                    <div class="content-card" onclick="alert('🎬 Menonton: <?php echo $anime['title']; ?>')">
                        <div class="card-image" style="background-image: url('<?php echo $anime['image']; ?>');">
                            <div class="card-rating">⭐ <?php echo $anime['rating']; ?></div>
                            <div class="card-status"><?php echo $anime['status']; ?></div>
                        </div>
                        <div class="card-info">
                            <h3><?php echo htmlspecialchars($anime['title']); ?></h3>
                            <p><?php echo htmlspecialchars($anime['genre']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if ($role == 'admin'): ?>
            <!-- Halaman Admin Statistik -->
            <div id="admin-page" class="dashboard-container" style="display: none;">
                <div class="dashboard-header">
                    <h1><i class="fas fa-chart-line"></i> Admin Dashboard</h1>
                </div>
                <div class="welcome-section" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                    <h2><i class="fas fa-shield-alt"></i> Panel Administrator</h2>
                    <p>Selamat datang, <?php echo htmlspecialchars($username); ?>! Anda memiliki akses penuh ke data statistik website.</p>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">👥</div>
                        <div class="stat-number"><?php echo $admin_stats['total_visitors']; ?></div>
                        <div class="stat-label">Total Visitors</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">📺</div>
                        <div class="stat-number"><?php echo $admin_stats['total_anime']; ?></div>
                        <div class="stat-label">Total Anime</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">📖</div>
                        <div class="stat-number"><?php echo $admin_stats['total_manga']; ?></div>
                        <div class="stat-label">Total Manga</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">👤</div>
                        <div class="stat-number"><?php echo $admin_stats['total_users']; ?></div>
                        <div class="stat-label">Registered Users</div>
                    </div>
                </div>
            </div>

            <!-- Halaman Kelola User -->
            <div id="users-page" class="dashboard-container" style="display: none;">
                <div class="dashboard-header">
                    <h1><i class="fas fa-users"></i> Kelola User</h1>
                    <div class="user-info">
                        <span>Total: <?php echo $total_users; ?> User (Tersimpan Permanen)</span>
                    </div>
                </div>
                
                <?php if ($edit_success): ?>
                    <div class="alert-success"><?php echo $edit_success; ?></div>
                <?php endif; ?>
                <?php if ($edit_error): ?>
                    <div class="alert-error"><?php echo $edit_error; ?></div>
                <?php endif; ?>
                <?php if ($delete_success): ?>
                    <div class="alert-success"><?php echo $delete_success; ?></div>
                <?php endif; ?>
                <?php if ($delete_error): ?>
                    <div class="alert-error"><?php echo $delete_error; ?></div>
                <?php endif; ?>
                
                <div class="profile-card">
                    <h3>📋 Daftar User Terdaftar</h3>
                    <div style="margin-top: 20px; overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align: left; padding: 10px; border-bottom: 1px solid #eee;">No</th>
                                    <th style="text-align: left; padding: 10px; border-bottom: 1px solid #eee;">Username</th>
                                    <th style="text-align: left; padding: 10px; border-bottom: 1px solid #eee;">Role</th>
                                    <th style="text-align: left; padding: 10px; border-bottom: 1px solid #eee;">Bergabung</th>
                                    <th style="text-align: left; padding: 10px; border-bottom: 1px solid #eee;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                foreach ($all_users as $user => $data): 
                                ?>
                                <tr>
                                    <td style="padding: 10px; border-bottom: 1px solid #f0f0f0;"><?php echo $no++; ?></td>
                                    <td style="padding: 10px; border-bottom: 1px solid #f0f0f0;">
                                        <?php echo htmlspecialchars($user); ?>
                                        <?php if ($user == $username): ?> <span style="color: #764ba2;">(Anda)</span><?php endif; ?>
                                    </td>
                                    <td style="padding: 10px; border-bottom: 1px solid #f0f0f0;">
                                        <?php if ($data['role'] == 'admin'): ?>
                                            <span style="background: linear-gradient(135deg, #f093fb, #f5576c); color: white; padding: 2px 8px; border-radius: 12px;">👑 Admin</span>
                                        <?php else: ?>
                                            <span style="background: #667eea; color: white; padding: 2px 8px; border-radius: 12px;">👤 User</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 10px; border-bottom: 1px solid #f0f0f0;"><?php echo $data['registered_at']; ?></td>
                                    <td style="padding: 10px; border-bottom: 1px solid #f0f0f0;">
                                        <button class="btn-edit" style="padding: 4px 12px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer;" 
                                                onclick="openEditModal('<?php echo htmlspecialchars($user); ?>', '<?php echo $data['role']; ?>')">✏️ Edit</button>
                                        <?php if ($user != $username): ?>
                                            <button class="btn-delete-user" style="padding: 4px 12px; background: #f5576c; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 5px;" 
                                                    onclick="confirmDelete('<?php echo htmlspecialchars($user); ?>')">🗑️ Hapus</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Halaman Tentang -->
            <div id="about-page" class="dashboard-container" style="display: none;">
                <div class="dashboard-header">
                    <h1><i class="fas fa-info-circle"></i> Tentang AnimePortal</h1>
                </div>
                <div class="welcome-section" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                    <h2>📖 Tentang Kami</h2>
                    <p>AnimePortal adalah platform streaming anime dan manga terbaik untuk para otaku sejati.</p>
                </div>
                <div class="profile-card">
                    <h3>✨ Fitur Unggulan</h3>
                    <ul style="text-align: left; margin-top: 15px; list-style: none;">
                        <li>✅ <strong>Streaming HD</strong> - Kualitas terbaik</li>
                        <li>✅ <strong>Subtitle Indonesia</strong> - Mudah dipahami</li>
                        <li>✅ <strong>Update Cepat</strong> - Episode terbaru setiap hari</li>
                        <li>✅ <strong>Komunitas Aktif</strong> - Diskusi dengan sesama otaku</li>
                        <li>✅ <strong>Rekomendasi Personal</strong> - Sesuai seleramu</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3><i class="fas fa-user-edit"></i> Edit User</h3>
            <form method="POST" action="">
                <input type="hidden" name="old_username" id="old_username">
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="new_username" id="new_username" required>
                </div>
                <div class="input-group">
                    <label>Role</label>
                    <select name="new_role" id="new_role">
                        <option value="user">👤 User Biasa</option>
                        <option value="admin">👑 Admin</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Password Baru (kosongkan jika tidak diubah)</label>
                    <input type="password" name="new_password" id="new_password" placeholder="Minimal 3 karakter">
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                    <button type="submit" name="edit_user" class="btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function createSakura() {
            const sakura = document.createElement('div');
            sakura.classList.add('sakura');
            const flowers = ['🌸', '🌸', '🌸', '🌸', '🌸'];
            sakura.innerHTML = flowers[Math.floor(Math.random() * flowers.length)];
            sakura.style.left = Math.random() * 100 + '%';
            const size = Math.random() * 20 + 10;
            sakura.style.fontSize = size + 'px';
            const duration = Math.random() * 5 + 5;
            sakura.style.animationDuration = duration + 's';
            sakura.style.animationDelay = Math.random() * 5 + 's';
            document.body.appendChild(sakura);
            setTimeout(function() {
                if(sakura && sakura.remove) sakura.remove();
            }, 10000);
        }
        
        setInterval(createSakura, 500);
        
        function showPage(page, element) {
            const pages = ['dashboard-page', 'anime-page', 'manga-page', 'akun-page', 'ongoing-page', 'completed-page', 'admin-page', 'users-page', 'about-page'];
            pages.forEach(function(p) {
                const pageEl = document.getElementById(p);
                if(pageEl) pageEl.style.display = 'none';
            });
            const activePage = document.getElementById(page + '-page');
            if(activePage) activePage.style.display = 'block';
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(function(item) {
                item.classList.remove('active');
            });
            if(element) element.classList.add('active');
        }
        
        function openEditModal(username, role) {
            document.getElementById('old_username').value = username;
            document.getElementById('new_username').value = username;
            document.getElementById('new_role').value = role;
            document.getElementById('new_password').value = '';
            document.getElementById('editModal').classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('editModal').classList.remove('active');
        }
        
        function confirmDelete(username) {
            if(confirm('Apakah Anda yakin ingin menghapus user "' + username + '" secara permanen?')) {
                window.location.href = 'dashboard.php?delete_user=' + encodeURIComponent(username);
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const page = urlParams.get('page');
            if(page === 'users' && document.getElementById('users-page')) {
                showPage('users', document.querySelector('.menu-item[onclick*="users"]'));
            }
        });
        
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) modal.classList.remove('active');
        }
    </script>
</body>
</html>
<style>
    /* ============ RESPONSIVE IMPROVEMENTS ============ */

/* Menggunakan clamp untuk ukuran font dan padding */
.header-container {
    padding: 0 clamp(15px, 4vw, 20px);
}

.logo {
    font-size: clamp(18px, 5vw, 24px);
}

.user-menu {
    gap: clamp(8px, 2vw, 15px);
}

.user-menu span, .user-menu a {
    font-size: clamp(12px, 3.5vw, 14px);
}

.sidebar {
    width: clamp(240px, 25vw, 280px);
}

.menu-item {
    padding: clamp(8px, 2vw, 12px) clamp(15px, 3vw, 25px);
    margin: clamp(3px, 1vw, 5px) clamp(10px, 2vw, 15px);
    font-size: clamp(13px, 3vw, 14px);
}

.main-content {
    padding: clamp(15px, 3vw, 20px) clamp(15px, 4vw, 30px);
}

.dashboard-container {
    padding: clamp(15px, 3vw, 20px);
}

.dashboard-header h1 {
    font-size: clamp(18px, 5vw, 24px);
}

.stat-number {
    font-size: clamp(1.5rem, 5vw, 2rem);
}

.stat-label {
    font-size: clamp(11px, 3vw, 14px);
}

.card-info h3 {
    font-size: clamp(14px, 3.5vw, 16px);
}

.card-info p {
    font-size: clamp(11px, 3vw, 12px);
}

.card-detail span {
    font-size: clamp(10px, 2.5vw, 12px);
}

/* Tablet (max-width: 992px) */
@media (max-width: 992px) {
    .sidebar {
        width: 260px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
    }
    
    .card-grid {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 15px;
    }
}

/* Mobile (max-width: 768px) */
@media (max-width: 768px) {
    .app-wrapper {
        flex-direction: column;
    }
    
    .sidebar {
        width: 100%;
        border-right: none;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        position: relative;
        top: 0;
    }
    
    .sidebar-footer {
        display: none;
    }
    
    .main-content {
        padding: 20px;
    }
    
    .dashboard-header {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
    
    .user-info {
        justify-content: center;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .card-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .section-tabs {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .section-tab {
        padding: 8px 16px;
        font-size: 13px;
    }
    
    .profile-card {
        padding: 20px;
    }
    
    .profile-avatar {
        font-size: 60px;
    }
    
    .profile-name {
        font-size: 18px;
    }
    
    table, thead, tbody, th, td, tr {
        display: block;
    }
    
    thead tr {
        position: absolute;
        top: -9999px;
        left: -9999px;
    }
    
    tr {
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 10px;
    }
    
    td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px !important;
        border-bottom: 1px solid #f0f0f0;
    }
    
    td:before {
        content: attr(data-label);
        font-weight: bold;
        width: 40%;
    }
}

/* Mobile kecil (max-width: 480px) */
@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .stat-card {
        padding: 15px;
    }
    
    .welcome-section {
        padding: 20px;
    }
    
    .welcome-section h2 {
        font-size: 18px;
    }
    
    .welcome-section p {
        font-size: 12px;
    }
    
    .modal-content {
        padding: 20px;
        width: 95%;
    }
    
    .modal-content h3 {
        font-size: 18px;
    }
    
    .modal-buttons button {
        padding: 8px;
        font-size: 13px;
    }
}

/* Laptop (min-width: 1200px) */
@media (min-width: 1200px) {
    .header-container {
        padding: 0 40px;
    }
    
    .main-content {
        padding: 20px 40px;
    }
    
    .dashboard-container {
        padding: 30px;
    }
    
    .card-grid {
        gap: 25px;
    }
}