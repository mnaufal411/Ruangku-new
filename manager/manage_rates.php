<?php
include("../includes/config.php");
include("../includes/functions.php");

redirectIfNotLoggedIn();
if (!isManager()) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $reference_id = $_POST['reference_id'];
    $rate = $_POST['rate'];

    if ($type == 'Room') {
        $stmt = $db->prepare("INSERT INTO tarif (tarif_ruang) VALUES (?)");
    } else {
        $stmt = $db->prepare("INSERT INTO tarif (tarif_alat) VALUES (?)");
    }
    
    $stmt->bind_param("d", $rate);
    $stmt->execute();

    header("Location: manage_rates.php");
    exit();
}

$rooms = $db->query("SELECT * FROM ruang")->fetch_all(MYSQLI_ASSOC);
$equipment = $db->query("SELECT * FROM alat")->fetch_all(MYSQLI_ASSOC);
$rates = $db->query("SELECT * FROM tarif")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Rates</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/op-manager_styles.css">
</head>
<body>
    <div class="header">
        <h1>Manage Rates</h1>
        <a href="manager_dashboard.php">Back to Dashboard</a>
    </div>
    <div class="container">
        <h2>Add Rate</h2>
        <form method="POST" action="">
            <label for="type">Type:</label>
            <select name="type" required>
                <option value="Room">Room</option>
                <option value="Equipment">Equipment</option>
            </select>

            <label for="reference_id">Reference:</label>
            <select name="reference_id" required>
                <optgroup label="Rooms">
                    <?php foreach ($rooms as $room): ?>
                    <option value="<?php echo $room['id_ruang']; ?>"><?php echo $room['nama_ruang']; ?></option>
                    <?php endforeach; ?>
                </optgroup>
                <optgroup label="Equipment">
                    <?php foreach ($equipment as $item): ?>
                    <option value="<?php echo $item['id_alat']; ?>"><?php echo $item['nama']; ?></option>
                    <?php endforeach; ?>
                </optgroup>
            </select>

            <label for="rate">Rate:</label>
            <input type="number" step="0.01" name="rate" required>
            
            <input type="submit" value="Add Rate">
        </form>

        <h2>Current Rates</h2>
        <table>
            <tr>
                <th>Type</th>
                <th>Reference</th>
                <th>Rate</th>
            </tr>
            <?php foreach ($rates as $rate): ?>
            <tr>
                <td><?php echo isset($rate['tarif_ruang']) ? 'Room' : 'Equipment'; ?></td>
                <td>
                    <?php
                    if (isset($rate['tarif_ruang'])) {
                        $ref = $db->query("SELECT nama_ruang FROM ruang WHERE id_ruang = {$rate['id_tarif']}")->fetch_assoc();
                        echo $ref['nama_ruang'];
                    } else {
                        $ref = $db->query("SELECT nama FROM alat WHERE id_alat = {$rate['id_tarif']}")->fetch_assoc();
                        echo $ref['nama'];
                    }
                    ?>
                </td>
                <td><?php echo isset($rate['tarif_ruang']) ? $rate['tarif_ruang'] : $rate['tarif_alat']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
