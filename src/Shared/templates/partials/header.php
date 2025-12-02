<?php 
declare(strict_types=1);

$currentUser = auth_user();
?>


<header class="page__header header">

    <div class="header__logo">
        <a href="index.php?view=home" class="header__logo-link">
            <img 
                src="assets/images/logo.png" 
                alt="Bookstore Logo"
                class="header__logo-img">
        </a>
    </div>

    <div class="header__search">

        <form 
            action="index.php" 
            method="get"
            class="header__search-form">

            <input 
                type="search" 
                name="q" 
                class="header__search-input"
                placeholder="Buscar por título, autor...">

        </form>

    </div>

    <div class="header__actions">

        <div class="header__user">

            <a 
                href="<?= $currentUser ? 'index.php?view=home' : 'index.php?view=login' ?>"
                class="header__action header__action--user"
                title="Usuario">

                <img 
                    src="assets/images/user.png" 
                    alt="user icon"
                    class="header__icon header__icon--user">
            </a>

            <!-- Si está logueado: saludo -->
            <?php if ($currentUser !== null): ?>
                <span class="header__user-name">
                    Hola,
                    <?= $currentUser->isAdmin() ? 'administrador' : e($currentUser->getUsername()) ?>
                </span>

                <form action="index.php?view=home" method="post" class="header__logout-form">
                    <button 
                        type="submit" 
                        name="action" 
                        value="logout"
                        class="header__logout-btn">
                        Cerrar sesión
                    </button>
                </form>
            <?php endif; ?>

        </div>

        <a 
            href="index.php?view=cart"
            class="header__action header__action--cart"
            title="Carrito">
            
            <img 
                src="assets/images/shopping-bag.png" 
                alt="Shopping bag icon"
                class="header__icon header__icon--cart">
        </a>

    </div>
        
</header>