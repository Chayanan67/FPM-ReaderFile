<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
if (!isset($_GET['folder'], $_GET['file'])) {
    header("Location: index.php?error=missingfile");
    exit;
}

$folder = basename($_GET['folder']);
$file = basename($_GET['file']);
$path = "upload/$folder/$file";
$err = '';
$success = '';

if (!file_exists($path)) {
    $err = "ไม่พบไฟล์";
}

// เมื่อกดบันทึกชื่อใหม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newname'])) {
    $newname = trim($_POST['newname']);
    // ตรวจสอบว่าไม่ว่างและไม่มีอักขระต้องห้าม
    if ($newname === '' || preg_match('/[\\\\\\/:"*?<>|]/', $newname)) {
        $err = "ชื่อไฟล์ใหม่ไม่ถูกต้อง";
    } else {
        $newpath = "upload/$folder/" . $newname;
        if (file_exists($newpath)) {
            $err = "มีไฟล์ชื่อนี้อยู่แล้ว";
        } else {
            if (rename($path, $newpath)) {
                $success = "เปลี่ยนชื่อไฟล์สำเร็จ!";
                // redirect กลับไปโฟลเดอร์
                header("Location: view_folder.php?folder=" . urlencode($folder));
                exit;
            } else {
                $err = "เกิดข้อผิดพลาดในการเปลี่ยนชื่อ";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เปลี่ยนชื่อไฟล์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container main-container">
    <div class="content-card" style="max-width:500px;margin:auto;">
        <h2 class="page-title mb-4"><i class="fas fa-edit me-2"></i>เปลี่ยนชื่อไฟล์</h2>
        <?php if ($err): ?>
            <div class="alert alert-danger-custom alert-custom"><?= htmlspecialchars($err) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success-custom alert-custom"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label form-label-custom">ชื่อไฟล์เดิม</label>
                <input type="text" class="form-control form-control-custom" value="<?= htmlspecialchars($file) ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="newname" class="form-label form-label-custom">ชื่อไฟล์ใหม่</label>
                <input type="text" id="newname" name="newname" class="form-control form-control-custom" required value="<?= htmlspecialchars($file) ?>">
                <div class="form-text text-danger">* ห้ามใช้ \ / : " * ? &lt; &gt; | ในชื่อไฟล์</div>
            </div>
            <div class="d-flex justify-content-between">
                <a href="view_folder.php?folder=<?= urlencode($folder) ?>" class="btn btn-secondary-custom btn-custom">
                    <i class="fas fa-arrow-left"></i> ย้อนกลับ
                </a>
                <button type="submit" class="btn btn-primary-custom btn-custom">
                    <i class="fas fa-save"></i> บันทึก
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>