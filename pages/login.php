<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');

if ($_POST) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE kullanici_adi = ? AND password = ? AND status = 1");
    $stmt->execute([$_POST['kullanici_adi'], $_POST['sifre']]);
    if ($user = $stmt->fetch()) {
        $_SESSION['user_id'] = $user['id_users'];
        $_SESSION['kullanici_adi'] = $user['kullanici_adi'];
        header("Location: yetkililer.php");
    } else $hata = "Kullanıcı adı veya şifre hatalı!";
}

?>

<!doctype html>
<html lang="tr">
<head>
    <?php include '../blocks/head.php'; ?>
</head>
<body class="bg-light">
<div class="container mt-5 ">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card " style="margin-top: 170px">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Giriş Yap</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($hata)): ?>
                        <div class="alert alert-danger"><?= $hata ?></div>
                    <?php endif; ?>



                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Kullanıcı Adı</label>
                            <input type="text" class="form-control" name="kullanici_adi" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Şifre</label>
                            <input type="password" class="form-control" name="sifre" required>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                            <label class="form-check-label" for="flexCheckDefault">
                               Beni hatırla
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-3">Giriş Yap</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../blocks/scripts.php'; ?>
</body>
</html>