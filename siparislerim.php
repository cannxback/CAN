<?php 
include 'header.php'; 

// Oturum kontrolü
if(!isset($_SESSION['admin_giris'])) {
    header("Location: login.php");
    exit;
}

$kul_id = $_SESSION['kullanici_id'];

// Kullanıcının ana siparişlerini (fatura başlıklarını) çekiyoruz
$sorgu = $db->prepare("SELECT * FROM siparisler WHERE kullanici_id = ? ORDER BY id DESC");
$sorgu->execute([$kul_id]);
$siparisler = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pcon" style="max-width: 1100px; margin: 40px auto; color: white; padding: 20px;">
    <div class="ptt" style="font-family: 'Bebas Neue', sans-serif; font-size: 42px; color: #5ba0f7; margin-bottom: 10px;">📦 SİPARİŞLERİM</div>
    <div class="pts" style="color: #8892b0; margin-bottom: 30px;">Geçmiş siparişleriniz ve kargo durumları.</div>

    <?php if(count($siparisler) > 0): ?>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach($siparisler as $siparis): ?>
                
                <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; overflow: hidden;">
                    <!-- Sipariş Başlığı (Gri Alan) -->
                    <div style="background: rgba(0,0,0,0.2); padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.05); flex-wrap: wrap; gap: 15px;">
                        <div>
                            <div style="color: #8892b0; font-size: 12px; text-transform: uppercase;">Sipariş Tarihi</div>
                            <div style="font-weight: bold; font-size: 14px;"><?= date('d.m.Y H:i', strtotime($siparis['siparis_tarihi'])) ?></div>
                        </div>
                        <div>
                            <div style="color: #8892b0; font-size: 12px; text-transform: uppercase;">Sipariş Özeti</div>
                            <div style="font-weight: bold; font-size: 14px; color: #25D366;"><?= number_format($siparis['toplam_tutar'], 2, ',', '.') ?> ₺</div>
                        </div>
                        <div>
                            <div style="color: #8892b0; font-size: 12px; text-transform: uppercase;">Sipariş No</div>
                            <div style="font-weight: bold; font-size: 14px; color: #fff;">#<?= $siparis['id'] ?></div>
                        </div>
                        <div>
                            <?php 
                                // Duruma göre rozet rengi ayarlama
                                $durum_renk = '#5ba0f7'; // Varsayılan mavi (Sipariş Alındı)
                                if($siparis['durum'] == 'Kargoya Verildi') $durum_renk = '#f59e0b'; // Turuncu
                                if($siparis['durum'] == 'Teslim Edildi') $durum_renk = '#25D366'; // Yeşil
                                if($siparis['durum'] == 'İptal Edildi') $durum_renk = '#ff6b6b'; // Kırmızı
                            ?>
                            <span style="background: <?= $durum_renk ?>20; color: <?= $durum_renk ?>; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: bold; border: 1px solid <?= $durum_renk ?>40;">
                                <?= htmlspecialchars($siparis['durum']) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Sipariş Detayları (İçindeki Ürünler) -->
                    <div style="padding: 20px;">
                        <?php
                            // Bu sipariş numarasına ait parçaları çekiyoruz
                            $detaySorgu = $db->prepare("
                                SELECT d.adet, d.birim_fiyat, u.urun_adi, u.resim_yolu, u.id as urun_id 
                                FROM siparis_detay d 
                                LEFT JOIN urunler u ON d.urun_id = u.id 
                                WHERE d.siparis_id = ?
                            ");
                            $detaySorgu->execute([$siparis['id']]);
                            $detaylar = $detaySorgu->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <?php foreach($detaylar as $detay): ?>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <?php $resim = !empty($detay['resim_yolu']) ? $detay['resim_yolu'] : 'default.jpg'; ?>
                                    <img src="uploads/<?= htmlspecialchars($resim) ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                                    <div style="flex: 1;">
                                        <a href="detay.php?id=<?= $detay['urun_id'] ?>" style="color: white; text-decoration: none; font-weight: bold; font-size: 15px; display: block; margin-bottom: 4px;">
                                            <?= htmlspecialchars($detay['urun_adi']) ?>
                                        </a>
                                        <div style="color: #8892b0; font-size: 13px;"><?= $detay['adet'] ?> Adet x <?= number_format($detay['birim_fiyat'], 2, ',', '.') ?> ₺</div>
                                    </div>
                                    
                                    <!-- Gelecekte ekleyeceğimiz Puanlama Sistemi için hazırlık butonu -->
                                    <?php if($siparis['durum'] == 'Teslim Edildi'): ?>
                                        <button style="background: transparent; border: 1px solid #5ba0f7; color: #5ba0f7; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; transition: 0.2s;">Değerlendir</button>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Kargo takip numarası varsa göster -->
                        <?php if($siparis['kargo_takip']): ?>
                            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed rgba(255,255,255,0.1); font-size: 13px; color: #8892b0;">
                                🚚 Kargo Takip No: <span style="color: white; font-weight: bold;"><?= htmlspecialchars($siparis['kargo_takip']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="background: rgba(255,255,255,0.01); padding: 60px; text-align: center; border-radius: 12px; border: 1px dashed rgba(255,255,255,0.1);">
            <div style="font-size: 48px; margin-bottom: 15px;">📦</div>
            <h3 style="margin: 0 0 15px 0; color: #8892b0; font-weight: 500;">Henüz hiç sipariş vermemişsiniz.</h3>
            <a href="katalog.php" style="color: #5ba0f7; text-decoration: none; font-weight: bold; font-size: 16px;">Alışverişe Başla →</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>