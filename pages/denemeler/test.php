<?php
error_reporting(0);

$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_POST){
    $errormsg = null;
    $successmsg = null;

// Telefon validasyonu ve temizleme
    if (isset($_POST['telefon']) && !empty($_POST['telefon'])) {
        $_POST['telefon'] = preg_replace('/[^0-9]/', '', $_POST['telefon']); // TEMİZLE
        if (!preg_match('/^0[0-9]{10}$/', $_POST['telefon'])) {
            $errormsg = "Geçerli telefon numarası girin";
        }
    }
    if(isset($_POST['E-posta']) && !filter_var($_POST['E-posta'], FILTER_VALIDATE_EMAIL)){
        $errormsg = "Geçerli Email adresi giriniz";
    }

    if(empty($_POST['ad']) || empty($_POST['soyad']) || empty($_POST['E-posta'])){
        $errormsg = "Lütfen tüm zorunlu alanları doldurun";
    }

    if (is_null($errormsg)){
        try {

            $stmt = $pdo->prepare("INSERT INTO test (isim, soyisim, email, telefon, cinsiyet) VALUES (?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $_POST['ad'],
                $_POST['soyad'],
                $_POST['E-posta'],
                $_POST['telefon'] ?? null,
                $_POST['cinsiyet'] ?? null
            ]);


            if ($result) {
                $successmsg="Form başarıyla gönderildi ";
                $_POST = array();

            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errormsg = "Bu form daha önce gönderildi!";
            } else {
                $errormsg = "Form gönderilirken hata oluştu! Hata: " . $e->getMessage();
            }
        }
    }
}
?>

<?php include '../blocks/head.php'; ?>

    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(195deg, #667eea 0%, #764ba2 100%);">
        <div class="card shadow-lg border-0 rounded-4" style="max-width: 900px; width: 100%;">
            <div class="card-header bg-transparent border-0 text-center py-4">
                <h2 class="card-title mb-2 fw-bold text-primary">Bilgi Formu</h2>
                <p class="text-muted mb-0">Lütfen aşağıdaki bilgileri doldurun</p>
            </div>

            <div class="card-body p-5">
                <?php if ($errormsg || $successmsg): ?>
                    <div class="alert alert-<?php echo $errormsg ? 'danger' : 'success'; ?> mb-4">
                        <?php echo $errormsg ?: $successmsg; ?>
                    </div>
                <?php endif; ?>

                <form class="row g-4 needs-validation" method="POST" novalidate>
                    <div class="col-md-6 position-relative">
                        <label for="validationTooltip01" class="form-label fw-semibold">Ad</label>
                        <input type="text" name="ad" class="form-control form-control-lg border-2 rounded-3" id="validationTooltip01" value="<?php echo htmlspecialchars($_POST['ad'] ?? ''); ?>" required>
                        <div class="valid-tooltip">
                            Harika görünüyor!
                        </div>
                    </div>

                    <div class="col-md-6 position-relative">
                        <label for="validationTooltip02" class="form-label fw-semibold">Soyad</label>
                        <input type="text" name="soyad" class="form-control form-control-lg border-2 rounded-3" id="validationTooltip02" value="<?php echo htmlspecialchars($_POST['soyad'] ?? ''); ?>" required>
                        <div class="valid-tooltip">
                            Harika görünüyor!
                        </div>
                    </div>

                    <div class="col-md-6 position-relative">
                        <label for="telefon" class="form-label fw-semibold">Telefon</label>
                        <input type="tel" name="telefon" class="form-control form-control-lg border-2 rounded-3" placeholder="0555-123-4567" value="<?php echo htmlspecialchars($_POST['telefon'] ?? ''); ?>">
                        <small class="text-muted">Format: 0xxx-xxx-xxxx</small>
                    </div>


                    <div class="col-md-6 position-relative">
                        <label class="form-label fw-semibold">Cinsiyet (İsteğe bağlı)</label>
                        <div class="d-flex gap-4 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="cinsiyet" value="Erkek" id="erkek" <?php echo (isset($_POST['cinsiyet']) && $_POST['cinsiyet'] == 'Erkek') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="erkek">Erkek</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="cinsiyet" value="Kadın" id="kadin" <?php echo (isset($_POST['cinsiyet']) && $_POST['cinsiyet'] == 'Kadın') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="kadin">Kadın</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="cinsiyet" value="Belirtmek istemiyorum" id="belirtmem" <?php echo (isset($_POST['cinsiyet']) && $_POST['cinsiyet'] == 'Belirtmek istemiyorum') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="belirtmem">Belirtmek istemiyorum</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 position-relative">
                        <label for="validationTooltipEmail" class="form-label fw-semibold">E-posta</label>
                        <div class="input-group input-group-lg has-validation">
                       <span class="input-group-text bg-light border-2 rounded-start-3">
                           <i class="bi bi-envelope"></i>
                       </span>
                            <input type="email" name="E-posta" class="form-control border-2 rounded-end-3" id="validationTooltipEmail" placeholder="ornek@email.com" value="<?php echo htmlspecialchars($_POST['E-posta'] ?? ''); ?>" required>
                            <div class="invalid-tooltip">
                                Lütfen geçerli bir e-posta adresi girin.
                            </div>
                        </div>
                    </div>

                    <div class="col-12 text-center pt-3">
                        <button class="btn btn-primary btn-lg px-5 py-3 rounded-3 fw-semibold" type="submit">
                            <i class="bi bi-check-circle me-2"></i>Formu Gönder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php include '../blocks/scripts.php'; ?>