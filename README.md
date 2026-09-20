# SymCity

Symfony tabanlı şehir rehberi ve yerel yaşam platformudur. Kullanıcılar şehirdeki yerleri, etkinlikleri, iş yerlerini, ilanları ve haberleri görüntüleyebilir; ayrıca kayıt, giriş ve kişisel profil işlemleri yapabilir.

## Proje Hakkında

SymCity, Symfony 4.x üzerine kurulmuş bir şehir rehberi uygulamasıdır. Uygulama içinde aşağıdaki ana bileşenler yer almaktadır:

- Şehirdeki mekanlar ve gezilecek yerler
- Etkinlik ve duyuru yönetimi
- İşletme ve kategori bazlı katalog
- Reklam ve kategori yönetimi
- Arama ve filtreleme
- Kullanıcı kaydı / giriş / profil işlemleri
- Yönetim tarafı işlemleri için Symfony Controller ve Entity yapısı

## Teknolojiler

- PHP 7.1+
- Symfony 4.4
- Doctrine ORM
- Twig
- MySQL / MariaDB
- Bootstrap
- Webpack Encore
- jQuery, Datatables, Select2, SweetAlert2
- PHPUnit

## Özellikler

- Şehir rehberi ve turistik yer listeleri
- Yer / mekan detay sayfaları
- Etkinlik ve duyuru yayınlama
- İşletme, kategori ve alt kategori yönetimi
- Reklam ilanları ve arama alanları
- Kullanıcı doğrulama ve güvenlik
- AJAX tabanlı veri işlemleri
- Modern frontend bileşenleri ve statik kaynak yönetimi

## Proje Yapısı

```text
SymCity/
├── assets/                # Frontend kaynak dosyaları
├── bin/                   # Symfony CLI yardımcı betikler
├── config/                # Symfony konfigürasyonları
├── public/                # Web erişimi olan dosyalar
├── src/                   # Uygulama kodları
│   ├── Controller/        # HTTP controller'lar
│   ├── Entity/            # Doctrine entity'leri
│   ├── Form/              # Form sınıfları
│   ├── Repository/        # Repository sınıfları
│   ├── Security/          # Güvenlik bileşenleri
│   └── ...
├── templates/             # Twig şablonları
├── tests/                 # Testler
├── translations/          # Çeviri dosyaları
├── composer.json          # PHP bağımlılıklar
├── package.json           # Frontend bağımlılıkları
├── phpunit.xml.dist       # PHPUnit ayarları
├── webpack.config.js      # Webpack yapılandırması
├── .env                   # Çevre değişkenleri
├── README.md              # Bu dosya
└── ...
```

## Gereksinimler

Aşağıdaki araçların sistemde yüklü olması gerekir:

- PHP 7.1.3 veya üzeri
- Composer
- Node.js ve npm / yarn
- MySQL veya MariaDB
- Symfony CLI (opsiyonel ama önerilir)

## Kurulum

1. Depoyu klonlayın:

```bash
git clone https://github.com/taner-dll/SymCity.git
cd SymCity
```

2. PHP bağımlılıklarını yükleyin:

```bash
composer install
```

3. Frontend bağımlılıklarını yükleyin:

```bash
npm install
# veya
yarn install
```

4. Çevre dosyasını oluşturup düzenleyin:

```bash
cp .env .env.local
```

`.env.local` dosyasında veritabanı bilgilerini ve ortam ayarlarını güncelleyin:

```env
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"
```

5. Veritabanını oluşturup migrasyonları çalıştırın:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

6. Uygulamayı başlatın:

```bash
php bin/console server:run
```

veya yerel geliştirme sunucusu için:

```bash
symfony server:start
```

Uygulama varsayılan olarak `http://127.0.0.1:8000` adresinde çalışacaktır.

## Geliştirme

Assets derleme işlemleri için:

```bash
npm run dev
```

Üretim için:

```bash
npm run build
```

Test çalıştırmak için:

```bash
php bin/phpunit
```

## Varsayılan Rota

Proje kök rotasında ana sayfa yönlendirmesi bulunmaktadır. `config/routes.yaml` içinde temel rota tanımları yer alır:

```yaml
homepage:
  path: /
  controller: App\Controller\WebSiteController:index
```

Bu yapı, uygulamanın ana sayfa kontrolcüsü üzerinden şehir rehberi arayüzünü açmasını sağlar.

## Katkıda Bulunma

1. Fork oluşturun.
2. Yeni bir branch açın.
3. Değişikliklerinizi yapın.
4. Commit atın.
5. Pull request oluşturun.

## Lisans

Bu proje için açık bir lisans dosyası belirtilmemiştir. Kullanmadan önce proje sahibiyle lisans durumunu kontrol etmeniz önerilir.

## Notlar

Bu repository, Symfony tabanlı bir yerel şehir rehberi projesidir. Proje yapısı, controller'lar, entity'ler ve template'ler üzerinden genişletilebilir. Özellikle şehir yönetimi, içerik ekleme, ilan ve etkinlik modülleri için geliştirmeler yapılabilir.

---

Geliştirmeniz için faydalı olması amacıyla hazırlanan bu README, projeyi daha kolay incelemeye ve çalıştırmaya yardımcı olacaktır.
