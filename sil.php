<?php
session_start();
include 'baglan.php';

// Güvenlik: Admin değilse veya URL'de ID yoksa işlem yapma
if(!isset($_SESSION['admin_giris']) || !isset($_GET['id'])) {
    header("Location: parcalarim.php");
    exit;
}

$id = $_GET['id'];

// 1. ADIM (İleri Seviye Challenge - Unlink): Önce silinecek resmin adını bul
$resim_sorgu = $db->prepare("SELECT resim_yolu FROM urunler WHERE id = ?");
$resim_sorgu->execute([$id]);
$parca = $resim_sorgu->fetch(PDO::FETCH_ASSOC);

// Eğer resim varsa, fiziksel olarak klasörden sil
if($parca && $parca['resim_yolu'] != "") {
    $dosya_yolu = 'uploads/' . $parca['resim_yolu'];
    if(file_exists($dosya_yolu)) {
        unlink($dosya_yolu); 
    }
}

// 2. ADIM: Veritabanındaki satırı sil (DELETE İşlemi)
$sil_sorgu = $db->prepare("DELETE FROM urunler WHERE id = ?");
$sil_sorgu->execute([$id]);

// İşlem bitince parçalarım sayfasına geri dön
header("Location: parcalarim.php");
exit;
?>