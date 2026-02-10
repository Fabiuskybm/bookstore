import Swal from 'sweetalert2';


/*
  where-map.js
  -------------
  - Inicializa el mapa (Leaflet) en la vista ?view=where
  - Pinta el marcador de la “tienda”
  - Gestiona el botón “Usar mi ubicación” (API Geolocation)
  - Si se obtiene ubicación:
      · coloca marcador de usuario
      · encuadra el mapa (tienda + usuario)
      · actualiza el enlace “Cómo llegar”
  - Muestra feedback visual con SweetAlert2 (i18n)
*/


export function initWhereMap() {
    const dom = getDom();
    if (!dom.mapEl) return;

    if (!isLeafletReady()) {
        console.warn("Leaflet (L) no está cargado.");
        return;
    }

    const store = getStoreFromDataset(dom.mapEl);
    if (!store) return;

    const { map, storeMarker } = createMap(dom.mapEl, store);
    setStorePopup(dom.mapEl, storeMarker);

    initDirectionsLink(dom.directionsEl, store);
    initGeolocation(dom, map, store);
}



// =================
// |  DOM helpers  |
// =================

/**
 * Obtiene los elementos DOM necesarios para la vista "Where".
 */
function getDom() {
    return {
        mapEl: document.getElementById("where-map"),
        locateBtn: document.getElementById("where-locate-btn"),
        statusEl: document.getElementById("where-status"),
        directionsEl: document.getElementById("where-directions-link"),
    };
}


/**
 * Actualiza el texto de estado usando claves data-* (i18n).
 * Ejemplo: setStatus(statusEl, "locating") -> data-locating="..."
 */
function setStatus(statusEl, key) {
    if (!statusEl) return;
    const msg = statusEl.dataset[key] ?? "";
    if (msg) statusEl.textContent = msg;
}


/**
 * Obtiene textos i18n desde data-* para SweetAlert2.
 * El HTML usa data-foo-bar, y JS lo expone como dataset.fooBar.
 *
 * Ejemplo:
 *  - HTML: data-locating-title="..."
 *  - JS:   getI18n(statusEl, "locating_title") -> dataset.locatingTitle
 */
function getI18n(statusEl, key) {
    if (!statusEl) return "";

    const prop = key.replace(/_([a-z])/g, (_, c) => c.toUpperCase());
    return statusEl.dataset[prop] ?? "";
}



// =====================
// |  Leaflet helpers  |
// =====================

/**
 * Comprueba si Leaflet está disponible globalmente.
 */
function isLeafletReady() {
    return typeof window.L !== "undefined";
}


/**
 * Obtiene los datos de la tienda desde data-* del mapa.
 */
function getStoreFromDataset(mapEl) {
    const lat = Number(mapEl.dataset.storeLat);
    const lng = Number(mapEl.dataset.storeLng);
    const zoom = Number(mapEl.dataset.storeZoom);

    if (!Number.isFinite(lat) || !Number.isFinite(lng) || !Number.isFinite(zoom)) {
        console.warn("Store data missing in dataset.");
        return null;
    }

    return { lat, lng, zoom };
}


/**
 * Inicializa el mapa Leaflet y el marcador de la tienda.
 */
function createMap(mapEl, store) {
    const map = window.L.map(mapEl).setView([store.lat, store.lng], store.zoom);

    window.L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap contributors",
    }).addTo(map);

    const storeMarker = window.L.marker([store.lat, store.lng]).addTo(map);

    return { map, storeMarker };
}


/**
 * Asocia el popup informativo al marcador de la tienda.
 */
function setStorePopup(mapEl, storeMarker) {
    const popupText = mapEl.dataset.popup || "";
    if (!popupText) return;

    storeMarker.bindPopup(popupText).openPopup();
}



// ========================
// |  Directions helpers  |
// ========================

/**
 * Inicializa el enlace "Cómo llegar" con destino a la tienda.
 */
function initDirectionsLink(directionsEl, store) {
    if (!directionsEl) return;

    const destination =
        directionsEl.dataset.destination || `${store.lat},${store.lng}`;

    directionsEl.href = buildDirectionsUrl({ destination });
}


/**
 * Construye una URL de Google Maps Directions.
 */
function buildDirectionsUrl({ origin, destination }) {
    const base = "https://www.google.com/maps/dir/?api=1";
    const params = new URLSearchParams();

    if (origin) params.set("origin", origin);
    params.set("destination", destination);

    return `${base}&${params.toString()}`;
}



// =================
// |  Geolocation  |
// =================

/**
 * Gestiona la obtención de la ubicación del usuario y el feedback UX.
 */
