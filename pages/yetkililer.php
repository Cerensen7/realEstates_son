<?php
$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');
$users = $pdo->query("SELECT u.*, y.yetki_ismi FROM users u LEFT JOIN yetki_seviyeleri y ON u.yetki = y.id_yetki")->fetchAll();

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

        <div class="col d-flex flex-column" style="min-height: 100vh;">
            <?php include '../blocks/header.php'; ?>
            <?php include '../blocks/breadcrumb.php'; ?>

            <div class="p-3 flex-grow-1">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Toplam <?= count($users ) ?> kullanıcı</span>
                            <div>
                                <button class="btn btn-outline-secondary btn-sm me-2"><i class="bi bi-funnel"></i> Filtre</button>
                                <a href="/realEstate/pages/yetkili_ekle.php" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Yeni Kullanıcı</a>
                            </div>
                        </div>

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
                                <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td><span class="badge <?= $u['status'] ? 'bg-success' : 'bg-danger' ?>"><?= $u['status'] ? 'Aktif' : 'Pasif' ?></span></td>
                                        <td><strong><?= $u['isim'] ?> <?= $u['soyisim'] ?></strong></td>
                                        <td><span class="text-muted"><?= $u['email'] ?></span></td>
                                        <td><span class="text-muted"><?= $u['telefon'] ?></span></td>
                                        <td><span class="badge <?= $u['yetki'] == 3 ? 'bg-danger' : ($u['yetki'] == 2 ? 'bg-warning text-dark' : 'bg-info text-dark') ?>"><?= $u['yetki_ismi'] ?: 'Bilinmeyen' ?></span></td>
                                        <td class="text-end">
                                            <a href="/realEstate/pages/edit_yetkili.php?id=<?= $u['id_users'] ?>" class="btn btn-primary btn-sm me-1" title="Düzenle">
                                                <i class="bi bi-pencil"></i>
                                            </a>




                                            <div class="btn-group dropleft">
                                                <button class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item text-danger" href="/realEstate/pages/delete_yetkili.php?id=<?= $u['id_users'] ?>"><i class="bi bi-trash"></i> Sil
                                                        </a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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


