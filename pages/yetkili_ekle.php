<?php
error_reporting(0);
$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function telefonFormat($telefon)
{
    return preg_replace('/^(5\d{2})(\d{3})(\d{4})$/', '($1) $2-$3', preg_replace('/\D/', '', $telefon));
}

if ($_POST) {
    $hata = null;

    // Şifre kontrolü
    if ($_POST['sifre'] !== $_POST['sifre_tekrar']) {
        $hata = "Şifreler eşleşmiyor!";
    }

    // Telefon validasyonu
    if (isset($_POST['telefon']) && $_POST['telefon']) {
        $telefon = preg_replace('/\D/', '', $_POST['telefon']); // Sadece rakamları al

        if (strlen($telefon) == 10 && preg_match('/^5[034568][0-9]{8}$/', $telefon)) {
            // Geçerli ise temizlenmiş halini $_POST'a geri koy
            $_POST['telefon'] = $telefon;
        } else {
            $hata = "Geçerli bir GSM numarası giriniz! (5XX XXX XXXX formatında)";
        }
    }

    // E-mail validasyonu
    //filter_var hazır fonksiyon
    if (isset($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $hata = "Geçerli e-mail adresinizi girin";
    }

    // Hata yoksa veritabanına kaydet
    if (is_null($hata)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (kullanici_adi, password, yetki, isim, soyisim, email, telefon, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            $result = $stmt->execute([
                $_POST['kullanici_adi'],
                password_hash($_POST['sifre'], PASSWORD_DEFAULT), // Şifreyi hashle güvenlik için
                $_POST['yetki_seviyesi'],
                $_POST['ad'],
                $_POST['soyad'],
                $_POST['email'],
                $_POST['telefon'],
                $_POST['durum']
            ]);

            if ($result) {
                header("Location: yetkililer.php?basarili=1");
                exit();
            }

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $hata = "Bu kullanıcı adı zaten kullanılıyor!";
            } else {
                $hata = "Kullanıcı eklenirken hata oluştu!";
            }
        }
    }
}

$yetkiler = $pdo->query("SELECT * FROM yetki_seviyeleri ORDER BY id_yetki")->fetchAll();
$subeler = $pdo->query("SELECT * FROM subeler ORDER BY id_sube")->fetchAll();
?>
<!doctype html>
<html lang="tr">
<head>
    <?php include '../blocks/head.php'; ?>
</head>
<body class="bg-light">
<div class="container-fluid p-0">
    <div class="row g-0">
        <?php include '../blocks/sidebar.php'; ?>

        <div class="col d-flex flex-column" style="margin-left: 224px; min-height: 100vh;">
            <?php include '../blocks/header.php'; ?>

            <div class="p-3 flex-grow-1">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Yeni Kullanıcı Ekle</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($hata)): ?>
                            <div class="alert alert-danger"><?= $hata ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Kullanıcı Adı</label>
                                        <input type="text" class="form-control" name="kullanici_adi"
                                               value="<?= $_POST['kullanici_adi'] ?? '' ?>"
                                               placeholder="Kullanıcı adı giriniz" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Şifre</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" name="sifre"
                                                   placeholder="Şifre giriniz" required>
                                            <button class="btn btn-outline-secondary" type="button">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Yetkili Şube</label>
                                        <select class="form-select" name="sube" required>
                                            <option value="" selected>Şube seçiniz</option>
                                            <?php foreach ($subeler as $s): ?>
                                                <option value="<?= $s['id_sube'] ?>" <?= (isset($_POST['sube']) && $_POST['sube'] == $s['id_sube']) ? 'selected' : '' ?>>
                                                    <?= $s['sube_ismi'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Kullanıcı Durumu</label>
                                        <select class="form-select" name="durum">
                                            <option value="1" <?= (!isset($_POST['durum']) || $_POST['durum'] == '1') ? 'selected' : '' ?>>
                                                Aktif
                                            </option>
                                            <option value="0" <?= (isset($_POST['durum']) && $_POST['durum'] == '0') ? 'selected' : '' ?>>
                                                Pasif
                                            </option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Şifre Tekrar</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" name="sifre_tekrar"
                                                   placeholder="Şifreyi tekrar giriniz" required>
                                            <button class="btn btn-outline-secondary" type="button">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Yetki Seviyesi</label>
                                        <select class="form-select" name="yetki_seviyesi" required>
                                            <option value="" selected>Yetki seviyesi seçiniz</option>
                                            <?php foreach ($yetkiler as $y): ?>
                                                <option value="<?= $y['id_yetki'] ?>" <?= (isset($_POST['yetki_seviyesi']) && $_POST['yetki_seviyesi'] == $y['id_yetki']) ? 'selected' : '' ?>>
                                                    <?= $y['yetki_ismi'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h6 class="mb-3">Kişisel Bilgiler</h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Ad</label>
                                        <input type="text" class="form-control" name="ad"
                                               value="<?= $_POST['ad'] ?? '' ?>"
                                               placeholder="Adınızı giriniz" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">E-posta</label>
                                        <input type="email" class="form-control" name="email"
                                               value="<?= $_POST['email'] ?? '' ?>"
                                               placeholder="E-posta adresinizi giriniz" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Soyad</label>
                                        <input type="text" class="form-control" name="soyad"
                                               value="<?= $_POST['soyad'] ?? '' ?>"
                                               placeholder="Soyadınızı giriniz" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Cep Telefonu</label>
                                        <div class="input-group">
                                            <span class="input-group-text">+90</span>
                                            <input type="tel" class="form-control" name="telefon"
                                                   value="<?= isset($_POST['telefon']) ? telefonFormat($_POST['telefon']) : '' ?>"
                                                   placeholder="5XX XXX XXXX" required>
                                        </div>
                                        <div class="form-text">Örnek: 532 123 4567</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="/realEstate/pages/yetkililer.php" class="btn btn-outline-secondary">İptal</a>
                                <button type="submit" class="btn btn-primary">Kullanıcıyı Kaydet</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php include '../blocks/footer.php'; ?>
        </div>

    </div>
</div>

<?php include '../blocks/scripts.php'; ?>
</body>
</html>