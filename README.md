#  Real Estate Management System

Bu proje, PHP ve MySQL ile geliştirilmiş temel bir **Emlak Yönetim Sistemi**dir. Admin paneli üzerinden gayrimenkul ekleme, silme, güncelleme gibi işlemler yapılabilir. Kullanıcı arayüzü ile ziyaretçiler gayrimenkulleri görebilir.

##  Kurulum

1. Bu repoyu klonlayın veya projeyi şu dizine yerleştirin:

2. Veritabanını içeri aktarın:
- `phpMyAdmin`'i açın: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
- Yeni bir veritabanı oluşturun (örneğin: `realestate_db`)
- Projedeki `database/realestate_db.sql` dosyasını içe aktarın.

3. `config.php` dosyasından veritabanı bağlantı bilgilerini güncelleyin.

4. Projeyi tarayıcıda çalıştırın:

##  Özellikler

-  Admin panel (giriş sistemi)
-  İlan ekleme, düzenleme ve silme
-  İlan listeleme
-  Resim yükleme (galeri desteği)
-  Kategori / filtreleme
-  AdminLTE v4 tabanlı arayüz

##  Kullanılan Teknolojiler

- PHP 8+
- MySQL / phpMyAdmin
- HTML / CSS / JavaScript
- AdminLTE v4 (dashboard teması)

## Proje Yapısı
realEstate/
│
├── assets/ # CSS, JS, img dosyaları
├── components/ # Header, footer gibi tekrar eden PHP bileşenleri
├── function/ # Yardımcı fonksiyonlar
├── pages/ # Giriş, panel, detay sayfaları vb.
├── database/ # SQL dosyası veya bağlantı betikleri
├── index.php # Giriş (anasayfa)
└── README.md # Bu dosya

##  Lisans

Bu proje kişisel kullanım ve eğitim amaçlıdır. Herhangi bir açık lisans içermemektedir.

---

**Hazırlayan:** [Cerensen7](https://github.com/Cerensen7)
