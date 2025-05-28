<?php
if (!defined('ABSPATH')) exit;

add_action('astra_header_after', function () {
    if (!is_user_logged_in()) return;

    $user_id = get_current_user_id();
    $credits = (int) get_user_meta($user_id, 'ponti_credits', true);
    $abonnement = get_user_meta($user_id, 'ponti_abonnement_illimite', true);

    $label = $abonnement ? '∞ cours' : $credits . ' crédit(s)';
    $style = '';
    echo "<div class='ponti-credit-counter-inline'>$label</div>";
});
