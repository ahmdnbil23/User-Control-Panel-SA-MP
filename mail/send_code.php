
<?php
session_start();

require '../PHPMailer-6.10.0/src/Exception.php';
require '../PHPMailer-6.10.0/src/PHPMailer.php';
require '../PHPMailer-6.10.0/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function generateCode() {
  return rand(100000, 999999);
}

$conn = new mysqli("localhost", "root", "", "grit");
if ($conn->connect_error) die("Koneksi gagal.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];

  $stmt = $conn->prepare("SELECT ID FROM playerucp WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();
  if ($stmt->num_rows > 0) {
    echo "Email sudah digunakan.";
    exit;
  }

  $code = generateCode();

  $mail = new PHPMailer(true);
  try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'isi dengan email mu';
    $mail->Password = 'isi dengan password aplication mu, jika bingung nonton di youtube';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('email mu isi di sini', 'SA-MP UCP');
    $mail->addAddress($email);
    $mail->Subject = 'Verification Code';
    $mail->Body    = "Your verification code is: $code";

    $mail->send();

    $_SESSION['verify_code'] = $code;
    $_SESSION['verify_email'] = $email;
    echo "Kode verifikasi telah dikirim ke email.";
  } catch (Exception $e) {
    echo "Gagal mengirim email: {$mail->ErrorInfo}";
  }
}
