<?php

$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'tümü';

if ($filter === 'tümü') {
    $stmt = $pdo->prepare("SELECT * FROM students");
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE class = ?");
    $stmt->execute([$filter]);
}

$students = $stmt->fetchAll();

?>
<?php include '../blocks/head.php'; ?>
    <body>
<nav class="navbar navbar-dark bg-primary">
    <div class="container">
            <span class="navbar-brand mb-0 h1">
                <i class="bi bi-mortarboard-fill me-2"></i>
                Öğrenci Yönetim Sistemi
            </span>
    </div>
</nav>

<div class="container my-5">
    <div class="text-center mb-4">
        <h2>Sınıfa Göre Filtrele</h2>

        <div class="mt-3">
            <a href="?filter=tümü" class="btn btn-outline-primary me-2 mb-2 <?= ($filter == 'tümü') ? 'active' : '' ?>">Tüm Öğrenciler</a>
            <a href="?filter=9A" class="btn btn-outline-primary me-2 mb-2 <?= ($filter == '9A') ? 'active' : '' ?>">9A</a>
            <a href="?filter=9B" class="btn btn-outline-primary me-2 mb-2 <?= ($filter == '9B') ? 'active' : '' ?>">9B</a>
            <a href="?filter=10A" class="btn btn-outline-primary me-2 mb-2 <?= ($filter == '10A') ? 'active' : '' ?>">10A</a>
            <a href="?filter=10B" class="btn btn-outline-primary me-2 mb-2 <?= ($filter == '10B') ? 'active' : '' ?>">10B</a>
        </div>
    </div>

    <div class="row">
        <?php foreach ($students as $student): ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= $student['name'] ?></h5>
                        <p class="card-text">
                            <strong>Sınıf:</strong> <?= $student['class'] ?><br>
                            <strong>Yaş:</strong> <?= $student['age'] ?><br>
                            <strong>Email:</strong> <?= $student['email'] ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../blocks/scripts.php'; ?>