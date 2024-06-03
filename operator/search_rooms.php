<?php
include("../includes/config.php");
include("../includes/functions.php");

redirectIfNotLoggedIn();
if (!isOperator()) {
    header("Location: ../index.php");
    exit();
}

$rooms = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $query = "SELECT * FROM ruang WHERE id_ruang NOT IN (SELECT id_ruang FROM transaksi WHERE waktu_mulai <= ? AND waktu_selesai >= ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param('ss', $end_time, $start_time);
    $stmt->execute();
    $result = $stmt->get_result();
    $rooms = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Search Rooms</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/op-manager_styles.css">
</head>
<body>
    <div class="header">
        <h1>Search Rooms</h1>
        <a href="operator_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
    <div class="container">
        <form method="POST" action="">
            <label for="start_time">Start Time:</label>
            <input type="datetime-local" name="start_time" id="start_time" required>
            <label for="end_time">End Time:</label>
            <input type="datetime-local" name="end_time" id="end_time" required>
            <input type="submit" value="Search Rooms" class="btn">
        </form>
        <table class="table">
            <tr>
                <th>ID</th>
                <th>Room</th>
                <th>Capacity</th>
                <th>Status</th>
            </tr>
            <?php foreach ($rooms as $room) { ?>
            <tr>
                <td><?php echo htmlspecialchars($room['id_ruang']); ?></td>
                <td><?php echo htmlspecialchars($room['nama_ruang']); ?></td>
                <td><?php echo htmlspecialchars($room['kapasitas']); ?></td>
                <td><?php echo htmlspecialchars($room['status']); ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
