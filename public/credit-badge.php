<?php
if (!defined('ABSPATH')) exit;

add_action('astra_header_after', function () {
    if (!is_user_logged_in()) return;

    $user_id = get_current_user_id();
    $credits = (int) get_user_meta($user_id, 'ponti_credits', true);
    $abonnement = get_user_meta($user_id, 'ponti_abonnement_illimite', true);

    $label = $abonnement ? '∞ cours' : $credits . ' crédit(s)';    $style = 'position:fixed;top:15px;right:120px;z-index:9999;
              background:#fff;color:#000;padding:4px 8px;border-radius:15px;
              box-shadow:0 2px 4px rgba(0,0,0,0.1);font-size:12px;
              font-weight:bold;font-family:sans-serif;';
    $responsive_css = '<style>@media (max-width: 600px) { .ponti-credit-counter-inline { top : 24px !important; right: 32px !important; left: auto !important; font-size:8px !important; padding:2px 8px !important; } }</style>';
    echo $responsive_css;
    echo "<div class='ponti-credit-counter-inline' style=\"$style\">$label</div>";
});
