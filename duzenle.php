<?php 
include 'header.php'; 

if(!isset($_SESSION['admin_giris']) || !isset($_GET['id'])) {
    header("Location: parcalarim.php");
    exit;
}

$id = $_GET['id'];

// Eski verileri veritabanından çek (UPDATE Adım 1)
$sorgu = $db->prepare("SELECT * FROM urunler WHERE id = ?");
$sorgu->execute([$id]);
$parca = $sorgu->fetch(PDO::FETCH_ASSOC);

if(!$parca) {
    echo "Böyle bir parça bulunamadı!";
    exit;
}

// Form Gönderildiğinde Çalışacak Güncelleme Kodları (UPDATE Adım 2)
if(isset($_POST['parca_guncelle'])) {
    $urun_adi = $_POST['urun_adi'];
    $parca_no = $_POST['parca_no'];
    $seri = $_POST['seri'];
    $kategori = $_POST['kategori'];
    $durum = $_POST['durum'];
    $fiyat = $_POST['fiyat'];
    $aciklama = $_POST['aciklama'];
    
    // Varsayılan olarak eski resmi koru
    $guncel_resim = $parca['resim_yolu']; 

    // Eğer YENİ BİR RESİM yüklendiyse
    if(isset($_FILES['gorsel']) && $_FILES['gorsel']['error'] == 0) {
        $dosya_adi = $_FILES['gorsel']['name'];
        $gecici_yol = $_FILES['gorsel']['tmp_name'];
        $uzanti = strtolower(pathinfo($dosya_adi, PATHINFO_EXTENSION));
        $izin_verilenler = array('jpg', 'jpeg', 'png');
        
        if(in_array($uzanti, $izin_verilenler)) {
            $yeni_isim = uniqid() . '.' . $uzanti;
            $hedef_yol = 'uploads/' . $yeni_isim;
            
            if(move_uploaded_file($gecici_yol, $hedef_yol)) {
                // Yeni resim yüklendiği için eski resmi sunucudan sil (Unlink)
                if(file_exists('uploads/' . $parca['resim_yolu'])) {
                    unlink('uploads/' . $parca['resim_yolu']);
                }
                $guncel_resim = $yeni_isim; // Veritabanına yazılacak yeni isim
            }
        } else {
            die("Sadece JPG, JPEG ve PNG formatları geçerlidir.");
        }
    }

    // PDO UPDATE Sorgusu
    $guncelle_sorgu = $db->prepare("UPDATE urunler SET urun_adi=?, parca_no=?, seri=?, kategori=?, durum=?, fiyat=?, aciklama=?, resim_yolu=? WHERE id=?");
    $islem = $guncelle_sorgu->execute([$urun_adi, $parca_no, $seri, $kategori, $durum, $fiyat, $aciklama, $guncel_resim, $id]);

    if($islem) {
        echo "<script>alert('Parça başarıyla güncellendi!'); window.location.href='parcalarim.php';</script>";
    }
}
?>

<div class="play">
  <div class="pcon" style="max-width: 800px; margin: 0 auto;">
    <div class="ptt">✏️ PARÇAYI DÜZENLE</div>
    <div class="pts">Şu an <b><?= $parca['urun_adi'] ?></b> parçasını düzenliyorsun.</div>

    <form action="" method="POST" enctype="multipart/form-data" class="fpanel">
      
      <div class="frow c2">
        <div>
            <label class="fl2">Parça Adı *</label>
            <input class="fi3" type="text" name="urun_adi" value="<?= $parca['urun_adi'] ?>" required>
        </div>
        <div>
            <label class="fl2">Parça Numarası (OEM)</label>
            <input class="fi3" type="text" name="parca_no" value="<?= $parca['parca_no'] ?>">
        </div>
      </div>

      <div class="frow c3">
        <div>
          <label class="fl2">BMW Serisi *</label>
          <select class="fsel2" name="seri" required>
            <option value="1 Serisi" <?= $parca['seri'] == '1 Serisi' ? 'selected' : '' ?>>1 Serisi</option>
            <option value="3 Serisi" <?= $parca['seri'] == '3 Serisi' ? 'selected' : '' ?>>3 Serisi</option>
            <option value="5 Serisi" <?= $parca['seri'] == '5 Serisi' ? 'selected' : '' ?>>5 Serisi</option>
            <option value="X Serisi" <?= $parca['seri'] == 'X Serisi' ? 'selected' : '' ?>>X Serisi</option>
            <option value="M Serisi" <?= $parca['seri'] == 'M Serisi' ? 'selected' : '' ?>>M Serisi</option>
          </select>
        </div>
        <div>
          <label class="fl2">Kategori *</label>
          <select class="fsel2" name="kategori" required>
            <option value="Motor" <?= $parca['kategori'] == 'Motor' ? 'selected' : '' ?>>Motor</option>
            <option value="Fren" <?= $parca['kategori'] == 'Fren' ? 'selected' : '' ?>>Fren</option>
            <option value="Süspansiyon" <?= $parca['kategori'] == 'Süspansiyon' ? 'selected' : '' ?>>Süspansiyon</option>
            <option value="Elektrik" <?= $parca['kategori'] == 'Elektrik' ? 'selected' : '' ?>>Elektrik</option>
            <option value="İç Aksam" <?= $parca['kategori'] == 'İç Aksam' ? 'selected' : '' ?>>İç Aksam</option>
          </select>
        </div>
        <div>
          <label class="fl2">Durum *</label>
          <select class="fsel2" name="durum" required>
            <option value="Sıfır" <?= $parca['durum'] == 'Sıfır' ? 'selected' : '' ?>>✨ Sıfır</option>
            <option value="Çıkma" <?= $parca['durum'] == 'Çıkma' ? 'selected' : '' ?>>♻️ Çıkma</option>
          </select>
        </div>
      </div>

      <div class="frow c2">
        <div>
          <label class="fl2">Fiyat (₺) *</label>
          <input class="fi3" type="number" name="fiyat" value="<?= $parca['fiyat'] ?>" required>
        </div>
        <div>
          <label class="fl2">Yeni Görsel Yükle (Zorunlu Değil)</label>
          <input class="fi3" type="file" name="gorsel" accept="image/*" style="padding-top: 9px;">
          <div class="form-hint">Eğer boş bırakırsan eski resim (<a href="uploads/<?= $parca['resim_yolu'] ?>" target="_blank">Mevcut Resim</a>) korunur.</div>
        </div>
      </div>

      <div class="frow">
        <div>
          <label class="fl2">Açıklama</label>
          <textarea class="fta2" name="aciklama"><?= $parca['aciklama'] ?></textarea>
        </div>
      </div>

      <div style="display:flex; gap:10px;">
          <button type="submit" name="parca_guncelle" class="bsub">💾 Değişiklikleri Kaydet</button>
          <a href="parcalarim.php" class="btn-np" style="line-height:45px; text-align:center;">İptal Et</a>
      </div>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>