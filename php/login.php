<?php
session_start();
$conn = new mysqli("localhost", "root", "", "grit");

$ucp = $_POST['ucp'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT ID, password, salt FROM playerucp WHERE ucp = ?");
$stmt->bind_param("s", $ucp);
$stmt->execute();
$stmt->bind_result($id, $stored_hash, $salt);

if ($stmt->fetch()) {
  $input_hash = strtoupper(hash("sha256", $password . $salt));
  if ($input_hash === $stored_hash) {
    $_SESSION['user_id'] = $id;
    $_SESSION['ucp'] = $ucp;
    header("Location: ../dashboard.php");
    exit;
  }
}

$_SESSION['notif'] = "Login gagal. Username atau password salah.";
$_SESSION['notif_type'] = "error";
header("Location: ../login.php");
exit;
