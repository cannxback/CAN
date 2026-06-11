<?php 
include 'header.php'; 

if(!isset($_SESSION['admin_giris'])) { header("Location: login.php"); exit; }
?>

<div class="pcon" style="max-width: 800px; margin: 40px auto; color: white; padding: 20px;">
    <div class="ptt" style="font-size: 32px; color: #5ba0f7; margin-bottom: 20px;">💳 ÖDEME VE TESLİMAT</div>
    
    <form id="odemeForm" class="fpanel" style="background: rgba(255,255,255,0.02); padding: 30px; border-radius: 12px;">
        <label class="fl2">Teslimat Adresi *</label>
        <textarea name="adres" class="fta2" required placeholder="Sokak, Mahalle, Bina No, Şehir..."></textarea>
        
        <label class="fl2" style="margin-top:20px;">Kart Bilgileri (Simülasyon)</label>
        <div class="frow c2">
            <input class="fi3" type="text" placeholder="Kart Sahibi" required>
            <input class="fi3" type="text" placeholder="Kart Numarası" maxlength="16" required>
        </div>
        
        <button type="button" onclick="siparisiTamamla()" style="width: 100%; background: #25D366; color: white; border: none; padding: 15px; border-radius: 8px; font-weight: bold; margin-top: 20px; cursor: pointer;">
            ÖDEMEYİ YAP VE SİPARİŞİ TAMAMLA →
        </button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function siparisiTamamla() {
    let adres = document.querySelector('textarea[name="adres"]').value;
    if(adres.length < 10) { alert("Lütfen geçerli bir adres girin."); return; }

    let veriler = new FormData();
    veriler.append('islem', 'siparisi_tamamla');
    veriler.append('adres', adres);

    fetch('sepet_api.php', { method: 'POST', body: veriler })
    .then(res => res.json())
    .then(data => {
        Swal.fire({
            title: data.basari ? 'Harika!' : 'Hata!',
            text: data.mesaj,
            icon: data.basari ? 'success' : 'error',
            background: '#111827',
            color: '#ffffff'
        }).then(() => { if(data.basari) window.location.href = 'siparislerim.php'; });
    });
}
</script>
<?php include 'footer.php'; ?>