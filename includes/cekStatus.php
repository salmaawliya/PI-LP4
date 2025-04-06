<?php
require_once 'db.php';

header('Content-Type: application/json');

// Ambil dan decode input JSON
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Validasi order_id
$order_id = $data['order_id'] ?? null;
if (!$order_id) {
  echo json_encode(['success' => false, 'message' => 'order_id tidak ditemukan']);
  exit;
}

// Cek status pembayaran
$stmt = $conn->prepare("SELECT status_pembayaran FROM transaksi WHERE order_id = ?");
$stmt->bind_param("s", $order_id);
$stmt->execute();
$stmt->bind_result($status);
$stmt->fetch();
$stmt->close();

if ($status) {
  echo json_encode(['success' => true, 'status' => $status]);
} else {
  echo json_encode(['success' => false, 'message' => 'Transaksi tidak ditemukan']);
}
?>
