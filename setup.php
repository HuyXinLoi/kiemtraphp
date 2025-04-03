<?php
// This script adds the MatKhau column to the SinhVien table and adds SoLuongDuKien to HocPhan table
// Run this script once to set up the database

// Database configuration
$host = 'localhost';
$dbname = 'test1';
$username = 'root';
$password = '';

try {
    // Create PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Add MatKhau column to SinhVien table if it doesn't exist
    $stmt = $pdo->query("SHOW COLUMNS FROM SinhVien LIKE 'MatKhau'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE SinhVien ADD COLUMN MatKhau VARCHAR(32)");
        echo "Added MatKhau column to SinhVien table.<br>";
        
        // Set default passwords for existing students (password = MaSV)
        $stmt = $pdo->query("SELECT MaSV FROM SinhVien");
        $students = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($students as $maSV) {
            $stmt = $pdo->prepare("UPDATE SinhVien SET MatKhau = ? WHERE MaSV = ?");
            $stmt->execute([md5($maSV), $maSV]);
        }
        
        echo "Set default passwords for existing students.<br>";
    } else {
        echo "MatKhau column already exists in SinhVien table.<br>";
    }
    
    // Add SoLuongDuKien column to HocPhan table if it doesn't exist
    $stmt = $pdo->query("SHOW COLUMNS FROM HocPhan LIKE 'SoLuongDuKien'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE HocPhan ADD COLUMN SoLuongDuKien INT DEFAULT 30");
        echo "Added SoLuongDuKien column to HocPhan table with default value 30.<br>";
    } else {
        echo "SoLuongDuKien column already exists in HocPhan table.<br>";
    }
    
    // Create admin user if it doesn't exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM SinhVien WHERE MaSV = 'admin'");
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO SinhVien (MaSV, HoTen, GioiTinh, NgaySinh, MaNganh, MatKhau) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['admin', 'Administrator', 'Nam', date('Y-m-d'), 'CNTT', md5('admin')]);
        echo "Created admin user (username: admin, password: admin).<br>";
    } else {
        echo "Admin user already exists.<br>";
    }
    
    echo "Setup completed successfully!";
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

