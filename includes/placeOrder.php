<?php
include "db.php";
require_once dirname(__FILE__) . '/midtrans-php-master/Midtrans.php';

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-xqKFnkFh5VFk7tU9dXOi81xr';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Ambil data dari frontend
$data = json_decode(file_get_contents("php://input"), true);
$nama = $data['name'];
$email = $data['email'];
$no_wa = $data['phone'];
$total = $data['total'];
$detail = $data['detail'];

$order_id = 'WZ-' . time() . '-' . rand(100, 999);
$tanggal = date("Y-m-d H:i:s");

// Simpan ke database
$stmt = $conn->prepare("INSERT INTO transaksi (order_id, nama, email, no_wa, total_harga, tanggal) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $order_id, $nama, $email, $no_wa, $total, $tanggal);

if ($stmt->execute()) {
    // Buat Snap Token Midtrans
    $params = [
        'transaction_details' => [
            'order_id' => $order_id,
            'gross_amount' => $total
        ],
        'customer_details' => [
            'first_name' => $nama,
            'email' => $email,
            'phone' => $no_wa
        ],
        'item_details' => [
            [
                'id' => 'paket',
                'price' => $total,
                'quantity' => 1,
                'name' => 'Pembelian Paket WatZap'
            ]
        ]
    ];

    try {
        $snapToken = \Midtrans\Snap::getSnapToken($params);
        echo json_encode(['snapToken' => $snapToken]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Gagal generate Snap Token', 'details' => $e->getMessage()]);
    }
} else {
    // Cek duplikat email/WA
    if (mysqli_errno($conn) == 1062) {
        echo json_encode(['error' => 'Email atau No. WA sudah digunakan']);
    } else {
        echo json_encode(['error' => 'Gagal menyimpan transaksi']);
    }
}
?>
