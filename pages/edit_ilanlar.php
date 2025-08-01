<?php

$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$emlakid = $_GET['id'] ?? 0;
$emlak = null;
$hata = "";
$basarili = "";

if ($emlakid > 0) {
    $stmt = $pdo->prepare("SELECT * FROM ilanlar WHERE id = ?");
    $stmt->execute([$emlakid]);
    $emlak = $stmt->fetch();

    if (!$emlak) {
        $hata = "İlan bulunamadı!";
    }
} else {
    $hata = "Geçersiz ilan ID!";
}

if ($_POST && $emlak) {
    $hata = null;

    // Telefon validasyonu
    if (isset($_POST['telefon_numarasi']) && $_POST['telefon_numarasi']) {
        $telefon = preg_replace('/\D/', '', $_POST['telefon_numarasi']); // Sadece rakamları al

        if(strlen($telefon) == 10 && preg_match('/^5[034568][0-9]{8}$/', $telefon)) {
            // Geçerli ise temizlenmiş halini $_POST'a geri koy
            $_POST['telefon_numarasi'] = $telefon;
        } else {
            $hata = "Geçerli bir GSM numarası giriniz! (5XX XXX XXXX formatında)";
        }
    }

    // Hata yoksa güncelle
    if (is_null($hata)) {
        try {
            $sql = "UPDATE ilanlar SET  isPublished = ?, idSube = ?,  idPerson = ?, type = ?, emlakDurumu = ?, price = ?,title = ?, aciklama = ?, il = ?, ilce = ?, 
                mahalle = ?, 
                sokak = ?, 
                videoUrl = ?, 
                sahip_ad = ?, 
                sahip_soyad = ?, 
                sahip_numara = ?, 
                sahip_email = ?, 
                konumUrl = ?, 
                eids_number = ? 
                WHERE id = ?";

            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $_POST['ilan_durumu'] === 'aktif' ? 1 : 0,
                $_POST['sube_seciniz'],
                1, // idPerson sabit olcak
                $_POST['emlak_tipi'],
                $_POST['emlak_durumu'],
                $_POST['fiyat'],
                $_POST['ilan_basligi'],
                $_POST['ilan_aciklamasi'],
                $_POST['il'],
                $_POST['ilce'],
                $_POST['mahalle'],
                $_POST['sokak'],
                $_POST['youtube_url'] ?? '',
                $_POST['mulk_sahibi_adi'],
                $_POST['mulk_sahibi_soyadi'],
                telefonFormat($_POST['telefon_numarasi']),
                $_POST['email'] ?? '',
                $_POST['google_maps_url'] ?? '',
                $_POST['eids_numarasi'],
                $emlakid
            ]);

            if ($result) {
                $basarili = "İlan başarıyla güncellendi!";
                // Güncellenmiş veriyi tekrar alıp yakaladık
                $stmt = $pdo->prepare("SELECT * FROM ilanlar WHERE id = ?");
                $stmt->execute([$emlakid]);
                $emlak = $stmt->fetch();
            }
        } catch (PDOException $e) {
            $hata = "Güncelleme hatası: " . $e->getMessage();
        }
    }
}
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
                        <h5 class="mb-0">İlan Düzenle - <?= $emlak ? $emlak['title'] : 'İlan' ?></h5>
                    </div>
                    <div class="card-body">

                        <?php if (!$emlak): ?>
                            <div class="alert alert-warning">
                                <h6>İlan Bulunamadı!</h6>
                                <p class="mb-0">Bu ilan mevcut değil. <a href="ilanlar.php">İlan listesine dön</a></p>
                            </div>
                        <?php else: ?>

                            <?php if ($basarili): ?>
                                <div class="alert alert-success"><?= $basarili ?></div>
                            <?php endif; ?>

                            <?php if ($hata): ?>
                                <div class="alert alert-danger"><?= $hata ?></div>
                            <?php endif; ?>

                            <form method="POST" enctype="multipart/form-data">
                                <!-- İlan Ayarları -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-1">İlan Ayarları</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="ilan_durumu" class="form-label">İlan Durumu*</label>
                                                <select class="form-select" id="ilan_durumu" name="ilan_durumu"
                                                        required>
                                                    <option value="aktif" <?php echo ($emlak['isPublished'] == 1) ? 'selected' : ''; ?>>
                                                        Aktif
                                                    </option>
                                                    <option value="pasif" <?php echo ($emlak['isPublished'] == 0) ? 'selected' : ''; ?>>
                                                        Pasif
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="sube_seciniz" class="form-label">Şube Seçiniz*</label>
                                                <select class="form-select" id="sube_seciniz" name="sube_seciniz"
                                                        required>
                                                    <option value="">Şube seçiniz</option>
                                                    <option value="merkez" <?php echo ($emlak['idSube'] == 'merkez') ? 'selected' : ''; ?>>
                                                        Merkez Şube
                                                    </option>
                                                    <option value="anadolu" <?php echo ($emlak['idSube'] == 'anadolu') ? 'selected' : ''; ?>>
                                                        Anadolu Şube
                                                    </option>
                                                    <option value="batikent" <?php echo ($emlak['idSube'] == 'batikent') ? 'selected' : ''; ?>>
                                                        Batıkent Şube
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ana Bilgiler -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-1">Ana Bilgiler</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="ilan_basligi" class="form-label">İlan Başlığı*</label>
                                                <input type="text" class="form-control" id="ilan_basligi"
                                                       name="ilan_basligi"
                                                       value="<?php echo htmlspecialchars($emlak['title'] ?? ''); ?>"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="emlak_tipi" class="form-label">Emlak Tipi*</label>
                                                <select class="form-select" id="emlak_tipi" name="emlak_tipi" required>
                                                    <option value="">Emlak tipini seçiniz</option>
                                                    <option value="daire" <?php echo ($emlak['type'] == 'daire') ? 'selected' : ''; ?>>
                                                        Daire
                                                    </option>
                                                    <option value="villa" <?php echo ($emlak['type'] == 'villa') ? 'selected' : ''; ?>>
                                                        Villa
                                                    </option>
                                                    <option value="arsa" <?php echo ($emlak['type'] == 'arsa') ? 'selected' : ''; ?>>
                                                        Arsa
                                                    </option>
                                                    <option value="is_yeri" <?php echo ($emlak['type'] == 'is_yeri') ? 'selected' : ''; ?>>
                                                        İş Yeri
                                                    </option>
                                                    <option value="ofis" <?php echo ($emlak['type'] == 'ofis') ? 'selected' : ''; ?>>
                                                        Ofis
                                                    </option>
                                                    <option value="dukkaan" <?php echo ($emlak['type'] == 'dukkaan') ? 'selected' : ''; ?>>
                                                        Dükkan
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="emlak_durumu" class="form-label">Emlak Durumu*</label>
                                                <select class="form-select" id="emlak_durumu" name="emlak_durumu"
                                                        required>
                                                    <option value="">Emlak durumunu seçiniz</option>
                                                    <option value="satilik" <?php echo ($emlak['emlakDurumu'] == 'satilik') ? 'selected' : ''; ?>>
                                                        Satılık
                                                    </option>
                                                    <option value="kiralik" <?php echo ($emlak['emlakDurumu'] == 'kiralik') ? 'selected' : ''; ?>>
                                                        Kiralık
                                                    </option>
                                                    <option value="devren" <?php echo ($emlak['emlakDurumu'] == 'devren') ? 'selected' : ''; ?>>
                                                        Devren
                                                    </option>
                                                    <option value="yatirim" <?php echo ($emlak['emlakDurumu'] == 'yatirim') ? 'selected' : ''; ?>>
                                                        Yatırım
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="fiyat" class="form-label">Fiyat*</label>
                                                <input type="text" class="form-control" id="fiyat" name="fiyat"
                                                       value="<?php echo htmlspecialchars($emlak['price'] ?? ''); ?>"
                                                       required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="eids_numarasi" class="form-label">EIDS Numarası*</label>
                                                <input type="text" class="form-control" id="eids_numarasi"
                                                       name="eids_numarasi"
                                                       value="<?php echo htmlspecialchars($emlak['eids_number'] ?? ''); ?>"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <label for="ilan_aciklamasi" class="form-label">İlan Açıklaması*</label>
                                            <textarea class="form-control" id="ilan_aciklamasi" name="ilan_aciklamasi"
                                                      rows="8"
                                                      ><?php echo htmlspecialchars($emlak['aciklama'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Konum Bilgileri -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-1">Konum Bilgileri</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="il" class="form-label">İl*</label>
                                                <select class="form-select" id="il" name="il" required>
                                                    <option value="">İl seçiniz</option>
                                                    <option value="eskisehir" <?php echo ($emlak['il'] == 'eskisehir') ? 'selected' : ''; ?>>
                                                        Eskişehir
                                                    </option>
                                                    <option value="istanbul" <?php echo ($emlak['il'] == 'istanbul') ? 'selected' : ''; ?>>
                                                        İstanbul
                                                    </option>
                                                    <option value="ankara" <?php echo ($emlak['il'] == 'ankara') ? 'selected' : ''; ?>>
                                                        Ankara
                                                    </option>
                                                    <option value="izmir" <?php echo ($emlak['il'] == 'izmir') ? 'selected' : ''; ?>>
                                                        İzmir
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="ilce" class="form-label">İlçe*</label>
                                                <select class="form-select" id="ilce" name="ilce" required>
                                                    <option value="">İlçe seçiniz</option>
                                                    <option value="tepebaşı" <?php echo ($emlak['ilce'] == 'tepebaşı') ? 'selected' : ''; ?>>
                                                        Tepebaşı
                                                    </option>
                                                    <option value="odunpazarı" <?php echo ($emlak['ilce'] == 'odunpazarı') ? 'selected' : ''; ?>>
                                                        Odunpazarı
                                                    </option>
                                                    <option value="sivrihisar" <?php echo ($emlak['ilce'] == 'sivrihisar') ? 'selected' : ''; ?>>
                                                        Sivrihisar
                                                    </option>
                                                    <option value="beylikova" <?php echo ($emlak['ilce'] == 'beylikova') ? 'selected' : ''; ?>>
                                                        Beylikova
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="mahalle" class="form-label">Mahalle*</label>
                                                <input type="text" class="form-control" id="mahalle" name="mahalle"
                                                       value="<?php echo htmlspecialchars($emlak['mahalle'] ?? ''); ?>"
                                                       required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="sokak" class="form-label">Sokak*</label>
                                                <input type="text" class="form-control" id="sokak" name="sokak"
                                                       value="<?php echo htmlspecialchars($emlak['sokak'] ?? ''); ?>"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <label for="google_maps_url" class="form-label">Google Maps Konum
                                                    URL'si</label>
                                                <input type="url" class="form-control" id="google_maps_url"
                                                       name="google_maps_url"
                                                       value="<?php echo htmlspecialchars($emlak['konumUrl'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Video Yükleme -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-1">Video Yükleme</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="youtube_url" class="form-label">YouTube Video URL</label>
                                            <input type="url" class="form-control" id="youtube_url" name="youtube_url"
                                                   value="<?php echo htmlspecialchars($emlak['videoUrl'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Mülk Sahibi Bilgileri -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-1">Mülk Sahibi Bilgileri</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="mulk_sahibi_adi" class="form-label">Mülk Sahibi Adı*</label>
                                                <input type="text" class="form-control" id="mulk_sahibi_adi"
                                                       name="mulk_sahibi_adi"
                                                       value="<?php echo htmlspecialchars($emlak['sahip_ad'] ?? ''); ?>"
                                                       required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="mulk_sahibi_soyadi" class="form-label">Mülk Sahibi
                                                    Soyadı*</label>
                                                <input type="text" class="form-control" id="mulk_sahibi_soyadi"
                                                       name="mulk_sahibi_soyadi"
                                                       value="<?php echo htmlspecialchars($emlak['sahip_soyad'] ?? ''); ?>"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Cep Telefonu*</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">+90</span>
                                                    <input type="tel" class="form-control" name="telefon_numarasi"
                                                           value="<?php echo htmlspecialchars($emlak['sahip_numara'] ?? ''); ?>"
                                                           placeholder="5XX XXX XXXX" required>
                                                </div>
                                                <div class="form-text">Örnek: 532 123 4567</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="email" class="form-label">E-posta Adresi</label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                       value="<?php echo htmlspecialchars($emlak['sahip_email'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="ilanlar.php" class="btn btn-outline-secondary">İptal</a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>İlanı Güncelle
                                    </button>
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