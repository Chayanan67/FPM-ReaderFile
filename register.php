<?php
session_start();
require 'db.php';

// ตั้งค่าเขตเวลาเป็นประเทศไทย
date_default_timezone_set('Asia/Bangkok');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = trim($_POST['role']);

    // Check username exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "ชื่อผู้ใช้นี้มีอยู่แล้ว กรุณาเลือกชื่อผู้ใช้อื่น";
    } else {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);
        if ($stmt->execute()) {
            $success = "เพิ่มผู้ใช้สำเร็จ!";
        } else {
            $error = "เกิดข้อผิดพลาดในการเพิ่มผู้ใช้";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User - File Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: #2196F3 !important;
            font-size: 1.5rem;
        }
        
        .main-container {
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        
        .welcome-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 2rem;
        }
        
        .welcome-title {
            color: #2196F3;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .user-info {
            color: #666;
            font-size: 1.1rem;
        }
        
        .user-role {
            display: inline-block;
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .content-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .page-title {
            color: #2196F3;
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }
        
        .btn-custom {
            border-radius: 25px;
            padding: 0.7rem 1.5rem;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
        }
        
        .btn-success-custom {
            background: linear-gradient(135deg, #4CAF50, #388E3C);
            color: white;
        }
        
        .btn-danger-custom {
            background: linear-gradient(135deg, #f44336, #d32f2f);
            color: white;
        }
        
        .btn-secondary-custom {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
        }
        
        .btn-outline-danger-custom {
            border: 2px solid #f44336;
            color: #f44336;
            background: transparent;
        }
        
        .btn-outline-danger-custom:hover {
            background: #f44336;
            color: white;
        }
        
        .form-control-custom {
            border: none;
            border-radius: 15px;
            padding: 1rem 1.5rem;
            background: #f8f9fa;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .form-control-custom:focus {
            background: white;
            border-color: #2196F3;
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
        }
        
        .form-label-custom {
            font-weight: 600;
            color: #2196F3;
            margin-bottom: 0.5rem;
        }
        
        .alert-custom {
            border: none;
            border-radius: 15px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .alert-success-custom {
            background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
            color: #2e7d32;
            border-left: 4px solid #4caf50;
        }
        
        .alert-danger-custom {
            background: linear-gradient(135deg, #ffebee, #ffcdd2);
            color: #c62828;
            border-left: 4px solid #f44336;
        }
        
        .form-container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        @media (max-width: 768px) {
            .main-container {
                margin-top: 1rem;
                padding: 0 1rem;
            }
            
            .welcome-card, .content-card {
                padding: 1.5rem;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .btn-custom {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-cloud-upload-alt me-2"></i>
                File Management System
            </a>
            
            <div class="navbar-nav ms-auto d-flex flex-row gap-2">
                <a href="index.php" class="btn btn-primary-custom btn-custom">
                    <i class="fas fa-home"></i>
                    หน้าหลัก
                </a>
                
                <a href="logout.php" class="btn btn-outline-danger-custom btn-custom">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container main-container" style="margin-top: 100px;">
        <!-- Welcome Section -->
        <div class="welcome-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="welcome-title">
                        <i class="fas fa-user-circle me-2"></i>
                        ยินดีต้อนรับ, <?php echo htmlspecialchars($user['username']); ?>!
                    </h2>
                    <p class="user-info">
                        สถานะ: <span class="user-role"><?php echo htmlspecialchars($user['role']); ?></span>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stat-card">
                        <div class="stat-number" style="font-size: 2.5rem; font-weight: 700; color: #2196F3; margin-bottom: 0.5rem;">
                            <i class="fas fa-user-plus fa-2x"></i>
                        </div>
                        <div class="stat-label" style="color: #666; font-weight: 600;">เพิ่มผู้ใช้ใหม่</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="page-title mb-0">
                    <i class="fas fa-user-plus me-2"></i>
                    เพิ่มผู้ใช้ใหม่
                </h1>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success-custom alert-custom">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger-custom alert-custom">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <form method="post" action="">
                    <div class="mb-4">
                        <label for="username" class="form-label form-label-custom">
                            <i class="fas fa-user me-2"></i>ชื่อผู้ใช้
                        </label>
                        <input type="text" 
                               class="form-control form-control-custom" 
                               id="username" 
                               name="username" 
                               placeholder="กรุณากรอกชื่อผู้ใช้" 
                               required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label form-label-custom">
                            <i class="fas fa-lock me-2"></i>รหัสผ่าน
                        </label>
                        <input type="password" 
                               class="form-control form-control-custom" 
                               id="password" 
                               name="password" 
                               placeholder="กรุณากรอกรหัสผ่าน" 
                               required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="role" class="form-label form-label-custom">
                            <i class="fas fa-user-tag me-2"></i>สิทธิ์การใช้งาน
                        </label>
                        <select class="form-control form-control-custom" id="role" name="role">
                            <option value="user">ผู้ใช้ทั่วไป (User)</option>
                            <option value="admin">ผู้ดูแลระบบ (Admin)</option>
                        </select>
                    </div>
                    
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="index.php" class="btn btn-secondary-custom btn-custom">
                            <i class="fas fa-arrow-left"></i>
                            ย้อนกลับ
                        </a>
                        <button type="submit" class="btn btn-success-custom btn-custom">
                            <i class="fas fa-user-plus"></i>
                            เพิ่มผู้ใช้
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>