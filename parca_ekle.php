<?php 
include 'header.php'; 

// Oturum kontrolü
if(!isset($_SESSION['admin_giris'])) {
    header("Location: login.php");
    exit;
}
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
      <a href="panel.php" class="pni"><span>📊</span> Dashboard</a>
      <a href="parca_ekle.php" class="pni on"><span>➕</span> Parça Ekle</a>
      <a href="parcalarim.php" class="pni"><span>🔧</span> Parçalarım</a>
    </div>
    <a href="cikis.php" class="pslo">🚪 <span>Çıkış Yap</span></a>
  </div>

  <!-- İçerik Alanı -->
  <div class="pcon">
    <div class="ptt">➕ PARÇA EKLE</div>
    <div class="pts">Yeni bir BMW yedek parça ilanı oluştur ve vitrinde yayınla.</div>

    <form action="parca_kaydet.php" method="POST" enctype="multipart/form-data" class="fpanel">
      
      <div class="frow c2">
        <div>
            <label class="fl2">Parça Adı *</label>
            <input class="fi3" type="text" name="urun_adi" placeholder="Örn: Ön Fren Diski" required>
        </div>
        <div>
            <label class="fl2">Parça Numarası (OEM)</label>
            <input class="fi3" type="text" name="parca_no" placeholder="Örn: 34116864905">
        </div>
      </div>

      <div class="frow c3">
        <div>
          <label class="fl2">BMW Serisi *</label>
          <select class="fsel2" name="seri" required>
            <option value="">Seçiniz</option>
            <option value="1 Serisi">1 Serisi</option>
            <option value="3 Serisi">3 Serisi</option>
            <option value="5 Serisi">5 Serisi</option>
            <option value="X Serisi">X Serisi</option>
            <option value="M Serisi">M Serisi</option>
          </select>
        </div>
        <div>
          <label class="fl2">Kategori *</label>
          <select class="fsel2" name="kategori" required>
            <option value="">Seçiniz</option>
            <option value="Motor">Motor</option>
            <option value="Fren">Fren</option>
            <option value="Süspansiyon">Süspansiyon</option>
            <option value="Elektrik">Elektrik</option>
            <option value="Diğer">Diğer</option>
          </select>
        </div>
        <div>
          <label class="fl2">Durum *</label>
          <select class="fsel2" name="durum" required>
            <option value="">Seçiniz</option>
            <option value="Sıfır">✨ Sıfır</option>
            <option value="Çıkma">♻️ Çıkma</option>
          </select>
        </div>
      </div>

      <!-- YENİ EKLENEN: STOK KUTUSU İÇİN ROW DÜZENİ -->
      <div class="frow c3">
        <div>
          <label class="fl2">Fiyat (₺) *</label>
          <input class="fi3" type="number" name="fiyat" placeholder="0" required>
        </div>
        <div>
          <label class="fl2">Stok Adedi *</label>
          <input class="fi3" type="number" name="stok" placeholder="Örn: 5" min="1" value="1" required>
        </div>
        <div>
          <label class="fl2">Ürün Görseli *</label>
          <input class="fi3" type="file" name="gorsel" accept="image/*" required style="padding-top: 9px;">
          <div class="form-hint" style="margin-top: 5px;">Sadece JPG, JPEG veya PNG</div>
        </div>
      </div>

      <div class="frow">
        <div>
          <label class="fl2">Açıklama</label>
          <textarea class="fta2" name="aciklama" placeholder="Parça hakkında detaylı bilgi..."></textarea>
        </div>
      </div>

      <button type="submit" name="parca_kaydet" class="bsub">➕ Parçayı Yayınla</button>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>