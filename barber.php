<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'barber') {
    header("Location: login.php"); exit;
}

if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $id_booking = $_GET['id'];
    $status_baru = ($_GET['aksi'] == 'panggil') ? 'Sedang Dilayani' : 'Selesai';
    mysqli_query($conn, "UPDATE booking SET status='$status_baru' WHERE id_booking='$id_booking'");
    header("Location: barber.php"); exit;
}

$id_barber = $_SESSION['id_user'];
$query_antrean = mysqli_query($conn, "SELECT b.*, l.nama_layanan, u.nama as nama_pelanggan FROM booking b 
                                      JOIN layanan l ON b.id_layanan = l.id_layanan 
                                      JOIN users u ON b.id_pelanggan = u.id_user 
                                      WHERE b.id_barber = '$id_barber' AND b.status != 'Selesai' ORDER BY b.tanggal_booking ASC, b.waktu_booking ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Barber Panel - Uno Barbershop</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: sans-serif; }
        body { background: #EFE6DE; color: #2b1414; }
        header { background: #9A0002; color: #EFE6DE; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .container { max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #e1d3c5;}
        .btn-logout { background: #2b1414; color: #EFE6DE; text-decoration: none; padding: 0.5rem 1rem; border-radius: 4px; font-weight: bold;}
        .btn-logout:hover { background: #1a0f0f; }
        .btn { padding: 0.6rem 1.2rem; color: white; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block; }
        .status-txt { font-weight: bold; color: #f59e0b; }
    </style>
</head>
<body>

<header>
    <h2>Panel Barber (Kapster)</h2>
    <div>Halo, <strong><?= $_SESSION['nama'] ?></strong>! &nbsp; <a href="logout.php" class="btn-logout">Logout</a></div>
</header>

<div class="container">
    <h2 style="margin-bottom: 1.5rem; color: #9A0002;">Daftar Tugas Antrean Anda</h2>
    
    <?php if(mysqli_num_rows($query_antrean) == 0): ?>
        <p style="text-align: center; color: #736a60; margin-top: 2rem;">Tidak ada antrean berjalan untuk Anda saat ini.</p>
    <?php endif; ?>

    <?php while ($row = mysqli_fetch_assoc($query_antrean)): ?>
        <div class="card">
            <div>
                <h3 style="color: #9A0002; font-size: 1.8rem; font-family: monospace; margin-bottom: 0.3rem;">A-<?= sprintf("%02d", $row['nomor_antrean']) ?></h3>
                <p style="font-size: 1.1rem; margin-bottom: 0.2rem;"><strong><?= $row['nama_pelanggan'] ?></strong> - <?= $row['nama_layanan'] ?></p>
                <p style="font-size: 0.9rem; color: #555;">Jadwal: <?= date('d M', strtotime($row['tanggal_booking'])) ?> | <?= date('H:i', strtotime($row['waktu_booking'])) ?> WIB</p>
                <p style="font-size: 0.9rem; margin-top: 0.4rem;">Status: <span class="status-txt" style="color: <?= ($row['status']=='Sedang Dilayani') ? '#3b82f6' : '#f59e0b' ?>;"><?= $row['status'] ?></span></p>
            </div>
            <div>
                <?php if ($row['status'] == 'Menunggu Antrean'): ?>
                    <a href="?aksi=panggil&id=<?= $row['id_booking'] ?>" class="btn" style="background: #3b82f6;">Mulai Layani</a>
                <?php elseif ($row['status'] == 'Sedang Dilayani'): ?>
                    <a href="?aksi=selesai&id=<?= $row['id_booking'] ?>" class="btn" style="background: #10b981;">Selesai</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>