<?php
declare(strict_types=1);

$errs = $data['errs'] ?? [];
$old  = $data['old'] ?? [];
?>


<form 
    action="index.php?view=login" 
    method="post"
    autocomplete="off"
    class="auth__form auth__form--register">

    <?php if (!empty($errs) && (($data['activeTab'] ?? 'login') === 'register')): ?>
        <ul class="auth__errors" aria-live="polite">
            <?php foreach ($errs as $err): ?>
                <li class="auth__errors-item"><?= e((string)$err) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="auth__field">
        <label 
            for="register-username"
            class="auth__label">
            <?= e(t('auth.register_username_label')) ?>
        </label>

        <input 
            type="text" 
            name="register_username" 
            id="register-username"
            class="auth__input"
            value="<?= e($old['register_username'] ?? '') ?>"
            autocomplete="off"
            required>

        <p class="auth__error-message" aria-live="polite"></p>
    </div>

    <div class="auth__field">
        <label 
            for="register-email"
            class="auth__label">
            <?= e(t('auth.register_email_label')) ?>
        </label>

        <input 
            type="email" 
            name="register_email" 
            id="register-email"
            class="auth__input"
            value="<?= e($old['register_email'] ?? '') ?>"
            autocomplete="off"
            required>

        <p class="auth__error-message" aria-live="polite"></p>
    </div>

    <div class="auth__field">
        <label 
            for="register-password"
            class="auth__label">
            <?= e(t('auth.register_password_label')) ?>
        </label>

        <input 
            type="password" 
            name="register_password" 
            id="register-password"
            class="auth__input"
            value=""
            autocomplete="off"
            required>

        <p class="auth__error-message" aria-live="polite"></p>
    </div>

    <div class="auth__field">
        <label 
            for="register-password-confirm"
            class="auth__label">
            <?= e(t('auth.register_password_confirm_label')) ?>
        </label>

        <input 
            type="password" 
            name="register_password_confirm" 
            id="register-password-confirm"
            class="auth__input"
            value=""
            autocomplete="off"
            required>

        <p class="auth__error-message" aria-live="polite"></p>
    </div>

    <button 
        type="submit"
        name="action"
        value="register"
        class="auth__submit">
        <?= e(t('auth.register_submit')) ?>
    </button>

</form>