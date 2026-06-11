<?php 
include 'header.php'; 

if(!isset($_SESSION['admin_giris'])) {
    header("Location: login.php");
    exit;
}

$kul_id = $_SESSION['kullanici_id'];

$sorgu = $db->prepare("
    SELECT s.id as sepet_id, s.adet, u.id as urun_id, u.urun_adi, u.fiyat, u.resim_yolu, u.seri, u.kategori, u.parca_no, u.stok 
    FROM sepet s 
    JOIN urunler u ON s.urun_id = u.id 
    WHERE s.kullanici_id = ?
    ORDER BY s.id DESC
");
$sorgu->execute([$kul_id]);
$sepet_urunler = $sorgu->fetchAll(PDO::FETCH_ASSOC);

$toplam_fiyat = 0;
foreach($sepet_urunler as $item) {
    $toplam_fiyat += $item['fiyat'] * $item['adet'];
}
?>

<div class="pcon" style="max-width: 1100px; margin: 40px auto; color: white; padding: 20px;">
    <div class="ptt" style="font-family: 'Bebas Neue', sans-serif; font-size: 42px; color: #5ba0f7; margin-bottom: 10px;">🛒 SEPETİM</div>
    <div class="pts" style="color: #8892b0; margin-bottom: 30px;">Satın almak üzere sepetine eklediğin yedek parçalar.</div>

    <?php if(count($sepet_urunler) > 0): ?>
        <div style="display: flex; gap: 30px; flex-wrap: wrap;">
            
            <div style="flex: 2; min-width: 600px;">
                <table class="ptbl">
                    <thead>
                        <tr>
                            <th>Parça Bilgisi</th>
                            <th>Adet Durumu</th>
                            <th>Fiyat</th>
                            <th>Toplam</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($sepet_urunler as $urun): ?>
                            <tr>
                                <td>
                                    <div class="tpc">
                                        <img src="uploads/<?= $urun['resim_yolu'] ?>" style="width:50px; height:50px; object-fit:cover; border-radius:8px;">
                                        <div>
                                            <div class="tpn"><?= $urun['urun_adi'] ?></div>
                                            <div class="tpno">OEM: <?= $urun['parca_no'] ?: 'Belirtilmemiş' ?></div>
                                            
                                            <?php if($urun['stok'] - $urun['adet'] == 0 || $urun['stok'] <= 1): ?>
                                                <div style="color: #ff6b6b; font-size: 12px; font-weight: bold; margin-top: 4px; background: rgba(255,107,107,0.1); display: inline-block; padding: 2px 8px; border-radius: 4px;">
                                                    ⚠️ Stokta son <?= $urun['stok'] ?> adet!
                                                </div>
                                            <?php else: ?>
                                                <div style="color: #25D366; font-size: 12px; margin-top: 4px;">Mevcut Stok: <?= $urun['stok'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                
                                <td>
                                    <div style="display:inline-flex; align-items:center; background: rgba(255,255,255,0.05); border-radius: 6px; padding: 5px;">
                                        <button onclick="sepetIslem(<?= $urun['urun_id'] ?>, 'cikar')" style="background:transparent; border:none; color:#a8d0ff; font-size:18px; cursor:pointer; padding: 0 10px; transition:0.2s;">-</button>
                                        <b style="font-size: 16px; min-width: 25px; text-align: center;"><?= $urun['adet'] ?></b>
                                        
                                        <?php if($urun['adet'] < $urun['stok']): ?>
                                            <button onclick="sepetIslem(<?= $urun['urun_id'] ?>, 'ekle')" style="background:transparent; border:none; color:#a8d0ff; font-size:18px; cursor:pointer; padding: 0 10px; transition:0.2s;">+</button>
                                        <?php else: ?>
                                            <button disabled style="background:transparent; border:none; color:#555; font-size:18px; padding: 0 10px; cursor:not-allowed;" title="Stok sınırına ulaştınız">+</button>
                                        <?php endif; ?>
                                    </div>
                                    <div style="margin-top: 8px;">
                                        <button onclick="sepetIslem(<?= $urun['urun_id'] ?>, 'sil')" style="background:transparent; border:none; color:#ff6b6b; font-size:13px; cursor:pointer; text-decoration:underline; display:flex; align-items:center; gap:5px;">
                                            🗑️ Sepetten Sil
                                        </button>
                                    </div>
                                </td>

                                <td class="tprice"><?= number_format($urun['fiyat'], 2, ',', '.') ?> ₺</td>
                                <td class="tprice" style="color: #5ba0f7;"><?= number_format($urun['fiyat'] * $urun['adet'], 2, ',', '.') ?> ₺</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="flex: 1; min-width: 300px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); padding: 25px; border-radius: 12px; height: fit-content;">
                <h3 style="margin-top: 0; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; font-family: 'Barlow Condensed', sans-serif; letter-spacing: 1px;">SİPARİŞ ÖZETİ</h3>
                <div style="display: flex; justify-content: space-between; margin: 20px 0; font-size: 18px;">
                    <span>Genel Toplam:</span>
                    <span style="color: #25D366; font-weight: bold; font-size: 24px;"><?= number_format($toplam_fiyat, 2, ',', '.') ?> ₺</span>
                </div>
               <button onclick="window.location.href='odeme.php'" style="width: 100%; background: #25D366; color: white; border: none; padding: 15px; border-radius: 8px; font-weight: bold; font-size: 16px; cursor: pointer; transition: 0.2s;">
    Siparişi Tamamla →
</button>
            </div>

        </div>
    <?php else: ?>
        <div style="background: rgba(255,255,255,0.01); padding: 60px; text-align: center; border-radius: 12px; border: 1px dashed rgba(255,255,255,0.1);">
            <div style="font-size: 48px; margin-bottom: 15px;">🛒</div>
            <h3 style="margin: 0 0 15px 0; color: #8892b0; font-weight: 500;">Sepetiniz şu anda boş.</h3>
            <a href="katalog.php" style="color: #5ba0f7; text-decoration: none; font-weight: bold; font-size: 16px;">Parçaları Keşfetmeye Başla →</a>
        </div>
    <?php endif; ?>
</div>

<!-- PROFESYONEL POP-UP İÇİN SWEETALERT2 KÜTÜPHANESİ -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function sepetIslem(urunId, islemTuru) {
    let veriler = new FormData();
    veriler.append('islem', islemTuru); 
    veriler.append('urun_id', urunId);

    fetch('sepet_api.php', {
        method: 'POST',
        body: veriler
    })
    .then(res => res.json())
    .then(data => {
        if(data.mesaj) {
            // Uyarı varsa profesyonel pop-up ile göster
            Swal.fire({
                title: data.basari ? 'Bilgi' : 'Uyarı!',
                text: data.mesaj,
                icon: data.basari ? (data.mesaj.includes('Dikkat') ? 'warning' : 'success') : 'error',
                background: '#111827',
                color: '#ffffff',
                confirmButtonColor: '#5ba0f7',
                confirmButtonText: 'Tamam',
                customClass: { popup: 'custom-swal-border' }
            }).then(() => {
                if(data.basari) location.reload(); 
            });
        } else {
            // Sadece artı/eksi yapıldıysa uyarı göstermeden sayfayı yenile
            if(data.basari) location.reload(); 
        }
    })
    .catch(err => console.error("Hata:", err));
}

function siparisiTamamla() {
    // Klasik confirm() yerine şık SweetAlert2 onay kutusu
    Swal.fire({
        title: 'Siparişi Onaylıyor musunuz?',
        text: 'Bu işlem ile seçtiğiniz parçalar stoktan düşülüp adınıza rezerve edilecektir.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#25D366',
        cancelButtonColor: '#ff6b6b',
        confirmButtonText: 'Evet, Siparişi Tamamla',
        cancelButtonText: 'İptal',
        background: '#111827',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            let veriler = new FormData();
            veriler.append('islem', 'siparisi_tamamla');

            fetch('sepet_api.php', {
                method: 'POST',
                body: veriler
            })
            .then(res => res.json())
            .then(data => {
                // İşlem sonucu bildirimi
                Swal.fire({
                    title: data.basari ? 'Başarılı!' : 'Hata!',
                    text: data.mesaj,
                    icon: data.basari ? 'success' : 'error',
                    background: '#111827',
                    color: '#ffffff',
                    confirmButtonColor: '#5ba0f7'
                }).then(() => {
                    if(data.basari) {
                        window.location.href = 'index.php'; // İşlem bittiyse ana sayfaya dön
                    }
                });
            })
            .catch(err => console.error("Hata:", err));
        }
    });
}
</script>

<style>
/* Pop-up çevresi için ince estetik detay */
.custom-swal-border {
    border: 1px solid rgba(255, 255, 255, 0.1);
}
</style>

<?php include 'footer.php'; ?>