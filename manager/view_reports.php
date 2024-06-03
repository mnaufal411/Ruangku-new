<?php
include("../includes/config.php");
include("../includes/functions.php");

redirectIfNotLoggedIn();
if (!isManager()) {
    header("Location: ../index.php");
    exit();
}

$start_date = $_POST['start_date'] ?? date('Y-m-d');
$end_date = $_POST['end_date'] ?? date('Y-m-d');

$query = "SELECT * FROM transaksi WHERE waktu_mulai BETWEEN ? AND ?";
$stmt = $db->prepare($query);
$stmt->bind_param('ss', $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
$transactions = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Reports</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/op-manager_styles.css">
</head>
<body>
    <div class="header">
        <h1>View Reports</h1>
        <a href="manager_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
    <div class="container">
        <h2>Transaction Reports</h2>
        <form method="POST" action="">
            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" required>
            <label for="end_date">End Date:</label>
            <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" required>
            <input type="submit" value="View Report" class="btn">
        </form>
        <table class="table">
            <tr>
                <th>Transaction ID</th>
                <th>Customer</th>
                <th>Room</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Total Cost</th>
                <th>Payment Status</th>
            </tr>
            <?php foreach ($transactions as $transaction) { ?>
            <tr>
                <td><?php echo htmlspecialchars($transaction['id_transaksi']); ?></td>
                <td>
                    <?php
                    $customer_result = $db->query("SELECT nama FROM pelanggan WHERE id_pelanggan = {$transaction['id_pelanggan']}");
                    $customer = $customer_result->fetch_assoc();
                    echo htmlspecialchars($customer['nama']);
                    ?>
                </td>
                <td>
                    <?php
                    $room_result = $db->query("SELECT nama_ruang FROM ruang WHERE id_ruang = {$transaction['id_ruang']}");
                    $room = $room_result->fetch_assoc();
                    echo htmlspecialchars($room['nama_ruang']);
                    ?>
                </td>
                <td><?php echo htmlspecialchars($transaction['waktu_mulai']); ?></td>
                <td><?php echo htmlspecialchars($transaction['waktu_selesai']); ?></td>
                <td><?php echo htmlspecialchars($transaction['total_biaya']); ?></td>
                <td><?php echo htmlspecialchars($transaction['status_pembayaran']); ?></td>
            </tr>
            <?php } ?>
        </table>

        <h2>Transaction Details</h2>
        <table class="table">
            <tr>
                <th>Transaction ID</th>
                <th>Equipment</th>
                <th>Equipment Rate</th>
                <th>Timestamp</th>
            </tr>
            <?php
            $details_query = "SELECT * FROM detail_transaksi WHERE id_transaksi IN (SELECT id_transaksi FROM transaksi WHERE waktu_mulai BETWEEN ? AND ?)";
            $details_stmt = $db->prepare($details_query);
            $details_stmt->bind_param('ss', $start_date, $end_date);
            $details_stmt->execute();
            $details_result = $details_stmt->get_result();
            $details = $details_result->fetch_all(MYSQLI_ASSOC);

            foreach ($details as $detail) { ?>
            <tr>
                <td><?php echo htmlspecialchars($detail['id_transaksi']); ?></td>
                <td>
                    <?php
                    $equipment_result = $db->query("SELECT nama FROM alat WHERE id_alat = {$detail['id_alat']}");
                    $equipment = $equipment_result->fetch_assoc();
                    echo htmlspecialchars($equipment['nama']);
                    ?>
                </td>
                <td><?php echo htmlspecialchars($detail['tarif_alat']); ?></td>
                <td><?php echo htmlspecialchars($detail['dibuat_pada']); ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
