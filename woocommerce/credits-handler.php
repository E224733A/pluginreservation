<?php
if (!defined('ABSPATH')) exit;

// Créditer l'utilisateur après une commande "terminée"
add_action('woocommerce_order_status_completed', 'ponti_ajouter_credits_par_produit_precis', 10, 1);

function ponti_ajouter_credits_par_produit_precis($order_id) {
    $order = wc_get_order($order_id);
    $user_id = $order->get_user_id();
    if (!$user_id) return;

    $credits = (int) get_user_meta($user_id, 'ponti_credits', true);
    $avait_abonnement = get_user_meta($user_id, 'ponti_abonnement_illimite', true);
    $activer_abonnement = false;

    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        $product_id = $product->get_id();
        $product_name = trim($product->get_name());
        $quantity = $item->get_quantity();

        if (!has_term('Pôle Dance', 'product_cat', $product_id)) {
			error_log("Produit analysé : {$product_name} | ID : {$product_id}");
            continue;
        }

        switch ($product_name) {
            case 'Réservation 1 cour':
                $credits += 1 * $quantity;
                break;

            case 'Réservation 5 cours':
                $credits += 5 * $quantity;
                break;

            case 'Réservation 10 cours':
                $credits += 10 * $quantity;
                break;

            case 'Abonnements':
                $activer_abonnement = true;
                break;

            default:
                break;
        }
    }

    // Mise à jour des crédits uniquement si l'utilisateur n'a pas d'abonnement actif ou activé maintenant
    if (!$avait_abonnement && !$activer_abonnement) {
        update_user_meta($user_id, 'ponti_credits', $credits);
        error_log("Crédits mis à jour pour user #{$user_id} : {$credits} crédits restants.");
    }

    // Activation de l'abonnement s'il était dans la commande
    if ($activer_abonnement) {
        update_user_meta($user_id, 'ponti_abonnement_illimite', true);
        error_log("Abonnement illimité activé pour user #{$user_id}.");
    }
}
