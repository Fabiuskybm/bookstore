
<?php $activeTab = $data['activeTab'] ?? 'login'; ?>
<?php $isRegister = ($activeTab === 'register'); ?>

<section class="auth">

    <div class="auth__tabs" role="tablist" aria-label="<?= e(t('auth.tabs_aria_label')) ?>">

        <button
            class="auth__tab <?= $isRegister ? '' : 'auth__tab--active' ?>"
            type="button"
            role="tab"
            aria-selected="<?= $isRegister ? 'false' : 'true' ?>"
            aria-controls="auth-panel-login"
            id="auth-tab-login">
            <?= e(t('auth.tabs_login_title')) ?>
        </button>

        <button
            class="auth__tab <?= $isRegister ? 'auth__tab--active' : '' ?>"
            type="button"
            role="tab"
            aria-selected="<?= $isRegister ? 'true' : 'false' ?>"
            aria-controls="auth-panel-register"
            id="auth-tab-register">
            <?= e(t('auth.tabs_register_title')) ?>
        </button>

    </div>

    <div class="auth__panels">

        <section
            class="auth__panel auth__panel--login <?= $isRegister ? '' : 'auth__panel--active' ?>"
            role="tabpanel"
            id="auth-panel-login"
            aria-labelledby="auth-tab-login"
            <?= $isRegister ? 'hidden' : '' ?>
        >

            <?php require __DIR__ . '/partials/login-form.php'; ?>
        </section>

        <section
            class="auth__panel auth__panel--register <?= $isRegister ? 'auth__panel--active' : '' ?>"
            role="tabpanel"
            id="auth-panel-register"
            aria-labelledby="auth-tab-register"
            <?= $isRegister ? '' : 'hidden' ?>
        >
            <?php require __DIR__ . '/partials/register-form.php'; ?>
        </section>

    </div>

</section>
