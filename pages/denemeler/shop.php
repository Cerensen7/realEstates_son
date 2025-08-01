<?php
error_reporting(0);

$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmtCategories = $pdo->query("SELECT DISTINCT categories FROM products");
$categories = $stmtCategories->fetchAll();

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

if ($filter === 'all') {
    $stmt = $pdo->query("SELECT * FROM products");
} else {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE categories LIKE ?");
    $stmt->execute(['%' . $filter . '%']);
}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../blocks/head.php'; ?>
    <link rel="stylesheet" href="../../css/shop.css">

    <div class="shop" id="shop">
        <div class="shop-wrapper">
            <div class="shop-header">
                <div class="section-badge">
                    <i class="bi bi-shop"></i>
                    <span>Premium Collection</span>
                </div>
                <h2 class="shop-title">
                    Discover Our
                    <span class="shop-highlight">Plant Paradise</span>
                </h2>
                <p class="shop-description">
                    Explore our carefully curated selection of premium plants, from beginner-friendly varieties
                    to exotic specimens that will transform your space into a green oasis.
                </p>
            </div>

            <!-- Filtreleme Butonları -->
            <div class="shop-filters">
                <a href="?filter=all">
                    <button class="filter-btn <?= ($filter == 'all') ? 'active' : '' ?>">All Plants</button>
                </a>
                <?php foreach ($categories as $category): ?>
                    <a href="?filter=<?= strtolower($category['categories']); ?>">
                        <button class="filter-btn <?= ($filter == strtolower($category['categories'])) ? 'active' : '' ?>">
                            <?= ucfirst($category['categories']); ?>
                        </button>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Ürünler -->
            <div class="shop-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card" data-category="<?= ($product['categories']) ?>">
                        <div class="product-image">
                            <img src="<?= ($product['image_url']) ?>" alt="<?= ($product['name']) ?>">
                            <div class="product-overlay">
                                <button class="quick-view-btn">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="add-to-cart-btn">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                            </div>

                            <div class="product-badge">Best Seller</div>
                        </div>
                        <div class="product-info">
                            <div class="product-rating">
                                <div class="stars">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                </div>
                                <span class="rating-count">(128)</span>
                            </div>
                            <h3 class="product-name"><?= $product['name']; ?></h3>
                            <p class="product-category"><?= $product['categories']; ?></p>
                            <div class="product-price">
                                <span class="current-price"><?= $product['price']; ?>₺</span>
                                <span class="original-price"><?= $product['original_price']; ?>₺</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Footer ve Diğer Bilgiler -->
            <div class="shop-footer">
                <button class="load-more-btn">
                    <i class="bi bi-plus-circle"></i>
                    Load More Plants
                </button>
                <div class="shop-info">
                    <div class="info-item">
                        <i class="bi bi-truck"></i>
                        <span>Free shipping on orders over $50</span>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-shield-check"></i>
                        <span>30-day money back guarantee</span>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-headset"></i>
                        <span>24/7 plant care support</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include '../blocks/scripts.php'; ?>