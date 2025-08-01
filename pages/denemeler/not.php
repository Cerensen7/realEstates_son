<?php
error_reporting(0);

$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$successmsg = "";
$errormsg = "";

// ADD NOTE
if ($_POST && !empty($_POST['title']) && !empty($_POST['content'])) {
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

// DELETE NOTE
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

// GET ALL NOTES
try {
    $stmt = $pdo->prepare("SELECT * FROM notes ORDER BY created_at DESC");
    $stmt->execute();
    $notes = $stmt->fetchAll();
} catch (PDOException $e) {
    $errormsg = "Hata: " . $e->getMessage();
    $notes = [];
}
?>

<?php include 'blocks/head.php'; ?>

    <div class="cyber-container">
        <div class="note-card mb-5">
            <div class="cyber-header">
                <div class="neon-title">
                    <h1 class="cyber-text mb-0">
                        <i class="bi bi-shield-lock-fill me-3 neon-icon"></i>
                        GİZLİ NOT DEFTERİ
                    </h1>
                    <p class="cyber-subtitle mt-2">Şifreli notlarınızı güvenle saklayın</p>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if ($errormsg || $successmsg): ?>
            <div class="alert alert-<?php echo $errormsg ? 'danger' : 'success'; ?> cyber-alert mb-4">
                <i class="bi bi-<?php echo $errormsg ? 'exclamation-triangle' : 'check-circle'; ?> me-2"></i>
                <?php echo $errormsg ?: $successmsg; ?>
            </div>
        <?php endif; ?>

        <!-- Add Note Form -->
        <div class="note-card mb-5">
            <div class="card-body p-4">
                <h4 class="cyber-text mb-4 text-center">
                    <i class="bi bi-plus-circle neon-icon me-2"></i>
                    YENİ NOT EKLE
                </h4>

                <form method="POST" class="cyber-form">
                    <div class="mb-4">
                        <label class="form-label cyber-label">
                            <i class="bi bi-card-heading me-2"></i>Not Başlığı
                        </label>
                        <input type="text" name="title" class="form-control cyber-input"
                               placeholder="Başlık yazın..." required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label cyber-label">
                            <i class="bi bi-file-text me-2"></i>Not İçeriği
                        </label>
                        <textarea name="content" rows="5" class="form-control cyber-input"
                                  placeholder="Notunuzu yazın..." required></textarea>
                    </div>

                    <button type="submit" class="btn cyber-btn-primary">
                        <i class="bi bi-shield-plus me-2"></i>
                        ŞİFRELEYEREK KAYDET
                    </button>
                </form>
            </div>
        </div>

        <!-- Notes List -->
        <?php if (empty($notes)): ?>
            <div class="note-card">
                <div class="card-body text-center py-5">
                    <div class="empty-state">
                        <i class="bi bi-journal-x display-1 neon-icon mb-4"></i>
                        <h4 class="cyber-text mb-3">Henüz not yok</h4>
                        <p class="text-muted">Yukarıdaki formdan ilk şifreli notunuzu ekleyin</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($notes as $note): ?>
                <div class="note-card">
                    <div class="card-body p-4">
                        <!-- Note Header -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="cyber-text mb-0 flex-grow-1">
                                <?php echo htmlspecialchars($note['title']); ?>
                            </h5>
                            <div class="btn-group">
                                <a href="?delete_id=<?php echo $note['id']; ?>"
                                   class="btn cyber-btn-danger btn-sm"
                                   onclick="return confirm('Bu notu silmek istediğinizden emin misiniz?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Note Content -->
                        <div class="mb-3">
                            <div class="cyber-content">
                                <div class="encrypted-text">
                                    <?php echo nl2br(htmlspecialchars($note['content'])); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Note Footer -->
                        <div class="cyber-footer">
                            <small class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                <?php echo date('d.m.Y H:i', strtotime($note['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <link rel="stylesheet" href="../../css/not.css">

<?php include 'blocks/scripts.php'; ?>