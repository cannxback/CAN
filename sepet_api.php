<?php
// ============================================================
// sepet_api.php — Stok Korumalı ve Sipariş Kayıtlı Sepet İşlemleri
// ============================================================
session_start(); 
require_once 'baglan.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_giris']) || $_SESSION['admin_giris'] !== true) {
    echo json_encode(['basari' => false, 'mesaj' => 'Giriş yapmanız gerekiyor.', 'giris_gerekli' => true]);
    exit;
}

if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'usta') {
    echo json_encode(['basari' => false, 'mesaj' => 'Ustalar sepet kullanamaz.']);
    exit;
}

$islem   = $_POST['islem'] ?? '';
$urun_id = (int)($_POST['urun_id'] ?? 0);
$kul_id  = $_SESSION['kullanici_id'];

function dinamikSepetSayisi($db, $kul_id) {
    $s = $db->prepare("SELECT SUM(adet) as toplam FROM sepet WHERE kullanici_id = ?");
    $s->execute([$kul_id]);
    $r = $s->fetch(PDO::FETCH_ASSOC);
    return (int)($r['toplam'] ?? 0);
}

// ──────────────────────────────────────────
// SİPARİŞİ TAMAMLA (VERİTABANINA KAYIT EKLENDİ)
// ──────────────────────────────────────────
// ──────────────────────────────────────────
// SİPARİŞİ TAMAMLA (ADRES VE KAYIT SİSTEMİ EKLİ)
// ──────────────────────────────────────────
if ($islem === 'siparisi_tamamla') {
    $adres = $_POST['adres'] ?? 'Belirtilmemiş';
    
    // Sepetteki ürünleri fiyatlarıyla beraber çekiyoruz
    $sepetSorgu = $db->prepare("SELECT s.urun_id, s.adet, u.fiyat FROM sepet s JOIN urunler u ON s.urun_id = u.id WHERE s.kullanici_id = ?");
    $sepetSorgu->execute([$kul_id]);
    $sepet_urunler = $sepetSorgu->fetchAll(PDO::FETCH_ASSOC);

    if(count($sepet_urunler) > 0) {
        // 1. ADIM: Toplam tutarı hesapla
        $toplam_tutar = 0;
        foreach($sepet_urunler as $item) {
            $toplam_tutar += ($item['fiyat'] * $item['adet']);
        }

        // 2. ADIM: Ana Siparişi (Faturayı) 'siparisler' tablosuna kaydet
        $siparisEkle = $db->prepare("INSERT INTO siparisler (kullanici_id, toplam_tutar, teslimat_adresi) VALUES (?, ?, ?)");
        $siparisEkle->execute([$kul_id, $toplam_tutar, $adres]);
        
        $yeni_siparis_id = $db->lastInsertId();

        // 3. ADIM: Sipariş detayına ekle ve stok düş
        $detayEkle = $db->prepare("INSERT INTO siparis_detay (siparis_id, urun_id, adet, birim_fiyat) VALUES (?, ?, ?, ?)");
        $stokDus = $db->prepare("UPDATE urunler SET stok = stok - ? WHERE id = ?");

        foreach($sepet_urunler as $item) {
            $detayEkle->execute([$yeni_siparis_id, $item['urun_id'], $item['adet'], $item['fiyat']]);
            $stokDus->execute([$item['adet'], $item['urun_id']]);
        }
        
        // 4. ADIM: Stoğu bitenleri 'Tükendi' yap
        $db->query("UPDATE urunler SET durum = 'Tükendi' WHERE stok <= 0");
        
        // 5. ADIM: Sepeti temizle
        $db->prepare("DELETE FROM sepet WHERE kullanici_id = ?")->execute([$kul_id]);
        
        echo json_encode(['basari' => true, 'mesaj' => '✅ Siparişiniz başarıyla oluşturuldu! Sipariş numaranız: #' . $yeni_siparis_id]);
    } else {
        echo json_encode(['basari' => false, 'mesaj' => 'Sepetiniz zaten boş.']);
    }
    exit;
}

// ──────────────────────────────────────────
// SEPETE EKLE (STOK KONTROLÜ İLE)
// ──────────────────────────────────────────
elseif ($islem === 'ekle') {
    if (!$urun_id) {
        echo json_encode(['basari' => false, 'mesaj' => 'Geçersiz ürün.']);
        exit;
    }

    $urun = $db->prepare("SELECT id, durum, stok FROM urunler WHERE id = ?");
    $urun->execute([$urun_id]);
    $urun = $urun->fetch();

    if (!$urun) {
        echo json_encode(['basari' => false, 'mesaj' => 'Ürün bulunamadı.']);
        exit;
    }

    if ($urun['durum'] === 'Tükendi' || $urun['stok'] <= 0) {
        echo json_encode(['basari' => false, 'mesaj' => 'Maalesef bu parça az önce satıldı/tükendi.']);
        exit;
    }

    $mevcut = $db->prepare("SELECT id, adet FROM sepet WHERE kullanici_id = ? AND urun_id = ?");
    $mevcut->execute([$kul_id, $urun_id]);
    $mevcut = $mevcut->fetch();

    $sepetteki_adet = $mevcut ? $mevcut['adet'] : 0;
    $istenen_yeni_adet = $sepetteki_adet + 1;

    if ($istenen_yeni_adet > $urun['stok']) {
        echo json_encode(['basari' => false, 'mesaj' => '⚠️ Maksimum stok sınırına ulaştınız! Bu üründen en fazla ' . $urun['stok'] . ' adet alabilirsiniz.']);
        exit;
    }

    if ($mevcut) {
        $db->prepare("UPDATE sepet SET adet = adet + 1 WHERE id = ?")->execute([$mevcut['id']]);
    } else {
        $db->prepare("INSERT INTO sepet (kullanici_id, urun_id, adet) VALUES (?, ?, 1)")->execute([$kul_id, $urun_id]);
    }

    $bildirim_mesaji = 'Ürün sepete eklendi!';
    if ($urun['stok'] - $istenen_yeni_adet == 0) {
        $bildirim_mesaji = "🔥 Ürün sepete eklendi! Dikkat: Stoktaki SON ürünü sepete aldınız, başkası almadan siparişi tamamlayın!";
    }

    echo json_encode([
        'basari'       => true,
        'mesaj'        => $bildirim_mesaji,
        'sepet_sayisi' => dinamikSepetSayisi($db, $kul_id)
    ]);
}

// ──────────────────────────────────────────
// SEPETTEN ÇIKAR (1 adet)
// ──────────────────────────────────────────
elseif ($islem === 'cikar') {
    $db->prepare("UPDATE sepet SET adet = adet - 1 WHERE kullanici_id = ? AND urun_id = ? AND adet > 1")->execute([$kul_id, $urun_id]);
    $db->prepare("DELETE FROM sepet WHERE kullanici_id = ? AND urun_id = ? AND adet < 1")->execute([$kul_id, $urun_id]);

    echo json_encode(['basari' => true, 'sepet_sayisi' => dinamikSepetSayisi($db, $kul_id)]);
}

// ──────────────────────────────────────────
// SEPETTEN TAMAMEN SİL
// ──────────────────────────────────────────
elseif ($islem === 'sil') {
    $db->prepare("DELETE FROM sepet WHERE kullanici_id = ? AND urun_id = ?")->execute([$kul_id, $urun_id]);
    echo json_encode(['basari' => true, 'sepet_sayisi' => dinamikSepetSayisi($db, $kul_id)]);
}

else {
    echo json_encode(['basari' => false, 'mesaj' => 'Geçersiz işlem.']);
}