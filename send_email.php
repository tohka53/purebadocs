<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_FILES['pdf'])) {
    $pdf = $_FILES['pdf'];

    $mail = new PHPMailer(true);

    try {
      $mail->isSMTP();
      $mail->Host = getenv('SMTP_HOST');
      $mail->SMTPAuth = true;
      $mail->Port = getenv('SMTP_PORT');
      $mail->Username = getenv('SMTP_USER');
      $mail->Password = getenv('SMTP_PASS');
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // o SMTPS si usas el puerto 465

      $mail->setFrom(getenv('FROM_EMAIL'), 'Qualitech');
      $mail->addAddress(getenv('TO_EMAIL'), 'Recipient Name');

      $mail->addAttachment($pdf['tmp_name'], $pdf['name']);

      $mail->isHTML(true);
      $mail->Subject = 'New Form Submission';
      $mail->Body = 'A new form has been submitted.';

      $mail->send();
      echo json_encode(['message' => 'Message has been sent']);
    } catch (Exception $e) {
      http_response_code(500);
      echo json_encode(['message' => $mail->ErrorInfo]);
    }
  } else {
    http_response_code(400);
    echo json_encode(['message' => 'No PDF file uploaded.']);
  }
} else {
  http_response_code(405);
  echo json_encode(['message' => 'Only POST requests are allowed.']);
}
?>