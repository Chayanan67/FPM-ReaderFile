<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die('คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
}

// รับค่า folder ผ่าน GET
$folder = isset($_GET['folder']) ? basename($_GET['folder']) : '';
$folderPath = "upload/$folder/";

// ตรวจสอบความถูกต้องของโฟลเดอร์
if (!$folder || !is_dir($folderPath)) {
    die('โฟลเดอร์ไม่ถูกต้อง');
}

// ส่วนประมวลผลการอัปโหลด
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
    $allowed_extensions = ['doc', 'docx', 'xls', 'xlsx', 'pdf', 'ppt', 'pptx'];
    $success_count = 0;
    $errors = [];

    foreach ($_FILES['files']['name'] as $i => $filename) {
        $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($file_extension, $allowed_extensions)) {
            $errors[] = "$filename: ประเภทไฟล์ไม่ถูกต้อง";
            continue;
        }
        $target_file = $folderPath . basename($filename);
        if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $target_file)) {
            $success_count++;
        } else {
            $errors[] = "$filename: อัปโหลดไม่สำเร็จ";
        }
    }
    if ($success_count) $success = "อัปโหลดไฟล์สำเร็จ $success_count ไฟล์";
    if ($errors) $error = implode('<br>', $errors);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>อัปโหลดไฟล์ไปยัง <?= htmlspecialchars($folder) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container main-container">
    <div class="content-card">
        <h2 class="page-title"><i class="fas fa-upload me-2"></i>อัปโหลดไฟล์ไปยัง: <?= htmlspecialchars($folder) ?></h2>
        <?php if ($success): ?>
            <div class="alert alert-success-custom alert-custom"><?= $success ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger-custom alert-custom"><?= $error ?></div>
        <?php endif; ?>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">เลือกไฟล์ (อัปโหลดได้หลายไฟล์)</label>
                <input type="file" name="files[]" class="form-control" multiple required>
            </div>
            <button type="submit" class="btn btn-success-custom btn-custom">
                <i class="fas fa-upload"></i> อัปโหลดไฟล์
            </button>
            <a href="view_folder.php?folder=<?= urlencode($folder) ?>" class="btn btn-secondary-custom btn-custom">ย้อนกลับ</a>
        </form>
    </div>
</div>
</body>
</html>