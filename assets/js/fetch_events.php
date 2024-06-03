<?php
include '../includes/config.php';

$sql = "SELECT t.id_transaksi, p.nama AS nama_pelanggan, r.nama AS nama_ruang, t.waktu_mulai, t.waktu_selesai 
        FROM transaksi t
        JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
        JOIN ruang r ON t.id_ruang = r.id_ruang";
$result = $conn->query($sql);

$events = array();

while($row = $result->fetch_assoc()) {
    $events[] = array(
        'id' => $row['id_transaksi'],
        'title' => $row['nama_pelanggan'] . ' - ' . $row['nama_ruang'],
        'start' => $row['waktu_mulai'],
        'end' => $row['waktu_selesai']
    );
}

echo json_encode($events);
?>
