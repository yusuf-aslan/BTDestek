# 🧠 Proje Hafızası (Gemini Context)

Bu dosya, Gemini CLI'ın proje durumunu ve geçmişini her oturumda hatırlaması için oluşturulmuştur.

## 🚀 Son Durum: v1.11 (13 Mart 2026)
- **Sistem Yedekleme:** `BackupService` ile JSON tabanlı (chunked) yedekleme ve geri yükleme sistemi kuruldu.
- **Dinamik IP:** `GeneralSetting` modeli üzerinden IP gösterim pozisyonu (footer, header, fixed, hidden) ayarlanabilir hale getirildi.
- **Teknisyen Mantığı:** `Ticket` modelinde `updating` event'i ile çözümü yapan kişinin otomatik atanması ve `resolved_by` alanının doldurulması sağlandı.
- **Bildirimler:** Teknikerlere yeni talep atandığında panel içi bildirim (notifications) eklendi.
- **Filament v4:** Form şemaları (`Schema`) ve buton eylemleri (`Actions`) v4 standartlarına taşındı.

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
