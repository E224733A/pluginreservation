<?php
if (!defined('ABSPATH')) exit;

add_action('wp_enqueue_scripts', 'ponti_enqueue_styles', 99);

add_action('astra_header_after', function () {
    if (!is_user_logged_in()) return;

    $user_id = get_current_user_id();
    $credits = (int) get_user_meta($user_id, 'ponti_credits', true);
    $abonnement = get_user_meta($user_id, 'ponti_abonnement_illimite', true);

    $label = $abonnement ? '∞ cours' : $credits . ' crédit(s)';
    $style = '';
    echo "<a href='https://www.pontipole.fr/mon-compte/' class='ponti-credit-counter-inline'>$label</a>";
});
