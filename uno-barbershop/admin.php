<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { #Proteksi Halaman Admin
    header("Location: login.php"); exit;
}

if (isset($_GET['hapus'])) { #Aksi Hapus Antrean
    $id_hapus = $_GET['hapus'];
    $delete = mysqli_query($conn, "DELETE FROM booking WHERE id_booking='$id_hapus'");
    if ($delete) {
        echo "<script>alert('Data antrean berhasil dihapus!'); window.location='admin.php';</script>";
    }
}

if (isset($_POST['update'])) { #Aksi Update Antrean
    $id_booking = $_POST['id_booking'];
    $id_layanan = $_POST['id_layanan'];
    $id_barber = $_POST['id_barber'];
    $tanggal = $_POST['tanggal'];
    $waktu = $_POST['waktu'];
    $status = $_POST['status'];

    $update = mysqli_query($conn, "UPDATE booking SET 
        id_layanan='$id_layanan', 
        id_barber='$id_barber', 
        tanggal_booking='$tanggal', 
        waktu_booking='$waktu', 
        status='$status' 
        WHERE id_booking='$id_booking'");

    if ($update) {
        echo "<script>alert('Data antrean berhasil diubah!'); window.location='admin.php';</script>";
    }
}

$data_edit = null; #Menyimpan Data Antrean yang Akan Diedit (mun aya)
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $query_edit = mysqli_query($conn, "SELECT * FROM booking WHERE id_booking='$id_edit'");
    $data_edit = mysqli_fetch_assoc($query_edit);
}

