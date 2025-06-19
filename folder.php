<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }
$user = $_SESSION['user'];
$folder = isset($_GET['f']) ? basename($_GET['f']) : '';
$targetDir = "upload/$folder/";
if (!$folder || !is_dir($targetDir)) { header("Location: index.php?error=nofolder"); exit; }
$files = array_diff(scandir($targetDir), array('.', '..'));
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ไฟล์ในโฟลเดอร์ <?= htmlspecialchars($folder) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container main-container" style="margin-top:100px;">
    <div class="content-card">
        <h2 class="page-title mb-4"><i class="fas fa-folder"></i> <?= htmlspecialchars($folder) ?></h2>
        <?php if ($user['role'] === 'admin'): ?>
        <form action="upload.php?f=<?= urlencode($folder) ?>" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label>เลือกไฟล์ (หลายไฟล์):</label>
                <input type="file" name="files[]" class="form-control" multiple required>
            </div>
            <button type="submit" class="btn btn-success-custom btn-custom"><i class="fas fa-upload"></i> อัปโหลด</button>
        </form>
        <hr>
        <?php endif; ?>
        <table class="table table-custom">
            <thead>
                <tr><th>#</th><th>ชื่อไฟล์</th><th>ตัวเลือก</th></tr>
            </thead>
            <tbody>
                <?php if (empty($files)): ?>
                    <tr><td colspan="3" class="text-center">ยังไม่มีไฟล์</td></tr>
                <?php else: $i=1; foreach ($files as $file): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($file) ?></td>
                        <td>
                            <a href="<?= $targetDir . urlencode($file) ?>" target="_blank" class="btn btn-info-custom btn-custom btn-sm">
                                <i class="fas fa-eye"></i> ดู
                            </a>
                            <?php if ($user['role'] === 'admin'): ?>
                                <a href="delete.php?f=<?= urlencode($folder) ?>&file=<?= urlencode($file) ?>" class="btn btn-danger-custom btn-custom btn-sm" onclick="return confirm('ลบไฟล์นี้?');">
                                    <i class="fas fa-trash"></i> ลบ
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
        <a href="index.php" class="btn btn-secondary btn-custom">ย้อนกลับ</a>
    </div>
</div>
</body>
</html>