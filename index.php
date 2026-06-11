<?php include 'header.php'; ?>

<div class="hero">
  <div class="hero-glow"></div>
  <div class="hero-lines"></div>
  <div class="hcon">
    <div class="h-tag"><span class="h-tag-pulse"></span> BMW Uzman Yedek Parça Platformu</div>
    <h1 class="hh1">
      BMW'NİN<br>
      <span class="ac">RUHUNU</span>
      <span class="ac2">YAŞAT</span>
    </h1>
    <p class="h-desc">Türkiye'nin en kapsamlı BMW yedek parça pazaryeri. Usta onaylı ilanlar, gerçek parça numaraları ile kolay temin imkanı.</p>
    <div class="h-cta">
      <a href="katalog.php" class="btn-hero blue"><span>⚙</span> Parçaları Keşfet</a>
      <a href="login.php" class="btn-hero outline"><span>🔑</span> Usta Girişi</a>
    </div>
  </div>
</div>

<div class="divl"></div>

<div class="sec-showcase" style="max-width: 1200px; margin: 80px auto; padding: 0 20px;">
    <div style="text-align: center; margin-bottom: 50px;">
        <h2 style="font-family: 'Bebas Neue', sans-serif; font-size: 42px; color: #fff; letter-spacing: 1px;">YENİ EKLENEN <span style="color: #5ba0f7;">PARÇALAR</span></h2>
        <p style="color: #8892b0; font-size: 16px;">Ustalarımız tarafından sisteme eklenen son orijinal ve çıkma parçalar.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 30px;">
        <?php
        // Veritabanından son eklenen 4 ürünü çekiyoruz
        $urunSorgu = $db->query("SELECT * FROM urunler ORDER BY id DESC LIMIT 4");
        
        if($urunSorgu->rowCount() > 0) {
            while($urun = $urunSorgu->fetch(PDO::FETCH_ASSOC)) {
                
                // Durum rengini ayarlıyoruz (Sıfır ise yeşil, Çıkma ise turuncu gibi)
                $durumRenk = ($urun['durum'] == 'Sıfır') ? '#25D366' : '#f59e0b';
                
                // Resim yoksa varsayılan bir görsel atıyoruz, varsa uploads klasörü ile birleştiriyoruz
                $resim = !empty($urun['resim_yolu']) ? 'uploads/' . $urun['resim_yolu'] : 'uploads/default.jpg';
        ?>
        
        <div class="product-card" style="background: #111827; border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; overflow: hidden; transition: 0.3s; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <div style="position: relative; height: 200px; background: #1f2937;">
                <span style="position: absolute; top: 10px; right: 10px; background: <?= $durumRenk ?>; color: #000; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 12px;">
                    <?= htmlspecialchars($urun['durum'] ?? 'Belirtilmemiş') ?>
                </span>
                <img src="<?= htmlspecialchars($resim) ?>" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div style="padding: 20px;">
                <div style="color: #5ba0f7; font-size: 12px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;">
                    <?= htmlspecialchars($urun['kategori'] ?? 'Kategori Yok') ?>
                </div>
                <h3 style="color: #fff; font-size: 18px; margin: 0 0 15px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?= htmlspecialchars($urun['urun_adi']) ?>
                </h3>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #fff; font-size: 20px; font-weight: bold;">
                        <?= number_format($urun['fiyat'], 2, ',', '.') ?> ₺
                    </span>
                    <a href="detay.php?id=<?= $urun['id'] ?>" style="color: #5ba0f7; text-decoration: none; font-size: 14px; font-weight: bold; display: flex; align-items: center; gap: 5px;">
                        İncele <span>→</span>
                    </a>
                </div>
            </div>
        </div>

        <?php 
            } // While bitişi
        } else {
            echo "<div style='grid-column: 1 / -1; text-align:center; color:#8892b0; padding: 40px;'>Henüz sisteme parça eklenmemiş.</div>";
        }
        ?>
    </div>

    <div style="text-align: center; margin-top: 50px;">
        <a href="katalog.php" class="btn-hero outline" style="display: inline-block;">Tüm Kataloğu Gör →</a>
    </div>
</div>

<div class="divl"></div>

<div class="sec">
  <div class="sec-hd">
    <div class="sec-tag">NEDEN BAŞKENT PARÇA ?</div>
    <div class="sec-h">Ankara içi kolay tedarik imkanı ve <br> güvenli satış politikası</div>
  </div>
  <div class="fg">
    <div class="fc">
      <div class="fi-icon">🔐</div>
      <div class="ft">Güvenli Alım ve Satım İşlemleri</div>
      <div class="fd">Kolayca üye ol, usta panelinle parçalarını yönet veya üye olup ürün satın al.</div>
    </div>
    <div class="fc">
      <div class="fi-icon">🔩</div>
      <div class="ft">Usta Onaylı İlanlar</div>
      <div class="fd">Tüm parçalar deneyimli BMW teknisyenlerinin panelinden eklenir.</div>
    </div>
    <div class="fc">
      <div class="fi-icon">💬</div>
      <div class="ft">Hızlı İletişim & Sipariş</div>
      <div class="fd">Beğendiğiniz parçalar için ustalarla WhatsApp üzerinden tek tıkla anında iletişime geçin.</div>
    </div>
  </div>
