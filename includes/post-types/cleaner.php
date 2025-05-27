<?php
if (!defined('ABSPATH')) exit;

/**
 * 🔁 Enregistre une tâche cron quotidienne au chargement du plugin
 */
add_action('init', function () {
    if (!wp_next_scheduled('ponti_creneaux_cleanup_daily')) {
        wp_schedule_event(time(), 'daily', 'ponti_creneaux_cleanup_daily');
    }
});

/**
 * 🧹 Fonction exécutée chaque jour automatiquement
 */
add_action('ponti_creneaux_cleanup_daily', function () {
    $today = date('Y-m-d');

    $query = new WP_Query([
        'post_type'      => 'creneau',
        'posts_per_page' => -1,
        'meta_query'     => [[
            'key'     => '_ponti_date',
            'value'   => $today,
            'compare' => '<',
            'type'    => 'DATE'
        ]]
    ]);

    foreach ($query->posts as $post) {
        wp_delete_post($post->ID, true);
        error_log("🗑️ Créneau supprimé automatiquement : #" . $post->ID);
    }
});
