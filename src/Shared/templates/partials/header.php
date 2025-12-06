<?php 
declare(strict_types=1);

$currentUser = auth_user();
?>


<header class="page__header header">

    <div class="header__logo">
        <a href="index.php?view=home" class="header__logo-link">
            <img 
                src="assets/images/logo.png" 
                alt="<?= e(t('header.logo_alt')) ?>"
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
                placeholder="<?= e(t('header.search_placeholder')) ?>">

        </form>

    </div>

    <div class="header__actions">

        <div class="header__user">

            <?php if ($currentUser === null): ?>
                
                <!-- Invitado: Icono lleva a login -->
                 <a 
                    href="index.php?view=login"
                    class="header__action header__action--user"
                    title="<?= e(t('header.login_title')) ?>">

                    <img 
                        src="assets/images/icons/user.svg" 
                        alt="<?= e(t('header.user_icon_alt')) ?>"
                        class="header__icon header__icon--user">
                </a>

            <?php else: ?>

                <!-- Usuario logueado: trigger + saludo + menú -->
                <button 
                    type="button"
                    class="header__user-trigger"
                    aria-haspopup="true"
                    aria-expanded="false"
                    aria-controls="header-user-menu">

                    <img 
                        src="assets/images/icons/user.svg" 
                        alt="<?= e(t('header.user_icon_alt')) ?>"
                        class="header__icon header__icon--user">

                    <span class="header__user-name">
                        <?= e(t('header.greeting_prefix')) ?>
                        <?= $currentUser->isAdmin() 
                            ? e(t('header.greeting_admin_label')) 
                            : e($currentUser->getUsername()) 
                        ?>
                    </span>

                </button>


                <div class="header__user-menu"
                    id="header-user-menu"
                    role="menu"
                    hidden>
                    <?php require __DIR__ . '/user-menu.php'; ?>
                </div>

            <?php endif; ?>

        </div>


        <a 
            href="index.php?view=cart"
            class="header__action header__action--cart"
            title="<?= e(t('header.cart_title')) ?>">
            
            <img 
                src="assets/images/icons/shopping-bag.svg" 
                alt="<?= e(t('header.cart_icon_alt')) ?>"
                class="header__icon header__icon--cart">
        </a>

    </div>
        
</header>