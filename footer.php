</div> <footer>
    <div class="ftin">
      <div class="ftgrid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 30px;">
        
        <div>
          <div class="ftbrand">Başkent<b>Parça</b></div>
          <p class="ftdesc">Türkiye'nin BMW yedek parça platformu. Tek tıkla aradığın parçaya ulaş.</p>
        </div>
        
        <div>
          <div class="fch">Platform</div>
          <ul class="fcl">
            <li><a href="index.php">Ana Sayfa</a></li>
            <li><a href="katalog.php">Katalog</a></li>
            <li><a href="login.php">Usta Paneli</a></li>
          </ul>
        </div>

        <div>
          <div class="fch">Merkez Ofis</div>
          <ul class="fcl" style="list-style: none; padding: 0; color: #8892b0; line-height: 2;">
            <li>📍 Eryaman, Ankara</li>
            <li>📞 +90 506 815 48 48</li>
            <li>✉️ info@baskentparca.com</li>
          </ul>
        </div>

        <div>
          <div class="fch">E-Bülten</div>
          <p style="color: #8892b0; font-size: 14px; margin-bottom: 15px; line-height: 1.5;">Yeni BMW ilanlarından ve indirimlerden ilk senin haberin olsun.</p>
          
          <form id="bulten-form" style="display: flex; gap: 8px;">
            <input type="email" id="bulten-eposta" name="eposta" placeholder="E-posta adresin" required style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: white; outline: none; font-family: inherit; font-size: 14px;">
            <button type="submit" style="padding: 12px 18px; border-radius: 6px; border: none; background: #5ba0f7; color: white; font-weight: bold; cursor: pointer; transition: 0.2s;">Katıl</button>
          </form>

          <div id="bulten-mesaj" style="margin-top: 10px; font-size: 13px; font-weight: bold; display: none;"></div>
        </div>

      </div>
      
      <div class="ftbot">
        <div class="ftcopy">© 2026 BaşkentParça. Tüm hakları saklıdır.</div>
      </div>
    </div>
  </footer>

  <script>
    // 1. Menü Scroll Efekti
    window.addEventListener('scroll', () => {
      document.getElementById('nav').classList.toggle('scrolled', window.scrollY > 20);
    });

    // 2. Bülten AJAX İşlemi (Sayfa Yenilenmeden Kayıt)
    const bultenForm = document.getElementById('bulten-form');
    const bultenMesaj = document.getElementById('bulten-mesaj');

    if(bultenForm) {
      bultenForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Sayfanın yenilenmesini durdur

        const epostaVal = document.getElementById('bulten-eposta').value;
        const formData = new FormData();
        formData.append('eposta', epostaVal);

        // Arka planda abone_ol.php'ye veriyi gönder
        fetch('abone_ol.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          // Kutuyu görünür yap ve mesajı içine yaz
          bultenMesaj.style.display = 'block';
          bultenMesaj.textContent = data.mesaj;

          if(data.durum === 'basarili') {
            bultenMesaj.style.color = '#25D366'; // Başarılıysa Yeşil renk
            bultenForm.reset(); // Kutunun içini temizle
          } else {
            bultenMesaj.style.color = '#ff6b6b'; // Hataysa Kırmızı renk
          }

          // 4 saniye sonra yazıyı otomatik kaybet
          setTimeout(() => {
            bultenMesaj.style.display = 'none';
          }, 4000);
        })
        .catch(error => {
          console.error('Bülten hatası:', error);
        });
      });
    }
  </script>
</body>
</html>

      </div>
      
      <div class="ftbot">
        <div class="ftcopy">© 2026 BaşkentParça. Tüm hakları saklıdır.</div>
      </div>
    </div>
  </footer>

  <script>
    // Nav Scroll Efekti
    window.addEventListener('scroll', () => {
      document.getElementById('nav').classList.toggle('scrolled', window.scrollY > 20);
    });
  </script>
</body>
</html>