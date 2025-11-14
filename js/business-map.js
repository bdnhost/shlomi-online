/**
 * Business Map JavaScript
 * שלומי אונליין - מפות עסקים
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // הקוד כבר נמצא בשורטקודים
        // זה placeholder למקרה שנצטרך פונקציות נוספות בעתיד

        // פונקציה עוזרת ליצירת מפה
        window.shlomiCreateBusinessMap = function(mapId, lat, lng, zoom, markers) {
            if (typeof L === 'undefined') {
                console.error('Leaflet is not loaded');
                return;
            }

            var map = L.map(mapId).setView([lat, lng], zoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            if (markers && markers.length > 0) {
                markers.forEach(function(markerData) {
                    var marker = L.marker([markerData.lat, markerData.lng]).addTo(map);
                    if (markerData.title) {
                        var popupContent = '<strong>' + markerData.title + '</strong>';
                        if (markerData.url) {
                            popupContent = '<a href="' + markerData.url + '">' + popupContent + '</a>';
                        }
                        marker.bindPopup(popupContent);
                    }
                });

                // התאמת המפה להציג את כל הסמנים
                if (markers.length > 1) {
                    var bounds = L.latLngBounds(markers.map(function(m) { return [m.lat, m.lng]; }));
                    map.fitBounds(bounds, { padding: [50, 50] });
                }
            }

            return map;
        };

    });

})(jQuery);
