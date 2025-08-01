<?php
$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$successmsg = "";
$errormsg = "";

// ADD TASK
if ($_POST && !empty($_POST['task'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO todo (task) VALUES (?)"); // ID auto_increment, sadece task
        $result = $stmt->execute([$_POST['task']]);

        if ($result) {
            $successmsg = "Task başarıyla eklendi";
            $_POST = array();
        }
    } catch (PDOException $e) {
        $errormsg = "Hata: " . $e->getMessage();
    }
}

// DELETE TASK
if (isset($_GET['delete_id'])) { // delete_id parametresini kullan
    $Id = $_GET['delete_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM todo WHERE id = ?");
        $result = $stmt->execute([$Id]);

        if ($stmt->rowCount() > 0) {
            $successmsg = "Task başarıyla silindi";
        } else {
            $errormsg = "Silinecek task bulunamadı";
        }
    } catch (PDOException $e) {
        $errormsg = "Hata: " . $e->getMessage();
    }
}

// GET ALL TASKS
try {
    $stmt = $pdo->prepare("SELECT * FROM todo ORDER BY id DESC");
    $stmt->execute();
    $todos = $stmt->fetchAll();
} catch (PDOException $e) {
    $errormsg = "Hata: " . $e->getMessage();
    $todos = [];
}
?>

<?php include '../blocks/head.php'; ?>

    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center todo-bg">
        <div class="card shadow-lg border-0 rounded-4 todo-card">
            <div class="card-header text-center bg-gradient-pink text-white rounded-top-4 py-4">
                <h3 class="fw-bold mb-0">💖 Yapılacaklar Listesi</h3>
            </div>

            <?php if ($errormsg || $successmsg): ?>
                <div class="alert alert-<?php echo $errormsg ? 'danger' : 'success'; ?> mx-3 mt-3">
                    <?php echo $errormsg ?: $successmsg; ?>
                </div>
            <?php endif; ?>

            <ul class="list-group list-group-flush">
                <?php if (empty($todos)): ?>
                    <li class="list-group-item text-center text-muted py-4">
                        <i class="bi bi-list-task display-4 mb-3"></i>
                        <p>Henüz görev yok</p>
                    </li>
                <?php else: ?>
                    <?php foreach ($todos as $todo): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><?php echo htmlspecialchars($todo['task']); ?></span>
                            <a href="?delete_id=<?php echo $todo['id']; ?>"
                               class="btn btn-sm btn-outline-danger rounded-pill"
                               onclick="return confirm('Bu görevi silmek istediğinizden emin misiniz?')">
                                Sil
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>

            <div class="card-footer bg-light-subtle py-4">
                <form method="POST" class="d-flex flex-column flex-md-row gap-3">
                    <input type="text" name="task" class="form-control form-control-lg rounded-3"
                           placeholder="Yeni görev ekle..." required>
                    <button type="submit" class="btn btn-lg btn-gradient-pink text-white fw-semibold px-4">
                        <i class="bi bi-plus-circle me-1"></i> Ekle
                    </button>
                </form>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="../../css/todo.css">
<?php include '../blocks/scripts.php'; ?>