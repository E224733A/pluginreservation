<?php
if (!defined('ABSPATH')) exit;

add_action('wp_enqueue_scripts', 'ponti_enqueue_styles', 99);

/**
 * 🔁 Traitement du formulaire de réservation
 */
add_action('init', function () {
    if (
        !is_user_logged_in() ||
        !isset($_POST['ponti_reservation_creneau_id']) ||
        !isset($_POST['ponti_nonce']) ||
        !wp_verify_nonce($_POST['ponti_nonce'], 'ponti_reserver_cours')
    ) return;
    
    $user_id = get_current_user_id();
    $creneau_id = intval($_POST['ponti_reservation_creneau_id']);
    $abonnement = get_user_meta($user_id, 'ponti_abonnement_illimite', true);
    $credits = (int) get_user_meta($user_id, 'ponti_credits', true);

    if (get_post_type($creneau_id) !== 'creneau') {
        wp_die('Créneau invalide.');
    }

    $reservations = get_user_meta($user_id, 'ponti_reservations', true);
    if (!is_array($reservations)) $reservations = [];

    if (in_array($creneau_id, $reservations)) {
        wp_die('Vous avez déjà réservé ce créneau.');
    }

    $places = (int) get_post_meta($creneau_id, '_ponti_places', true);
    if ($places <= 0) {
        wp_die('Il n’y a plus de places disponibles pour ce créneau.');
    }

    if (!$abonnement && $credits <= 0) {
        wp_die('Vous n’avez pas assez de crédits pour réserver.');
    }

    $reservations[] = $creneau_id;
    update_user_meta($user_id, 'ponti_reservations', $reservations);

    if (!$abonnement) {
        update_user_meta($user_id, 'ponti_credits', $credits - 1);
    }

    update_post_meta($creneau_id, '_ponti_places', max(0, $places - 1));
    do_action('ponti_apres_reservation', $user_id, $creneau_id);
    wp_redirect(add_query_arg('reservation', 'success', wp_get_referer()));
    exit;
});

/**
 * 🔳 Shortcode [agenda-cours]
 */
add_shortcode('agenda-cours', 'ponti_afficher_agenda_cours');

