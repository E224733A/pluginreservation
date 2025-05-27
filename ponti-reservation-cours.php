<?php
/**
 * Plugin Name: Ponti - Réservation de Cours
 * Description: Plugin de réservation de cours avec crédits WooCommerce.
 * Version: 1.0
 * Author: Nono
 * git : oui
 * 
 */

if (!defined('ABSPATH')) exit;

// Définir le chemin de base
define('PONTI_PLUGIN_PATH', plugin_dir_path(__FILE__));

$init = plugin_dir_path(__FILE__) . 'includes/init.php';
$loader = plugin_dir_path(__FILE__) . 'includes/class-ponti-loader.php';

if (file_exists($init)) require_once $init;
if (file_exists($loader)) {
    require_once $loader;
    if (class_exists('Ponti_Loader')) {
        Ponti_Loader::init();
    } else {
        error_log('❌ Classe Ponti_Loader non trouvée.');
    }
} else {
    error_log('❌ Fichier class-ponti-loader.php manquant.');
}

function ponti_enqueue_styles() {
    wp_enqueue_style('ponti-css', plugin_dir_url(__FILE__) . 'assets/css/ponti.css', array(), '1.0');
}
add_action('wp_enqueue_scripts', 'ponti_enqueue_styles');
