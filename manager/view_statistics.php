<?php
include("../includes/config.php");
include("../includes/functions.php");

redirectIfNotLoggedIn();
if (!isManager()) {
    header("Location: ../index.php");
    exit();
}

$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;

$room_occupancy_query = "SELECT nama_ruang, COUNT(*) as count FROM transaksi JOIN ruang ON transaksi.id_ruang = ruang.id_ruang";
if ($start_date && $end_date) {
    $room_occupancy_query .= " WHERE tanggal BETWEEN '$start_date' AND '$end_date'";
}
$room_occupancy_query .= " GROUP BY nama_ruang";
$room_occupancy = $db->query($room_occupancy_query)->fetch_all(MYSQLI_ASSOC);

$equipment_rental_query = "SELECT nama, COUNT(*) as count FROM detail_transaksi JOIN alat ON detail_transaksi.id_alat = alat.id_alat";
if ($start_date && $end_date) {
    $equipment_rental_query .= " WHERE waktu_mulai BETWEEN '$start_date' AND '$end_date'";
}
$equipment_rental_query .= " GROUP BY nama";
$equipment_rental = $db->query($equipment_rental_query)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Statistics</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/op-manager_styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="header">
        <h1>View Statistics</h1>
        <a href="manager_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
    <div class="container">
        <form method="POST" action="">
            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" id="start_date" required>
            <label for="end_date">End Date:</label>
            <input type="date" name="end_date" id="end_date" required>
            <input type="submit" value="Filter" class="btn">
        </form>
        <h2>Room Occupancy</h2>
        <canvas id="roomOccupancyChart"></canvas>
        <script>
            var ctx = document.getElementById('roomOccupancyChart').getContext('2d');
            var roomOccupancyChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [<?php foreach ($room_occupancy as $data) { echo "'".$data['nama_ruang']."',"; } ?>],
                    datasets: [{
                        label: '# of Rentals',
                        data: [<?php foreach ($room_occupancy as $data) { echo $data['count'].","; } ?>],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
        <h2>Equipment Rental</h2>
        <canvas id="equipmentRentalChart"></canvas>
        <script>
            var ctx = document.getElementById('equipmentRentalChart').getContext('2d');
            var equipmentRentalChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [<?php foreach ($equipment_rental as $data) { echo "'".$data['nama']."',"; } ?>],
                    datasets: [{
                        label: '# of Rentals',
                        data: [<?php foreach ($equipment_rental as $data) { echo $data['count'].","; } ?>],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    </div>
</body>
</html>
