<?php 
include 'header.php'; 

if(!isset($_SESSION['admin_giris'])) {
    header("Location: login.php");
    exit;
}

// Veritabanından İstatistikleri Çekme (READ İşlemi)
$toplam_parca = $db->query("SELECT COUNT(*) FROM urunler")->fetchColumn();
$sifir_parca = $db->query("SELECT COUNT(*) FROM urunler WHERE durum = 'Sıfır'")->fetchColumn();
$cikma_parca = $db->query("SELECT COUNT(*) FROM urunler WHERE durum = 'Çıkma'")->fetchColumn();
$toplam_deger = $db->query("SELECT SUM(fiyat) FROM urunler")->fetchColumn();

// Eğer hiç parça yoksa değer null döner, onu 0 yapıyoruz
if(!$toplam_deger) $toplam_deger = 0;
?>

<div class="play">
  <!-- Sidebar (Sol Menü) -->
  <div class="psb">
    <div class="psu">
      <div class="psav">U</div>
      <div class="psname"><?= $_SESSION['yonetici_adi'] ?></div>
      <div class="psrole">BMW Uzman Teknisyeni</div>
    </div>
    <div class="pnav">
      <a href="panel.php" class="pni on"><span>📊</span> Dashboard</a>
      <a href="parca_ekle.php" class="pni"><span>➕</span> Parça Ekle</a>
      <a href="parcalarim.php" class="pni"><span>🔧</span> Parçalarım</a>
      </div>
    <a href="cikis.php" class="pslo">🚪 <span>Çıkış Yap</span></a>
  </div>

  <!-- İçerik Alanı -->
  <div class="pcon">
    <div class="ptt">📊 DASHBOARD</div>
    <div class="pts">Hoş geldin, <?= $_SESSION['yonetici_adi'] ?>! Burası yönetim paneli.</div>
    
    <!-- Dinamik İstatistik Kartları -->
    <div class="srow">
        <div class="sc">
            <div class="sc-ico">🔩</div>
            <div class="scl">Toplam Parça</div>
            <div class="scn"><?= $toplam_parca ?></div>
        </div>
        <div class="sc">
            <div class="sc-ico">✨</div>
            <div class="scl">Sıfır Parça</div>
            <div class="scn green"><?= $sifir_parca ?></div>
        </div>
        <div class="sc">
            <div class="sc-ico">♻️</div>
            <div class="scl">Çıkma Parça</div>
            <div class="scn"><?= $cikma_parca ?></div>
        </div>
        <div class="sc">
            <div class="sc-ico">💰</div>
            <div class="scl">Toplam Değer</div>
            <!-- Hocanın istediği number_format() fonksiyonu kullanımı -->
            <div class="scn gold" style="font-size:26px"><?= number_format($toplam_deger, 2, ',', '.') ?> ₺</div>
        </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>