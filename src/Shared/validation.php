<?php
declare(strict_types=1);


/*----------------
|     HELPERS     
|----------------*/

/*
ok
-------------------------------------------------
- Devuelve un resultado de éxito con el valor válido.
- Estructura de retorno:
    ['ok' => true, 'valor' => $valor, 'errores' => []]
*/

function ok(mixed $valor): array {
    return ['ok' => true, 'valor' => $valor, 'errores' => []];
}


/*
error
-------------------------------------------------
- Devuelve un resultado de error con uno o varios mensajes.
- Acepta string o array (si string, se convierte en array).
- Estructura de retorno:
    ['ok' => false, 'valor' => null, 'errores' => $errores]
*/

function error(string|array $errores): array {
    return ['ok' => false, 'valor' => null, 'errores' => (array) $errores];
}



/*
resultado
-------------------------------------------------
- Construye el array final dependiendo de si hay errores.
- Si $errores está vacío → ok=true, valor real.
- Si hay errores → ok=false, valor=null.
- Estructura estándar:
    ['ok', 'valor', 'errores']
*/

function resultado(mixed $valor, array $errores): array {
    $ok = empty($errores);
    return ['ok' => $ok, 'valor' => $ok ? $valor : null, 'errores' => $errores];
}



/*
limpiar_texto
-------------------------------------------------
- Recorta espacios en los extremos (trim).
- Si $colapsar=true → reemplaza secuencias de espacios por uno solo (regex /\s+/u).
- Devuelve texto limpio.
*/

function limpiar_texto(string $texto, bool $colapsar = true): string {
    $texto = trim($texto);
    return $colapsar ? (preg_replace('/\s+/u', ' ', $texto) ?? $texto) : $texto;
}



/*
normalizar_coma
-------------------------------------------------
- Convierte la primera coma decimal en punto si no hay punto.
- Ejemplo: “3,5” → “3.5”.
- Devuelve la cadena normalizada.
*/

function normalizar_coma(string $cadena): string {
    if (strpos($cadena, '.') === false && strpos($cadena, ',') !== false) {
        return preg_replace('/,/', '.', $cadena, 1) ?? $cadena;
    }

    return $cadena;
}




/*
validarOpcionSimple
-------------------------------------------------
- Usada por select simple o radio.
- Pasos:
    1. Leer el valor con filter_input(..., FILTER_UNSAFE_RAW).
    2. Si es string → trim().
    3. Si vacío:
         - Si requerido → error("Debes seleccionar una opción...").
         - Si opcional  → resultado(null, []).
    4. Comprobar que el valor está en la lista de permitidos (in_array estricto).
    5. Si no → error("La opción no es válida...").
    6. Si sí → ok($valor).
*/

function validarOpcionSimple(
    string $campo,
    array $permitidos,
    bool $req = true,
    int $src = INPUT_POST
): array {

    $raw = filter_input($src, $campo, FILTER_UNSAFE_RAW) ?? '';
    $val = is_string($raw) ? trim($raw) : $raw;

    if ($val === '')
        return $req ? error("Debes seleccionar una opción en $campo.") : resultado(null, []);

    if (!in_array($val, $permitidos, true))
        return error("La opción seleccionada no es válida en $campo.");


    return ok($val);
}




/*-----------------------------------------
|     FUNCIONES DE VALIDACIÓN DE TEXTO     
|-----------------------------------------*/


/*
validarTexto
-------------------------------------------------
1. Leer valor del campo (FILTER_UNSAFE_RAW) y limpiar con limpiar_texto().
2. Si vacío:
     - Si requerido → error("El campo ... es obligatorio").
     - Si opcional  → resultado(null, []).
3. Calcular longitud (mb_strlen):
     - Si menor que $min → error "no puede ser inferior a $min".
     - Si mayor que $max → error "no puede ser superior a $max".
4. Si $patron !== null → comprobar preg_match, y si no cumple → error formato.
5. Devolver resultado($txt, $errores).
*/

