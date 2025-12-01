<?php 
declare(strict_types=1);
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

        <a 
            href="index.php?view=login"
            class="header__action header__action--user"
            title="Usuario">
            
            <img 
                src="assets/images/user.png" 
                alt="user icon"
                class="header__icon header__icon--user">
        </a>

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