function ponti_afficher_agenda_cours() {
    if (!is_user_logged_in()) {
        return '<div class="agenda-wrapper"><p class="agenda-message">Veuillez vous connecter pour accéder à votre agenda de cours.</p></div>';
    }

    $user_id = get_current_user_id();
    $abonnement = get_user_meta($user_id, 'ponti_abonnement_illimite', true);
    $credits = (int) get_user_meta($user_id, 'ponti_credits', true);
    $reservations = get_user_meta($user_id, 'ponti_reservations', true);
    if (!is_array($reservations)) $reservations = [];

    $tri = isset($_GET['tri']) ? sanitize_text_field($_GET['tri']) : 'date_asc';
    $filtre_niveau = isset($_GET['niveau']) ? sanitize_text_field($_GET['niveau']) : '';

    $order = $tri === 'date_desc' ? 'DESC' : 'ASC';

    $meta_query = [[
        'key'     => '_ponti_date',
        'value'   => date('Y-m-d'),
        'compare' => '>=',
        'type'    => 'DATE',
    ]];

    if ($filtre_niveau) {
        $meta_query[] = ['key' => '_ponti_niveau', 'value' => $filtre_niveau, 'compare' => '='];
    }

    $query = new WP_Query([
        'post_type'      => 'creneau',
        'posts_per_page' => -1,
        'orderby'        => 'meta_value',
        'order'          => $order,
        'meta_query'     => $meta_query,
    ]);

    if (!$query->have_posts()) {
        return '<div class="agenda-wrapper"><p class="agenda-message">Aucun créneau disponible pour le moment.</p></div>';
    }

    ob_start();
    echo '<div class="agenda-wrapper">';
    echo '<h2 class="agenda-title">Réservez vos créneaux</h2>';
    echo '<p class="agenda-legende-niveaux">🌹 = Débutant &nbsp;&nbsp; 🌹🌹 = Intermédiaire &nbsp;&nbsp; 🌹🌹🌹 = Confirmé</p>';

    echo '<form method="get">';
    echo '<div class="agenda-filtres">';

    echo '<div class="filtre-bloc">';
    echo '<label for="tri">Trier par :</label>';
    echo '<select name="tri" id="tri">
        <option value="date_asc"' . selected($tri, 'date_asc', false) . '>Date croissante</option>
        <option value="date_desc"' . selected($tri, 'date_desc', false) . '>Date décroissante</option>
    </select>';
    echo '</div>';

    echo '<div class="filtre-bloc">';
    echo '<label for="niveau">Niveau :</label>';
    echo '<select name="niveau" id="niveau">
        <option value="">Tous</option>
        <option value="debutant"' . selected($filtre_niveau, 'debutant', false) . '>Débutant</option>
        <option value="intermediaire"' . selected($filtre_niveau, 'intermediaire', false) . '>Intermédiaire</option>
        <option value="avance"' . selected($filtre_niveau, 'avance', false) . '>Avancé</option>
    </select>';
    echo '</div>';

    echo '</div>';
    echo '</form>';

    echo <<<JS
<script>
document.addEventListener('DOMContentLoaded', function () {
    const triSelect = document.getElementById('tri');
    const niveauSelect = document.getElementById('niveau');

    function updateFilters() {
        const params = new URLSearchParams(window.location.search);
        params.set('tri', triSelect.value);
        params.set('niveau', niveauSelect.value);
        window.location.search = params.toString();
    }

    triSelect.addEventListener('change', updateFilters);
    niveauSelect.addEventListener('change', updateFilters);
});
</script>
JS;

    echo '<ul class="agenda-liste">';
    while ($query->have_posts()) {
        $query->the_post();
        $id = get_the_ID();
        $date = get_post_meta($id, '_ponti_date', true);
        $heure = get_post_meta($id, '_ponti_heure', true);
        $coach = get_post_meta($id, '_ponti_coach', true);
        $niveau = get_post_meta($id, '_ponti_niveau', true);
        $places = get_post_meta($id, '_ponti_places', true);

        $date_obj = new DateTime($date);
        $heure_obj = new DateTime($heure);
        $date_formatted = date_i18n('l j F Y', $date_obj->getTimestamp());
        $heure_formatted = date_i18n('H\hi', $heure_obj->getTimestamp());

        $niveau_fleurs = $niveau === 'debutant' ? '🌹' : ($niveau === 'intermediaire' ? '🌹🌹' : '🌹🌹🌹');

        echo '<li class="agenda-item">';
        echo "<div class='agenda-info'>";
        echo "<span class='agenda-date'>{$date_formatted} à {$heure_formatted}</span><br>";
        echo "Coach : <span class='agenda-coach'>{$coach}</span> | Niveau : <span class='agenda-niveau'>{$niveau_fleurs}</span> | Places : <span class='agenda-places'>{$places}</span>";
        echo "</div>";

        if (in_array($id, $reservations)) {
            echo "<span class='agenda-reserved-badge'>Déjà Réservé</span>";
        } elseif ($abonnement || $credits > 0) {
            echo "<form method='post' class='agenda-form'>";
            wp_nonce_field('ponti_reserver_cours', 'ponti_nonce');
            echo "<input type='hidden' name='ponti_reservation_creneau_id' value='{$id}'>";
            echo "<button type='submit' class='agenda-button'>Réserver</button>";
            echo "</form>";
            echo "<p class='agenda-note'>⛔ Réservation non annulable. Demande de remboursement possible jusqu’à 72h avant le cours via notre <a href='/contact'>formulaire</a> ou téléphone.</p>";
        } else {
            echo "<p class='agenda-message agenda-message-error'>Vous n'avez plus de crédits disponibles.</p>";
        }

        echo '</li>';
    }

    echo '</ul>';
    echo '</div>';

    wp_reset_postdata();
    return ob_get_clean();
}