$query_semua = mysqli_query($conn, "SELECT b.*, l.nama_layanan, u.nama as nama_pelanggan, bar.nama as nama_barber 
                                    FROM booking b 
                                    JOIN layanan l ON b.id_layanan = l.id_layanan 
                                    JOIN users u ON b.id_pelanggan = u.id_user 
                                    JOIN users bar ON b.id_barber = bar.id_user 
                                    ORDER BY b.tanggal_booking DESC, b.waktu_booking ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Uno Barbershop</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { display: flex; font-family: sans-serif; background: #EFE6DE; color: #2b1414; min-height: 100vh;}
        
        .sidebar { width: 250px; background: #2b1414; color: #EFE6DE; padding: 2rem 1rem; }
        .sidebar h2 { text-align: center; margin-bottom: 2rem; color: #EFE6DE; }
        .sidebar p { background: #9A0002; padding: 0.75rem; border-radius: 4px; font-weight: bold; text-align: center; color: white;}
        
        .main { flex: 1; padding: 2rem; }
        .header-title { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 8px; overflow: hidden; border: 1px solid #e1d3c5; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #9A0002; color: white; font-weight: bold; }
        tr:hover { background-color: #faf7f2; }
        
        /* CSS FIX Biar Tombol Gak Overlap */
        .btn { display: inline-block; padding: 0.4rem 0.8rem; border-radius: 4px; color: white; text-decoration: none; font-size: 0.85rem; cursor: pointer; border: none; font-weight: bold; margin: 0.2rem 0.1rem; }
        .btn-edit { background: #f59e0b; }
        .btn-edit:hover { background: #d97706; }
        .btn-hapus { background: #ef4444; }
        .btn-hapus:hover { background: #dc2626; }
        .btn-logout { background: #2b1414; padding: 0.6rem 1.2rem; color: #EFE6DE; }
        .btn-logout:hover { background: #1a0f0f; }
        
        table td:last-child { white-space: nowrap; width: 1%; } /* Mencegah kolom aksi patah/turun baris */

        .edit-box { background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border-left: 5px solid #9A0002; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-top: 1px solid #e1d3c5; border-right: 1px solid #e1d3c5; border-bottom: 1px solid #e1d3c5; }
        .edit-box h3 { color: #9A0002; margin-bottom: 1rem; }
        .edit-box input, .edit-box select { padding: 0.5rem; margin: 0.5rem 1rem 1rem 0; border: 1px solid #ccc; border-radius: 4px; background: #fcfaf7; }
        .btn-simpan { background: #9A0002; }
        .btn-batal { background: #6b7280; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Area</h2>
    <hr style="border-color: #374151; margin-bottom: 1rem;">
    <p>Kelola Antrean</p>
</div>

<div class="main">
    <div class="header-title">
        <h2 style="color: #9A0002;">Monitoring & Akses Penuh Antrean</h2>
        <a href="logout.php" class="btn btn-logout">Logout</a>
    </div>

    <?php if ($data_edit): ?>
    <div class="edit-box">
        <h3>Edit Jadwal Antrean: A-<?= sprintf("%02d", $data_edit['nomor_antrean']) ?></h3>
        <form method="POST">
            <input type="hidden" name="id_booking" value="<?= $data_edit['id_booking'] ?>">
            
            <label>Layanan: </label>
            <select name="id_layanan" required>
                <?php
                $layanans = mysqli_query($conn, "SELECT * FROM layanan");
                while ($l = mysqli_fetch_assoc($layanans)) {
                    $selected = ($l['id_layanan'] == $data_edit['id_layanan']) ? "selected" : "";
                    echo "<option value='{$l['id_layanan']}' $selected>{$l['nama_layanan']}</option>";
                }
                ?>
            </select>

            <label>Barber: </label>
            <select name="id_barber" required>
                <?php
                $barbers = mysqli_query($conn, "SELECT * FROM users WHERE role='barber'");
                while ($b = mysqli_fetch_assoc($barbers)) {
                    $selected = ($b['id_user'] == $data_edit['id_barber']) ? "selected" : "";
                    echo "<option value='{$b['id_user']}' $selected>{$b['nama']}</option>";
                }
                ?>
            </select>

            <label>Tanggal: </label>
            <input type="date" name="tanggal" value="<?= $data_edit['tanggal_booking'] ?>" required>
            
            <label>Waktu: </label>
            <input type="time" name="waktu" value="<?= $data_edit['waktu_booking'] ?>" required>

            <label>Status: </label>
            <select name="status" required>
                <option value="Menunggu Antrean" <?= ($data_edit['status'] == 'Menunggu Antrean') ? 'selected' : '' ?>>Menunggu Antrean</option>
                <option value="Sedang Dilayani" <?= ($data_edit['status'] == 'Sedang Dilayani') ? 'selected' : '' ?>>Sedang Dilayani</option>
                <option value="Selesai" <?= ($data_edit['status'] == 'Selesai') ? 'selected' : '' ?>>Selesai</option>
            </select>

            <br>
            <button type="submit" name="update" class="btn btn-simpan">Simpan Perubahan</button>
            <a href="admin.php" class="btn btn-batal">Batal</a>
        </form>
    </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>No Antrean</th>
                <th>Pelanggan</th>
                <th>Layanan</th>
                <th>Barber</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($query_semua)): ?>
            <tr>
                <td style="font-weight: 500;"><?= date('d M Y', strtotime($row['tanggal_booking'])) ?></td>
                <td><?= date('H:i', strtotime($row['waktu_booking'])) ?> WIB</td>
                <td style="font-family: monospace; font-size: 1.1rem;"><strong>A-<?= sprintf("%02d", $row['nomor_antrean']) ?></strong></td>
                <td><?= $row['nama_pelanggan'] ?></td>
                <td><?= $row['nama_layanan'] ?></td>
                <td><?= $row['nama_barber'] ?></td>
                <td style="font-weight:bold; color: <?= ($row['status']=='Selesai') ? '#10b981' : (($row['status']=='Sedang Dilayani') ? '#3b82f6' : '#f59e0b') ?>;">
                    <?= $row['status'] ?>
                </td>
                <td>
                    <a href="admin.php?edit=<?= $row['id_booking'] ?>" class="btn btn-edit">Edit</a>
                    <a href="admin.php?hapus=<?= $row['id_booking'] ?>" class="btn btn-hapus" onclick="return confirm('Yakin ingin menghapus antrean ini?');">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>