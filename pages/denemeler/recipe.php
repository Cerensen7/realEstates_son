<?php

$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$succesmsg="";
$errormsg="";
// EDIT için veri getirme
if (isset($_GET['edit'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ?");
        $stmt->execute([$_GET['edit']]);
        $editRecipe = $stmt->fetch();

        if (!$editRecipe) {
            $errormsg = "Düzenlenecek tarif bulunamadı!";
        }
    } catch (PDOException $e) {
        $errormsg = "Tarif getirme hatası: ".$e->getMessage();
    }
}

// YENİ TARİF EKLEME
if($_POST && isset($_POST['add_recipe'])){
    try {
        $stmt = $pdo->prepare("INSERT INTO recipes (recipe_name,ingredients,instructions,cook_time) VALUES (?,?,?,?)");
        $result = $stmt->execute([
            $_POST['recipe_name'],
            $_POST['ingredients'],
            $_POST['instructions'],
            $_POST['cook_time']
        ]);

        if ($result) {
            $succesmsg = "Tarif başarıyla eklendi!";
            $_POST = array();
        }

    } catch (PDOException $e) {
        $errormsg = "Ekleme hatası: ".$e->getMessage();
    }
}

// TARİF GÜNCELLEME
if($_POST && isset($_POST['update_recipe'])){
    try {
        $stmt = $pdo->prepare("UPDATE recipes SET recipe_name = ?, ingredients = ?, instructions = ?, cook_time = ? WHERE id = ?");
        $result = $stmt->execute([
            $_POST['recipe_name'],
            $_POST['ingredients'],
            $_POST['instructions'],
            $_POST['cook_time'],
            $_POST['edit_id']
        ]);

        if ($result) {
            $succesmsg = "Tarif başarıyla güncellendi!";
            // Edit modundan çık
            header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
            exit;
        }

    } catch (PDOException $e) {
        $errormsg = "Güncelleme hatası: ".$e->getMessage();
    }
}

// TARİF SİLME
if (isset($_GET['delete_recipe'])) {
    $Id = $_GET['delete_recipe'];

    try {
        $stmt = $pdo->prepare("DELETE FROM recipes WHERE id = ?");
        $delete = $stmt->execute([$Id]);

        if ($stmt->rowCount() > 0) {
            $succesmsg = "Tarif başarıyla silindi!";
        } else {
            $errormsg = "Silinecek tarif bulunamadı!";
        }
    } catch (PDOException $e) {
        $errormsg = "Silme hatası: ".$e->getMessage();
    }
}

// TÜM TARİFLERİ GETİRME
try{
    $stmt = $pdo->prepare("SELECT * FROM recipes ORDER BY id DESC");
    $stmt->execute();
    $recipes = $stmt->fetchAll();
} catch (PDOException $e) {
    $errormsg = "Tarif listesi getirme hatası: ".$e->getMessage();
    $recipes=array();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Manager</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/recipe.css">
</head>
<body>
<div class="container-custom">
    <header class="header">
        <h1 class="logo">Recipe Manager</h1>
    </header>

    <?php if(!empty($succesmsg)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $succesmsg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(!empty($errormsg)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $errormsg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <h2>
            <i class="fas fa-<?= isset($_GET['edit']) ? 'edit' : 'plus-circle' ?>"></i>
            <?= isset($_GET['edit']) ? 'Tarif Düzenle' : 'Yeni Tarif Ekle' ?>
        </h2>

        <?php if(isset($_GET['edit'])): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> <strong><?= htmlspecialchars($editRecipe['recipe_name']) ?></strong> tarifini düzenliyorsunuz.
                <a href="?" class="btn btn-sm btn-outline-secondary ms-2">İptal Et</a>
            </div>
        <?php endif; ?>

        <form method="POST" class="recipe-form">
            <!-- Edit için gizli ID -->
            <?php if(isset($_GET['edit'])): ?>
                <input type="hidden" name="edit_id" value="<?= $_GET['edit'] ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><i class="fas fa-utensils"></i> Tarif Adı:</label>
                        <input type="text" name="recipe_name" class="form-control" required
                               placeholder="Tarif adını girin..."
                               value="<?= isset($_GET['edit']) && $editRecipe ? htmlspecialchars($editRecipe['recipe_name']) : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><i class="fas fa-clock"></i> Pişirme Süresi (dakika):</label>
                        <input type="number" name="cook_time" class="form-control" required
                               placeholder="30" min="1"
                               value="<?= isset($_GET['edit']) && $editRecipe ? $editRecipe['cook_time'] : '' ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-list-ul"></i> Malzemeler:</label>
                <textarea name="ingredients" class="form-control" rows="3" required
                          placeholder="Malzemeleri listeleyin (her satıra bir malzeme)..."><?= isset($_GET['edit']) && $editRecipe ? htmlspecialchars($editRecipe['ingredients']) : '' ?></textarea>
            </div>

            <div class="form-group">
                <label><i class="fas fa-clipboard-list"></i> Yapılış Talimatları:</label>
                <textarea name="instructions" class="form-control" rows="5" required
                          placeholder="Tarif yapılışını adım adım yazın..."><?= isset($_GET['edit']) && $editRecipe ? htmlspecialchars($editRecipe['instructions']) : '' ?></textarea>
            </div>

            <div class="text-center">
                <?php if(isset($_GET['edit'])): ?>
                    <button type="submit" name="update_recipe" class="btn btn-warning">
                        <i class="fas fa-save"></i> Tarifi Güncelle
                    </button>
                    <a href="?" class="btn btn-secondary ms-2">
                        <i class="fas fa-times"></i> İptal Et
                    </a>
                <?php else: ?>
                    <button type="submit" name="add_recipe" class="btn btn-custom-add">
                        <i class="fas fa-plus"></i> Tarif Ekle
                    </button>
                    <button type="reset" class="btn btn-secondary ms-2">
                        <i class="fas fa-eraser"></i> Temizle
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="cards-container">
        <?php if(empty($recipes)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> Henüz tarif eklenmemiş. İlk tarifini eklemek için yukarıdaki formu kullan!
            </div>
        <?php else: ?>
            <?php foreach($recipes as $recipe): ?>
                <div class="recipe-card <?= (isset($_GET['edit']) && $_GET['edit'] == $recipe['id']) ? 'editing' : '' ?>">
                    <div class="recipe-header">
                        <h4><?= htmlspecialchars($recipe['recipe_name']) ?></h4>
                        <div class="cook-time">
                            <i class="fas fa-clock"></i> <?= $recipe['cook_time'] ?> dk
                        </div>
                    </div>

                    <div class="recipe-content">
                        <div class="ingredients-section">
                            <h6><i class="fas fa-list-ul"></i> Malzemeler:</h6>
                            <ul class="ingredients-list">
                                <?php
                                $ingredients = explode("\n", $recipe['ingredients']);
                                foreach($ingredients as $ingredient):
                                    if(trim($ingredient)):
                                        ?>
                                        <li><?= htmlspecialchars(trim($ingredient)) ?></li>
                                    <?php
                                    endif;
                                endforeach;
                                ?>
                            </ul>
                        </div>

                        <div class="instructions-section">
                            <h6><i class="fas fa-clipboard-list"></i> Yapılışı:</h6>
                            <p class="instructions-text">
                                <?= nl2br(htmlspecialchars($recipe['instructions'])) ?>
                            </p>
                        </div>
                    </div>

                    <div class="card-actions">
                        <?php if(isset($_GET['edit']) && $_GET['edit'] == $recipe['id']): ?>
                            <span class="badge bg-warning">Düzenleniyor...</span>
                        <?php else: ?>
                            <a href="?edit=<?= $recipe['id'] ?>" class="btn-edit">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                            <a href="?delete_recipe=<?= $recipe['id'] ?>" class="btn-delete"
                               onclick="return confirm('Silmek istediğinizden emin misiniz?')">
                                <i class="fas fa-trash"></i> Sil
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>