function validarTexto(
    string $campo,
    int $min = 0,
    int $max = 255,
    bool $req = true,
    ?string $patron = null,
    int $src = INPUT_POST
): array {

    $raw = filter_input($src, $campo, FILTER_UNSAFE_RAW) ?? '';
    $val = limpiar_texto($raw);

    // VALIDACIONES.
    $errs = [];

    // Obligatoriedad.
    if ($val === '')
        return $req ? error("El campo $campo es obligatorio.") : resultado(null, []);

    
    // Longitud mínima y máxima.
    $len = mb_strlen($val);

    if ($len < $min) $errs[] = "El campo $campo no puede ser inferior a $min caracteres.";
    if ($len > $max) $errs[] = "El campo $campo no puede ser superior a $max caracteres.";


    // Formato.
    if ($patron && !preg_match($patron, $val))
        $errs[] = "El campo $campo no cumple con el formato requerido.";

    
    return resultado($val, $errs);
}



/*
validarEmail
-------------------------------------------------
1. Leer valor del campo (FILTER_UNSAFE_RAW) y hacer trim().
2. Si vacío:
     - Si requerido → error obligatorio.
     - Si opcional  → resultado(null, []).
3. Validar formato con FILTER_VALIDATE_EMAIL.
     - Si no cumple → error("El campo ... no cumple con el formato").
4. Devolver resultado($email, $errores).
*/

function validarEmail(
    string $campo,
    bool $req = true,
    int $src = INPUT_POST
): array {

    $raw = filter_input($src, $campo, FILTER_UNSAFE_RAW) ?? '';
    $val = trim($raw);

    // VALIDACIONES.
    $errs = [];

    // Obligatoriedad.
    if ($val === '')
        return $req ? error("El campo $campo es obligatorio.") : resultado(null, []);


    // Formato.
    if (!filter_var($val, FILTER_VALIDATE_EMAIL))
        $errs[] = "El campo $campo no cumple con el formato requerido.";

    
    return resultado($val, $errs);
}




/*-----------------------------------------
|     FUNCIONES DE VALIDACIÓN NUMÉRICA     
|-----------------------------------------*/


/*
validarNumero
-------------------------------------------------
1. Leer valor (FILTER_UNSAFE_RAW) y trim().
2. Si vacío:
     - Si requerido → error obligatorio.
     - Si opcional  → resultado(null, []).
3. Si $perm_coma=true → aplicar normalizar_coma().
4. Convertir tipo a minúsculas ($tipo = strtolower($tipo)).
5. Validar:
     - Si 'float'  → FILTER_VALIDATE_FLOAT.
     - Si 'int'    → FILTER_VALIDATE_INT.
     - Si false → error de formato.
6. Validar rango:
     - Si $min !== null && $num < $min → error "no puede ser menor que $min".
     - Si $max !== null && $num > $max → error "no puede ser mayor que $max".
7. Devolver resultado($num, $errores).
*/


function validarNumero(
    string $campo,
    string $tipo = 'int',
    bool $req = true,
    ?float $min = null,
    ?float $max = null,
    int $src = INPUT_POST,
    bool $perm_coma = true
): array {

    $raw = filter_input($src, $campo, FILTER_UNSAFE_RAW) ?? '';
    $val = trim($raw);

    // VALIDACIONES.
    $errs = [];

    // Obligatoriedad.
    if ($val === '')
        return $req ? error("El campo $campo es obligatorio.") : resultado(null, []);


    // Permitir coma.
    if ($perm_coma) $val = normalizar_coma($val);
    

    // Validar tipos.
    $tipo = mb_strtolower($tipo);

    if ($tipo === 'float') {
        $num = filter_var($val, FILTER_VALIDATE_FLOAT);

        if ($num === false)
            return error("El campo $campo debe ser un número válido.");
    } else {
        $num = filter_var($val, FILTER_VALIDATE_INT);

        if ($num === false)
            return error("El campo $campo debe ser un número entero.");
    }

    // Rango mínimo y máximo.
    if ($min !== null && $num < $min) $errs[] = "El campo $campo no puede ser menor que $min.";
    if ($max !== null && $num > $max) $errs[] = "El campo $campo no puede ser mayor que $max.";


    return resultado($num, $errs);
}




