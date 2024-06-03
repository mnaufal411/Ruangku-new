<?php
include("../includes/config.php");
include("../includes/functions.php");

redirectIfNotLoggedIn();
if (!isOperator()) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];
    $room_id = $_POST['room_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $equipment_ids = $_POST['equipment_id'];

    mysqli_begin_transaction($db);
    
    $insert_customer_query = "INSERT INTO pelanggan (nama, nomor_hp) VALUES (?, ?)";
    $stmt = $db->prepare($insert_customer_query);
    $stmt->bind_param('ss', $customer_name, $customer_phone);

    if ($stmt->execute()) {
        $customer_id = $stmt->insert_id;

        $insert_rental_query = "INSERT INTO transaksi (id_pelanggan, id_ruang, waktu_mulai, waktu_selesai) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($insert_rental_query);
        $stmt->bind_param('iiss', $customer_id, $room_id, $start_time, $end_time);
        
        if ($stmt->execute()) {
            $rental_id = $stmt->insert_id;
            $all_inserts_success = true;

            foreach ($equipment_ids as $equipment_id) {
                if ($equipment_id) {
                    $insert_equipment_rental_query = "INSERT INTO detail_transaksi (id_transaksi, id_alat, tarif_alat) VALUES (?, ?, ?)";
                    $stmt = $db->prepare($insert_equipment_rental_query);
                    $stmt->bind_param('iii', $rental_id, $equipment_id, $tarif_alat);
                    if (!$stmt->execute()) {
                        $all_inserts_success = false;
                        break;
                    }
                }
            }

            if ($all_inserts_success) {
                mysqli_commit($db);
                header("Location: operator_dashboard.php");
                exit();
            } else {
                mysqli_rollback($db);
                echo "Error: Gagal memasukkan transaksi alat.";
            }
        } else {
            mysqli_rollback($db);
            echo "Error: " . $stmt->error;
        }
    } else {
        mysqli_rollback($db);
        echo "Error: " . $stmt->error;
    }
}

$rooms_query = "SELECT * FROM ruang WHERE status = 'tersedia'";
$rooms_result = mysqli_query($db, $rooms_query);
$rooms = mysqli_fetch_all($rooms_result, MYSQLI_ASSOC);

$equipment_query = "SELECT * FROM alat WHERE status = 'tersedia'";
$equipment_result = mysqli_query($db, $equipment_query);
$equipment = mysqli_fetch_all($equipment_result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Process Rental</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/op-manager_styles.css">
</head>
<body>
    <div class="header">
        <h1>Process Rental</h1>
        <a href="operator_dashboard.php">Kembali ke Dashboard</a>
    </div>
    <div class="container">
        <form method="POST" action="">
            <h2>Informasi Pelanggan</h2>
            <input type="text" name="customer_name" placeholder="Nama Pelanggan" required>
            <input type="text" name="customer_phone" placeholder="Nomor Telepon Pelanggan" required>

            <h2>Informasi Rental</h2>
            <label for="room_id">Pilih Ruangan:</label>
            <select name="room_id" required>
                <?php foreach ($rooms as $room): ?>
                <option value="<?php echo $room['id_ruang']; ?>"><?php echo htmlspecialchars($room['nama_ruang']); ?> (Kapasitas: <?php echo $room['kapasitas']; ?>)</option>
                <?php endforeach; ?>
            </select>

            <label for="equipment_id">Pilih Alat:</label>
            <div id="equipment_list">
                <select name="equipment_id[]">
                    <option value="">Tidak Ada</option>
                    <?php foreach ($equipment as $item): ?>
                    <option value="<?php echo $item['id_alat']; ?>"><?php echo htmlspecialchars($item['nama']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="button" onclick="addEquipment()">Tambah Alat</button>

            <label for="start_time">Waktu Mulai:</label>
            <input type="datetime-local" name="start_time" required>
            <label for="end_time">Waktu Selesai:</label>
            <input type="datetime-local" name="end_time" required>
            
            <input type="submit" value="Proses Rental">
        </form>
    </div>

    <script>
        function addEquipment() {
            var equipmentList = document.getElementById("equipment_list");
            var newEquipmentSelect = document.createElement("select");
            newEquipmentSelect.setAttribute("name", "equipment_id[]");
            
            var noneOption = document
            .createElement("option");
            noneOption.value = "";
            noneOption.text = "Tidak Ada";
            newEquipmentSelect.appendChild(noneOption);
            
            <?php foreach ($equipment as $item): ?>
            var option = document.createElement("option");
            option.value = "<?php echo $item['id_alat']; ?>";
            option.text = "<?php echo htmlspecialchars($item['nama']); ?>";
            newEquipmentSelect.appendChild(option);
            <?php endforeach; ?>

            var deleteButton = document.createElement("button");
            deleteButton.innerHTML = "Hapus";
            deleteButton.type = "button";
            deleteButton.onclick = function() {
                equipmentList.removeChild(newEquipmentSelect);
                equipmentList.removeChild(deleteButton);
            };
            
            equipmentList.appendChild(newEquipmentSelect);
            equipmentList.appendChild(deleteButton);
        }
    </script>
</body>
</html>
