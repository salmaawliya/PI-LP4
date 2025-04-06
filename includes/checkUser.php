<?php
require 'db.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

$email = $data['email'];
$phone = $data['phone'];

$response = [
  'emailExists' => false,
  'phoneExists' => false
];

$stmt = $conn->prepare("SELECT COUNT(*) FROM transaksi WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($countEmail);
$stmt->fetch();
$response['emailExists'] = $countEmail > 0;
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) FROM transaksi WHERE no_wa = ?");
$stmt->bind_param("s", $phone);
$stmt->execute();
$stmt->bind_result($countPhone);
$stmt->fetch();
$response['phoneExists'] = $countPhone > 0;
$stmt->close();

echo json_encode($response);
?>
