<?php

include 'header.php';



// Eğer linkte bir ID yoksa doğrudan kataloğa geri yolla

if(!isset($_GET['id'])) {

    header("Location: katalog.php");

    exit;

}



$id = $_GET['id'];



// O ID'ye ait olan parçayı veritabanından çekiyoruz

$sorgu = $db->prepare("SELECT * FROM urunler WHERE id = ?");

$sorgu->execute([$id]);

$parca = $sorgu->fetch(PDO::FETCH_ASSOC);



// Eğer sahte bir ID girilmişse ve parça yoksa

if(!$parca) {

    echo "<div style='color:white; text-align:center; padding:10px;'><h2>Parça Bulunamadı!</h2><a href='katalog.php' style='color:#5ba0f7;'>Kataloğa Dön</a></div>";

    include 'footer.php';

    exit;

}

?>



<div id="toast-container" style="position: fixed; top: 30px; right: 30px; z-index: 99999; display: flex; flex-direction: column; gap: 10px;"></div>



<div class="pcon" style="max-width: 1000px; margin: 40px auto; display:flex; gap:40px; flex-wrap:wrap; color:white;">

   

    <div style="flex:1; min-width:300px;">

        <img src="uploads/<?= $parca['resim_yolu'] ?>" style="width:100%; border-radius:12px; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">

    </div>



    <div style="flex:1; min-width:300px;">

        <div style="color: #5ba0f7; font-size: 14px; letter-spacing: 2px; text-transform:uppercase; margin-bottom:10px;">BMW <?= $parca['seri'] ?> • <?= $parca['kategori'] ?></div>

        <h1 style="font-family: 'Bebas Neue', sans-serif; font-size: 48px; margin:0 0 10px 0;"><?= $parca['urun_adi'] ?></h1>

       

        <div style="display:flex; gap:10px; margin-bottom: 25px;">

            <span style="background: rgba(255,255,255,0.1); padding: 5px 12px; border-radius: 6px; font-size:14px;">Durum: <b><?= $parca['durum'] ?></b></span>

            <span style="background: rgba(255,255,255,0.1); padding: 5px 12px; border-radius: 6px; font-size:14px;">OEM No: <b><?= $parca['parca_no'] ?: 'Belirtilmemiş' ?></b></span>

        </div>



        <div style="font-size: 36px; color: #a8d0ff; font-weight: bold; margin-bottom: 20px;">

            <?= number_format($parca['fiyat'], 2, ',', '.') ?> ₺

        </div>



        <p style="color: #8892b0; line-height: 1.6; font-size: 16px; margin-bottom: 30px;">

            <?= $parca['aciklama'] ?: 'Bu parça için detaylı bir açıklama girilmemiştir.' ?>

        </p>



        <?php

        // WhatsApp mesajını otomatik doldurmak için urlencode kullanıyoruz

        $whatsapp_mesaj = urlencode("Merhaba, sitenizdeki '" . $parca['urun_adi'] . "' (OEM: " . $parca['parca_no'] . ") isimli parça ile ilgileniyorum.");

        ?>

       

        <div style="display:flex; gap:15px; flex-wrap:wrap; align-items:center;">

            <a href="https://wa.me/905000000000?text=<?= $whatsapp_mesaj ?>" target="_blank" style="display:inline-block; background: #25D366; color:white; padding: 15px 30px; border-radius: 8px; text-decoration:none; font-weight:bold; font-size:18px; transition: 0.2s;">

                💬 WhatsApp Sipariş

            </a>



            <?php if(!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'usta'): ?>

                <button onclick="sepeteEkleAJAX(<?= $parca['id'] ?>)" style="cursor:pointer; display:inline-block; background: #5ba0f7; color:white; padding: 15px 30px; border-radius: 8px; border:none; font-weight:bold; font-size:18px; transition: 0.2s;">

                    🛒 Sepete Ekle

                </button>

            <?php endif; ?>

        </div>

    </div>

</div>



<script>

// Dinamik ve akıcı bildirim penceresi üreten ana fonksiyon

function modernBildirimgoster(mesaj, tip = 'basari') {

    const container = document.getElementById('toast-container');

    const toast = document.createElement('div');

   

    // Tasarım stilleri doğrudan JS ile verilerek CSS çakışmaları engellendi

    toast.style.padding = '16px 28px';

    toast.style.borderRadius = '10px';

    toast.style.fontFamily = "'Barlow', sans-serif";

    toast.style.fontWeight = '600';

    toast.style.fontSize = '15px';

    toast.style.boxShadow = '0 10px 25px rgba(0,0,0,0.4)';

    toast.style.display = 'flex';

    toast.style.alignItems = 'center';

    toast.style.gap = '10px';

    toast.style.color = '#white';

    toast.style.transition = 'all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)';

    toast.style.opacity = '0';

    toast.style.transform = 'translateX(100px)';



    if (tip === 'basari') {

        toast.style.background = '#25D366';

        toast.style.color = '#fff';

        toast.innerHTML = '✅ ' + mesaj;

    } else {

        toast.style.background = '#ff6b6b';

        toast.style.color = '#fff';

        toast.innerHTML = '⚠️ ' + mesaj;

    }



    container.appendChild(toast);



    // Ekrana giriş animasyonu

    setTimeout(() => {

        toast.style.opacity = '1';

        toast.style.transform = 'translateX(0)';

    }, 50);



    // 1.5 saniye sonra otomatik kaybolma ve silinme animasyonu

    setTimeout(() => {

        toast.style.opacity = '0';

        toast.style.transform = 'translateX(100px)';

        setTimeout(() => { toast.remove(); }, 400);

    }, 2000);

}



function sepeteEkleAJAX(urunId) {

    let veriler = new FormData();

    veriler.append('islem', 'ekle');

    veriler.append('urun_id', urunId);



    fetch('sepet_api.php', {

        method: 'POST',

        body: veriler

    })

    .then(res => res.json())

    .then(data => {

        if(data.basari) {

            // BAŞARILI: Modern yeşil bildirim tetiklenir

            modernBildirimgoster(data.mesaj, 'basari');

           

            // Kullanıcı bildirimi görebilsin diye sepet yenilemesini 1 saniye geciktiriyoruz

            setTimeout(() => { location.reload(); }, 1200);

        } else {

            if(data.giris_gerekli) {

                modernBildirimgoster('Önce müşteri hesabı ile giriş yapmalısınız!', 'hata');

                setTimeout(() => { window.location.href = 'login.php'; }, 1500);

            } else {

                modernBildirimgoster(data.mesaj, 'hata');

            }

        }

    })

    .catch(err => {

        console.error("Hata:", err);

        modernBildirimgoster('Sistem bağlantısında bir hata oluştu.', 'hata');

    });

}

</script>



<?php include 'footer.php'; ?>