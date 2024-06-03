<?php
include("../includes/config.php");
include("../includes/functions.php");

redirectIfNotLoggedIn();
if (!isOperator()) {
    header("Location: ../index.php");
    exit();
}

$equipment = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $query = "SELECT * FROM alat WHERE id_alat NOT IN (SELECT id_alat FROM detail_transaksi WHERE id_transaksi IN (SELECT id_transaksi FROM transaksi WHERE waktu_mulai <= ? AND waktu_selesai >= ?))";
    $stmt = $db->prepare($query);
    $stmt->bind_param('ss', $end_time, $start_time);
    $stmt->execute();
    $result = $stmt->get_result();
    $equipment = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Search Equipment</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/op-manager_styles.css">
</head>
<body>
    <div class="header">
        <h1>Search Equipment</h1>
        <a href="operator_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
    <div class="container">
        <form method="POST" action="">
            <label for="start_time">Start Time:</label>
            <input type="datetime-local" name="start_time" id="start_time" required>
            <label for="end_time">End Time:</label>
            <input type="datetime-local" name="end_time" id="end_time" required>
            <input type="submit" value="Search Equipment" class="btn">
        </form>
        <table class="table">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
            </tr>
            <?php foreach ($equipment as $equip) { ?>
            <tr>
                <td><?php echo htmlspecialchars($equip['id_alat']); ?></td>
                <td><?php echo htmlspecialchars($equip['nama']); ?></td>
                <td><?php echo htmlspecialchars($equip['deskripsi']); ?></td>
                <td><?php echo htmlspecialchars($equip['status']); ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
