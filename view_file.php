<?php
session_start();
require 'db.php';
date_default_timezone_set('Asia/Bangkok');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
if (!isset($_GET['folder']) || !isset($_GET['file'])) {
    header("Location: index.php?error=missingfile");
    exit;
}

$user = $_SESSION['user'];
$folder = basename($_GET['folder']);
$filename = basename($_GET['file']);
$filepath = "upload/$folder/$filename";

if (!file_exists($filepath)) {
    header("Location: index.php?error=filenotfound");
    exit;
}

// บันทึกข้อมูลการดูไฟล์ (เก็บชื่อแบบ "โฟลเดอร์/ไฟล์" เพื่อไม่ซ้ำกัน)
$full_filename = $folder . '/' . $filename;
$stmt = $conn->prepare("INSERT INTO file_views (filename, user_id, username, view_time) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("sis", $full_filename, $user['id'], $user['username']);
$stmt->execute();

// ส่งไฟล์ให้ดู (inline)
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $filepath);
finfo_close($finfo);

header("Content-Type: $mime");
header('Content-Disposition: inline; filename="' . $filename . '"');
readfile($filepath);
exit;
?>