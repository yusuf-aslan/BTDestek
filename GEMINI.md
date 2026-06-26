# 🧠 Proje Hafızası (Gemini Context)

Bu dosya, Gemini CLI'ın proje durumunu ve geçmişini her oturumda hatırlaması için oluşturulmuştur.

## 🚀 Son Durum: v1.15 (26 Haziran 2026)
- **Faaliyet Yönetimi (IT Activity Log):** BT personelinin bilet dışındaki plansız faaliyetlerini (saha çalışmaları, telefon destekleri, toplantılar) kolayca kaydedebilmesi için arayüzde Header kısmına (arama kutusunun yanı) "Hızlı Faaliyet Ekle" modalı entegre edildi.
- **Faaliyet Raporlama & Filtreleme:** Teknisyenlerin kendi faaliyetlerini takip edebileceği, yöneticilerin ise tüm faaliyetleri görüp tarih/teknisyen bazlı filtreleyebileceği ve Excel olarak dışa aktarabileceği "Faaliyetlerim" menüsü (Resource) oluşturuldu.

## 🚀 Önceki Durum: v1.14 (26 Haziran 2026)
- **IP Tabanlı Talep Geçmişi:** Kullanıcıların talep numaralarını unutma ihtimaline karşı sorgulama alanının üstünde son 24 saat içinde o IP'den açılan biletleri listeleyen dinamik ve tek tıkla sorgulama sağlayan alan eklendi.
- **Global Genişlik & UI İyileştirmeleri:** Yönetim paneli genelinde varsayılan sayfa genişliği `screen-2xl` yapıldı. Talepler tablosunda satır eylemleri "İşlemler" dropdown butonu altında toplandı, Takip No sütunu varsayılan olarak gizlendi.
- **Çeviri Önbellek Fix:** Sütun menüsündeki Türkçe dil anahtarları cache sorunları giderildi.
- **Akıllı Bildirimler:** HTTP sunucular için sesli uyarı ve otomatik sayfa yenileme desteği eklendi.
- **QR Kod Sistemi:** Envanterdeki ürünler için QR kodlu barkod yazdırma (80x50mm) özelliği getirildi.
- **Dinamik UI:** Admin paneli için Yatay ve Dikey menü seçenekleri eklendi.
- **Veri Organizasyonu:** Bilgi bankasına etiket sistemi ve gelişmiş arama entegre edildi.
- **Hiyerarşik Düzen:** Admin menüleri (Talep Yönetimi, Envanter, Ayarlar vb.) yeniden yapılandırıldı.
- **IP Kopyalama Revizyonu:** "Talep Yazılan PC IP" ve "Arızalı PC IP" alanları için ID tabanlı, HTTP/HTTPS uyumlu ve takılmayan (stop.prevent) kopyalama sistemi geliştirildi.
- **Gerçek Zamanlı Senkronizasyon:** Talepler listesi (10sn) ve düzenleme sayfası (15sn) için otomatik yenileme (polling) eklenerek ekip içi koordinasyon güçlendirildi.
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

## 🛠️ Teknik Standartlar & Kurallar
- **Filament v4 Namespace:** `Action` sınıfı için her zaman `Filament\Actions\Action` kullanılmalıdır. `Filament\Forms\Components\Actions\Action` hatalıdır ve kullanılmamalıdır.
- **Form Bileşenleri:** Filament formlarında `suffixAction` veya `prefixAction` gibi bileşenlerde kullanılan `Action` sınıfı da yukarıdaki kurala tabidir.
- **Veri Güvenliği:** IP adresi gibi hassas veriler kopyalanırken kullanıcıya bildirim verilmelidir.
