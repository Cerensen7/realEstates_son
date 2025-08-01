<?php

$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function telefonFormat($telefon) {
    return preg_replace('/^(5\d{2})(\d{3})(\d{4})$/', '($1) $2-$3', preg_replace('/\D/', '', $telefon));
}

$hata = null;
$basarili = "";

if ($_POST) {
    // Telefon validasyonu
    if (isset($_POST['telefon_numarasi']) && $_POST['telefon_numarasi']) {
        $telefon = preg_replace('/\D/', '', $_POST['telefon_numarasi']); // Sadece rakamları al

        if (strlen($telefon) == 10 && preg_match('/^5[034568][0-9]{8}$/', $telefon)) {
            // Geçerli ise temizlenmiş halini $_POST'a geri koy
            $_POST['telefon_numarasi'] = $telefon;
        } else {
            $hata = "Geçerli bir GSM numarası giriniz! (5XX XXX XXXX formatında)";
        }
    }

    // Hata yoksa veritabanına kaydet
    if (is_null($hata)) {
        $stmt = $pdo->prepare("INSERT INTO ilanlar (isPublished, idSube, idPerson, type, emlakDurumu, price, title, aciklama, il, ilce, mahalle, sokak, image, videoUrl, sahip_ad, sahip_soyad, sahip_numara, sahip_email, konumUrl, eids_number) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $result = $stmt->execute([
            $_POST['ilan_durumu'] == 'aktif' ? 1 : 0,
            $_POST['sube_seciniz'],
            1,
            $_POST['emlak_tipi'],
            $_POST['emlak_durumu'],
            $_POST['fiyat'],
            $_POST['ilan_basligi'],
            $_POST['ilan_aciklamasi'],
            $_POST['il'],
            $_POST['ilce'],
            $_POST['mahalle'],
            $_POST['sokak'],
            $_POST['image'] ?? null,
            $_POST['youtube_url'] ?? null,
            $_POST['mulk_sahibi_adi'],
            $_POST['mulk_sahibi_soyadi'],
            $_POST['telefon_numarasi'],
            $_POST['email'] ?? null,
            $_POST['google_maps_url'] ?? null,
            $_POST['eids_numarasi']
        ]);

        if ($result) {
            $basarili = "İlan başarıyla eklendi!";
        } else {
            $hata = "İlan eklenirken hata oluştu!";
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

            <?php if (isset($hata)): ?>
                <div class="alert alert-danger mx-4 mt-3" role="alert">
                    <?php echo $hata; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($basarili)): ?>
                <div class="alert alert-success mx-4 mt-3" role="alert">
                    <?php echo $basarili; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <div class="card mb-4 m-lg-4">
                    <div class="card-header">
                        <h5 class="mb-1">İlan Ayarları</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="ilan_durumu" class="form-label">İlan Durumu*</label>
                                <select class="form-select" id="ilan_durumu" name="ilan_durumu" required>
                                    <option value="aktif" <?= (!isset($_POST['ilan_durumu']) || $_POST['ilan_durumu'] == 'aktif') ? 'selected' : '' ?>>Aktif</option>
                                    <option value="pasif" <?= (isset($_POST['ilan_durumu']) && $_POST['ilan_durumu'] == 'pasif') ? 'selected' : '' ?>>Pasif</option>
                                    <option value="beklemede" <?= (isset($_POST['ilan_durumu']) && $_POST['ilan_durumu'] == 'beklemede') ? 'selected' : '' ?>>Beklemede</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="sube_seciniz" class="form-label">Şube Seçiniz*</label>
                                <select class="form-select" id="sube_seciniz" name="sube_seciniz" required>
                                    <option value="">Şube seçiniz</option>
                                    <option value="merkez" <?= (isset($_POST['sube_seciniz']) && $_POST['sube_seciniz'] == 'merkez') ? 'selected' : '' ?>>Merkez Şube</option>
                                    <option value="anadolu" <?= (isset($_POST['sube_seciniz']) && $_POST['sube_seciniz'] == 'anadolu') ? 'selected' : '' ?>>Anadolu Şube</option>
                                    <option value="batikent" <?= (isset($_POST['sube_seciniz']) && $_POST['sube_seciniz'] == 'batikent') ? 'selected' : '' ?>>Batıkent Şube</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 m-lg-4">
                    <div class="card-header">
                        <h5 class="mb-1">Ana Bilgiler</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="ilan_basligi" class="form-label">İlan Başlığı*</label>
                                <input type="text" class="form-control" id="ilan_basligi" name="ilan_basligi"
                                       value="<?= $_POST['ilan_basligi'] ?? '' ?>"
                                       placeholder="İlan başlığını giriniz" required>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="emlak_tipi" class="form-label">Emlak Tipi*</label>
                                <select class="form-select" id="emlak_tipi" name="emlak_tipi" required>
                                    <option value="">Emlak tipini seçiniz</option>
                                    <option value="daire" <?= (isset($_POST['emlak_tipi']) && $_POST['emlak_tipi'] == 'daire') ? 'selected' : '' ?>>Daire</option>
                                    <option value="villa" <?= (isset($_POST['emlak_tipi']) && $_POST['emlak_tipi'] == 'villa') ? 'selected' : '' ?>>Villa</option>
                                    <option value="arsa" <?= (isset($_POST['emlak_tipi']) && $_POST['emlak_tipi'] == 'arsa') ? 'selected' : '' ?>>Arsa</option>
                                    <option value="ofis" <?= (isset($_POST['emlak_tipi']) && $_POST['emlak_tipi'] == 'ofis') ? 'selected' : '' ?>>Ofis</option>
                                    <option value="dukkaan" <?= (isset($_POST['emlak_tipi']) && $_POST['emlak_tipi'] == 'dukkaan') ? 'selected' : '' ?>>Dükkan</option>
                                    <option value="konut" <?= (isset($_POST['emlak_tipi']) && $_POST['emlak_tipi'] == 'konut') ? 'selected' : '' ?>>Konut</option>
                                    <option value="ticari" <?= (isset($_POST['emlak_tipi']) && $_POST['emlak_tipi'] == 'ticari') ? 'selected' : '' ?>>Ticari</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="emlak_durumu" class="form-label">Emlak Durumu*</label>
                                <select class="form-select" id="emlak_durumu" name="emlak_durumu" required>
                                    <option value="">Emlak durumunu seçiniz</option>
                                    <option value="satilik" <?= (isset($_POST['emlak_durumu']) && $_POST['emlak_durumu'] == 'satilik') ? 'selected' : '' ?>>Satılık</option>
                                    <option value="kiralik" <?= (isset($_POST['emlak_durumu']) && $_POST['emlak_durumu'] == 'kiralik') ? 'selected' : '' ?>>Kiralık</option>
                                    <option value="devren" <?= (isset($_POST['emlak_durumu']) && $_POST['emlak_durumu'] == 'satıldı') ? 'selected' : '' ?>>Satıldı</option>
                                    <option value="yatirim" <?= (isset($_POST['emlak_durumu']) && $_POST['emlak_durumu'] == 'kiralandı') ? 'selected' : '' ?>>Kiralandı</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="fiyat" class="form-label">Fiyat*</label>
                                <input type="text" class="form-control" id="fiyat" name="fiyat"
                                       value="<?= $_POST['fiyat'] ?? '' ?>"
                                       placeholder="Fiyat giriniz" required>
                            </div>
                            <div class="col-md-6">
                                <label for="eids_numarasi" class="form-label">EIDS Numarası*</label>
                                <input type="text" class="form-control" id="eids_numarasi" name="eids_numarasi"
                                       value="<?= $_POST['eids_numarasi'] ?? '' ?>"
                                       placeholder="EIDS Numarasını giriniz" required>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="ilan_aciklamasi" class="form-label">İlan Açıklaması*</label>
                            <textarea class="form-control" id="ilan_aciklamasi" name="ilan_aciklamasi" rows="8" placeholder="İlan açıklamasını giriniz..."><?= $_POST['ilan_aciklamasi'] ?? '' ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 m-lg-4">
                    <div class="card-header">
                        <h5 class="mb-1">Konum Bilgileri</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="il" class="form-label">İl*</label>
                                <select class="form-select" id="il" name="il" required>
                                    <option value="">İl seçiniz</option>
                                    <option value="eskisehir" <?= (!isset($_POST['il']) || $_POST['il'] == 'eskisehir') ? 'selected' : '' ?>>Eskişehir</option>
                                    <option value="istanbul" <?= (isset($_POST['il']) && $_POST['il'] == 'istanbul') ? 'selected' : '' ?>>İstanbul</option>
                                    <option value="ankara" <?= (isset($_POST['il']) && $_POST['il'] == 'ankara') ? 'selected' : '' ?>>Ankara</option>
                                    <option value="izmir" <?= (isset($_POST['il']) && $_POST['il'] == 'izmir') ? 'selected' : '' ?>>İzmir</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="ilce" class="form-label">İlçe*</label>
                                <select class="form-select" id="ilce" name="ilce" required>
                                    <option value="">İlçe seçiniz</option>
                                    <option value="tepebaşı" <?= (isset($_POST['ilce']) && $_POST['ilce'] == 'tepebaşı') ? 'selected' : '' ?>>Tepebaşı</option>
                                    <option value="odunpazarı" <?= (isset($_POST['ilce']) && $_POST['ilce'] == 'odunpazarı') ? 'selected' : '' ?>>Odunpazarı</option>
                                    <option value="sivrihisar" <?= (isset($_POST['ilce']) && $_POST['ilce'] == 'sivrihisar') ? 'selected' : '' ?>>Sivrihisar</option>
                                    <option value="beylikova" <?= (isset($_POST['ilce']) && $_POST['ilce'] == 'beylikova') ? 'selected' : '' ?>>Beylikova</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="mahalle" class="form-label">Mahalle*</label>
                                <input type="text" class="form-control" id="mahalle" name="mahalle"
                                       value="<?= $_POST['mahalle'] ?? '' ?>"
                                       placeholder="Mahalle adını giriniz" required>
                            </div>
                            <div class="col-md-6">
                                <label for="sokak" class="form-label">Sokak*</label>
                                <input type="text" class="form-control" id="sokak" name="sokak"
                                       value="<?= $_POST['sokak'] ?? '' ?>"
                                       placeholder="Sokak adını giriniz" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="google_maps_url" class="form-label">Google Maps Konum URL'si</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-map-marker-alt text-danger"></i>
                                    </span>
                                    <input type="url" class="form-control" id="google_maps_url" name="google_maps_url"
                                           value="<?= $_POST['google_maps_url'] ?? '' ?>"
                                           placeholder="https://www.google.com/maps/place/...">
                                </div>
                                <small class="form-text text-muted">Google Maps'te konum işaretleyip URL'yi kopyalayabilirsiniz.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 m-lg-4">
                    <div class="card-header">
                        <h5 class="mb-1">Fotoğraf Yükleme</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="ana_fotograf" class="form-label">Ana Fotoğraf</label>
                            <input class="form-control" type="file" id="ana_fotograf" name="image" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label for="carousel_fotograflar" class="form-label">Carousel Fotoğraflar</label>
                            <input class="form-control" type="file" id="carousel_fotograflar" name="image_carousel[]" multiple accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="card mb-4 m-lg-4">
                    <div class="card-header">
                        <h5 class="mb-1">Video Yükleme</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="youtube_url" class="form-label">YouTube Video URL</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fab fa-youtube text-danger"></i>
                                </span>
                                <input type="url" class="form-control" id="youtube_url" name="youtube_url"
                                       value="<?= $_POST['youtube_url'] ?? '' ?>"
                                       placeholder="https://www.youtube.com/watch?v=VIDEO_ID">
                            </div>
                            <small class="form-text text-muted">Gayrimenkul tanıtım videosu varsa YouTube bağlantısını ekleyebilirsiniz.</small>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 m-lg-4">
                    <div class="card-header">
                        <h5 class="mb-1">Mülk Sahibi Bilgileri</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="mulk_sahibi_adi" class="form-label">Mülk Sahibi Adı*</label>
                                <input type="text" class="form-control" id="mulk_sahibi_adi" name="mulk_sahibi_adi"
                                       value="<?= $_POST['mulk_sahibi_adi'] ?? '' ?>"
                                       placeholder="Adı" required>
                            </div>
                            <div class="col-md-6">
                                <label for="mulk_sahibi_soyadi" class="form-label">Mülk Sahibi Soyadı*</label>
                                <input type="text" class="form-control" id="mulk_sahibi_soyadi" name="mulk_sahibi_soyadi"
                                       value="<?= $_POST['mulk_sahibi_soyadi'] ?? '' ?>"
                                       placeholder="Soyadı" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="telefon_numarasi" class="form-label">Telefon Numarası*</label>
                                <div class="input-group">
                                    <span class="input-group-text">+90</span>
                                    <input type="tel" class="form-control" id="telefon_numarasi" name="telefon_numarasi"
                                           value="<?=isset($_POST['telefon_numarasi']) ? telefonFormat($_POST['telefon_numarasi']) :''?>"
                                           placeholder="555 123 45 67" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">E-posta Adresi</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= $_POST['email'] ?? '' ?>"
                                       placeholder="ornek@email.com">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mb-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-paper-plane me-2"></i>İlanı Yayınla
                    </button>
                </div>
            </form>

            <?php include '../blocks/footer.php'; ?>
        </div>
    </div>
</div>

<?php include '../blocks/scripts.php'; ?>
</body>
</html>