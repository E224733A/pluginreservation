<?php
if (!defined('ABSPATH')) exit;

add_action('astra_header_after', function () {
    if (!is_user_logged_in()) return;

    $user_id = get_current_user_id();
    $credits = (int) get_user_meta($user_id, 'ponti_credits', true);
    $abonnement = get_user_meta($user_id, 'ponti_abonnement_illimite', true);

    $label = $abonnement ? '∞ cours' : $credits . ' crédit(s)';
    $style = 'position:fixed;top:20px;right:70px;z-index:9999;
              background:#fff;color:#000;padding:6px 12px;border-radius:20px;
              box-shadow:0 2px 6px rgba(0,0,0,0.2);font-size:14px;
              font-weight:bold;font-family:sans-serif;';
    $responsive_css = '<style>@media (max-width: 600px) { .ponti-credit-counter-inline { top: auto !important; bottom: 20px !important; right: 20px !important; left: auto !important; font-size:13px !important; padding:5px 10px !important; } }</style>';
    echo $responsive_css;
    echo "<div class='ponti-credit-counter-inline' style=\"$style\">$label</div>";
});
