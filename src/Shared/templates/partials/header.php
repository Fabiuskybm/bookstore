<?php 
declare(strict_types=1);

$currentUser = auth_user();
$currentLang = pref_language();
$currentView = $_GET['view'] ?? 'home';
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

        <!-- Bloque idioma (dropdown genérico) -->
        <div 
            class="header__dropdown header__dropdown--lang"
            data-dropdown="lang">

            <button 
                type="button"
                class="header__dropdown-trigger header__dropdown-trigger--lang"
                aria-haspopup="true"
                aria-expanded="false"
                aria-controls="header-lang-menu"
                title="<?= e(t('header.language_title')) ?>">

                <img 
                    src="assets/images/icons/lang.svg" 
                    alt="<?= e(t('header.language_icon_alt')) ?>"
                    class="header__icon header__icon--lang">

                <span class="header__lang-label">
                    <?= strtoupper($currentLang) ?>
                </span>
            </button>

            <div 
                class="header__dropdown-menu header__dropdown-menu--lang"
                id="header-lang-menu"
                role="menu"
                hidden>
                <?php require __DIR__ . '/lang-menu.php'; ?>
            </div>
        </div>


        <!-- Bloque usuario (dropdown genérico + compatibilidad con JS actual) -->
        <div 
            class="header__dropdown header__dropdown--user"
            data-dropdown="user">

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
                    class="header__dropdown-trigger header__dropdown-trigger--user"
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


                <div 
                    class="header__dropdown-menu header__dropdown-menu--user"
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
