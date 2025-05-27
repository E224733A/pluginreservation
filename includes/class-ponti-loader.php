<?php
class Ponti_Loader {
    public static function init() {
        // CPT créneaux
        require_once PONTI_PLUGIN_PATH . 'includes/post-types/creneau.php';

        // Shortcodes
        require_once PONTI_PLUGIN_PATH . 'public/shortcode-agenda.php';

        // Admin uniquement
        if (is_admin()) {
            require_once PONTI_PLUGIN_PATH . 'admin/admin-menu.php';

            // Champs personnalisés (meta boxes) pour les créneaux
            require_once PONTI_PLUGIN_PATH . 'admin/meta-boxes/creneau-meta.php';
        }

        // WooCommerce : crédits à l'achat
        require_once PONTI_PLUGIN_PATH . 'woocommerce/credits-handler.php';
        require_once PONTI_PLUGIN_PATH . 'public/shortcode-mes-cours.php';
        require_once PONTI_PLUGIN_PATH . 'public/credit-badge.php';
        require_once PONTI_PLUGIN_PATH . 'includes/post-types/notifier.php';
        require_once PONTI_PLUGIN_PATH . 'includes/post-types/cleaner.php';
        require_once PONTI_PLUGIN_PATH . 'admin/export-reservations.php';
		require_once PONTI_PLUGIN_PATH . 'admin/admin-abonnements.php';



    }
}