</div>
<div class="sec-badges" style="max-width: 1200px; margin: 80px auto; padding: 0 20px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
        
        <div class="badge-card">
            <div class="badge-icon">🛡️</div>
            <h3>%100 Güvenli Ödeme</h3>
            <p>Paranız usta parçayı kargolayıp siz onaylayana kadar havuz hesabımızda güvende kalır.</p>
        </div>

        <div class="badge-card">
            <div class="badge-icon">🔧</div>
            <h3>Usta Onaylı İlanlar</h3>
            <p>Sistemdeki tüm yedek parçalar uzman BMW teknisyenleri tarafından kontrol edilerek listelenir.</p>
        </div>

        <div class="badge-card">
            <div class="badge-icon">📦</div>
            <h3>Hızlı Tedarik</h3>
            <p>Ankara içi aynı gün kurye, Türkiye'nin her yerine 24 saat içinde garantili kargo imkanı.</p>
        </div>

    </div>
</div>

<div class="sec-stats" style="background: linear-gradient(180deg, rgba(13,17,28,0) 0%, rgba(15,20,32,0.6) 100%); padding: 60px 20px; border-top: 1px solid rgba(255,255,255,0.02);">
    <div style="max-width: 1000px; margin: 0 auto; display: flex; justify-content: space-around; flex-wrap: wrap; gap: 40px; text-align: center;">
        
        <div class="stat-item">
            <div class="stat-num">3.450+</div>
            <div class="stat-label">Orijinal BMW Parçası</div>
        </div>

        <div class="stat-item">
            <div class="stat-num">180+</div>
            <div class="stat-label">Aktif Kayıtlı Usta</div>
        </div>

        <div class="stat-item">
            <div class="stat-num">%99.4</div>
            <div class="stat-label">Müşteri Memnuniyeti</div>
        </div>

    </div>
</div>
<div class="sec-reviews" style="max-width: 1200px; margin: 80px auto; padding: 0 20px;">
    <div style="text-align: center; margin-bottom: 50px;">
        <h2 style="font-family: 'Bebas Neue', sans-serif; font-size: 42px; color: #fff; letter-spacing: 1px;">MÜŞTERİ <span style="color: #5ba0f7;">YORUMLARI</span></h2>
        <p style="color: #8892b0; font-size: 16px;">BaşkentParça üzerinden güvenle alışveriş yapan BMW tutkunlarının deneyimleri.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
        
        <div class="review-card">
            <div class="stars">⭐⭐⭐⭐⭐</div>
            <p class="review-text">"E36 aracım için aylardır temiz bir stop lambası arıyordum. Sincan'dan sipariş verdim, ustalar anında kargoladı. İlandaki fotoğraflarla birebir aynı ürün geldi, sorunsuz çalışıyor."</p>
            <div class="reviewer-info">
                <div class="r-avatar">A</div>
                <div>
                    <div class="r-name">Ahmet K.</div>
                    <div class="r-car">BMW E36 Sahibi</div>
                </div>
            </div>
        </div>

        <div class="review-card">
            <div class="stars">⭐⭐⭐⭐⭐</div>
            <p class="review-text">"E60 kasamın kronik sorunları için doğru parçayı bulmak eziyetti. Buradaki uzman BMW teknisyenleri sayesinde hem doğru parçayı sipariş ettim hem de uyumluluk derdi yaşamadım. Efsane sistem."</p>
            <div class="reviewer-info">
                <div class="r-avatar">B</div>
                <div>
                    <div class="r-name">Burak Y.</div>
                    <div class="r-car">BMW E60 Sahibi</div>
                </div>
            </div>
        </div>

        <div class="review-card">
            <div class="stars">⭐⭐⭐⭐⭐</div>
            <p class="review-text">"G20 için M tampon arıyordum, piyasadaki fahiş fiyatlar yerine direkt BMW ustasından birinci elden çok daha uyguna aldım. Kargolama çok özenliydi, kutusunda en ufak çizik bile yoktu."</p>
            <div class="reviewer-info">
                <div class="r-avatar">C</div>
                <div>
                    <div class="r-name">Cem M.</div>
                    <div class="r-car">BMW G20 Sahibi</div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php include 'footer.php'; ?>