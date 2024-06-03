<?php
include("../includes/config.php");
include("../includes/functions.php");

redirectIfNotLoggedIn();
if (!isManager()) {
    header("Location: ../index.php");
    exit();
}

$result = mysqli_query($db, "SELECT * FROM ruang");
$rooms = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Rooms</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/op-manager_styles.css">
</head>
<body>
    <div class="header">
        <h1>View Rooms</h1>
        <a href="manager_dashboard.php">Back to Dashboard</a>
    </div>
    <div class="container">
        <h2>Room Information</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Capacity</th>
                <th>Status</th>
            </tr>
            <?php foreach ($rooms as $room): ?>
            <tr>
                <td><?php echo htmlspecialchars($room['id_ruang']); ?></td>
                <td><?php echo htmlspecialchars($room['nama_ruang']); ?></td>
                <td><?php echo htmlspecialchars($room['kapasitas']); ?></td>
                <td><?php echo htmlspecialchars($room['status']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
