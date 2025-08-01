<?php
$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');

function telefonFormat($telefon) {
    return preg_replace('/^(5\d{2})(\d{3})(\d{4})$/', '($1) $2-$3', preg_replace('/\D/', '', $telefon));
}

$userId = $_GET['id'] ?? 0;
$user = null;
$hata = "";

if ($userId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id_users = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
}

// Telefon numarası validasyonu
if (isset($_POST['telefon']) && $_POST['telefon']) {
    $telefon = preg_replace('/\D/', '', $_POST['telefon']); // Sadece rakamları al

    if(strlen($telefon) == 10 && preg_match('/^5[034568][0-9]{8}$/', $telefon)) {
        // Geçerli ise temizlenmiş halini $_POST'a geri koy
        $_POST['telefon'] = $telefon;
    } else {
        $hata = "Geçerli bir GSM numarası giriniz! (5XX XXX XXXX formatında)";
    }
}

if ($_POST && $user && !$hata) { // Hata yoksa işlem yap
    if ($_POST['sifre'] && $_POST['sifre'] != $_POST['sifre_tekrar']) {
        $hata = "Şifreler eşleşmiyor!";
    } else {
        if ($_POST['sifre']) {
            // Şifreli güncelleme ama eşleşmiyosa
            $sql = "UPDATE users SET kullanici_adi=?, password=?, yetki=?, isim=?, soyisim=?, email=?, telefon=?, status=? WHERE id_users=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $_POST['kullanici_adi'],
                password_hash($_POST['sifre'], PASSWORD_DEFAULT),
                $_POST['yetki_seviyesi'],
                $_POST['ad'],
                $_POST['soyad'],
                $_POST['email'],
                $_POST['telefon'],
                $_POST['durum'],
                $userId
            ]);
        } else {
            $sql = "UPDATE users SET kullanici_adi=?, yetki=?, isim=?, soyisim=?, email=?, telefon=?, status=? WHERE id_users=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $_POST['kullanici_adi'],
                $_POST['yetki_seviyesi'],
                $_POST['ad'],
                $_POST['soyad'],
                $_POST['email'],
                $_POST['telefon'],
                $_POST['durum'],
                $userId
            ]);
        }

        header("Location: yetkililer.php?basarili=1");
        exit();
    }
}

$yetkiler = $pdo->query("SELECT * FROM yetki_seviyeleri")->fetchAll();
$subeler = $pdo->query("SELECT * FROM subeler")->fetchAll();

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
                        <h5 class="mb-0">Kullanıcı Düzenle - <?= $user['isim'] ?> <?= $user['soyisim'] ?></h5>
                    </div>
                    <div class="card-body">
                        <?php if (!$user): ?>
                            <div class="alert alert-warning">
                                <h6>Kullanıcı Bulunamadı!</h6>
                                <p class="mb-0">Bu kullanıcı mevcut değil. <a href="yetkililer.php">Kullanıcı listesine dön</a></p>
                            </div>
                        <?php else: ?>
                            <?php if ($hata): ?>
                                <div class="alert alert-danger"><?= $hata ?></div>
                            <?php endif; ?>

                            <form method="POST" action="">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Kullanıcı Adı</label>
                                            <input type="text" class="form-control" name="kullanici_adi" value="<?= $user['kullanici_adi'] ?>" placeholder="Kullanıcı adı giriniz" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Yeni Şifre (Boş bırakılırsa değişmez)</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="sifre" placeholder="Yeni şifre (opsiyonel)">
                                                <button class="btn btn-outline-secondary" type="button">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Yetkili Şube</label>
                                            <select class="form-select" name="sube">
                                                <option>Şube seçiniz</option>
                                                <?php foreach ($subeler as $s): ?>
                                                    <option value="<?= $s['id_sube'] ?>"><?= $s['sube_ismi'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Kullanıcı Durumu</label>
                                            <select class="form-select" name="durum">
                                                <option value="1" <?= $user['status'] == 1 ? 'selected' : '' ?>>Aktif</option>
                                                <option value="0" <?= $user['status'] == 0 ? 'selected' : '' ?>>Pasif</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Yeni Şifre Tekrar</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="sifre_tekrar" placeholder="Yeni şifreyi tekrar giriniz">
                                                <button class="btn btn-outline-secondary" type="button">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Yetki Seviyesi</label>
                                            <select class="form-select" name="yetki_seviyesi">
                                                <option>Yetki seviyesi seçiniz</option>
                                                <?php foreach ($yetkiler as $y): ?>
                                                    <option value="<?= $y['id_yetki'] ?>" <?= $user['yetki'] == $y['id_yetki'] ? 'selected' : '' ?>><?= $y['yetki_ismi'] ?></option>
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
                                            <input type="text" class="form-control" name="ad" value="<?= $user['isim'] ?>" placeholder="Adınızı giriniz" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">E-posta</label>
                                            <input type="email" class="form-control" name="email" value="<?= $user['email'] ?>" placeholder="E-posta adresinizi giriniz" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Soyad</label>
                                            <input type="text" class="form-control" name="soyad" value="<?= $user['soyisim'] ?>" placeholder="Soyadınızı giriniz" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Cep Telefonu</label>
                                            <div class="input-group">
                                                <span class="input-group-text">+90</span>
                                                <input type="tel" class="form-control" name="telefon" value="<?= telefonFormat($user['telefon']) ?>" placeholder="5XX XXX XXXX" required>
                                            </div>
                                            <div class="form-text">Örnek: 532 123 4567</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="/realEstate/pages/yetkililer.php" class="btn btn-outline-secondary">İptal</a>
                                    <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
                                </div>
                            </form>
                        <?php endif; ?>
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