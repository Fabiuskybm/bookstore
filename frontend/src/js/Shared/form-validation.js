
// ==================================================
//  FORM VALIDATION
//  - initLiveValidation
//  - Reglas helpers reutilizables
// ==================================================



// ==================================================
//  REGISTRO DE VALIDACIÓN EN VIVO
// ==================================================

/**
 * Registra la validación en vivo para un input de texto.
 *
 * - Ejecuta un conjunto de reglas sobre el valor.
 * - Muestra/oculta el mensaje de error.
 * - Añade clases de estado al input (válido / inválido).
 *
 * Devuelve un objeto con:
 *   { validate: () => boolean }
 */
export function initLiveValidation(
    input,
    messageElement,
    config = {}
) {
    if (!input || !messageElement) return null;

    const {
        rules = [],
        events = ['input', 'blur'],
        validClass = 'input--valid',
        invalidClass = 'input--invalid',
        initialSilent = true
    } = config;

    const runValidation = () => {
        const value = input.value ?? '';

        for (const rule of rules) {
            const result = rule(value);

            if (result && result.valid === false) {
                const msg = result.message || '';

                messageElement.textContent = msg;
                messageElement.hidden = !msg;

                input.classList.remove(validClass);
                input.classList.add(invalidClass);

                return false;
            }
        }

        // Si ninguna regla falla, el campo es válido
        messageElement.textContent = '';
        messageElement.hidden = true;

        input.classList.remove(invalidClass);
        input.classList.add(validClass);

        return true;
    };

    // Validación inicial (opcional)
    if (!initialSilent) {
        runValidation();
    }

    // Enganchar eventos configurados
    events.forEach((eventName) => {
        input.addEventListener(eventName, runValidation);
    });

    return {
        validate: runValidation
    };
}



// ==================================================
//  REGLAS DE AYUDA COMUNES
// ==================================================

/**
 * Regla: campo obligatorio (no vacío).
 */
export function createRequiredRule(message) {
    return (value) => {
        const trimmed = (value || '').trim();
        const ok = trimmed.length > 0;

        return {
            valid: ok,
            message: ok ? '' : message
        };
    };
}


/**
 * Regla: longitud mínima.
 */
export function createMinLengthRule(minLength, message) {
    const min = Number(minLength) || 0;

    return (value) => {
        const val = value || '';
        const ok = val.length >= min;

        return {
            valid: ok,
            message: ok ? '' : message
        };
    };
}


/**
 * Regla: patrón (RegExp).
 * Si allowEmpty es true, el valor vacío se considera válido.
 */
export function createPatternRule(pattern, message, allowEmpty = false) {
    const regex = pattern instanceof RegExp ? pattern : new RegExp(String(pattern));

    return (value) => {
        const val = value || '';

        if (allowEmpty && val.trim() === '') {
            return { valid: true, message: '' };
        }

        const ok = regex.test(val);

        return {
            valid: ok,
            message: ok ? '' : message
        };
    };
}


/**
 * Regla totalmente personalizada.
 * fn debe devolver { valid, message }.
 */
export function createCustomRule(fn) {
    return (value) => {
        const result = fn(value);

        if (!result) {
            return { valid: true, message: '' };
        }

        return {
            valid: Boolean(result.valid),
            message: result.message || ''
        };
    };
}
