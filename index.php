<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$user = $_SESSION['user'];
date_default_timezone_set('Asia/Bangkok');

// อ่านโฟลเดอร์ทั้งหมดใน upload/
$uploadDir = 'upload/';
$folders = [];
if (is_dir($uploadDir)) {
    foreach (scandir($uploadDir) as $folder) {
        if ($folder != '.' && $folder != '..' && is_dir($uploadDir . $folder)) {
            $folders[] = $folder;
        }
    }
    // เรียงใหม่ล่าสุดอยู่บน
    rsort($folders, SORT_NATURAL | SORT_FLAG_CASE);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - File Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">File Management System</a>
            <div class="navbar-nav ms-auto d-flex flex-row gap-2">
                <?php if ($user['role'] === 'admin'): ?>
                    <a href="register.php" class="btn btn-success-custom btn-custom">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                    <a href="account.php" class="btn btn-primary-custom btn-custom">
                        <i class="fas fa-users-cog"></i> จัดการบัญชีผู้ใช้
                    </a>
                    <a href="create_folder.php" class="btn btn-secondary-custom btn-custom">
                        <i class="fas fa-folder-plus"></i> สร้างโฟลเดอร์
                    </a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-outline-danger-custom btn-custom">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container main-container">
        <div class="content-card">

            <?php if (isset($_GET['success'])): ?>
                <?php if ($_GET['success'] === 'deleted'): ?>
                    <div class="alert alert-success-custom alert-custom mt-2 mb-4">ลบโฟลเดอร์สำเร็จ</div>
                <?php elseif ($_GET['success'] === 'folder_renamed'): ?>
                    <div class="alert alert-success-custom alert-custom mt-2 mb-4">
                        <i class="fas fa-check-circle me-2"></i>
                        เปลี่ยนชื่อโฟลเดอร์สำเร็จ จาก "<?= htmlspecialchars($_GET['old_name'] ?? '') ?>" เป็น "<?= htmlspecialchars($_GET['new_name'] ?? '') ?>"
                    </div>
                <?php endif; ?>
            <?php elseif (isset($_GET['error'])): ?>
                <?php if ($_GET['error'] === 'folder_not_found'): ?>
                    <div class="alert alert-danger-custom alert-custom mt-2 mb-4">ไม่พบโฟลเดอร์ที่ต้องการแก้ไข</div>
                <?php else: ?>
                    <div class="alert alert-danger-custom alert-custom mt-2 mb-4">เกิดข้อผิดพลาดในการดำเนินการ</div>
                <?php endif; ?>
            <?php endif; ?>

            <h1 class="page-title mb-4">
                <i class="fas fa-folder-open me-2"></i>
                รายการโฟลเดอร์
            </h1>
            <?php if (empty($folders)): ?>
                <div class="text-center text-muted py-4">ไม่มีโฟลเดอร์ในระบบ</div>
            <?php else: ?>
                <div class="list-group">
                <?php foreach ($folders as $folder): ?>
                    <div class="d-flex align-items-center mb-2">
                        <a href="view_folder.php?folder=<?= urlencode($folder) ?>" class="list-group-item list-group-item-action flex-grow-1">
                            <i class="fas fa-folder me-2 text-warning"></i> <?= htmlspecialchars($folder) ?>
                        </a>
                        <?php if ($user['role'] === 'admin'): ?>
                            <div class="ms-2 d-flex gap-1">
                                <a href="edit_folder.php?folder=<?= urlencode($folder) ?>" class="btn btn-custom btn-sm" style="background: linear-gradient(135deg, #ff9800, #f57c00); color: white;" title="แก้ไขชื่อโฟลเดอร์">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="post" action="delete_folder.php" onsubmit="return confirm('ยืนยันการลบโฟลเดอร์นี้? การลบโฟลเดอร์จะลบไฟล์ทั้งหมดในโฟลเดอร์นี้ด้วย!');" class="d-inline">
                                    <input type="hidden" name="folder" value="<?= htmlspecialchars($folder) ?>">
                                    <button type="submit" class="btn btn-danger-custom btn-custom btn-sm" title="ลบโฟลเดอร์">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>