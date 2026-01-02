# 🏥 TOTM BT Destek Sistemi

Modern, hızlı ve kullanıcı dostu bir hastane Bilgi İşlem (BT) talep yönetim ve iletişim portalı.

Bu platform, personel ile Bilgi İşlem birimi arasındaki iletişimi dijitalleştirerek, arıza ve destek süreçlerini hızlandırmak, dokümante etmek ve raporlamak amacıyla geliştirilmiştir.

---

## 🚀 Öne Çıkan Özellikler

### 🖱️ Kullanıcı Portalı (Önyüz)
*   **Hızlı Destek Talebi:** Minimalist form yapısıyla saniyeler içinde talep oluşturma.
*   **Dosya Eki Desteği:** Taleplere ekran görüntüsü, log veya belge ekleyebilme.
*   **Modern Talep Sorgulama:** Sayfa yenilenmeden, şık bir pop-up (modal) üzerinden anlık durum takibi.
*   **Akıllı Duyurular:** Önemli gelişmeleri ana sayfada ve özel modal pencerelerde görme. "Okudum" işaretlenen duyuruların tekrar rahatsız etmemesi.
*   **Bilgi Bankası (Knowledge Base):** Sıkça sorulan sorular ve çözüm rehberleri için kategori bazlı makale sistemi.
*   **Dinamik Menü:** Admin panelinden yönetilebilen, alt menü (dropdown) destekli navigasyon yapısı.
*   **Görünüm Seçenekleri:** Kullanıcı tercihine göre Gece (Dark) veya Gündüz (Light) modu.

### 🛠️ Yönetim Paneli (Admin)
*   **Gerçek Zamanlı Takip:** Yeni bir talep düştüğünde sekme başlığında, yan menüde ve ekran bildirimlerinde anlık uyarı.
*   **Gelişmiş Filtreleme:** Talepleri durum (Bekleyen, Çözülen, İptal), kategori, öncelik ve tarih aralığına göre süzebilme.
*   **Modül & Kategori Bazlı Yetki:** Personellere sadece belirli kategorileri veya modülleri (Duyurular, Ayarlar vb.) görme yetkisi tanımlama.
*   **Ortam Kütüphanesi:** Yüklenen tüm dosyaların merkezi yönetimi, önizlemesi ve indirilmesi.
*   **Hazır Cevaplar (Canned Responses):** Sık kullanılan çözüm metinlerini şablon olarak kaydedip taleplere tek tıkla uygulama.
*   **Mesai Saatleri Yönetimi:** Mesai saati dışı veya hafta sonu talep alımını kısıtlayabilme.

---

## 🛠️ Teknik Altyapı

*   **Framework:** Laravel 12 (PHP 8.2+)
*   **Admin Panel:** Filament 4 (Modern, PHP-Native yapı)
*   **Reaktif Bileşenler:** Livewire 3 & Alpine.js
*   **Tasarım:** Tailwind CSS
*   **Veritabanı:** MySQL / PostgreSQL / SQLite desteği
*   **Önbellekleme:** Performans için Cache entegrasyonu (Menüler ve Ayarlar için)

---

## 📦 Kurulum

1. Depoyu klonlayın: `git clone <repo-url>`
2. Bağımlılıkları yükleyin: `composer install` ve `npm install`
3. Çevre değişkenlerini ayarlayın: `.env.example` dosyasını `.env` olarak kopyalayın.
4. Veritabanını oluşturun ve migrationları çalıştırın: `php artisan migrate`
5. Depolama linkini oluşturun: `php artisan storage:link`
6. Uygulamayı ayağa kaldırın: `php artisan serve`

---

## 📅 Güncel Durum (Son Güncellemeler)
*   **[BUGÜN]** Dinamik menü yönetim sistemi eklendi.
*   **[BUGÜN]** Ortam kütüphanesi ve merkezi dosya yönetimi kuruldu.
*   **[BUGÜN]** Gerçek zamanlı bildirim ve canlı talep sayacı entegre edildi.
*   **[DÜN]** Kategori bazlı erişim kontrolü ve mesai saatleri kısıtlaması getirildi.
*   **[DÜN]** Bilgi Bankası (Knowledge Base) modülü tamamlandı.

---
*Bu proje sürekli geliştirilmekte olup, gün sonlarında README dosyası güncellenmektedir.*