<?php
// -------------------------
// create_folder.php
// หน้าสร้างโฟลเดอร์ใหม่สำหรับเก็บไฟล์ (เฉพาะ admin)
// สร้างโฟลเดอร์ตามรูปแบบ: "หัวข้อ ครั้งที่ X ณ วันที่"
// -------------------------

// เริ่มต้น session เพื่อเข้าถึงข้อมูล user ที่ล็อกอิน
session_start();

// ตรวจสอบสิทธิ์การเข้าถึง - เฉพาะ admin เท่านั้น
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    // หากไม่ใช่ admin ให้ redirect ไปหน้าหลัก
    header("Location: index.php");
    exit;
}

// ตัวแปรสำหรับเก็บข้อความ error
$error = '';

// ตรวจสอบว่ามีการส่งข้อมูลแบบ POST หรือไม่ (กดปุ่มสร้างโฟลเดอร์)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับข้อมูลจากฟอร์มและลบช่องว่างข้างหน้า-หลัง
    $topic  = trim($_POST['topic']);    // ชื่อหัวข้อ/โฟลเดอร์
    $round  = trim($_POST['round']);    // ครั้งที่
    $date   = trim($_POST['date']);     // วันที่
    
    // สร้างชื่อโฟลเดอร์ตามรูปแบบที่กำหนด
    $folder_name = "{$topic} ครั้งที่ {$round} ณ {$date}";
    
    // ลบอักขระพิเศษที่ไม่สามารถใช้ในชื่อโฟลเดอร์ได้
    // ใช้ regular expression เพื่อลบอักขระต้องห้าม
    $folder_name = preg_replace('/[\\\\\/\?\%\*\:\|\"<>\.]/u', '', $folder_name);
    
    // กำหนดตำแหน่งที่จะสร้างโฟลเดอร์
    $uploadDir = 'upload/';
    
    // ตรวจสอบว่าโฟลเดอร์ชื่อนี้มีอยู่แล้วหรือไม่
    if (!is_dir($uploadDir . $folder_name)) {
        // หากไม่มี ให้สร้างโฟลเดอร์ใหม่
        // 0777: กำหนดสิทธิ์ในการเข้าถึงโฟลเดอร์
        // true: สร้างโฟลเดอร์แบบ recursive (สร้างโฟลเดอร์ย่อยด้วยหากจำเป็น)
        mkdir($uploadDir . $folder_name, 0777, true);
        
        // สร้างสำเร็จแล้ว redirect ไปหน้าหลัก
        header("Location: index.php");
        exit;
    } else {
        // หากมีโฟลเดอร์ชื่อนี้อยู่แล้ว
        $error = "โฟลเดอร์นี้มีอยู่แล้ว";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สร้างโฟลเดอร์ใหม่</title>
    
    <!-- เรียกใช้ Bootstrap CSS สำหรับการจัดรูปแบบ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- เรียกใช้ CSS ที่กำหนดเอง -->
    <link rel="stylesheet" href="style.css">
    
    <!-- CSS เฉพาะสำหรับหน้านี้ -->
    <style>
        /* สไตล์สำหรับ label ของฟอร์ม */
        .form-label-custom {
            font-weight: 600;           /* ความหนาของตัวอักษร */
            color: #2196F3;             /* สีฟ้า */
            margin-bottom: 0.5rem;      /* ระยะห่างด้านล่าง */
        }
        
        /* สไตล์สำหรับแถวของฟิลด์ input */
        .field-row {
            display: flex;              /* จัดเรียงแบบ flexible */
            align-items: center;        /* จัดให้อยู่กลางแนวตั้ง */
            gap: 0.8em;                /* ระยะห่างระหว่างองค์ประกอบ */
            margin-bottom: 1.25rem;     /* ระยะห่างด้านล่าง */
        }
        
        /* สไตล์สำหรับไอคอนใน input field */
        .field-row .input-icon {
            color: #1976D2;             /* สีฟ้าเข้ม */
            font-size: 1.15rem;         /* ขนาดตัวอักษร */
            min-width: 1.6em;           /* ความกว้างขั้นต่ำ */
            text-align: center;         /* จัดไอคอนให้อยู่กลาง */
        }
        
        /* สไตล์สำหรับ input field */
        .field-row input {
            border: 2px solid #2196F3;     /* ขอบสีฟ้า */
            border-radius: 12px;            /* มุมโค้ง */
            padding: 0.7em 1em;             /* padding ภายใน */
            font-size: 1.07rem;             /* ขนาดตัวอักษร */
            width: 100%;                    /* ความกว้างเต็ม */
            background: #f8fafd;            /* สีพื้นหลังอ่อน */
            transition: border-color 0.3s, box-shadow 0.3s;  /* เอฟเฟกต์เปลี่ยนแปลง */
        }
        
        /* สไตล์เมื่อ focus ที่ input */
        .field-row input:focus {
            border-color: #1976D2;          /* เปลี่ยนสีขอบเป็นฟ้าเข้ม */
            background: #fff;               /* เปลี่ยนพื้นหลังเป็นสีขาว */
            box-shadow: 0 2px 8px rgba(33,150,243,0.08);  /* เงา */
        }
        
        /* จำกัดความกว้างของ content card */
        .content-card {
            max-width: 550px;
            margin: auto;
        }
    </style>
    
    <!-- เรียกใช้ Font Awesome สำหรับไอคอน -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container main-container">
        <div class="content-card">
            <!-- หัวข้อหน้า พร้อมไอคอน -->
            <h2 class="page-title mb-4"><i class="fas fa-folder-plus me-2"></i>สร้างโฟลเดอร์ใหม่</h2>
            
            <?php if (!empty($error)): ?>
                <!-- แสดงข้อความ error หากมี -->
                <div class="alert alert-danger-custom alert-custom"><?= $error ?></div>
            <?php endif; ?>
            
            <!-- ฟอร์มสำหรับสร้างโฟลเดอร์ -->
            <!-- autocomplete="off": ปิดการจำค่าอัตโนมัติของเบราว์เซอร์ -->
            <form method="post" autocomplete="off">
                <!-- ฟิลด์ชื่อโฟลเดอร์/หัวข้อ -->
                <div class="field-row">
                    <span class="input-icon"><i class="fas fa-folder"></i></span>
                    <div style="flex:1">
                        <label class="form-label form-label-custom mb-1">ชื่อโฟลเดอร์</label>
                        <input type="text" name="topic" class="form-control-custom" required>
                    </div>
                </div>
                
                <!-- ฟิลด์ครั้งที่ -->
                <div class="field-row">
                    <span class="input-icon"><i class="fas fa-hashtag"></i></span>
                    <div style="flex:1">
                        <label class="form-label form-label-custom mb-1">ครั้งที่</label>
                        <input type="text" name="round" class="form-control-custom" required>
                    </div>
                </div>
                
                <!-- ฟิลด์วันที่ -->
                <div class="field-row">
                    <span class="input-icon"><i class="fas fa-calendar-alt"></i></span>
                    <div style="flex:1">
                        <label class="form-label form-label-custom mb-1">เดือน/ปี (เช่น 06/2568)</label>
                        <input type="text" name="date" class="form-control-custom" required>
                    </div>
                </div>
                
                <!-- ปุ่มสร้างโฟลเดอร์ -->
                <button type="submit" class="btn btn-success-custom btn-custom">
                    <i class="fas fa-folder-plus"></i> สร้างโฟลเดอร์
                </button>
                
                <!-- ปุ่มกลับไปหน้าหลัก -->
                <a href="index.php" class="btn btn-secondary-custom btn-custom">
                    <i class="fas fa-arrow-left"></i> กลับ
                </a>
            </form>
        </div>
    </div>
</body>
</html>