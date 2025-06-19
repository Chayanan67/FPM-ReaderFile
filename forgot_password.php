<?php
require 'db.php';
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username']);
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $token = bin2hex(random_bytes(16));
        $stmt2 = $conn->prepare("UPDATE users SET reset_token=? WHERE username=?");
        $stmt2->bind_param("ss", $token, $user);
        $stmt2->execute();
        $stmt2->close();
        $success = "ลิงก์รีเซ็ตรหัสผ่าน: <a href='reset_password.php?token=$token'>กดที่นี่เพื่อรีเซ็ต</a>";
    } else {
        $error = "ไม่พบชื่อผู้ใช้นี้";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ลืมรหัสผ่าน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container main-container">
    <div class="content-card">
        <h2 class="page-title"><i class="fas fa-unlock-alt me-2"></i>ลืมรหัสผ่าน</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger-custom alert-custom"><?= $error ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success-custom alert-custom"><?= $success ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">ชื่อผู้ใช้</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary-custom btn-custom">
                <i class="fas fa-paper-plane"></i> ขอรีเซ็ตรหัสผ่าน
            </button>
            <a href="login.php" class="btn btn-secondary-custom btn-custom">กลับเข้าสู่ระบบ</a>
        </form>
    </div>
</div>
</body>
</html>