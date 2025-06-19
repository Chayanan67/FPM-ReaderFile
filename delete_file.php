<?php
// -------------------------
// delete_file.php
// สำหรับลบไฟล์ในโฟลเดอร์ upload เฉพาะ admin เท่านั้น
// -------------------------

session_start();

// ตรวจสอบสิทธิ์ผู้ใช้ ต้องเป็น admin เท่านั้น
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("สิทธิ์ไม่เพียงพอ");
}

// ตรวจสอบว่ามีการส่งข้อมูลแบบ POST และมีค่าที่จำเป็น
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['folder'], $_POST['file'])) {
    // ป้องกัน path traversal ด้วย basename
    $folder = basename($_POST['folder']);
    $file = basename($_POST['file']);
    $path = "upload/$folder/$file";

    // ตรวจสอบว่าไฟล์มีอยู่จริงหรือไม่
    if (file_exists($path)) {
        unlink($path); // ลบไฟล์
        // กลับไปหน้ารายการไฟล์ในโฟลเดอร์เดิม
        header("Location: view_folder.php?folder=" . urlencode($folder));
        exit;
    } else {
        die("ไม่พบไฟล์");
    }
} else {
    die("ข้อมูลไม่ครบหรือวิธีส่งผิด");
}
?>