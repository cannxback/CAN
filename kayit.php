<?php 
include 'header.php'; 

// Eğer zaten giriş yapılmışsa, kayıt sayfasına girmesine gerek yok
if(isset($_SESSION['admin_giris'])) {
    header("Location: panel.php");
    exit;
}

$mesaj = "";

if(isset($_POST['kayit_ol'])) {
    $kullanici = $_POST['kullanici_adi'];
    $sifre = $_POST['sifre'];
    $sifre_tekrar = $_POST['sifre_tekrar'];
    $rol = $_POST['rol']; 

    // Güvenlik önlemi: Eğer HTML ile oynanıp farklı bir değer gönderilirse varsayılan olarak kullanıcı yap
    if($rol !== 'usta' && $rol !== 'kullanici') {
        $rol = 'kullanici';
    }

    // 1. KONTROL: Şifreler eşleşiyor mu?
    if($sifre !== $sifre_tekrar) {
        $mesaj = "<div class='lerr' style='display:block; background:rgba(255, 107, 107, 0.1); color:#ff6b6b; border: 1px solid #ff6b6b;'>⚠️ Şifreler birbiriyle eşleşmiyor!</div>";
    } else {
        // 2. KONTROL: Bu kullanıcı adı daha önce alınmış mı?
        $sorgu = $db->prepare("SELECT * FROM yoneticiler WHERE kullanici_adi = ?");
        $sorgu->execute([$kullanici]);
        
        if($sorgu->rowCount() > 0) {
            $mesaj = "<div class='lerr' style='display:block; background:rgba(255, 107, 107, 0.1); color:#ff6b6b; border: 1px solid #ff6b6b;'>⚠️ Bu kullanıcı adı zaten kullanılıyor. Lütfen başka bir ad seçin.</div>";
        } else {
            // YENİ GÜVENLİK ADIMI: Şifreyi geri döndürülemez şekilde şifreliyoruz (Hashing)
            $sifre_hash = password_hash($sifre, PASSWORD_DEFAULT);

            // 3. İŞLEM: Her şey tamamsa veritabanına kaydet 
            $ekle = $db->prepare("INSERT INTO yoneticiler (kullanici_adi, sifre, rol) VALUES (?, ?, ?)");
            // Düz $sifre yerine şifrelenmiş olan $sifre_hash değişkenini gönderiyoruz
            $basarili = $ekle->execute([$kullanici, $sifre_hash, $rol]); 
            
            if($basarili) {
                $mesaj = "<div class='lerr' style='display:block; background:rgba(37, 211, 102, 0.1); color:#25D366; border: 1px solid #25D366;'>✅ Kayıt başarılı! <br><br><a href='login.php' class='bli' style='text-align:center; padding:10px; display:block;'>Hemen Giriş Yap</a></div>";
            }
        }
    }
}
?>

<div class="lwrap">
  <div class="lbox">
    <div class="lhd">
      <div class="lrdl"></div>
      <div class="ltitle">KAYIT OL</div>
      <div class="lsub">BaşkentParça Sistemine Katıl</div>
    </div>

    <form method="POST" action="">
      <div class="fg">
        <label class="fl">Hesap Türü</label>
        <select class="fi" name="rol" required style="cursor: pointer;">
            <option value="kullanici">Müşteri (Parça Satın Alacağım)</option>
            <option value="usta">Usta (Parça Satacağım)</option>
        </select>
      </div>

      <div class="fg">
        <label class="fl">Kullanıcı Adı</label>
        <input class="fi" type="text" name="kullanici_adi" placeholder="Örn: ahmetusta" required>
      </div>
      <div class="fg">
        <label class="fl">Şifre</label>
        <input class="fi" type="password" name="sifre" placeholder="••••••••" required>
      </div>
      <div class="fg">
        <label class="fl">Şifre (Tekrar)</label>
        <input class="fi" type="password" name="sifre_tekrar" placeholder="••••••••" required>
      </div>
      
      <button type="submit" name="kayit_ol" class="bli">KAYDINI OLUŞTUR →</button>
      
      <?= $mesaj ?>

      <div style="text-align:center; margin-top:20px; font-size:14px; color:#8892b0;">
        Zaten hesabın var mı? <a href="login.php" style="color:#5ba0f7; text-decoration:none; font-weight:bold;">Giriş Yap</a>
      </div>
    </form>

  </div>
</div>

<?php include 'footer.php'; ?>