
/*
  where-map.js
  -------------
  - Inicializa el mapa (Leaflet) en la vista ?view=where
  - Pinta el marcador de la “tienda”
  - Gestiona el botón “Usar mi ubicación” (API Geolocation)
  - Si se obtiene ubicación: coloca marcador de usuario, encuadra el mapa y actualiza “Cómo llegar”
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

function getDom() {
    return {
        mapEl: document.getElementById("where-map"),
        locateBtn: document.getElementById("where-locate-btn"),
        statusEl: document.getElementById("where-status"),
        directionsEl: document.getElementById("where-directions-link"),
    };
}


function setStatus(statusEl, key) {
    if (!statusEl) return;
    const msg = statusEl.dataset[key] ?? "";
    if (msg) statusEl.textContent = msg;
}



// =====================
// |  Leaflet helpers  |
// =====================

function isLeafletReady() {
    return typeof window.L !== "undefined";
}


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


function createMap(mapEl, store) {
    const map = window.L.map(mapEl).setView([store.lat, store.lng], store.zoom);

    window.L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap contributors",
    }).addTo(map);

    const storeMarker = window.L.marker([store.lat, store.lng]).addTo(map);

    return { map, storeMarker };
}


function setStorePopup(mapEl, storeMarker) {
    const popupText = mapEl.dataset.popup || "";
    if (!popupText) return;

    storeMarker.bindPopup(popupText).openPopup();
}



// ========================
// |  Directions helpers  |
// ========================

function initDirectionsLink(directionsEl, store) {
    if (!directionsEl) return;

    const destination =
        directionsEl.dataset.destination || `${store.lat},${store.lng}`;

    directionsEl.href = buildDirectionsUrl({ destination });
}


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

function initGeolocation(dom, map, store) {
    const { locateBtn, statusEl, directionsEl } = dom;
    if (!locateBtn || !statusEl) return;

    let userMarker = null;

    locateBtn.addEventListener("click", () => {
        if (locateBtn.disabled) return;

        if (!supportsGeolocation()) {
            setStatus(statusEl, "unavailable");
            return;
        }

        setStatus(statusEl, "locating");
        locateBtn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const user = {
                    lat: pos.coords.latitude,
                    lng: pos.coords.longitude,
                };

                userMarker = upsertUserMarker(userMarker, map, user);
                fitMapToPoints(map, [store, user]);

                setStatus(statusEl, "located");
                updateDirectionsWithOrigin(directionsEl, store, user);

                locateBtn.disabled = false;
            },
            (err) => {
                handleGeoError(statusEl, err);
                locateBtn.disabled = false;
            },
            getGeoOptions()
        );
    });
}


function supportsGeolocation() {
    return "geolocation" in navigator;
}


function getGeoOptions() {
    return {
        enableHighAccuracy: false,
        timeout: 8000,
        maximumAge: 60000,
    };
}


function upsertUserMarker(existingMarker, map, user) {

    if (existingMarker) {
        existingMarker.setLatLng([user.lat, user.lng]);
        return existingMarker;
    }

    return window.L.marker([user.lat, user.lng]).addTo(map);
}


function fitMapToPoints(map, points) {
    const bounds = window.L.latLngBounds(points.map((p) => [p.lat, p.lng]));
    map.fitBounds(bounds, { padding: [24, 24] });
}


function updateDirectionsWithOrigin(directionsEl, store, user) {
    if (!directionsEl) return;

    const destination =
        directionsEl.dataset.destination || `${store.lat},${store.lng}`;

    const origin = `${user.lat},${user.lng}`;

    directionsEl.href = buildDirectionsUrl({ origin, destination });
}


function handleGeoError(statusEl, err) {
    // 1: permiso denegado | 2: no disponible | 3: timeout
    if (err?.code === 1) {
        setStatus(statusEl, "denied");
        return;
    }

    setStatus(statusEl, "error");
}
