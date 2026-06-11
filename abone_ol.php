<?php
session_start();
include 'baglan.php';

// Sisteme HTML değil, JSON (saf veri) döndüreceğimizi söylüyoruz
header('Content-Type: application/json');

if(isset($_POST['eposta'])) {
    $eposta = filter_var($_POST['eposta'], FILTER_SANITIZE_EMAIL);
    
    if(filter_var($eposta, FILTER_VALIDATE_EMAIL)) {
        try {
            $sorgu = $db->prepare("INSERT INTO aboneler (eposta) VALUES (?)");
            $sorgu->execute([$eposta]);
            
            // Başarılı durumu
            echo json_encode(["durum" => "basarili", "mesaj" => "✅ Bültene başarıyla abone oldunuz!"]);
        } catch (PDOException $e) {
            if($e->getCode() == 23000) {
                 // Çakışma durumu
                 echo json_encode(["durum" => "hata", "mesaj" => "⚠️ Bu e-posta zaten kayıtlı."]);
            } else {
                 // Diğer hatalar
                 echo json_encode(["durum" => "hata", "mesaj" => "❌ Kayıt sırasında hata oluştu."]);
            }
        }
    } else {
        echo json_encode(["durum" => "hata", "mesaj" => "⚠️ Geçerli bir e-posta girin."]);
    }
} else {
    echo json_encode(["durum" => "hata", "mesaj" => "Geçersiz istek."]);
}
?>