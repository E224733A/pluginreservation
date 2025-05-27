<?php
// Exemple de menu admin
add_action('admin_menu', function () {
    add_menu_page(
        'Ponti Cours',
        'Ponti Cours',
        'manage_options',
        'ponti-cours',
        function () {
            echo '<div class="wrap"><h1>Bienvenue dans le plugin Ponti Cours</h1></div>';
        }
    );
});
