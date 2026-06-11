<?php 
include 'header.php'; 

// Filtreleri al
$seri_filtre = $_GET['seri'] ?? '';
$durum_filtre = $_GET['durum'] ?? '';
$arama = $_GET['q'] ?? '';

// Dinamik SQL Sorgusu oluştur
$sql = "SELECT * FROM urunler WHERE 1=1";
$params = [];

if($arama) { $sql .= " AND (urun_adi LIKE ? OR kategori LIKE ?)"; $params[] = "%$arama%"; $params[] = "%$arama%"; }
if($seri_filtre) { $sql .= " AND seri = ?"; $params[] = $seri_filtre; }
if($durum_filtre) { $sql .= " AND durum = ?"; $params[] = $durum_filtre; }

$sql .= " ORDER BY id DESC";
$sorgu = $db->prepare($sql);
$sorgu->execute($params);
$parcalar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="kbody" style="display: flex; gap: 30px; max-width: 1200px; margin: 40px auto; padding: 0 20px;">
    
    <!-- SOL FİLTRE MENÜSÜ -->
    <aside style="width: 250px; background: rgba(255,255,255,0.02); padding: 20px; border-radius: 12px; height: fit-content;">
        <h3 style="color: #5ba0f7; margin-top:0;">FİLTRELER</h3>
        <form method="GET" action="katalog.php">
            <label style="display:block; margin: 15px 0 5px; color:#8892b0;">BMW Serisi</label>
            <select name="seri" class="fsel2" onchange="this.form.submit()">
                <option value="">Tümü</option>
                <option value="1 Serisi" <?= $seri_filtre=='1 Serisi'?'selected':'' ?>>1 Serisi</option>
                <option value="3 Serisi" <?= $seri_filtre=='3 Serisi'?'selected':'' ?>>3 Serisi</option>
                <option value="5 Serisi" <?= $seri_filtre=='5 Serisi'?'selected':'' ?>>5 Serisi</option>
            </select>

            <label style="display:block; margin: 15px 0 5px; color:#8892b0;">Durum</label>
            <select name="durum" class="fsel2" onchange="this.form.submit()">
                <option value="">Tümü</option>
                <option value="Sıfır" <?= $durum_filtre=='Sıfır'?'selected':'' ?>>Sıfır</option>
                <option value="Çıkma" <?= $durum_filtre=='Çıkma'?'selected':'' ?>>Çıkma</option>
            </select>
            
            <a href="katalog.php" style="display:block; margin-top:20px; color:#ff6b6b; font-size:12px; text-decoration:none;">Filtreleri Temizle</a>
        </form>
    </aside>

    <!-- KATALOG LİSTESİ -->
    <div style="flex: 1;">
        <div class="cgrid" id="kgrid" style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));">
            <?php foreach($parcalar as $parca): ?>
            <a href="detay.php?id=<?= $parca['id'] ?>" class="ccard">
              <div class="ccimg"><img src="uploads/<?= $parca['resim_yolu'] ?>" style="width:100%; height:100%; object-fit:cover;"></div>
              <div class="ccbdy">
                <div class="cccat"><?= $parca['kategori'] ?></div>
                <div class="ccname"><?= $parca['urun_adi'] ?></div>
                <div class="ccprice"><?= number_format($parca['fiyat'], 2, ',', '.') ?> ₺</div>
              </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>