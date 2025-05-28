<?php
if (!defined('ABSPATH')) exit;

add_action('astra_header_after', function () {
    if (!is_user_logged_in()) return;

    $user_id = get_current_user_id();
    $credits = (int) get_user_meta($user_id, 'ponti_credits', true);
    $abonnement = get_user_meta($user_id, 'ponti_abonnement_illimite', true);    $label = $abonnement ? '∞ cours' : $credits . ' crédit(s)';    $style = 'position:fixed;top:2vh;right:2vw;z-index:9999;
              background:#fff;color:#000;padding:0.4vh 2vw;border-radius:15px;
              box-shadow:0 1px 3px rgba(0,0,0,0.1);font-size:1.8vh;
              font-weight:bold;font-family:sans-serif;';
    $responsive_css = '<style>
        @media (max-width: 1024px) {
            .ponti-credit-counter-inline {
                position: absolute !important;
                top: 2.5vh !important;
                left: 50% !important;
                transform: translateX(-50%) !important;
                font-size: 1.6vh !important;
                padding: 0.3vh 1.5vw !important;
            }
        }
        @media (max-width: 768px) {
            .ponti-credit-counter-inline {
                top: 3vh !important;
                font-size: 1.5vh !important;
                padding: 0.3vh 1.5vw !important;
            }
        }
        @media (max-width: 480px) {
            .ponti-credit-counter-inline {
                top: 3.5vh !important;
                font-size: 1.4vh !important;
                padding: 0.25vh 1.5vw !important;
            }
        }
    </style>';
    echo $responsive_css;
    echo "<div class='ponti-credit-counter-inline' style=\"$style\">$label</div>";
});
