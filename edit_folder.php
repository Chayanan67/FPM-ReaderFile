<?php
session_start();
date_default_timezone_set('Asia/Bangkok');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$error = '';
$success = '';

// รับชื่อโฟลเดอร์เดิมจาก GET parameter
if (!isset($_GET['folder']) || trim($_GET['folder']) === '') {
    header("Location: index.php");
    exit;
}

$oldFolderName = basename($_GET['folder']);
$oldFolderPath = 'upload/' . $oldFolderName . '/';

// ตรวจสอบว่าโฟลเดอร์มีอยู่จริง
if (!is_dir($oldFolderPath)) {
    header("Location: index.php?error=folder_not_found");
    exit;
}

// ประมวลผลการแก้ไขชื่อโฟลเดอร์
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newFolderName = trim($_POST['new_folder_name']);
    
    // ตรวจสอบชื่อโฟลเดอร์ใหม่
    if (empty($newFolderName)) {
        $error = 'กรุณากรอกชื่อโฟลเดอร์ใหม่';
    } elseif ($newFolderName === $oldFolderName) {
        $error = 'ชื่อโฟลเดอร์ใหม่ต้องแตกต่างจากชื่อเดิม';
    } elseif (!preg_match('/^[a-zA-Z0-9ก-๙_\-\s]+$/', $newFolderName)) {
        $error = 'ชื่อโฟลเดอร์ประกอบด้วยตัวอักษร ตัวเลข ขีดเส้น และช่องว่างเท่านั้น';
    } else {
        $newFolderPath = 'upload/' . $newFolderName . '/';
        
        // ตรวจสอบว่าโฟลเดอร์ชื่อใหม่มีอยู่แล้วหรือไม่
        if (is_dir($newFolderPath)) {
            $error = 'มีโฟลเดอร์ชื่อนี้อยู่แล้ว';
        } else {
            // เปลี่ยนชื่อโฟลเดอร์
            if (rename($oldFolderPath, $newFolderPath)) {
                header("Location: index.php?success=folder_renamed&old_name=" . urlencode($oldFolderName) . "&new_name=" . urlencode($newFolderName));
                exit;
            } else {
                $error = 'ไม่สามารถเปลี่ยนชื่อโฟลเดอร์ได้ กรุณาลองใหม่';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขชื่อโฟลเดอร์ - <?= htmlspecialchars($oldFolderName) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">File Management System</a>
            <div class="navbar-nav ms-auto d-flex flex-row gap-2">
                <a href="index.php" class="btn btn-secondary-custom btn-custom">
                    <i class="fas fa-arrow-left"></i> กลับหน้าหลัก
                </a>
                <a href="logout.php" class="btn btn-outline-danger-custom btn-custom">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container main-container">
        <div class="content-card">
            <h2 class="page-title mb-4">
                <i class="fas fa-edit me-2"></i>
                แก้ไขชื่อโฟลเดอร์
            </h2>

            <?php if ($error): ?>
                <div class="alert alert-danger-custom alert-custom">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="card content-card">
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label for="old_folder_name" class="form-label-custom">ชื่อโฟลเดอร์เดิม:</label>
                            <input type="text" class="form-control form-control-custom" id="old_folder_name" value="<?= htmlspecialchars($oldFolderName) ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_folder_name" class="form-label-custom">ชื่อโฟลเดอร์ใหม่: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-custom" id="new_folder_name" name="new_folder_name" 
                                   value="<?= isset($_POST['new_folder_name']) ? htmlspecialchars($_POST['new_folder_name']) : htmlspecialchars($oldFolderName) ?>" 
                                   placeholder="กรอกชื่อโฟลเดอร์ใหม่" required maxlength="100">
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i>
                                ชื่อโฟลเดอร์สามารถใช้ตัวอักษรไทย-อังกฤษ ตัวเลข ขีดเส้น (-_) และช่องว่าง
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-md-row gap-2">
                            <button type="submit" class="btn btn-success-custom btn-custom">
                                <i class="fas fa-save"></i> บันทึกการเปลี่ยนแปลง
                            </button>
                            <a href="index.php" class="btn btn-secondary-custom btn-custom">
                                <i class="fas fa-times"></i> ยกเลิก
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4">
                <div class="alert alert-custom" style="background: linear-gradient(135deg, #e3f2fd, #bbdefb); color: #0d47a1; border-left: 4px solid #2196f3;">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>หมายเหตุ:</strong> การเปลี่ยนชื่อโฟลเดอร์จะไม่ส่งผลกระทบต่อไฟล์ที่อยู่ภายในโฟลเดอร์
                </div>
            </div>
        </div>
    </div>

    <script>
        // Focus ที่ input field เมื่อหน้าโหลดเสร็จ
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('new_folder_name').focus();
            document.getElementById('new_folder_name').select();
        });
    </script>
</body>
</html>