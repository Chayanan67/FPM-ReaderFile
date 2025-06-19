<?php
session_start();
require 'db.php';

date_default_timezone_set('Asia/Bangkok');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$user = $_SESSION['user'];
if ($user['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
if (!isset($_GET['folder'], $_GET['file'])) {
    header("Location: index.php?error=missingfile");
    exit;
}
$folder = basename($_GET['folder']);
$file = basename($_GET['file']);
$filename = $folder . '/' . $file;

$stmt = $conn->prepare("SELECT username, view_time FROM file_views WHERE filename = ? ORDER BY view_time DESC");
$stmt->bind_param("s", $filename);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ดูผู้เข้าชมไฟล์ <?php echo htmlspecialchars($file); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container main-container">
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="page-title mb-0">
                    <i class="fas fa-users me-2"></i>
                    รายชื่อผู้เข้าชมไฟล์: <span class="text-primary"><?php echo htmlspecialchars($file); ?></span>
                </h1>
                <a href="index.php" class="btn btn-primary-custom btn-custom">
                    <i class="fas fa-arrow-left"></i>
                    กลับหน้าหลัก
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th scope="col"><i class="fas fa-hashtag me-1"></i> ลำดับ</th>
                            <th scope="col"><i class="fas fa-user me-1"></i> ชื่อผู้ใช้งาน</th>
                            <th scope="col"><i class="fas fa-clock me-1"></i> เวลาเปิดไฟล์</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>
                                        <td>' . $i . '</td>
                                        <td>' . htmlspecialchars($row['username']) . '</td>
                                        <td>' . date("d/m/Y H:i:s", strtotime($row['view_time'])) . '</td>
                                      </tr>';
                                $i++;
                            }
                        } else {
                            echo '<tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="fas fa-user-slash fa-2x mb-2 d-block"></i>
                                        ยังไม่มีการเข้าชมไฟล์นี้
                                    </td>
                                  </tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>