<?php
// Sécurité : empêche l'exécution directe
if (!defined('ABSPATH')) exit;

/**
 * Enregistrement du Custom Post Type "Créneau"
 */
add_action('init', function () {
    register_post_type('creneau', [
        'label' => 'Créneaux',
        'labels' => [
            'name' => 'Créneaux',
            'singular_name' => 'Créneau',
            'add_new' => 'Ajouter un créneau',
            'add_new_item' => 'Ajouter un nouveau créneau',
            'edit_item' => 'Modifier le créneau',
            'new_item' => 'Nouveau créneau',
            'view_item' => 'Voir le créneau',
            'search_items' => 'Rechercher un créneau',
            'not_found' => 'Aucun créneau trouvé',
            'menu_name' => 'Créneaux'
        ],
        'public' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-calendar-alt',
        'supports' => ['title'],
        'show_in_rest' => true, // Pour compatibilité avec Gutenberg/Elementor
        'rewrite' => ['slug' => 'creneaux'],
    ]);
});
