<?php
if (!defined('ABSPATH')) exit;
class Ponti_Loader {
    private static function safe_require($path) {
        if (file_exists($path)) {
            require_once $path;
        } else {
            error_log("❌ Fichier manquant : $path");
        }
    }

    public static function init() {
        self::safe_require(PONTI_PLUGIN_PATH . 'includes/post-types/creneau.php');
        self::safe_require(PONTI_PLUGIN_PATH . 'public/shortcode-agenda.php');
        self::safe_require(PONTI_PLUGIN_PATH . 'public/shortcode-mes-cours.php');
        self::safe_require(PONTI_PLUGIN_PATH . 'public/shortcode-mes-cours-coach.php');
        self::safe_require(PONTI_PLUGIN_PATH . 'public/credit-badge.php');
        self::safe_require(PONTI_PLUGIN_PATH . 'woocommerce/credits-handler.php');

        self::safe_require(PONTI_PLUGIN_PATH . 'includes/post-types/notifier.php');
        self::safe_require(PONTI_PLUGIN_PATH . 'includes/post-types/cleaner.php');

        if (is_admin()) {
            self::safe_require(PONTI_PLUGIN_PATH . 'admin/admin-menu.php');
            self::safe_require(PONTI_PLUGIN_PATH . 'admin/meta-boxes/creneau-meta.php');
            self::safe_require(PONTI_PLUGIN_PATH . 'admin/export-reservations.php');
            self::safe_require(PONTI_PLUGIN_PATH . 'admin/admin-abonnements.php');
        }
    }
}