function initGeolocation(dom, map, store) {
    const { locateBtn, statusEl, directionsEl } = dom;
    if (!locateBtn || !statusEl) return;

    let userMarker = null;

    const userPopupText = dom.mapEl?.dataset.userPopup;

    // Flag para controlar si el modal "localizando" está abierto
    let isLoadingOpen = false;

    // Controla si ya se localizó de verdad en esta sesión (con permiso concedido)
    let hasLocatedOnce = false;

    const saved = sessionStorage.getItem("where_user_location");

    // Restaurar ubicación SOLO si el permiso sigue en "granted"
    if (saved) {
        if ("permissions" in navigator && navigator.permissions?.query) {
            navigator.permissions
                .query({ name: "geolocation" })
                .then((perm) => {
                    if (perm.state !== "granted") {
                        // Si el usuario resetea/quita permisos, limpiamos y volvemos a estado inicial
                        sessionStorage.removeItem("where_user_location");
                        removeUserMarker(userMarker, map);
                        userMarker = null;
                        setStatus(statusEl, "ready");
                        return;
                    }

                    try {
                        const user = JSON.parse(saved);

                        userMarker = upsertUserMarker(userMarker, map, user, userPopupText);
                        fitMapToPoints(map, [store, user]);
                        setStatus(statusEl, "located");
                        updateDirectionsWithOrigin(directionsEl, store, user);
                        hasLocatedOnce = true;
                    } catch (_) {
                        sessionStorage.removeItem("where_user_location");
                        setStatus(statusEl, "ready");
                    }
                })
                .catch(() => {
                    sessionStorage.removeItem("where_user_location");
                    setStatus(statusEl, "ready");
                });
        } else {
            // Fallback: si no hay Permissions API, mantenemos para cambio de idioma.
            // No es posible detectar de forma fiable un "reset" de permisos.
            try {
                const user = JSON.parse(saved);

                userMarker = upsertUserMarker(userMarker, map, user, userPopupText);
                fitMapToPoints(map, [store, user]);
                setStatus(statusEl, "located");
                updateDirectionsWithOrigin(directionsEl, store, user);
                hasLocatedOnce = true;
            } catch (_) {
                sessionStorage.removeItem("where_user_location");
                setStatus(statusEl, "ready");
            }
        }
    }

    locateBtn.addEventListener("click", () => {
        if (locateBtn.disabled) return;

        if (!supportsGeolocation()) {
            setStatus(statusEl, "unavailable");
            return;
        }

        setStatus(statusEl, "locating");
        locateBtn.disabled = true;

        // SweetAlert: esperando permiso / localización (sin timer) -> SIEMPRE
        Swal.fire({
            title: getI18n(statusEl, "locating_title"),
            text: getI18n(statusEl, "locating_text"),
            icon: "info",
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
        });

        isLoadingOpen = true;

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const user = {
                    lat: pos.coords.latitude,
                    lng: pos.coords.longitude,
                };

                sessionStorage.setItem("where_user_location", JSON.stringify(user));

                userMarker = upsertUserMarker(
                    userMarker,
                    map,
                    user,
                    userPopupText
                );

                fitMapToPoints(map, [store, user]);

                setStatus(statusEl, "located");

                const wasLocatedOnce = hasLocatedOnce;
                hasLocatedOnce = true;

                if (isLoadingOpen) {
                    Swal.close();
                    isLoadingOpen = false;
                }

                // Feedback de "ubicación detectada" solo la primera vez real
                if (!wasLocatedOnce) {
                    Swal.fire({
                        title: getI18n(statusEl, "located_title"),
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1500,
                    });
                }

                updateDirectionsWithOrigin(directionsEl, store, user);

                locateBtn.disabled = false;
            },
            (err) => {
                // Limpiar ubicación guardada + marcador si hay error o denegación
                userMarker = handleGeoError(statusEl, err, isLoadingOpen, userMarker, map);

                isLoadingOpen = false;
                locateBtn.disabled = false;
                hasLocatedOnce = false;
            },
            getGeoOptions()
        );
    });
}


/**
 * Comprueba si el navegador soporta Geolocation API.
 */
function supportsGeolocation() {
    return "geolocation" in navigator;
}


/**
 * Opciones de configuración para Geolocation API.
 * Nota: el timeout demasiado bajo puede disparar errores antes
 * de que el usuario acepte/deniegue permisos.
 */
function getGeoOptions() {
    return {
        enableHighAccuracy: false,
        timeout: 30000,
        maximumAge: 60000,
    };
}


/**
 * Crea o actualiza el marcador del usuario.
 */
function upsertUserMarker(existingMarker, map, user, popupText) {

    if (existingMarker) {
        existingMarker.setLatLng([user.lat, user.lng]);
        return existingMarker;
    }

    const marker = window.L.marker([user.lat, user.lng]).addTo(map);

    if (popupText) {
        marker.bindPopup(popupText);
    }

    return marker;
}


/**
 * Elimina el marcador del usuario del mapa (si existe).
 */
function removeUserMarker(marker, map) {
    if (!marker) return;
    try {
        map.removeLayer(marker);
    } catch (_) {
        // noop
    }
}


/**
 * Ajusta el mapa para mostrar todos los puntos indicados.
 */
function fitMapToPoints(map, points) {
    const bounds = window.L.latLngBounds(points.map((p) => [p.lat, p.lng]));
    map.fitBounds(bounds, { padding: [24, 24] });
}


/**
 * Actualiza el enlace "Cómo llegar" incluyendo el origen del usuario.
 */
function updateDirectionsWithOrigin(directionsEl, store, user) {
    if (!directionsEl) return;

    const destination =
        directionsEl.dataset.destination || `${store.lat},${store.lng}`;

    const origin = `${user.lat},${user.lng}`;

    directionsEl.href = buildDirectionsUrl({ origin, destination });
}


/**
 * Gestiona los errores de geolocalización y muestra feedback visual.
 */
function handleGeoError(statusEl, err, isLoadingOpen, userMarker, map) {
    if (isLoadingOpen) Swal.close();

    // 1: permiso denegado | 2: no disponible | 3: timeout
    if (err?.code === 1) {
        setStatus(statusEl, "denied");
        sessionStorage.removeItem("where_user_location");

        removeUserMarker(userMarker, map);
        userMarker = null;

        Swal.fire({
            title: getI18n(statusEl, "denied_title"),
            icon: "warning",
        });

        return userMarker;
    }

    setStatus(statusEl, "error");
    sessionStorage.removeItem("where_user_location");

    removeUserMarker(userMarker, map);
    userMarker = null;

    Swal.fire({
        title: getI18n(statusEl, "error_title"),
        icon: "error",
    });

    return userMarker;
}
