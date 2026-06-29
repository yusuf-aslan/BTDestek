# 🧠 Proje Hafızası (Gemini Context)

Bu dosya, Gemini CLI'ın proje durumunu ve geçmişini her oturumda hatırlaması için oluşturulmuştur.

## 🚀 Son Durum: v1.17 (29 Haziran 2026)
- **Talep Başarı Ekranı & Akış İyileştirmesi:** Talep başarıyla oluşturulduktan sonra kafaları karıştırmaması için doldurulan form alanı ve başlık kısmı gizlendi. Sadece üretilen talep bilgilerini içeren başarı kartı gösterildi ve altına sayfayı ilk haline sıfırlayan "Yeni Talep Ekle" butonu eklendi.
- **E-posta & Arızalı PC IP Görünürlük Kontrolleri:** 
  - Yönetici Ayarlarında E-posta veya SMTP ayarları pasif olduğunda, sadece talep formunda değil, admin paneli "Talep Düzenleme" (Edit) ekranında da e-posta alanı gizlenecek şekilde güncellendi.
  - Talep formundaki "Arızalı PC IP (Zorunlu Değil)" alanını isteğe bağlı olarak tamamen açıp kapatabilmek için Genel Ayarlar / Görünüm sekmesine bir ayar toggle'ı eklendi. Pasifleştirildiğinde hem kullanıcı formundan hem de admin paneli talep detayından gizlenir.

## 🚀 Önceki Durum: v1.16 (29 Haziran 2026)
- **Hızlı Faaliyet Modalı — UI Düzeltmesi:** Modal içindeki alanlar arası boşluklar, textarea genişliği ve buton hizalaması düzeltildi. Tüm stiller Filament admin panel CSS uyumsuzluğunu aşmak için inline CSS'e dönüştürüldü.
- **Faaliyet Yetkilendirmesi:** `accessible_modules` listesine `Faaliyetlerim` seçeneği eklendi; admin olmayan teknisyenlere de bu modül yetkisi verilebilir hale getirildi. `ActivityResource`'a `canAccess()` kontrolü eklendi.
- **Teknisyen Bazlı Gelişmiş Rapor Sayfası:** Raporlar sayfası tamamen yeniden tasarlandı:
  - Teknisyen + tarih aralığı filtresi (canlı güncelleme)
  - 5 özet kart: Çözülen Talep, Atanan Talep, Ort. Çözüm Süresi, Faaliyet Sayısı, Toplam Faaliyet Süresi
  - İki sekme: Kapatılan Talepler + Faaliyetler (faaliyet türüne göre renkli badge + toplam satırı)
  - Excel export: Talepler ve Faaliyetler ayrı sheet'lerde tek dosya (`TechnicianReportExport`)
  - Sistem geneli özet kartları (her zaman görünür)
- **Raporlar Karanlık Tema Desteği:** `<style>` bloğu + `.dark` CSS selector yöntemi ile hem aydınlık hem karanlık tema uyumlu hale getirildi (inline CSS'in tema sınırlamasını aşar).
- **Veritabanı Performans İndexleri:** `tickets` tablosuna 7 index (`status`, `assigned_to`, `resolved_by`, `created_at`, `ip_address`, bileşik indexler), `activities` tablosuna 3 index eklendi.
- **Ses Dosyası Yerelleştirildi:** TicketWatcher bildirimi için dış CDN yerine `public/sounds/notification.mp3` (yerel dosya) kullanılmaya başlandı — internet kesintisinde de çalışır.
- **Polling Süresi Optimizasyonu:** TicketWatcher kontrol aralığı 20sn → 30sn olarak güncellendi, sunucu yükü %33 azaltıldı.

## 🚀 Önceki Durum: v1.15 (26 Haziran 2026)
- **Faaliyet Yönetimi (IT Activity Log):** BT personelinin bilet dışındaki plansız faaliyetlerini (saha çalışmaları, telefon destekleri, toplantılar) kolayca kaydedebilmesi için arayüzde Header kısmına (kullanıcı profil menüsünün soluna) "Hızlı Faaliyet Ekle" modalı entegre edildi.
- **Faaliyet Raporlama & Filtreleme:** Teknisyenlerin kendi faaliyetlerini takip edebileceği, yöneticilerin ise tüm faaliyetleri görüp tarih/teknisyen bazlı filtreleyebileceği ve Excel olarak dışa aktarabileceği "Faaliyetlerim" menüsü (Resource) oluşturuldu.
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
- **SLA Sistemi:** Öncelik bazlı hedef çözüm süreleri ve aşım bildirimleri.
- **Otomatik Talep Atama:** Yeni talep gelince en az meşgul teknisyene round-robin atama.
- **Teknisyen İş Yükü Görünümü:** Atama ekranında kaç açık talep olduğu bilgisi.
- **Otomatik Yedekleme:** Scheduled (zamanlanmış) yedekleme rutinleri.

## 🛠️ Teknik Standartlar & Kurallar
- **Filament v4 Namespace:** `Action` sınıfı için her zaman `Filament\Actions\Action` kullanılmalıdır. `Filament\Forms\Components\Actions\Action` hatalıdır ve kullanılmamalıdır.
- **Form Bileşenleri:** Filament formlarında `suffixAction` veya `prefixAction` gibi bileşenlerde kullanılan `Action` sınıfı da yukarıdaki kurala tabidir.
- **Veri Güvenliği:** IP adresi gibi hassas veriler kopyalanırken kullanıcıya bildirim verilmelidir.
