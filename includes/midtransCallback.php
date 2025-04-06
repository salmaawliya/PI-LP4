<?php
include "db.php";
require_once dirname(__FILE__) . '/midtrans-php-master/Midtrans.php';

\Midtrans\Config::$serverKey = 'SB-Mid-server-xqKFnkFh5VFk7tU9dXOi81xr';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

header("Content-Type: application/json");

$json = file_get_contents("php://input");
file_put_contents("debug-callback.txt", $json); // Optional log

$data = json_decode($json, true);

if (!$data || !isset($data['order_id']) || !isset($data['transaction_status'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$order_id = $data['order_id'];
$status = $data['transaction_status'];

switch ($status) {
    case "settlement":
    case "capture":
        $status_pembayaran = "Success";
        break;
    case "pending":
        $status_pembayaran = "Pending";
        break;
    case "deny":
    case "expire":
    case "cancel":
        $status_pembayaran = "Failed";
        break;
    default:
        $status_pembayaran = "Pending";
}

$stmt = $conn->prepare("UPDATE transaksi SET status_pembayaran = ? WHERE order_id = ?");
$stmt->bind_param("ss", $status_pembayaran, $order_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Status pembayaran berhasil diperbarui']);
} else {
    echo json_encode(['error' => 'Gagal memperbarui status pembayaran']);
}
$stmt->close();

http_response_code(200); // Beri respon sukses ke Midtrans
?>
