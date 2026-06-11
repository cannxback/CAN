<?php
ob_start();
session_start();
include 'baglan.php'; // Veritabanı köprüsü
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BaşkentParça — BMW Yedek Parça Merkezi</title>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800&family=Barlow+Condensed:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav id="nav">
    <a href="index.php" class="nav-logo">
      <div class="rdl"></div>
      <div class="logo-tx">Başkent<b>Parça</b></div>
    </a>
    
    <div class="nav-links">
      <a href="index.php" class="nl">Ana Sayfa</a>
      <a href="katalog.php" class="nl">Katalog</a>
      
      <?php if(isset($_SESSION['admin_giris']) && isset($_SESSION['rol']) && $_SESSION['rol'] == 'usta'): ?>
          <a href="parcalarim.php" class="nl">Parçalarım</a>
          <a href="parca_ekle.php" class="nl">Parça Ekle</a>
      <?php endif; ?>
    </div>

    <div class="nav-search" style="margin: 0 15px; display: flex; flex: 1; max-width: 300px;">
        <form action="katalog.php" method="GET" style="display:flex; align-items:center; width: 100%;">
            <input type="text" name="q" placeholder="Parça, kategori veya OEM ara..." style="width: 100%; padding: 8px 15px; border-radius: 20px 0 0 20px; border: 1px solid rgba(255,255,255,0.1); background: #0f1420; color: #fff; outline: none; font-family: 'Barlow', sans-serif; font-size: 14px;">
            <button type="submit" style="padding: 8px 15px; border-radius: 0 20px 20px 0; border: 1px solid #5ba0f7; background: #5ba0f7; color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.3s;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
            </button>
        </form>
    </div>

    <div class="nav-r" style="display:flex; gap:10px; align-items:center;">
        <?php if(isset($_SESSION['admin_giris'])): ?>
 <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] == 'usta'): ?>
            <a href="panel.php" class="btn-np">Usta Paneli</a>
        <?php else: ?>
            <a href="siparislerim.php" class="btn-np" style="background: transparent; border: 1px solid #a8d0ff; color: #a8d0ff;">Siparişlerim</a>
            <a href="sepet.php" class="btn-np" style="background: transparent; border: 1px solid #25D366; color: #25D366;">Sepetim</a>
        <?php endif; ?>
            <a href="cikis.php" class="btn-np" style="background: transparent; border: 1px solid #ff6b6b; color: #ff6b6b;">Çıkış</a>
            
        <?php else: ?>
            <a href="login.php" class="btn-np" style="background: transparent; border: 1px solid #5ba0f7; color: #5ba0f7;">Giriş Yap</a>
            <a href="kayit.php" class="btn-np">Kayıt Ol</a>
        <?php endif; ?>
    </div>
  </nav>

  <div class="page on" style="display:block;">