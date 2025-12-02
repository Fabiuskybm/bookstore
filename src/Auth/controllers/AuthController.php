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
        array $errs = []
    ): array {
        return [
            'view' => 'login',
            'data' => compact('old', 'errs')
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


    public function logout(): array {
        auth_logout();
        return [ 'redirect' => 'home' ];
    }

}