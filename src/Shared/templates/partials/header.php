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

                <svg 
                    class="header__icon header__icon--lang"
                    width="24" 
                    height="24" 
                    viewBox="0 0 24 24" 
                    fill="none" 
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path 
                        d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M9.5,17c0.6,3.1,1.7,5,2.5,5s1.9-1.9,2.5-5H9.5z
                            M16.6,17c-0.3,1.7-0.8,3.3-1.4,4.5c2.3-0.8,4.3-2.4,5.5-4.5H16.6z M3.3,17c1.2,2.1,3.2,3.7,5.5,4.5c-0.6-1.2-1.1-2.8-1.4-4.5H3.3
                            z M16.9,15h4.7c0.2-0.9,0.4-2,0.4-3s-0.2-2.1-0.5-3h-4.7c0.2,1,0.2,2,0.2,3S17,14,16.9,15z M9.2,15h5.7c0.1-0.9,0.2-1.9,0.2-3
                            S15,9.9,14.9,9H9.2C9.1,9.9,9,10.9,9,12C9,13.1,9.1,14.1,9.2,15z M2.5,15h4.7c-0.1-1-0.1-2-0.1-3s0-2,0.1-3H2.5
                            C2.2,9.9,2,11,2,12S2.2,14.1,2.5,15z M16.6,7h4.1c-1.2-2.1-3.2-3.7-5.5-4.5C15.8,3.7,16.3,5.3,16.6,7z M9.5,7h5.1
                            c-0.6-3.1-1.7-5-2.5-5C11.3,2,10.1,3.9,9.5,7z M3.3,7h4.1c0.3-1.7,0.8-3.3,1.4-4.5C6.5,3.3,4.6,4.9,3.3,7z"
                        fill="currentColor"
                    />
                </svg>

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

                    <svg
                        class="header__icon header__icon--user" 
                        width="24" 
                        height="24" 
                        viewBox="0 0 24 24">

                        <path 
                            fill-rule="evenodd" 
                            clip-rule="evenodd"
                            d="M8.25 9C8.25 6.92893 9.92893 5.25 12 5.25C14.0711 5.25 15.75 6.92893 15.75 9C15.75 11.0711 14.0711 12.75 12 12.75C9.92893 12.75 8.25 11.0711 8.25 9ZM12 6.75C10.7574 6.75 9.75 7.75736 9.75 9C9.75 10.2426 10.7574 11.25 12 11.25C13.2426 11.25 14.25 10.2426 14.25 9C14.25 7.75736 13.2426 6.75 12 6.75Z"
                            fill="currentColor"
                        />

                        <path 
                            fill-rule="evenodd" 
                            clip-rule="evenodd"
                            d="M1.25 12C1.25 6.06294 6.06294 1.25 12 1.25C17.9371 1.25 22.75 6.06294 22.75 12C22.75 17.9371 17.9371 22.75 12 22.75C6.06294 22.75 1.25 17.9371 1.25 12ZM12 2.75C6.89137 2.75 2.75 6.89137 2.75 12C2.75 14.5456 3.77827 16.851 5.4421 18.5235C5.6225 17.5504 5.97694 16.6329 6.68837 15.8951C7.75252 14.7915 9.45416 14.25 12 14.25C14.5457 14.25 16.2474 14.7915 17.3115 15.8951C18.023 16.6329 18.3774 17.5505 18.5578 18.5236C20.2217 16.8511 21.25 14.5456 21.25 12C21.25 6.89137 17.1086 2.75 12 2.75ZM17.1937 19.6554C17.0918 18.4435 16.8286 17.5553 16.2318 16.9363C15.5823 16.2628 14.3789 15.75 12 15.75C9.62099 15.75 8.41761 16.2628 7.76815 16.9363C7.17127 17.5553 6.90811 18.4434 6.80622 19.6553C8.28684 20.6618 10.0747 21.25 12 21.25C13.9252 21.25 15.7131 20.6618 17.1937 19.6554Z"
                            fill="currentColor"
                        />
                    </svg>
                </a>

            <?php else: ?>

                <!-- Usuario logueado: trigger + saludo + menú -->
                <button 
                    type="button"
                    class="header__dropdown-trigger header__dropdown-trigger--user"
                    aria-haspopup="true"
                    aria-expanded="false"
                    aria-controls="header-user-menu">

                    <svg
                        class="header__icon header__icon--user" 
                        width="24" 
                        height="24" 
                        viewBox="0 0 24 24">

                        <path 
                            fill-rule="evenodd" 
                            clip-rule="evenodd"
                            d="M8.25 9C8.25 6.92893 9.92893 5.25 12 5.25C14.0711 5.25 15.75 6.92893 15.75 9C15.75 11.0711 14.0711 12.75 12 12.75C9.92893 12.75 8.25 11.0711 8.25 9ZM12 6.75C10.7574 6.75 9.75 7.75736 9.75 9C9.75 10.2426 10.7574 11.25 12 11.25C13.2426 11.25 14.25 10.2426 14.25 9C14.25 7.75736 13.2426 6.75 12 6.75Z"
                            fill="currentColor"
                        />

                        <path 
                            fill-rule="evenodd" 
                            clip-rule="evenodd"
                            d="M1.25 12C1.25 6.06294 6.06294 1.25 12 1.25C17.9371 1.25 22.75 6.06294 22.75 12C22.75 17.9371 17.9371 22.75 12 22.75C6.06294 22.75 1.25 17.9371 1.25 12ZM12 2.75C6.89137 2.75 2.75 6.89137 2.75 12C2.75 14.5456 3.77827 16.851 5.4421 18.5235C5.6225 17.5504 5.97694 16.6329 6.68837 15.8951C7.75252 14.7915 9.45416 14.25 12 14.25C14.5457 14.25 16.2474 14.7915 17.3115 15.8951C18.023 16.6329 18.3774 17.5505 18.5578 18.5236C20.2217 16.8511 21.25 14.5456 21.25 12C21.25 6.89137 17.1086 2.75 12 2.75ZM17.1937 19.6554C17.0918 18.4435 16.8286 17.5553 16.2318 16.9363C15.5823 16.2628 14.3789 15.75 12 15.75C9.62099 15.75 8.41761 16.2628 7.76815 16.9363C7.17127 17.5553 6.90811 18.4434 6.80622 19.6553C8.28684 20.6618 10.0747 21.25 12 21.25C13.9252 21.25 15.7131 20.6618 17.1937 19.6554Z"
                            fill="currentColor"
                        />
                    </svg>

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
            
            <svg
                class="header__icon header__icon--cart"
                width="24"
                height="24"
                viewBox="0 0 217.791 217.791"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M187.129,0H36.465c-7.864,0-14.667,6.122-15.49,13.951L1.332,201.979
                    c-0.43,4.081,0.871,8.127,3.556,11.11c2.691,2.989,6.587,4.702,10.693,4.702h186.465
                    c4.052,0,7.936-1.677,10.651-4.6c2.715-2.924,4.105-6.922,3.801-10.961L202.47,14.237
                    C201.873,6.253,195.136,0,187.129,0z M19.549,199.89l19.01-181.99h146.224l13.587,181.99H19.549z"
                    fill="currentColor"
                />
                <path
                    d="M148.344,35.055c-7.817,0-14.171,6.355-14.171,14.171c0,6.79,4.803,12.471,11.188,13.843
                    c-0.591,9.887-3.055,24.482-11.868,33.79c-6.009,6.343-14.315,9.356-24.584,9.052
                    c-10.358-0.346-18.73-3.998-24.876-10.86c-8.175-9.105-11.122-22.256-12.131-32.102
                    c6.188-1.522,10.806-7.077,10.806-13.724c0-7.817-6.355-14.171-14.171-14.171S54.366,41.41,54.366,49.227
                    c0,6.904,4.97,12.656,11.522,13.903C66.932,73.876,70.16,88.513,79.57,99.02c7.28,8.127,17.083,12.459,29.142,12.859
                    c0.597,0.018,1.187,0.03,1.778,0.03c11.277,0,20.478-3.682,27.352-10.961c10.233-10.824,12.942-27.102,13.533-37.896
                    c6.355-1.396,11.134-7.059,11.134-13.825C162.515,41.41,156.161,35.055,148.344,35.055z
                    M60.333,49.227c0-4.523,3.682-8.204,8.204-8.204c4.523,0,8.204,3.682,8.204,8.204
                    c0,3.485-2.196,6.45-5.269,7.638c-0.167-4.427,0.036-7.315,0.042-7.411c0.125-1.641-1.104-3.079-2.745-3.198
                    c-1.683-0.167-3.079,1.104-3.204,2.745c-0.03,0.382-0.233,3.377-0.09,7.823C62.469,55.605,60.333,52.664,60.333,49.227z
                    M151.519,56.787c-0.036-2.745-0.173-4.559-0.203-4.845c-0.149-1.641-1.617-2.828-3.234-2.703
                    c-1.641,0.143-2.852,1.599-2.709,3.234c0.006,0.072,0.137,1.754,0.155,4.427c-3.133-1.152-5.388-4.141-5.388-7.673
                    c0-4.523,3.682-8.204,8.204-8.204s8.204,3.682,8.204,8.204C156.549,52.622,154.472,55.546,151.519,56.787z"
                    fill="currentColor"
                />
            </svg>

            <span 
                class="header__cart-badge"
                data-cart-badge
            ></span>

        </a>

    </div>
        
</header>
