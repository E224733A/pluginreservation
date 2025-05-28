<?php
if (!defined('ABSPATH')) exit;

add_action('astra_header_after', function () {
    if (!is_user_logged_in()) return;

    $user_id = get_current_user_id();
    $credits = (int) get_user_meta($user_id, 'ponti_credits', true);
    $abonnement = get_user_meta($user_id, 'ponti_abonnement_illimite', true);

    $label = $abonnement ? '∞ cours' : $credits . ' crédit(s)';    $style = 'position:relative;display:inline-block;vertical-align:middle;margin-left:10px;
              background:#fff;color:#000;padding:4px 12px;border-radius:20px;
              box-shadow:0 1px 3px rgba(0,0,0,0.1);font-size:13px;
              font-weight:bold;font-family:sans-serif;';
    $responsive_css = '<style>
        .ponti-credit-counter-inline {
            display: inline-block !important;
            vertical-align: middle !important;
            margin: 0 10px !important;
        }
        @media (max-width: 768px) {
            .ponti-credit-counter-inline {
                font-size: 11px !important;
                padding: 3px 10px !important;
            }
        }
    </style>';
    echo $responsive_css;
    echo "<div class='ponti-credit-counter-inline' style=\"$style\">$label</div>";
});
