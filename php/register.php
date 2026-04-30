<?php
session_start();
$conn = new mysqli("localhost", "root", "", "grit");
if ($conn->connect_error) die("Koneksi gagal.");

$ucp = $_POST['ucp'];
$email = $_POST['email'];
$password = $_POST['password'];
$verifycode = $_POST['verifycode'];

if ($verifycode != $_SESSION['verify_code'] || $email != $_SESSION['verify_email']) {
  $_SESSION['notif'] = "Kode verifikasi salah atau email tidak sesuai.";
  $_SESSION['notif_type'] = "error";
  header("Location: ../index.php");
  exit;
}

// Generate salt dan hash pakai SHA256
$salt = bin2hex(random_bytes(8)); // 16 karakter hex
$raw = $password . $salt;
$hashed = strtoupper(hash("sha256", $raw)); // Capital agar cocok dengan SHA256_PassHash di Pawn

// Simpan ke DB
$stmt = $conn->prepare("INSERT INTO playerucp (ucp, verifycode, email, password, salt, extrac) VALUES (?, ?, ?, ?, ?, 0)");
$stmt->bind_param("sisss", $ucp, $verifycode, $email, $hashed, $salt);

if ($stmt->execute()) {
  $_SESSION['notif'] = "Registrasi berhasil! Silakan login.";
  $_SESSION['notif_type'] = "success";
  header("Location: ../login.php");
  exit;
} else {
  $_SESSION['notif'] = "Gagal mendaftar: " . $stmt->error;
  $_SESSION['notif_type'] = "error";
  header("Location: ../index.php");
  exit;
}
