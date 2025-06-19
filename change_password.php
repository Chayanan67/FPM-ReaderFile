<?php
// -------------------------
// change_password.php
// หน้าเปลี่ยนรหัสผ่านสำหรับผู้ใช้ (เฉพาะ admin สามารถเข้าถึงได้)
// -------------------------

// เริ่มต้น session เพื่อเข้าถึงข้อมูล user ที่ล็อกอิน
session_start();

// เชื่อมต่อฐานข้อมูล
require 'db.php';

// ตรวจสอบสิทธิ์การเข้าถึง - เฉพาะ admin เท่านั้น
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    // หากไม่ใช่ admin ให้ redirect ไปหน้าหลัก
    header("Location: index.php");
    exit;
}

// ตรวจสอบ parameter id ที่ส่งมาจาก URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // หาก id ไม่ถูกต้องให้ redirect กลับไปหน้า account พร้อมข้อความ error
    header("Location: account.php?error=invalidid");
    exit;
}

// แปลง id เป็น integer เพื่อความปลอดภัย
$id = intval($_GET['id']);

// ตัวแปรสำหรับเก็บข้อความ error และ success
$error = '';
$success = '';

// ตรวจสอบว่ามีการส่งข้อมูลแบบ POST หรือไม่ (กดปุ่มบันทึก)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับรหัสผ่านใหม่และลบช่องว่างข้างหน้า-หลัง
    $newpass = trim($_POST['newpass']);
    
    // ตรวจสอบความยาวรหัสผ่าน (ต้องมีอย่างน้อย 6 ตัวอักษร)
    if (strlen($newpass) < 6) {
        $error = "รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร";
    } else {
        // เข้ารหัสรหัสผ่านด้วย PHP password_hash (ใช้ algorithm เริ่มต้น)
        $hash = password_hash($newpass, PASSWORD_DEFAULT);
        
        // เตรียม prepared statement เพื่ออัปเดตรหัสผ่าน
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        
        // ผูกค่า parameter (s = string, i = integer)
        $stmt->bind_param("si", $hash, $id);
        
        // ดำเนินการ query
        $stmt->execute();
        
        // ปิด statement
        $stmt->close();
        
        $success = "เปลี่ยนรหัสผ่านสำเร็จ";
    }
}

// ดึงชื่อผู้ใช้จากฐานข้อมูลเพื่อแสดงในหน้า
$stmt = $conn->prepare("SELECT username FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เปลี่ยนรหัสผ่าน: <?= htmlspecialchars($username) ?></title>
    
    <!-- เรียกใช้ Bootstrap CSS สำหรับการจัดรูปแบบ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- เรียกใช้ CSS ที่กำหนดเอง -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container main-container">
    <div class="content-card">
        <!-- หัวข้อหน้า แสดงชื่อผู้ใช้ที่จะเปลี่ยนรหัสผ่าน -->
        <h2 class="page-title"><i class="fas fa-key me-2"></i>เปลี่ยนรหัสผ่าน: <?= htmlspecialchars($username) ?></h2>
        
        <?php if ($error): ?>
            <!-- แสดงข้อความ error หากมี -->
            <div class="alert alert-danger-custom alert-custom"><?= $error ?></div>
        <?php elseif ($success): ?>
            <!-- แสดงข้อความ success หากมี -->
            <div class="alert alert-success-custom alert-custom"><?= $success ?></div>
        <?php endif; ?>
        
        <!-- ฟอร์มสำหรับเปลี่ยนรหัสผ่าน -->
        <form method="post">
            <div class="mb-3">
                <label class="form-label">รหัสผ่านใหม่</label>
                <!-- input สำหรับรหัสผ่านใหม่ -->
                <!-- required: จำเป็นต้องกรอก -->
                <!-- minlength="6": ต้องมีอย่างน้อย 6 ตัวอักษร -->
                <input type="password" name="newpass" class="form-control" required minlength="6">
            </div>
            
            <!-- ปุ่มบันทึก -->
            <button type="submit" class="btn btn-success-custom btn-custom">
                <i class="fas fa-save"></i> บันทึก
            </button>
            
            <!-- ปุ่มย้อนกลับไปหน้าจัดการบัญชี -->
            <a href="account.php" class="btn btn-secondary-custom btn-custom">ย้อนกลับ</a>
        </form>
    </div>
</div>
</body>
</html>