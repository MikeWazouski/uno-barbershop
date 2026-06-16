<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pelanggan') {
    header("Location: login.php"); exit;
}

if (isset($_POST['booking'])) {
    $id_user = $_SESSION['id_user'];
    $id_layanan = $_POST['id_layanan'];
    $id_barber = $_POST['id_barber'];
    $tanggal = $_POST['tanggal'];
    $waktu = $_POST['waktu'];

    $cek_antrean = mysqli_query($conn, "SELECT MAX(nomor_antrean) as max_antrean FROM booking WHERE tanggal_booking='$tanggal'");
    $data_antrean = mysqli_fetch_assoc($cek_antrean);
    $no_antrean = ($data_antrean['max_antrean'] == null) ? 1 : $data_antrean['max_antrean'] + 1;

    $query = mysqli_query($conn, "INSERT INTO booking (id_pelanggan, id_layanan, id_barber, tanggal_booking, waktu_booking, nomor_antrean, status) 
                                  VALUES ('$id_user', '$id_layanan', '$id_barber', '$tanggal', '$waktu', '$no_antrean', 'Menunggu Antrean')");
    if ($query) echo "<script>alert('Booking berhasil!'); window.location='index.php';</script>";
}

$id_pelanggan = $_SESSION['id_user'];
$cek_booking = mysqli_query($conn, "SELECT b.*, l.nama_layanan, u.nama as nama_barber FROM booking b 
                                    JOIN layanan l ON b.id_layanan = l.id_layanan 
                                    JOIN users u ON b.id_barber = u.id_user 
                                    WHERE b.id_pelanggan = '$id_pelanggan' AND b.status != 'Selesai' ORDER BY id_booking DESC LIMIT 1");
$booking_aktif = mysqli_fetch_assoc($cek_booking);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Uno Barbershop</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: sans-serif; }
        body { background: #EFE6DE; color: #2b1414; }
        header { background: #9A0002; color: #EFE6DE; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .container { max-width: 1000px; margin: 2rem auto; display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; padding: 0 1rem; }
        .card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e1d3c5; }
        .card h2 { color: #9A0002; margin-bottom: 1rem; font-size: 1.3rem; border-bottom: 2px solid #EFE6DE; padding-bottom: 0.5rem; }
        label { display: block; margin-top: 0.8rem; font-weight: bold; font-size: 0.9rem; }
        input, select { width: 100%; padding: 0.75rem; margin-top: 0.4rem; border: 1px solid #ccc; border-radius: 4px; background: #fcfaf7; color: #2b1414; }
        button { width: 100%; padding: 0.75rem; background: #9A0002; color: #EFE6DE; font-weight: bold; border: none; border-radius: 4px; cursor: pointer; margin-top: 1.5rem; font-size: 1rem; }
        button:hover { background: #730001; }
        .btn-logout { background: #2b1414; padding: 0.5rem 1rem; color: #EFE6DE; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 0.9rem; }
        .btn-logout:hover { background: #1a0f0f; }
        .antrean-box { text-align: center; background: #9A0002; color: #EFE6DE; }
        .antrean-box h3 { color: #e1d3c5; font-weight: normal; }
        .status-badge { display: inline-block; background: #2b1414; color: #EFE6DE; padding: 0.3rem 1rem; border-radius: 99px; font-size: 0.85rem; font-weight: bold; margin-top: 0.5rem; }
    </style>
</head>
<body>

<header>
    <h2>Uno Barbershop</h2>
    <div>
        Halo, <strong><?= $_SESSION['nama'] ?></strong>! &nbsp;
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>
</header>

<div class="container">
    <div class="card">
        <h2>Form Booking Layanan</h2>
        <form method="POST">
            <label>Pilih Layanan</label>
            <select name="id_layanan" required>
                <?php
                $layanan = mysqli_query($conn, "SELECT * FROM layanan");
                while ($row = mysqli_fetch_assoc($layanan)) {
                    echo "<option value='{$row['id_layanan']}'>{$row['nama_layanan']} - Rp " . number_format($row['harga'], 0, ',', '.') . "</option>";
                }
                ?>
            </select>

            <label>Pilih Barber</label>
            <select name="id_barber" required>
                <?php
                $barber = mysqli_query($conn, "SELECT * FROM users WHERE role='barber'");
                while ($row = mysqli_fetch_assoc($barber)) {
                    echo "<option value='{$row['id_user']}'>{$row['nama']}</option>";
                }
                ?>
            </select>

            <label>Tanggal Pelayanan</label>
            <input type="date" name="tanggal" required>
            
            <label>Waktu Pelayanan</label>
            <input type="time" name="waktu" required>

            <button type="submit" name="booking">Konfirmasi Booking</button>
        </form>
    </div>

    <div class="card antrean-box">
        <?php if ($booking_aktif): ?>
            <h3>Nomor Antrean Anda</h3>
            <h1 style="color: #EFE6DE; font-size: 4rem; margin: 0.5rem 0; font-family: monospace;">A-<?= sprintf("%02d", $booking_aktif['nomor_antrean']) ?></h1>
            <div class="status-badge"><?= $booking_aktif['status'] ?></div>
            <hr style="border-color: #730001; margin: 1.5rem 0;">
            <p style="text-align: left; font-size: 0.95rem; margin-bottom: 0.5rem;"><strong>Layanan:</strong> <?= $booking_aktif['nama_layanan'] ?></p>
            <p style="text-align: left; font-size: 0.95rem;"><strong>Barber:</strong> <?= $booking_aktif['nama_barber'] ?></p>
        <?php else: ?>
            <p style="color: #e1d3c5; padding: 2rem 0;">Belum ada antrean aktif. Silakan isi form di samping.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>