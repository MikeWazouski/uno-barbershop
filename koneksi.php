<?php
$conn = mysqli_connect("localhost", "root", "", "db_barbershop_uno");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>