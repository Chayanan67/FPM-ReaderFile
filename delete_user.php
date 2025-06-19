<?php
session_start();
require 'db.php';

// ตรวจสอบสิทธิ์
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// ตรวจสอบ CSRF token
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF validation failed');
}

// รับ id ที่ต้องการลบ และตรวจสอบความถูกต้อง
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    die('Invalid user id');
}
$id = (int)$_POST['id'];

// ดึงข้อมูล username ของ user ที่จะลบ
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($username);
if (!$stmt->fetch()) {
    $stmt->close();
    die('User not found');
}
$stmt->close();

// ไม่อนุญาตให้ลบ Admin หลัก
if (strtolower($username) === 'admin') {
    die('ไม่สามารถลบผู้ดูแลระบบหลักได้');
}

// ลบผู้ใช้
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    $stmt->close();
    header("Location: account.php?success=delete");
    exit;
} else {
    $stmt->close();
    header("Location: account.php?error=delete");
    exit;
}
?>