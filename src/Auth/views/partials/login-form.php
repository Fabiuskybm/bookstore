<?php
declare(strict_types=1);
?>


<form 
    action="index.php?view=login" 
    method="post"
    autocomplete="off"
    class="auth__form auth__form--login">

    <div class="auth__field">
        <label 
            for="login-username"
            class="auth__label">
            Usuario
        </label>

        <input 
            type="text" 
            name="username" 
            id="login-username"
            class="auth__input"
            value=""
            autocomplete="off"
            required>

        <p class="auth__error-message" aria-live="polite"></p>
    </div>

    <div class="auth__field">
        <label 
            for="login-password"
            class="auth__label">
            Contraseña
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
        Iniciar sesión
    </button>

</form>