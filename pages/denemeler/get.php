<?php
error_reporting(0);
$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');

$kategori = $_GET['kategori'] ?? '';
$durum = $_GET['durum'] ?? '';

$sql = "SELECT * FROM getcikk WHERE 1=1";
$params = [];

if($kategori !== '') {
    $sql .= " AND kategori = ?";
    $params[] = $kategori;
}

if($durum !== '') {
    $sql .= " AND durum = ?";
    $params[] = $durum;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$urunler = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basit Filtreleme</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Ürün Filtresi</h5>

            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <select name="kategori" class="form-select">
                        <option value="">Kategori Seç</option>
                        <option value="telefon" <?= $kategori == 'telefon' ? 'selected' : '' ?>>Telefon</option>
                        <option value="laptop" <?= $kategori == 'laptop' ? 'selected' : '' ?>>Laptop</option>
                        <option value="tablet" <?= $kategori == 'tablet' ? 'selected' : '' ?>>Tablet</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <select name="durum" class="form-select">
                        <option value="">Durum Seç</option>
                        <option value="yeni" <?= $durum == 'yeni' ? 'selected' : '' ?>>Yeni</option>
                        <option value="ikinci" <?= $durum == 'ikinci' ? 'selected' : '' ?>>İkinci El</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Filtrele</button>
                    <a href="?" class="btn btn-outline-secondary">Temizle</a>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-4">
        <h6>Sonuçlar: (<?= count($urunler) ?> adet)</h6>
        <div class="list-group">
            <?php foreach($urunler as $urun): ?>
                <div class="list-group-item"><?= $urun['ad'] ?> - <?= $urun['kategori'] ?> - <?= $urun['durum'] ?></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</body>
</html>