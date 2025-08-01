<?php

$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if (!empty($_POST['title']) && !isset($_POST['edit_id'])) {
    try {
        $stmt=$pdo->prepare("INSERT INTO task  (title, description,status) VALUES (?,?,?)");
        $result=$stmt->execute([
            $_POST['title'],
            $_POST['description'],
            $_POST['status']
        ]);
      if ($result) {
          echo "Task Added";
      }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    try {
        $stmt=$pdo->prepare("SELECT * FROM notes ORDER BY created_at DESC");
        $result=$stmt->execute();
        $tasks=$stmt->fetchAll();
    }catch (PDOException $e){
        echo $e->getMessage();
        $tasks=[];
    }

}

?>
<?php include '../blocks/head.php'; ?>

    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <span class="navbar-brand">
                <i class="bi bi-list-task me-2"></i>
                Basit Task Sistemi
            </span>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Basit Başlık -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h2>Görevlerim</h2>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-plus"></i>
                    Yeni Görev
                </button>
            </div>
        </div>

        <!-- Tek Status Filtresi -->
        <div class="mb-3">
            <h6>Duruma Göre Filtrele:</h6>
            <a href="?status=all" class="btn btn-outline-secondary me-2">Tümü</a>
            <a href="?status=pending" class="btn btn-outline-warning me-2">Beklemede</a>
            <a href="?status=completed" class="btn btn-outline-success">Tamamlandı</a>
        </div>

        <!-- Basit Task Listesi -->
        <div class="row">
            <!-- Örnek Task 1 -->
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Website Tasarımı</h5>
                        <p class="card-text">Ana sayfa tasarımını tamamla</p>
                        <span class="badge bg-warning">Beklemede</span>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-primary">Düzenle</button>
                            <button class="btn btn-sm btn-danger">Sil</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Örnek Task 2 -->
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Veritabanı Kurulumu</h5>
                        <p class="card-text">MySQL veritabanını ayarla</p>
                        <span class="badge bg-success">Tamamlandı</span>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-primary">Düzenle</button>
                            <button class="btn btn-sm btn-danger">Sil</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Örnek Task 3 -->
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">API Geliştirme</h5>
                        <p class="card-text">REST API endpoints oluştur</p>
                        <span class="badge bg-warning">Beklemede</span>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-primary">Düzenle</button>
                            <button class="btn btn-sm btn-danger">Sil</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Basit İstatistik -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-warning text-white text-center">
                    <div class="card-body">
                        <h3>2</h3>
                        <p>Beklemede</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white text-center">
                    <div class="card-body">
                        <h3>1</h3>
                        <p>Tamamlandı</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white text-center">
                    <div class="card-body">
                        <h3>3</h3>
                        <p>Toplam</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Basit Yeni Görev Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Yeni Görev Ekle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Görev Adı</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Açıklama</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Durum</label>
                            <select class="form-select" name="status">
                                <option value="pending">Beklemede</option>
                                <option value="completed">Tamamlandı</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-success">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include '../blocks/scripts.php'; ?>