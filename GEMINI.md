# 🧠 Proje Hafızası (Gemini Context)

Bu dosya, Gemini CLI'ın proje durumunu ve geçmişini her oturumda hatırlaması için oluşturulmuştur.

## 🚀 Son Durum: v1.12 (12 Nisan 2026)
- **Ek Yönetimi:** Admin panelinde görseller için Galeri ve Lightbox (tam boy önizleme) özelliği eklendi.
- **Sürükle-Bırak:** Talep oluşturma sayfasındaki ekler kısmına sürükle-bırak desteği kazandırıldı.
- **Hata Düzeltmeleri:** Dosya indirme işlemlerindeki 404 hatası (disk uyuşmazlığı) giderildi.
- **UI & Terminoloji:** "Bölüm/Oda" ifadesi "Bölüm" olarak güncellendi, talep başlığına yönlendirici örnekler eklendi ve buton yerleşimleri optimize edildi.
- **Sistem Bağımsızlığı:** Captive portal ve internet kesintilerinden etkilenmemek için CDN bağımlılıkları azaltıldı ve yerelleştirme adımları atıldı.

## 🛠️ Özel Dağıtım (Deployment) Süreci
Sunucuya yükleme yaparken şu adımlar izlenmelidir (Permission denied hatası için `sudo chown -R $USER:$USER .` gerekebilir):
1. `docker-compose down`
2. `git restore .`
3. `git pull origin master`
4. `docker-compose build --no-cache`
5. `chmod +x docker-entrypoint.sh`
6. `docker-compose up -d`
7. `docker-compose exec app php artisan migrate --force`
8. `docker-compose exec app php artisan optimize`
9. `docker-compose exec app php artisan filament:assets --force`
10. `docker-compose exec app php artisan cache:clear`

## 📋 Gelecek Planlar
- **Çözüm Süresi Raporları:** Tekniker bazlı ortalama çözüm süreleri (Resolution Time).
- **Otomatik Yedekleme:** Scheduled (zamanlanmış) yedekleme rutinleri.
