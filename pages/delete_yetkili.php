<?php
if (!isset($_GET['id'])) {
    exit();
}
$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');

$userId = $_GET['id'];
$user = null;
$hata = "";
$basarili = "";

$stmt = $pdo->prepare("SELECT * FROM users WHERE id_users = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (empty($user)) {
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id_users = ?");
    $result = $stmt->execute([$userId]);

    if ($result) {
        $basarili = "Kullanıcı başarıyla silindi!";
        $user = null;
    } else {
        $hata = "Kullanıcı silinemedi. Kayıt bulunamadı.";
    }
} catch (Exception $e) {
    $hata = "Veritabanı hatası: " . $e->getMessage();

}

$url = "/realEstate/pages/yetkililer.php";
header('Location: ' . $url);
die();

?>
