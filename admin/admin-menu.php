<?php
add_action('admin_menu', function () {
    add_menu_page(
        'Ponti Réservations',
        'Ponti Réservations',
        'manage_options',
        'ponti-reservations',
        function () {
            echo '<div class="wrap"><h1>Ponti – Réservation de Cours</h1><p>Configuration et suivi à venir.</p></div>';
        },
        'dashicons-calendar-alt'
    );
});
