<?php
$host = 'localhost';
$dbname = 'aracparca';
$username = 'root';
$password = ''; // XAMPP'ta varsayılan şifre boştur

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Hataları daha net görebilmek için hata modunu aktif ediyoruz
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Bağlantı başarılı!"; // Her şey yolundaysa bu yazıyı göreceksin
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>