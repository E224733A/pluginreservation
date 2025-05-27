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


add_shortcode('mes-cours-coach', function () {
    if (!is_user_logged_in()) {
        return '<p> Veuillez vous connecter pour voir vos cours.</p>';
    }

    $user = wp_get_current_user();
    $coach_login = $user->user_login;
    $coach_email = $user->user_email;

    // Liste des coachs autorisés (identifiants exacts)
    $coachs_autorises = ['Manon'];

    // Liste sécurisée des emails associés (optionnel pour verrou plus strict)
    $emails_autorises = ['alexispontikis1@gmail.com']; // remplace par les vrais emails si besoin

    if (!in_array($coach_login, $coachs_autorises) || !in_array($coach_email, $emails_autorises)) {
        return '<p>⛔ Accès refusé : cette section est réservée aux coachs autorisés.</p>';
    }

    $query = new WP_Query([
        'post_type'      => 'creneau',
        'posts_per_page' => -1,
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
        'meta_query'     => [[
            'key'     => '_ponti_coach',
            'value'   => $coach_login,
            'compare' => '='
        ]]
    ]);

    if (!$query->have_posts()) {
        return '<p>Vous n’avez aucun cours programmé pour le moment.</p>';
    }

    ob_start();
    echo '<div class="coach-cours-wrapper">';
    echo '<h2> Mes créneaux en tant que coach</h2>';
    echo '<ul class="coach-cours-list">';

    while ($query->have_posts()) {
        $query->the_post();
        $id = get_the_ID();
        $date = get_post_meta($id, '_ponti_date', true);
        $heure = get_post_meta($id, '_ponti_heure', true);
        $niveau = get_post_meta($id, '_ponti_niveau', true);

        $date_fmt = date_i18n('l j F Y', strtotime($date));
        $heure_fmt = date_i18n('H\hi', strtotime($heure));

        echo "<li> {$date_fmt} à {$heure_fmt} – Niveau : <strong>{$niveau}</strong></li>";
    }

    echo '</ul>';
    echo '</div>';

    wp_reset_postdata();
    return ob_get_clean();
});
