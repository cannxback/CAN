<?php 
include 'header.php'; 

// Eğer zaten giriş yapılmışsa, rolüne göre doğru sayfaya yönlendirir
if(isset($_SESSION['admin_giris'])) {
    if($_SESSION['rol'] == 'usta') {
        header("Location: panel.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$hata_mesaji = "";

// Form gönderildiğinde çalışacak PHP kodları
if(isset($_POST['giris_yap'])) {
    $kullanici = $_POST['kullanici_adi'];
    $sifre = $_POST['sifre'];

    // YENİ GÜVENLİK ADIMI: Önce sadece kullanıcı adını arıyoruz (Şifreyi SQL'de aramıyoruz)
    $sorgu = $db->prepare("SELECT * FROM yoneticiler WHERE kullanici_adi = :k_adi");
    $sorgu->execute(array(
        'k_adi' => $kullanici
    ));

    $yonetici = $sorgu->fetch(PDO::FETCH_ASSOC);

    // Eşleşme varsa ve girilen şifre veritabanındaki şifrelenmiş (hashlenmiş) kod ile uyumluysa
    if($yonetici && password_verify($sifre, $yonetici['sifre'])) {
        $_SESSION['admin_giris'] = true;
        $_SESSION['yonetici_adi'] = $yonetici['kullanici_adi'];
        $_SESSION['kullanici_id'] = $yonetici['id']; 
        $_SESSION['rol'] = $yonetici['rol'];         

        if($yonetici['rol'] == 'usta') {
            header("Location: panel.php"); 
        } else {
            header("Location: index.php"); 
        }
        exit;
    } else {
        $hata_mesaji = "Kullanıcı adı veya şifre hatalı!";
    }
}
?>

<div class="lwrap">
  <div class="lbox">
    <div class="lhd">
      <div class="lrdl"></div>
      <div class="ltitle">GİRİŞ YAP</div>
      <div class="lsub">BaşkentParça Müşteri ve Usta Girişi</div>
    </div>

    <form method="POST" action="">
      <div class="fg">
        <label class="fl">Kullanıcı Adı</label>
        <input class="fi" type="text" name="kullanici_adi" placeholder="Kullanıcı adınız" required>
      </div>
      <div class="fg">
        <label class="fl">Şifre</label>
        <input class="fi" type="password" name="sifre" placeholder="••••••••" required>
      </div>
      
      <button type="submit" name="giris_yap" class="bli">GİRİŞ YAP →</button>
      
      <?php if($hata_mesaji != ""): ?>
      <div class="lerr" style="display:block;">
        ⚠️ <?= $hata_mesaji ?>
      </div>
      <?php endif; ?>
    </form>

  </div>
</div>

<?php include 'footer.php'; ?>