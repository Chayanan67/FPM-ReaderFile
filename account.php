<?php
// -------------------------
// account.php
// หน้าจัดการบัญชีผู้ใช้สำหรับ Admin
// แสดงรายการผู้ใช้ทั้งหมดและให้ Admin สามารถจัดการได้
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

// สร้าง CSRF token เพื่อป้องกันการโจมตี CSRF (Cross-Site Request Forgery)
if (empty($_SESSION['csrf_token'])) {
    // สร้าง token แบบสุ่ม 32 bytes และแปลงเป็น hexadecimal
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ดึงข้อมูลผู้ใช้ทั้งหมดจากฐานข้อมูล (แสดงเฉพาะ id, username, role)
$users = $conn->query("SELECT id, username, role FROM users");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการบัญชีผู้ใช้</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- เรียกใช้ Bootstrap CSS สำหรับการจัดรูปแบบ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- เรียกใช้ CSS ที่กำหนดเอง -->
    <link rel="stylesheet" href="style.css">
    
    <!-- เรียกใช้ Font Awesome สำหรับไอคอน -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container main-container">
    <div class="content-card">
        <!-- หัวข้อหน้า พร้อมไอคอน -->
        <h2 class="page-title"><i class="fas fa-users-cog me-2"></i>จัดการบัญชีผู้ใช้</h2>
        
        <!-- ตารางแสดงรายการผู้ใช้ -->
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Username</th>      <!-- คอลัมน์ชื่อผู้ใช้ -->
                    <th>Role</th>          <!-- คอลัมน์บทบาท (admin/user) -->
                    <th>Action</th>        <!-- คอลัมน์การดำเนินการ -->
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $users->fetch_assoc()): ?>
                <tr>
                    <!-- แสดงชื่อผู้ใช้ (ป้องกัน XSS ด้วย htmlspecialchars) -->
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    
                    <!-- แสดงบทบาทของผู้ใช้ -->
                    <td><?= htmlspecialchars($row['role']) ?></td>
                    
                    <td>
                        <!-- ปุ่มเปลี่ยนรหัสผ่าน - ส่ง id ของผู้ใช้ผ่าน URL -->
                        <a href="change_password.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-key"></i> เปลี่ยนรหัสผ่าน
                        </a>
                        
                        <?php if ($row['username'] !== 'Admin'): ?>
                        <!-- ปุ่มลบผู้ใช้ - แสดงเฉพาะเมื่อไม่ใช่ Admin หลัก -->
                        <form action="delete_user.php" method="post" style="display:inline;" onsubmit="return confirm('ลบผู้ใช้นี้?')">
                            <!-- ส่ง ID ของผู้ใช้ที่จะลบ -->
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            
                            <!-- ส่ง CSRF token เพื่อป้องกันการโจมตี -->
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            
                            <!-- ปุ่มลบ พร้อม JavaScript confirm เพื่อยืนยัน -->
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> ลบ
                            </button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        
        <!-- ปุ่มย้อนกลับไปหน้าหลัก -->
        <a href="index.php" class="btn btn-secondary-custom btn-custom mt-3">
            <i class="fas fa-arrow-left"></i> ย้อนกลับ
        </a>
    </div>
</div>
</body>
</html>