<?php
if (!defined('ABSPATH')) exit;

add_action('wp_enqueue_scripts', 'ponti_enqueue_styles', 99);

function ponti_enqueue_styles() {
    if (!is_user_logged_in()) return;

    global $post;
    if (isset($post) && has_shortcode($post->post_content, 'agenda-cours')) {
        wp_enqueue_style(
            'ponti-style',
            plugin_dir_url(__FILE__) . '../assets/css/ponti.css',
            [],
            '1.0',
            'all'
        );
    }
}

/**
 * Shortcode [mes-cours] - Affiche les crédits restants et les réservations de l'utilisateur
 */
add_shortcode('mes-cours', function () {
    if (!is_user_logged_in()) {
        return '<p class="ponti-message">Veuillez vous connecter pour consulter vos cours.</p>';
    }

    $user_id = get_current_user_id();
    $credits = (int) get_user_meta($user_id, 'ponti_credits', true);
    $abonnement = get_user_meta($user_id, 'ponti_abonnement_illimite', true);

    ob_start();

    echo '<div class="ponti-compte-wrapper">';
    echo '<h2> Mon Compte Pole Dance</h2>';

    echo '<p><strong>Crédits disponibles :</strong> ';
    echo $abonnement ? '∞ (Abonnement illimité)' : $credits . ' crédit(s)';
    echo '</p>';
	
	  echo '<h2>Cours réservé :</h2> ';
    // Récupérer et nettoyer les réservations
    $reservations = get_user_meta($user_id, 'ponti_reservations', true);
    if (!is_array($reservations)) $reservations = [];

    $reservations_valides = [];
    $today = date('Y-m-d');

    foreach ($reservations as $creneau_id) {
        if (get_post_type($creneau_id) !== 'creneau') {
            continue; // supprimé ou invalide
        }

        $date = get_post_meta($creneau_id, '_ponti_date', true);
        $heure = get_post_meta($creneau_id, '_ponti_heure', true);
        $coach = get_post_meta($creneau_id, '_ponti_coach', true);

        if (!$date || $date < $today) {
            continue; // passé ou mal configuré
        }

        $reservations_valides[] = $creneau_id;

        $heure_formatted = date_i18n('H\hi', strtotime($heure));
        $date_formatted = date_i18n('Y-m-d', strtotime($date));
        echo "<li>Le {$date_formatted} à {$heure_formatted} avec {$coach}</li>";
    }

    // Mettre à jour la liste nettoyée
    update_user_meta($user_id, 'ponti_reservations', $reservations_valides);

    if (empty($reservations_valides)) {
        echo '<p>Vous n\'avez réservé aucun créneau à venir.</p>';
    }

    echo '</ul>';
    echo '</div>';

    return ob_get_clean();
});
