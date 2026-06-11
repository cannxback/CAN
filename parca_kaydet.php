<?php
session_start();
include 'baglan.php';

// Güvenlik: Oturum yoksa işlem yapamaz
if(!isset($_SESSION['admin_giris'])) {
    header("Location: login.php");
    exit;
}

if(isset($_POST['parca_kaydet'])) {
    // Formdan gelen yazılı verileri alıyoruz
    $urun_adi = $_POST['urun_adi'];
    $parca_no = $_POST['parca_no'];
    $seri = $_POST['seri'];
    $kategori = $_POST['kategori'];
    $durum = $_POST['durum'];
    $fiyat = $_POST['fiyat'];
    $aciklama = $_POST['aciklama'];
    
    // YENİ EKLENEN: Stok miktarını alıyoruz (Eğer boş bırakılırsa veya 0 yazılırsa 1 olarak kabul et)
    $stok = (int)$_POST['stok'];
    if($stok <= 0) {
        $stok = 1;
    }
    
    $resim_yolu = "";

    // Dosya (Görsel) Yükleme İşlemi
    if(isset($_FILES['gorsel']) && $_FILES['gorsel']['error'] == 0) {
        $dosya_adi = $_FILES['gorsel']['name'];
        $gecici_yol = $_FILES['gorsel']['tmp_name'];
        
        $uzanti = strtolower(pathinfo($dosya_adi, PATHINFO_EXTENSION));
        $izin_verilenler = array('jpg', 'jpeg', 'png');
        
        if(in_array($uzanti, $izin_verilenler)) {
            $yeni_isim = uniqid() . '.' . $uzanti;
            $hedef_yol = 'uploads/' . $yeni_isim;
            
            if(move_uploaded_file($gecici_yol, $hedef_yol)) {
                $resim_yolu = $yeni_isim;
            }
        } else {
            die("Güvenlik Uyarısı: Sadece JPG, JPEG ve PNG yükleyebilirsiniz.");
        }
    }

    // YENİ EKLENEN: Veritabanına yazarken 'stok' verisini de dahil ediyoruz
    $sorgu = $db->prepare("INSERT INTO urunler (ekleyen_id, urun_adi, parca_no, seri, kategori, durum, fiyat, aciklama, resim_yolu, stok) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $ekle = $sorgu->execute([$_SESSION['kullanici_id'], $urun_adi, $parca_no, $seri, $kategori, $durum, $fiyat, $aciklama, $resim_yolu, $stok]);

    if($ekle) {
        echo "<script>alert('Parça Başarıyla Eklendi!'); window.location.href='parca_ekle.php';</script>";
    } else {
        echo "Eklerken bir hata oluştu.";
    }
}
?>