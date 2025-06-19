<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['folder'])) {
    $folder = basename($_POST['folder']); // ป้องกัน path traversal
    $dir = "upload/" . $folder;
    if (is_dir($dir)) {
        // ฟังก์ชันลบโฟลเดอร์และไฟล์ทั้งหมดข้างใน
        function deleteDir($dirPath) {
            if (!is_dir($dirPath)) return;
            $files = array_diff(scandir($dirPath), ['.','..']);
            foreach ($files as $file) {
                $fullPath = "$dirPath/$file";
                is_dir($fullPath) ? deleteDir($fullPath) : unlink($fullPath);
            }
            rmdir($dirPath);
        }
        deleteDir($dir);
        header("Location: index.php?success=deleted");
        exit;
    }
}
header("Location: index.php?error=cannotdelete");
exit;