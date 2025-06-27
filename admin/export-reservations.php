<?php
if (!defined('ABSPATH')) exit;

/**
 * Ajoute un sous-menu d'export CSV sous "Ponti Réservations"
 */
add_action('admin_menu', function () {
    add_submenu_page(
        'ponti-reservations',
        'Exporter les réservations',
        'Export CSV',
        'manage_options',
        'ponti-export',
        'ponti_export_reservations_page'
    );
});

/**
 * Affichage du formulaire et export CSV
 */
function ponti_export_reservations_page() {
    if (
        isset($_POST['creneau_id']) &&
        isset($_POST['export_nonce']) &&
        wp_verify_nonce($_POST['export_nonce'], 'export_csv_reservations')
    ) {
        $creneau_id = intval($_POST['creneau_id']);
        ponti_generer_csv($creneau_id);
        exit;
    }

    $creneaux = get_posts([
        'post_type' => 'creneau',
        'numberposts' => -1,
        'orderby' => 'meta_value',
        'meta_key' => '_ponti_date',
        'order' => 'DESC'
    ]);

    echo '<div class="wrap">';
    echo '<h1>Exporter les réservations</h1>';
    echo '<form method="post">';
    wp_nonce_field('export_csv_reservations', 'export_nonce');
    echo '<label for="creneau_id">Sélectionnez un créneau :</label><br>';
    echo '<select name="creneau_id" required>';
    foreach ($creneaux as $creneau) {
        $date = get_post_meta($creneau->ID, '_ponti_date', true);
        $heure = get_post_meta($creneau->ID, '_ponti_heure', true);
        echo "<option value='{$creneau->ID}'>{$date} à {$heure}</option>";
    }
    echo '</select><br><br>';
    echo '<button type="submit" class="button button-primary">Télécharger CSV</button>';
    echo '</form>';
    echo '</div>';
}

/**
 * Génère et envoie un fichier CSV des réservations du créneau
 */
function ponti_generer_csv($creneau_id) {
    if (!current_user_can('manage_options')) {
        wp_die('Accès non autorisé');
    }

    // Nettoyage du tampon de sortie
    if (ob_get_level()) {
        ob_end_clean();
    }

    // Forcer le téléchargement
    $filename = "reservations_creneau_{$creneau_id}.csv";
    header('Content-Type: text/csv; charset=UTF-8');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // Ajout d'un BOM UTF-8 pour Excel
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // En-têtes CSV
    fputcsv($output, ['ID Utilisateur', 'Nom', 'Email'], ';');

    $users = get_users();
    foreach ($users as $user) {
        $reservations = get_user_meta($user->ID, 'ponti_reservations', true);
        if (is_array($reservations) && in_array($creneau_id, $reservations)) {
            fputcsv($output, [$user->ID, $user->display_name, $user->user_email], ';');
        }
    }

    fclose($output);
    exit;
}
