<?php
session_start();
date_default_timezone_set('Asia/Bangkok'); // ตั้งค่าเขตเวลาไทย

if (!isset($_SESSION['user'])) {
    die('Session หมดอายุ กรุณาเข้าสู่ระบบใหม่');
}
$user = $_SESSION['user'];

if (!isset($_GET['folder']) || trim($_GET['folder']) === '') {
    die('ไม่ได้ระบุโฟลเดอร์');
}
$folder = basename($_GET['folder']);
$folderPath = 'upload/' . $folder . '/';

if (!is_dir($folderPath)) {
    die('ไม่พบโฟลเดอร์ ' . htmlspecialchars($folder));
}

$files = array_values(array_diff(scandir($folderPath), ['.', '..']));
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ไฟล์ในโฟลเดอร์: <?= htmlspecialchars($folder) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container main-container">
    <div class="content-card">
        <h2 class="page-title">ไฟล์ในโฟลเดอร์: <?= htmlspecialchars($folder) ?></h2>
        <?php if ($user['role'] === 'admin'): ?>
            <a href="formforUpload.php?folder=<?= urlencode($folder) ?>" class="btn btn-success-custom btn-custom mb-3">
                <i class="fas fa-upload"></i> +Upload
            </a>
        <?php endif; ?>
        <ul class="list-group">
        <?php if (empty($files)): ?>
            <li class="list-group-item text-muted">ไม่มีไฟล์ในโฟลเดอร์นี้</li>
        <?php else: ?>
            <?php foreach ($files as $file): ?>
                <?php
                    $fileUrl = "view_file.php?folder=" . urlencode($folder) . "&file=" . urlencode($file);
                    $viewersUrl = "viewers.php?folder=" . urlencode($folder) . "&file=" . urlencode($file);
                    $editUrl = "edit_file.php?folder=" . urlencode($folder) . "&file=" . urlencode($file);
                    $deleteUrl = "delete_file.php";
                    $filePath = $folderPath . $file;
                    $uploadTime = date("d/m/Y H:i", filemtime($filePath)); // เวลาไทย
                ?>
                <li class="list-group-item d-flex align-items-center justify-content-between">
                    <div>
                        <a href="<?= $fileUrl ?>" target="_blank">
                            <i class="fas fa-file-alt me-1"></i>
                            <?= htmlspecialchars($file) ?>
                        </a>
                        <span class="badge bg-light text-secondary ms-2" style="font-size:0.9em;">อัพโหลด: <?= $uploadTime ?></span>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <a href="<?= $fileUrl ?>" class="btn btn-info" title="ดูไฟล์" target="_blank">
                            <i class="fas fa-eye"></i>
                        </a>
                        <?php if ($user['role'] === 'admin'): ?>
                            <a href="<?= $editUrl ?>" class="btn btn-warning" title="เปลี่ยนชื่อไฟล์">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= $viewersUrl ?>" class="btn btn-primary" title="ดูผู้ชมไฟล์">
                                <i class="fas fa-users"></i>
                            </a>
                            <form action="<?= $deleteUrl ?>" method="post" style="display:inline;" onsubmit="return confirm('ลบไฟล์นี้?')">
                                <input type="hidden" name="folder" value="<?= htmlspecialchars($folder) ?>">
                                <input type="hidden" name="file" value="<?= htmlspecialchars($file) ?>">
                                <button type="submit" class="btn btn-danger" title="ลบไฟล์">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
        </ul>
        <a href="index.php" class="btn btn-secondary mt-3">ย้อนกลับ</a>
    </div>
</div>
</body>
</html>