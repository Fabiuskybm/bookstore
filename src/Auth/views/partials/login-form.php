<?php
declare(strict_types=1);

$errs = $data['errs'] ?? [];
$old  = $data['old'] ?? [];
?>


<form 
    action="index.php?view=login" 
    method="post"
    autocomplete="off"
    class="auth__form auth__form--login">

    <ul class="auth__errors" aria-live="polite">
        <?php if (!empty($errs) && (($data['activeTab'] ?? 'login') === 'login')): ?>
            <?php foreach ($errs as $err): ?>
                <li class="auth__errors-item"><?= e((string)$err) ?></li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>


    <div class="auth__field">
        <label 
            for="login-username"
            class="auth__label">
            <?= e(t('auth.login_username_label')) ?>
        </label>

        <input 
            type="text" 
            name="username" 
            id="login-username"
            class="auth__input"
            value="<?= e($old['username'] ?? '') ?>"
            autocomplete="off"
            required>

        <p class="auth__error-message" aria-live="polite"></p>
    </div>

    <div class="auth__field">
        <label 
            for="login-password"
            class="auth__label">
            <?= e(t('auth.login_password_label')) ?>
        </label>

        <input 
            type="password" 
            name="password" 
            id="login-password"
            class="auth__input"
            value=""
            autocomplete="off"
            required>

        <p class="auth__error-message" aria-live="polite"></p>
    </div>

    <button 
        type="submit"
        name="action"
        value="login"
        class="auth__submit">
        <?= e(t('auth.login_submit')) ?>
    </button>

</form>