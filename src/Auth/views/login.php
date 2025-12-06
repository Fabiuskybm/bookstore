<?php
declare(strict_types=1);
?>

<section class="auth-wrapper">

    <section class="auth">
        
        <div 
            class="auth__tabs"
            role="tablist"
            aria-label="<?= e(t('auth.tabs_aria_label')) ?>">
    
            <button
                class="auth__tab auth__tab--active" 
                type="button"
                role="tab"
                aria-selected="true"
                aria-controls="auth-panel-login"
                id="auth-tab-login">
                <?= e(t('auth.tabs_login_title')) ?>
            </button>
    
            <button
                class="auth__tab" 
                type="button"
                role="tab"
                aria-selected="false"
                aria-controls="auth-panel-register"
                id="auth-tab-register">
                <?= e(t('auth.tabs_register_title')) ?>
            </button>
    
        </div>
    
        <div class="auth__panels">
    
            <section 
                class="auth__panel auth__panel--login auth__panel--active"
                role="tabpanel"
                id="auth-panel-login"
                aria-labelledby="auth-tab-login">
    
                <?php require __DIR__ . '/partials/login-form.php'; ?>
            </section>
    
            <section 
                class="auth__panel auth__panel--register"
                role="tabpanel"
                id="auth-panel-register"
                aria-labelledby="auth-tab-register"
                hidden>
    
                <?php require __DIR__ . '/partials/register-form.php'; ?>
            </section>
    
        </div>
    
    </section>
    
</section>

