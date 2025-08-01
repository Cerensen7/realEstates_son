<?php

$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');

$aktiflik = isset($_GET['aktiflik']) && $_GET['aktiflik'] !== '' ? $_GET['aktiflik'] : '';
$isYeri = isset($_GET['isYeri']) && $_GET['isYeri'] !== '' ? $_GET['isYeri'] : '';
$durum = isset($_GET['durum']) && $_GET['durum'] !== '' ? $_GET['durum'] : '';
$aramaTerimi = isset($_GET['aramaTerimi']) && $_GET['aramaTerimi'] !== '' ? $_GET['aramaTerimi'] : '';

$sql = "SELECT * FROM ilanlar WHERE 1=1";
$params = [];

if($aktiflik !== '') {
    $sql .= " AND isPublished = ?";
    $params[] = ($aktiflik === 'aktif') ? 1 : 0;
}

if($isYeri !== '') {
    if($isYeri === 'isyeri') {
        $filterValue = 'İş Yeri';
    } elseif($isYeri === 'dükkan') {
        $filterValue = 'Dükkan';
    } else {
        $filterValue = ucfirst(strtolower($isYeri));
    }
    $sql .= " AND type = ?";
    $params[] = $filterValue;
}

if($durum !== '') {
    $sql .= " AND emlakDurumu = ?";
    $params[] = ucfirst(strtolower($durum));
}

if($aramaTerimi !== '') {
    $sql .= " AND title LIKE ?";
    $params[] = '%' . $aramaTerimi . '%';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ilanlar = $stmt->fetchAll();

function buildUrl($params) {
    $cleanParams = [];
    foreach($params as $key => $value) {
        if($value !== '') {
            $cleanParams[$key] = $value;
        }
    }
    return empty($cleanParams) ? '?' : '?' . http_build_query($cleanParams);
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
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Toplam <?= count($ilanlar) ?> sonuç (1-<?= count($ilanlar) ?> gösteriliyor)</span>
                            <div>
                                <button class="btn btn-outline-secondary btn-sm me-2" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                                    <i class="bi bi-funnel"></i> Filtre
                                </button>
                                <a href="/realEstate/pages/ilan_ekle.php" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus"></i> Yeni İlan
                                </a>
                            </div>
                        </div>

                        <!-- Filtre Collapse Bölümü -->
                        <div class="collapse" id="filterCollapse">
                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body">
                                    <form method="GET" action="">
                                        <div class="row g-3">
                                            <!-- Aktiflik -->
                                            <div class="col-12">
                                                <select name="aktiflik" class="form-select">
                                                    <option value="">Aktiflik</option>
                                                    <option value="aktif" <?= ($aktiflik === 'aktif') ? 'selected' : '' ?>>Aktif</option>
                                                    <option value="pasif" <?= ($aktiflik === 'pasif') ? 'selected' : '' ?>>Pasif</option>
                                                </select>
                                            </div>

                                            <!-- İş Yeri -->
                                            <div class="col-12">
                                                <select name="isYeri" class="form-select">
                                                    <option value="">İş Yeri</option>
                                                    <option value="villa" <?= ($isYeri === 'villa') ? 'selected' : '' ?>>Villa</option>
                                                    <option value="daire" <?= ($isYeri === 'daire') ? 'selected' : '' ?>>Daire</option>
                                                    <option value="arsa" <?= ($isYeri === 'arsa') ? 'selected' : '' ?>>Arsa</option>
                                                    <option value="isyeri" <?= ($isYeri === 'isyeri') ? 'selected' : '' ?>>İş Yeri</option>
                                                    <option value="ofis" <?= ($isYeri === 'ofis') ? 'selected' : '' ?>>Ofis</option>
                                                    <option value="dükkan" <?= ($isYeri === 'dükkan') ? 'selected' : '' ?>>Dükkan</option>
                                                </select>
                                            </div>

                                            <!-- Durum -->
                                            <div class="col-12">
                                                <select name="durum" class="form-select">
                                                    <option value="">Durum</option>
                                                    <option value="satılık" <?= ($durum === 'satılık') ? 'selected' : '' ?>>Satılık</option>
                                                    <option value="kiralık" <?= ($durum === 'kiralık') ? 'selected' : '' ?>>Kiralık</option>
                                                    <option value="satıldı" <?= ($durum === 'satıldı') ? 'selected' : '' ?>>Satıldı</option>
                                                    <option value="kiralandı" <?= ($durum === 'kiralandı') ? 'selected' : '' ?>>Kiralandı</option>

                                                </select>
                                            </div>


                                            <!-- İlan Başlığı Ara -->
                                            <div class="col-12">
                                                <input type="text" name="aramaTerimi" class="form-control"
                                                       placeholder="İlan Başlığı Ara" value="<?= htmlspecialchars($aramaTerimi) ?>">
                                            </div>

                                            <!-- Butonlar -->
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

                        <!-- Sonuç Durumu -->
                        <?php if(empty($ilanlar) && ($aktiflik !== '' || $isYeri !== '' || $durum !== '' || $aramaTerimi !== '')): ?>
                            <div class="alert alert-warning" role="alert">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>Sonuç bulunamadı!</strong> Filtrelenen kriterlere uygun ilan bulunamadı. Lütfen filtre seçeneklerinizi değiştirip tekrar deneyin.
                            </div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                <tr>
                                    <th>İlan</th>
                                    <th>İlan Numarası</th>
                                    <th>Başlık</th>
                                    <th>Tip</th>
                                    <th>Durum</th>
                                    <th class="text-end">İşlemler</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(!empty($ilanlar)): ?>
                                    <?php foreach ($ilanlar as $u): ?>
                                        <tr>
                                            <td>
                                                <span class="badge <?= $u['isPublished'] ? 'bg-success' : 'bg-danger' ?>">
                                                    <?= $u['isPublished'] ? 'Aktif' : 'Pasif' ?>
                                                </span>
                                            </td>
                                            <td><strong><?= $u['id'] ?></strong></td>
                                            <td><span class="text-muted"><?= htmlspecialchars($u['title']) ?></span></td>
                                            <td><span class="text-muted"><?= htmlspecialchars($u['type']) ?></span></td>
                                            <td><span class="text-muted"><?= htmlspecialchars($u['emlakDurumu']) ?></span></td>

                                            <td class="text-end">
                                                <a href="/realEstate/pages/edit_ilanlar.php?id=<?= $u['id'] ?>" class="btn btn-primary btn-sm me-1" title="Düzenle">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <div class="btn-group dropleft">
                                                    <button class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item text-danger" href="/realEstate/pages/delete_ilan.php?id=<?= $u['id'] ?>">
                                                                <i class="bi bi-trash"></i> Sil
                                                            </a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?php if($aktiflik === '' && $isYeri === '' && $durum === '' && $aramaTerimi === ''): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox"></i><br>
                                                Henüz ilan bulunmuyor.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
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