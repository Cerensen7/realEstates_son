<?php
if (!isset($_GET['id'])) {
    exit();
}
$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');

$hata = "";
$basarili = "";

$ilanID = $_GET['id'];
$ilan = null;


$stmt = $pdo->prepare("SELECT * FROM ilanlar WHERE id=? ");
$stmt->execute([$ilanID]);
$ilan = $stmt->fetch();

if (empty($ilan)) {
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM ilanlar WHERE id=?");
    $result = $stmt->execute([$ilanID]);

    if ($result) {
        $basarili = "İlan başarıyla silindi";
        $ilan = null;

    } else {
        $hata = "İlan silinemedi!";
    }

} catch (Exception $e) {
    $hata = "Silinemedi! :" . $e->getMessage();
}
$url = "http://localhost/realEstate/pages/ilanlar.php";
header("Location:" . $url);
die();
?>