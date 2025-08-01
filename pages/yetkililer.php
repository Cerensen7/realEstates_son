<?php
$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');

$aktiflik = $_GET['aktiflik'] ?? '';
$yetkiSeviyesi = $_GET['yetkiSeviyesi'] ?? '';
$aramaTerimi = $_GET['aramaTerimi'] ?? '';

$sql = "SELECT u.*, y.yetki_ismi FROM users u LEFT JOIN yetki_seviyeleri y ON u.yetki = y.id_yetki WHERE 1=1";
$params = [];

if($aktiflik == 'aktif') {
    $sql .= " AND u.status = 1";
}
if($aktiflik == 'pasif') {
    $sql .= " AND u.status = 0";
}

if($yetkiSeviyesi == 'sekreter') {
    $sql .= " AND u.yetki = 0";
}
if($yetkiSeviyesi == 'satış personeli') {
    $sql .= " AND u.yetki = 1";
}
if($yetkiSeviyesi == 'müdür') {
    $sql .= " AND u.yetki = 2";
}
if($yetkiSeviyesi == 'patron') {
    $sql .= " AND u.yetki = 3";
}

if($aramaTerimi != '') {
    $sql .= " AND (u.isim LIKE ? OR u.soyisim LIKE ? OR u.email LIKE ?)";
    $search = '%' . $aramaTerimi . '%';
    $params[] = $search;
    $params[] = $search;
    $params[] = $search;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();
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
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Toplam <?= count($users) ?> kullanıcı</span>
                            <div>
                                <button class="btn btn-outline-secondary btn-sm me-2" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                                    <i class="bi bi-funnel"></i> Filtre
                                </button>
                                <a href="/realEstate/pages/yetkili_ekle.php" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus"></i> Yeni Kullanıcı
                                </a>
                            </div>
                        </div>

                        <div class="collapse" id="filterCollapse">
                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body">
                                    <form method="GET" action="">
                                        <div class="row g-3">

                                            <div class="col-md-4">
                                                <label class="form-label">Aktiflik Durumu</label>
                                                <select name="aktiflik" class="form-select">
                                                    <option value="">Tümü</option>
                                                    <option value="aktif" <?= ($aktiflik === 'aktif') ? 'selected' : '' ?>>Aktif</option>
                                                    <option value="pasif" <?= ($aktiflik === 'pasif') ? 'selected' : '' ?>>Pasif</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Yetki Seviyesi</label>
                                                <select name="yetkiSeviyesi" class="form-select">
                                                    <option value="">Tümü</option>
                                                    <option value="sekreter" <?= ($yetkiSeviyesi === 'sekreter') ? 'selected' : '' ?>>Sekreter</option>
                                                    <option value="satış personeli" <?= ($yetkiSeviyesi === 'satış personeli') ? 'selected' : '' ?>>Satış Personeli</option>
                                                    <option value="müdür" <?= ($yetkiSeviyesi === 'müdür') ? 'selected' : '' ?>>Müdür</option>
                                                    <option value="patron" <?= ($yetkiSeviyesi === 'patron') ? 'selected' : '' ?>>Patron</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Kullanıcı Ara</label>
                                                <input type="text" name="aramaTerimi" class="form-control"
                                                       placeholder="İsim, soyisim veya email ara..."
                                                       value="<?= $aramaTerimi ?>">
                                            </div>

                                            <div class="col-12">
                                                <div class="d-flex gap-2">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="bi bi-search"></i> Filtrele
                                                    </button>
                                                    <a href="?" class="btn btn-outline-secondary">
                                                        <i class="bi bi-x-circle"></i> Temizle
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <?php if(empty($users) && ($aktiflik !== '' || $yetkiSeviyesi !== '' || $aramaTerimi !== '')): ?>
                            <div class="alert alert-warning" role="alert">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>Sonuç bulunamadı!</strong> Filtrelenen kriterlere uygun kullanıcı bulunamadı. Lütfen filtre seçeneklerinizi değiştirip tekrar deneyin.
                            </div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                <tr>
                                    <th>Durum</th>
                                    <th>Ad Soyad</th>
                                    <th>E-posta</th>
                                    <th>Telefon</th>
                                    <th>Yetki</th>
                                    <th class="text-end">İşlemler</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(empty($users)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                            Henüz kullanıcı bulunmuyor.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $u): ?>
                                        <tr>
                                            <td>
                                                <?php if($u['status'] == 1): ?>
                                                    <span class="badge bg-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Pasif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><strong><?= $u['isim'] ?> <?= $u['soyisim'] ?></strong></td>
                                            <td><span class="text-muted"><?= $u['email'] ?></span></td>
                                            <td><span class="text-muted"><?= $u['telefon'] ?></span></td>
                                            <td>
                                                <?php
                                                if($u['yetki'] == 0) echo '<span class="badge bg-secondary">Sekreter</span>';
                                                if($u['yetki'] == 1) echo '<span class="badge bg-info">Satış Personeli</span>';
                                                if($u['yetki'] == 2) echo '<span class="badge bg-warning text-dark">Müdür</span>';
                                                if($u['yetki'] == 3) echo '<span class="badge bg-danger">Patron</span>';
                                                ?>
                                            </td>
                                            <td class="text-end">
                                                <a href="/realEstate/pages/edit_yetkili.php?id=<?= $u['id_users'] ?>"
                                                   class="btn btn-primary btn-sm me-1" title="Düzenle">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <div class="btn-group dropleft">
                                                    <button class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item text-danger"
                                                               href="/realEstate/pages/delete_yetkili.php?id=<?= $u['id_users'] ?>"
                                                               onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">
                                                                <i class="bi bi-trash"></i> Sil
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
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