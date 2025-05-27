<?php
if (!defined('ABSPATH')) exit;

/**
 * Ajoute une page admin pour gérer les abonnements manuellement
 */
add_action('admin_menu', function () {
    add_submenu_page(
        'ponti-reservations',
        'Gestion des abonnements',
        'Abonnements',
        'manage_options',
        'ponti-abonnements',
        'ponti_afficher_page_abonnements'
    );
});

/**
 * Affiche la page des abonnements avec option de résiliation
 */
function ponti_afficher_page_abonnements() {
    // Résiliation déclenchée ?
    if (isset($_POST['ponti_stop_abonnement'])) {
        $uid = intval($_POST['ponti_stop_abonnement']);
        update_user_meta($uid, 'ponti_abonnement_illimite', false);
        echo "<div class='updated notice'><p>Abonnement résilié pour l'utilisateur #" . esc_html($uid) . ".</p></div>";
    }

    // Récupération des utilisateurs abonnés
    $users = get_users([
        'meta_key'   => 'ponti_abonnement_illimite',
        'meta_value' => true,
    ]);

    echo '<div class="wrap"><h1>Abonnements actifs</h1>';

    if (empty($users)) {
        echo '<p>Aucun abonnement illimité actif actuellement.</p>';
    } else {
        echo '<table class="widefat fixed striped">';
        echo '<thead><tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Crédits</th>
                <th>Rôle</th>
                <th>Actions</th>
              </tr></thead><tbody>';

        foreach ($users as $user) {
            $credits = (int) get_user_meta($user->ID, 'ponti_credits', true);

            echo '<tr>';
            echo '<td>' . esc_html($user->display_name) . '</td>';
            echo '<td><a href="mailto:' . esc_attr($user->user_email) . '">' . esc_html($user->user_email) . '</a></td>';
            echo '<td>' . ($credits ?: '0') . '</td>';
            echo '<td>' . implode(', ', $user->roles) . '</td>';
            echo '<td>';
            echo '<form method="post" onsubmit="return confirm(\'Confirmer la résiliation de cet abonnement ?\');">';
            echo '<input type="hidden" name="ponti_stop_abonnement" value="' . esc_attr($user->ID) . '">';
            submit_button('Résilier', 'delete', '', false);
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    }

    echo '</div>';
}
