<?php
declare(strict_types=1);


require_once __DIR__ . '/../../Shared/validation.php';
require_once __DIR__ . '/../../Shared/html.php';
require_once __DIR__ . '/../../Shared/config.php';
require_once __DIR__ . '/../services/AuthService.php';


class AuthController {


    // Muestra el formulario de login
    public function showLogin(
        array $old = [],
        array $errs = [],
        string $activeTab = 'login',
        array $flash = []
    ): array {
        return [
            'view' => 'login',
            'data' => compact('old', 'errs', 'activeTab', 'flash')
        ];
    }




    // Procesa el formulario de login
    public function processLogin(): array
    {
        $old = [
            'username' => $_POST['username'] ?? ''
        ];

        $validUser = validarTexto('username', 2, 50, true, NAME_PATTERN);
        $validPassword = validarTexto('password', 4);

        $errs = array_merge(
            $validUser['errores'],
            $validPassword['errores']
        );


        if (!empty($errs)) return $this->showLogin($old, $errs);

        $user = auth_authenticate(
            $validUser['valor'],
            $validPassword['valor']
        );

        if ($user === null) {
            $errs[] = 'Usuario o contraseña incorrectos.';
            return $this->showLogin($old, $errs);
        }

        auth_login($user);

        return [ 'redirect' => 'home' ];
    }


    public function processRegister(): array
    {
        $old = [
            'register_username' => $_POST['register_username'] ?? '',
            'register_email' => $_POST['register_email'] ?? '',
        ];

        $vUser  = validarTexto('register_username', 2, 50, true, NAME_PATTERN);
        $vEmail = validarEmail('register_email', true);
        $vPass  = validarTexto('register_password', 4, 255, true);
        $vPass2 = validarTexto('register_password_confirm', 4, 255, true);

        $errs = array_merge(
            $vUser['errores'],
            $vEmail['errores'],
            $vPass['errores'],
            $vPass2['errores']
        );

        if (empty($errs) && $vPass['valor'] !== $vPass2['valor']) {
            $errs[] = 'Las contraseñas no coinciden.';
        }

        if (!empty($errs)) {
            return $this->showLogin($old, $errs, 'register');
        }

        $user = auth_register($vUser['valor'], $vEmail['valor'], $vPass['valor']);

        if ($user === null) {
            $errs[] = 'El usuario o email ya existe.';
            return $this->showLogin($old, $errs, 'register');
        }

        return $this->showLogin(
            [],
            [],
            'register',
            ['success' => 'Usuario creado correctamente. Ya puedes iniciar sesión.']
        );
    }



    public function logout(): array {
        auth_logout();
        return [ 'redirect' => 'home' ];
    }

}