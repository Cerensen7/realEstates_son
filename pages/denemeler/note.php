<?php
error_reporting(0);

$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$successmsg = "";
$errormsg = "";

// ADD NOTE (Yeni not ekleme)
if ($_POST && !empty($_POST['title']) && !empty($_POST['content']) && !isset($_POST['edit_id'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO notes (title, content) VALUES (?, ?)");
        $result = $stmt->execute([
            $_POST['title'],
            $_POST['content']
        ]);

        if ($result) {
            $successmsg = "Not başarıyla oluşturuldu";
            $_POST = array();
        }
    } catch (PDOException $e) {
        $errormsg = "Not oluşturulurken bir hata oldu! " . $e->getMessage();
    }
}

// EDIT NOTE (Not düzenleme)
if ($_POST && !empty($_POST['title']) && !empty($_POST['content']) && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];

    try {
        $stmt = $pdo->prepare("UPDATE notes SET title = ?, content = ? WHERE id = ?");
        $result = $stmt->execute([
            $_POST['title'],
            $_POST['content'],
            $edit_id
        ]);

        if ($result) {
            $successmsg = "Not başarıyla güncellendi";
            $_POST = array();
        }
    } catch (PDOException $e) {
        $errormsg = "Not güncellenirken bir hata oldu! " . $e->getMessage();
    }
}

// DELETE NOTE (Not silme)
if (isset($_GET['delete_id'])) {
    $Id = $_GET['delete_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ?");
        $result = $stmt->execute([$Id]);

        if ($stmt->rowCount() > 0) {
            $successmsg = "Not başarıyla silindi";
        } else {
            $errormsg = "Silinecek not bulunamadı";
        }
    } catch (PDOException $e) {
        $errormsg = "Hata: " . $e->getMessage();
    }
}

// EDIT sayfası için not bilgisini getir
$editNote = null;
if (isset($_GET['edit_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ?");
        $stmt->execute([$_GET['edit_id']]);
        $editNote = $stmt->fetch();
    } catch (PDOException $e) {
        $errormsg = "Not bulunamadı!";
    }
}

// GET ALL NOTES (Tüm notları getir)
try {
    $stmt = $pdo->prepare("SELECT * FROM notes ORDER BY created_at DESC");
    $stmt->execute();
    $notes = $stmt->fetchAll();
} catch (PDOException $e) {
    $errormsg = "Hata: " . $e->getMessage();
    $notes = [];
}
?>

<?php include '../blocks/head.php'; ?>

<body class="bg-light d-flex flex-column min-vh-100">

<div class="container my-5 d-flex flex-column align-items-center">

    <h2 class="mb-4 text-primary fw-bold">Notlarım</h2>

    <?php if ($errormsg || $successmsg): ?>
        <div class="alert alert-<?php echo $errormsg ? 'danger' : 'success'; ?> alert-dismissible fade show w-100" role="alert" style="max-width: 900px;">
            <i class="bi bi-<?php echo $errormsg ? 'exclamation-triangle' : 'check-circle'; ?> me-2"></i>
            <?php echo $errormsg ?: $successmsg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>


    <!-- Not Ekleme/Düzenleme Formu -->
    <div class="card shadow rounded-3 border-0 mb-4 w-100" style="max-width: 600px;">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-<?php echo $editNote ? 'pencil' : 'plus'; ?>-lg me-2"></i>
                <?php echo $editNote ? 'Not Düzenle' : 'Yeni Not Ekle'; ?>
            </h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <?php if ($editNote): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $editNote['id']; ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="noteTitle" class="form-label">Başlık</label>
                    <input type="text" class="form-control" name="title" id="noteTitle"
                           value="<?php echo $editNote ? ($editNote['title']) : ''; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="noteContent" class="form-label">İçerik</label>
                    <textarea class="form-control" name="content" id="noteContent" rows="4" required><?php echo $editNote ? htmlspecialchars($editNote['content']) : ''; ?></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-<?php echo $editNote ? 'primary' : 'success'; ?>">
                        <i class="bi bi-<?php echo $editNote ? 'check' : 'plus'; ?>-lg me-1"></i>
                        <?php echo $editNote ? 'Güncelle' : 'Kaydet'; ?>
                    </button>

                    <?php if ($editNote): ?>
                        <a href="?" class="btn btn-secondary">
                            <i class="bi bi-x-lg me-1"></i>
                            İptal
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Not Listesi -->
    <div id="notesList" class="row justify-content-center gx-4 gy-4 w-100" style="max-width: 900px;">

        <?php if (empty($notes)): ?>
            <!-- Boş durum -->
            <div class="col-12 text-center">
                <div class="card shadow rounded-3 border-0 p-5">
                    <i class="bi bi-journal-x display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">Henüz not yok</h4>
                    <p class="text-secondary">Yukarıdaki formdan ilk notunuzu ekleyin</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($notes as $note): ?>
                <div class="col-sm-6 col-md-4">
                    <div class="card shadow rounded-3 border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-primary fw-semibold">
                                <?php echo ($note['title']); ?>
                            </h5>
                            <p class="card-text text-secondary flex-grow-1">
                                <?php
                                $content = ($note['content']);
                                echo strlen($content) > 100 ? substr($content, 0, 100) . '...' : $content;
                                ?>
                            </p>

                            <!-- Tarih bilgisi -->
                            <small class="text-muted mb-3">
                                <i class="bi bi-calendar3 me-1"></i>
                                <?php echo date('d.m.Y H:i', strtotime($note['created_at'])); ?>
                            </small>

                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <a href="?edit_id=<?php echo $note['id']; ?>" class="btn btn-sm btn-outline-primary" title="Düzenle">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <a href="?delete_id=<?php echo $note['id']; ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   title="Sil"
                                   onclick="return confirm('Bu notu silmek istediğinizden emin misiniz?')">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>