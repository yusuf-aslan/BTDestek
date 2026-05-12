# 🧠 Proje Hafızası (Gemini Context)

Bu dosya, Gemini CLI'ın proje durumunu ve geçmişini her oturumda hatırlaması için oluşturulmuştur.

## 🚀 Son Durum: v1.13 (12 Mayıs 2026)
- **Akıllı Bildirimler:** HTTP sunucular için sesli uyarı ve otomatik sayfa yenileme desteği eklendi.
- **QR Kod Sistemi:** Envanterdeki ürünler için QR kodlu barkod yazdırma (80x50mm) özelliği getirildi.
- **Dinamik UI:** Admin paneli için Yatay ve Dikey menü seçenekleri eklendi.
- **Veri Organizasyonu:** Bilgi bankasına etiket sistemi ve gelişmiş arama entegre edildi.
- **Hiyerarşik Düzen:** Admin menüleri (Talep Yönetimi, Envanter, Ayarlar vb.) yeniden yapılandırıldı.
- **IP Kopyalama Revizyonu:** "Talep Yazılan PC IP" ve "Arızalı PC IP" alanları için ID tabanlı, HTTP/HTTPS uyumlu ve takılmayan (stop.prevent) kopyalama sistemi geliştirildi.
- **Gerçek Zamanlı Senkronizasyon:** Talepler listesi (10sn) ve düzenleme sayfası (15sn) için otomatik yenileme (polling) eklenerek ekip içi koordinasyon güçlendirildi.

## 🚀 Önceki Durum: v1.12 (12 Nisan 2026)
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
