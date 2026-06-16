<?php
session_start();
include 'koneksi.php';
if (isset($_POST['register'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', 'pelanggan')");
    if ($query) {
        echo "<script>alert('Pendaftaran berhasil! Silakan login.'); window.location='login.php';</script>";
    }
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$password' AND role='$role'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = $data['role'];

        if ($role == 'admin') header("Location: admin.php");
        else if ($role == 'barber') header("Location: barber.php");
        else header("Location: index.php");
        exit;
    } else {
        echo "<script>alert('Akun tidak ditemukan atau Role salah!'); window.location='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Uno Barbershop</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; }
        body { background: #EFE6DE; display: flex; justify-content: center; align-items: center; height: 100vh; color: #2b1414; }
        .auth-container { background: #fff; width: 400px; border-radius: 8px; box-shadow: 0 4px 15px rgba(154,0,2,0.15); overflow: hidden; border: 1px solid #e1d3c5; }
        .auth-tabs { display: flex; background: #2b1414; }
        .tab-btn { flex: 1; padding: 1rem; border: none; background: transparent; color: #e1d3c5; cursor: pointer; font-weight: bold; font-size: 1rem; transition: 0.3s; }
        .tab-btn.active { background: #9A0002; color: #EFE6DE; }
        .auth-form { padding: 2.5rem 2rem; display: none; }
        .auth-form.active { display: block; }
        .auth-form h2 { margin-bottom: 1.5rem; text-align: center; color: #9A0002; }
        .form-group { margin-bottom: 1.2rem; }
        label { display: block; margin-bottom: 0.4rem; font-weight: bold; font-size: 0.9rem; color: #2b1414; }
        input, select { width: 100%; padding: 0.75rem; border: 1px solid #d1g5db; border-radius: 4px; background-color: #fcfaf7; font-size: 1rem; color: #2b1414; }
        input:focus, select:focus { outline: 2px solid #9A0002; }
        button { width: 100%; padding: 0.75rem; background: #9A0002; color: #EFE6DE; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 1rem; margin-top: 1rem; transition: background 0.2s; }
        button:hover { background: #730001; }
    </style>
</head>
<body>
<div class="auth-container">
    <div class="auth-tabs">
        <button class="tab-btn active" onclick="switchTab('login')">Masuk</button>
        <button class="tab-btn" onclick="switchTab('register')">Daftar</button>
    </div>

    <div id="login" class="auth-form active">
        <h2>Login Sistem</h2>
        <form method="POST">
            <div class="form-group">
                <label>Masuk Sebagai</label>
                <select name="role" required>
                    <option value="pelanggan">Pelanggan</option>
                    <option value="barber">Barber (Kapster)</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label>Email / No. HP</label>
                <input type="text" name="email" placeholder="Masukkan Email/HP" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan Password" required>
            </div>
            <button type="submit" name="login">Masuk ke Antrean</button>
        </form>
    </div>

    <div id="register" class="auth-form">
        <h2>Buat Akun Pelanggan</h2>
        <form method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" placeholder="Nama Anda" required>
            </div>
            <div class="form-group">
                <label>Email / No. HP</label>
                <input type="text" name="email" placeholder="Email atau HP aktif" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Buat Password" required>
            </div>
            <button type="submit" name="register">Daftar Sekarang</button>
        </form>
    </div>
</div>

<script>
    function switchTab(id) {
        document.querySelectorAll('.auth-form').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById(id).classList.add('active');
        event.target.classList.add('active');
    }
</script>
</body>
</html>