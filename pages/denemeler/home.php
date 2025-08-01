<?php
error_reporting(0);

$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>

<?php include '../blocks/head.php'; ?>
<link rel="stylesheet" href="../../css/plant.css">

<div class="plant-container">
    <div class="header-wrapper">
        <header class="plant-header">
            <div class="logo">
                <img src="../../assets/img/logo.png" alt="Plant Logo" class="logo-image">
                <!--                <span class="logo-text">green</span>-->
            </div>

            <nav class="header-nav">
                <a href="#" class="nav-link">Home</a>
                <a href="#" class="nav-link">About Us</a>
                <a href="shop.php" class="nav-link">Shop</a>
                <a href="#" class="nav-link">Contact</a>


            </nav>
            <nav class="page-nav d-flex gap-3 p-3">
                <a href="#" class="nav-link text-decoration-none">
                    <i class="bi bi-bag me-2"></i>
                </a>
                <a href="#" class="nav-link text-decoration-none">
                    <i class="bi bi-search me-2"></i>
                </a>
            </nav>
        </header>
        <div class="hero">
            <div class="hero-wrapper">
                <div class="hero-content">
                    <div class="hero-left">
                        <div class="hero-badge">
                            <i class="bi bi-award"></i>
                            <span>Premium Quality Plants</span>
                        </div>

                        <h1 class="hero-title">
                            Bring Nature's Magic
                            <span class="hero-highlight">Into Your Home</span>
                        </h1>

                        <p class="hero-description">
                            Transform your living space with our carefully curated collection of premium plants.
                            From air-purifying beauties to stunning decorative pieces, discover the perfect
                            green companions for your home.
                        </p>

                        <div class="hero-stats">
                            <div class="stat-item">
                                <span class="stat-number">10,000+</span>
                                <span class="stat-label">Happy Customers</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">500+</span>
                                <span class="stat-label">Plant Varieties</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">95%</span>
                                <span class="stat-label">Success Rate</span>
                            </div>
                        </div>

                        <div class="hero-actions">
                            <button class="btn-primary hero-btn">
                                <i class="bi bi-leaf"></i>
                                Explore Collection
                            </button>
                            <button class="btn-secondary hero-btn">
                                <i class="bi bi-play-circle"></i>
                                Watch Video
                            </button>
                        </div>
                    </div>

                    <div class="hero-right">
                        <div class="plant-cards">
                            <div class="plant-card card-1">
                                <div class="plant-image">
                                    <i class="bi bi-flower1"></i>
                                </div>
                                <h3>Monstera Deliciosa</h3>
                                <p>$45.99</p>
                                <div class="plant-rating">
                                    <i class="bi bi-star-fill"></i>
                                    <span>4.9</span>
                                </div>
                            </div>

                            <div class="plant-card card-2">
                                <div class="plant-image">
                                    <i class="bi bi-tree"></i>
                                </div>
                                <h3>Fiddle Leaf Fig</h3>
                                <p>$65.99</p>
                                <div class="plant-rating">
                                    <i class="bi bi-star-fill"></i>
                                    <span>4.8</span>
                                </div>
                            </div>

                            <div class="plant-card card-3">
                                <div class="plant-image">
                                    <i class="bi bi-flower2"></i>
                                </div>
                                <h3>Snake Plant</h3>
                                <p>$29.99</p>
                                <div class="plant-rating">
                                    <i class="bi bi-star-fill"></i>
                                    <span>4.7</span>
                                </div>
                            </div>
                        </div>

                        <div class="floating-elements">
                            <div class="floating-leaf leaf-1">
                                <i class="bi bi-tree-fill"></i>
                            </div>
                            <div class="floating-leaf leaf-2">
                                <i class="bi bi-flower1"></i>
                            </div>
                            <div class="floating-leaf leaf-3">
                                <i class="bi bi-flower3"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hero-scroll-indicator">
                    <div class="scroll-line"></div>
                    <span>Scroll to explore</span>
                    <i class="bi bi-chevron-down"></i>
                </div>
            </div>
        </div>
    </div>

    <main class="plant-main">
    </main>

    <footer class="plant-footer">

    </footer>
</div>

<?php include '../blocks/scripts.php'; ?>
