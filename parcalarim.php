<?php 
include 'header.php'; 

// Oturum Kontrolü
if(!isset($_SESSION['admin_giris'])) {
    header("Location: login.php");
    exit;
}

// GÜNCELLEME: Sadece giriş yapan ustanın (ekleyen_id) parçalarını çekiyoruz
$sorgu = $db->prepare("SELECT * FROM urunler WHERE ekleyen_id = ? ORDER BY id DESC");
$sorgu->execute([$_SESSION['kullanici_id']]);
$parcalar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="play">
  <div class="psb">
    <div class="psu">
      <div class="psav">U</div>
      <div class="psname"><?= $_SESSION['yonetici_adi'] ?></div>
      <div class="psrole">BMW Uzman Teknisyeni</div>
    </div>
    <div class="pnav">
      <a href="panel.php" class="pni"><span>📊</span> Dashboard</a>
      <a href="parca_ekle.php" class="pni"><span>➕</span> Parça Ekle</a>
      <a href="parcalarim.php" class="pni on"><span>🔧</span> Parçalarım</a>
    </div>
    <a href="cikis.php" class="pslo">🚪 <span>Çıkış Yap</span></a>
  </div>

  <div class="pcon">
    <div class="ptt">🔧 PARÇALARIM</div>
    <div class="pts">Eklediğin tüm BMW yedek parça ilanlarını buradan yönetebilirsin.</div>

    <div class="twrap">
      <div class="tw-head">
        <div class="tw-title">Parça Listesi</div>
        <div class="tw-cnt"><?= count($parcalar) ?> parça bulundu</div>
      </div>
      
      <table class="ptbl">
        <thead>
          <tr>
            <th>Görsel & Ad</th>
            <th>Seri</th>
            <th>Kategori</th>
            <th>Durum</th>
            <th>Fiyat</th>
            <th>İşlemler</th>
          </tr>
        </thead>
        <tbody>
            
          <?php if(count($parcalar) > 0): ?>
              <?php foreach($parcalar as $parca): ?>
              <tr>
                <td>
                  <div class="tpc">
                    <img src="uploads/<?= $parca['resim_yolu'] ?>" style="width:40px; height:40px; object-fit:cover; border-radius:8px;">
                    <div>
                      <div class="tpn"><?= $parca['urun_adi'] ?></div>
                      <div class="tpno">OEM: <?= $parca['parca_no'] ?: 'Belirtilmemiş' ?></div>
                    </div>
                  </div>
                </td>
                <td><?= $parca['seri'] ?></td>
                <td><?= $parca['kategori'] ?></td>
                <td><span class="act-pill <?= $parca['durum'] == 'Sıfır' ? 'ps' : 'pc' ?>"><?= $parca['durum'] ?></span></td>
                <td class="tprice"><?= number_format($parca['fiyat'], 2, ',', '.') ?> ₺</td>
                <td>
                  <a href="duzenle.php?id=<?= $parca['id'] ?>" class="btn-np" style="padding: 6px 12px; font-size:10px;">Düzenle</a>
                  <a href="sil.php?id=<?= $parca['id'] ?>" class="bdel" onclick="return confirm('Bu parçayı silmek istediğine emin misin?');">Sil</a>
                </td>
              </tr>
              <?php endforeach; ?>
          <?php else: ?>
              <tr><td colspan="6" class="nop">Henüz parça eklemediniz.</td></tr>
          <?php endif; ?>
          
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>