/*---------------------------------------
|     FUNCIONES DE VALIDACIÓN SELECT     
|----------------------------------------*/

/*
validarSelect
-------------------------------------------------
A) SELECT MÚLTIPLE ($multiple=true)
    1. Leer array con FILTER_REQUIRE_ARRAY.
    2. Si null o []:
         - Si requerido → error("Debes seleccionar al menos una opción").
         - Si opcional  → ok([]).
    3. Normalizar cada valor (trim si string).
    4. Validar que todos estén en $permitidos (in_array estricto).
    5. Eliminar duplicados (array_unique) y reindexar (array_values).
    6. ok($valores).

B) SELECT SIMPLE ($multiple=false)
    - Delegar en validarOpcionSimple().
*/


function validarSelect(
    string $campo,
    array $permitidos,
    bool $req = true,
    int $src = INPUT_POST,
    bool $multiple = false
): array {

    // --- SELECT MÚLTIPLE ---
    if ($multiple) {
        $vals = filter_input($src, $campo, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        if ($vals === null || $vals === [])
            return $req ? error("Debes seleccionar al menos una opción en $campo") : ok([]);

        // Normalizar valores.
        $vals = array_map(static fn($v) => is_string($v) ? trim($v) : $v, $vals);

        foreach ($vals as $v) {
            if (!in_array($v, $permitidos, true))
                return error("La opción seleccionada no es válida en $campo.");
        }

        $vals = array_values(array_unique($vals));

        return ok($vals);
    }


    // --- SELECT SIMPLE ---
    return validarOpcionSimple($campo, $permitidos, $req, $src);
}




/*-----------------------------------------
|     FUNCIONES DE VALIDACIÓN CHECKBOX     
|-----------------------------------------*/


/*
validarCheckbox
-------------------------------------------------
1. Leer valor con filter_input().
2. Si no existe ($raw === null):
     - Si requerido → error("Debes marcar...").
     - Si opcional  → ok(false).
3. Si existe:
     - Normalizar (trim + minúsculas).
     - Preparar lista de valores verdaderos (on, 1, true) normalizados.
     - Comprobar in_array($val, $true_vals, true).
     - Si requerido y no marcado → error.
4. ok($marcado).
*/

function validarCheckbox(
    string $campo,
    bool $req = true,
    int $src = INPUT_POST,
    array $valores_verdaderos = ['on', '1', 'true'],
): array {

    $raw = filter_input($src, $campo, FILTER_UNSAFE_RAW);

    // Obligatoriedad.
    if ($raw === null)
        return $req ? error("Debes marcar el campo $campo.") : ok(false);

    // Normalizar.
    $val = is_string($raw) ? mb_strtolower(trim($raw)) : $raw;

    // Convertir lista de valores a minúscula.
    $true_vals = array_map(static fn($v) => mb_strtolower((string) $v), $valores_verdaderos);

    // ¿Marcado?
    $marcado = in_array($val, $true_vals, true);

    if ($req && !$marcado)
        return error("Debes marcar el campo $campo.");

    return ok($marcado);
}



/*-----------------------------------------
|     FUNCIÓN DE VALIDACIÓN RADIO BUTTON    
|------------------------------------------*/


/*
validarRadio
-------------------------------------------------
- Igual que select simple.
- Delegar en validarOpcionSimple().
- Mensajes:
   "Debes seleccionar una opción en ..."
   "La opción seleccionada no es válida en ..."
*/


function validarRadio(
    string $campo,
    array $permitidos,
    bool $req = true,
    int $src = INPUT_POST
): array {
    return validarOpcionSimple($campo, $permitidos, $req, $src);
}

