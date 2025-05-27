<?php
// Sécurité
if (!defined('ABSPATH')) exit;

/**
 * Enregistre la meta box dans l’admin pour le CPT "créneau"
 */
add_action('add_meta_boxes', function () {
    add_meta_box(
        'ponti_creneau_infos',
        'Détails du créneau',
        'ponti_afficher_formulaire_creneau',
        'creneau',
        'normal',
        'default'
    );
});

/**
 * Affiche le formulaire
 */
function ponti_afficher_formulaire_creneau($post) {
    $date   = get_post_meta($post->ID, '_ponti_date', true);
    $heure  = get_post_meta($post->ID, '_ponti_heure', true);
    $coach  = get_post_meta($post->ID, '_ponti_coach', true);
    $niveau = get_post_meta($post->ID, '_ponti_niveau', true);
    $places = get_post_meta($post->ID, '_ponti_places', true);
    ?>
    <p>
        <label for="_ponti_date"><strong>Date :</strong></label><br>
        <input type="date" id="_ponti_date" name="_ponti_date" value="<?php echo esc_attr($date); ?>" required>
    </p>

    <p>
        <label for="_ponti_heure"><strong>Heure :</strong></label><br>
        <input type="time" id="_ponti_heure" name="_ponti_heure" value="<?php echo esc_attr($heure); ?>" required>
    </p>

    <p>
        <label for="_ponti_coach"><strong>Coach :</strong></label><br>
        <select id="_ponti_coach" name="_ponti_coach" required>
            <?php
            $coachs = ['Manon'];
            foreach ($coachs as $c) {
                echo "<option value='{$c}' " . selected($coach, $c, false) . ">{$c}</option>";
            }
            ?>
        </select>
    </p>

    <p>
        <label for="_ponti_niveau"><strong>Niveau :</strong></label><br>
        <select id="_ponti_niveau" name="_ponti_niveau" required>
            <option value="">-- Sélectionnez --</option>
            <option value="debutant" <?php selected($niveau, 'debutant'); ?>>Débutant</option>
            <option value="intermediaire" <?php selected($niveau, 'intermediaire'); ?>>Intermédiaire</option>
            <option value="avance" <?php selected($niveau, 'avance'); ?>>Avancé</option>
        </select>
    </p>

    <p>
        <label for="_ponti_places"><strong>Nombre de places :</strong></label><br>
        <input type="number" id="_ponti_places" name="_ponti_places" min="1" value="<?php echo esc_attr($places); ?>" required>
    </p>
    <?php
}

/**
 * Sauvegarde des champs avec validation
 */
add_action('save_post_creneau', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $errors = [];

    $date   = sanitize_text_field($_POST['_ponti_date'] ?? '');
    $heure  = sanitize_text_field($_POST['_ponti_heure'] ?? '');
    $coach  = sanitize_text_field($_POST['_ponti_coach'] ?? '');
    $niveau = sanitize_text_field($_POST['_ponti_niveau'] ?? '');
    $places = intval($_POST['_ponti_places'] ?? 0);

    if (empty($date))   $errors[] = 'La date est obligatoire.';
    if (empty($heure))  $errors[] = 'L\'heure est obligatoire.';
    if (empty($coach))  $errors[] = 'Le coach est obligatoire.';
    if (empty($niveau)) $errors[] = 'Le niveau est obligatoire.';
    if ($places < 1)    $errors[] = 'Le nombre de places doit être au moins égal à 1.';

    if (!empty($errors)) {
        // Sauvegarde une erreur temporaire (non bloquante mais informative)
        set_transient("ponti_creneau_erreur_$post_id", implode('<br>', $errors), 10);
        return;
    }

    update_post_meta($post_id, '_ponti_date', $date);
    update_post_meta($post_id, '_ponti_heure', $heure);
    update_post_meta($post_id, '_ponti_coach', $coach);
    update_post_meta($post_id, '_ponti_niveau', $niveau);
    update_post_meta($post_id, '_ponti_places', $places);
});

/**
 * Affichage des erreurs de validation après enregistrement
 */
add_action('admin_notices', function () {
    global $post;
    if (isset($post->ID)) {
        $message = get_transient("ponti_creneau_erreur_{$post->ID}");
        if ($message) {
            echo '<div class="notice notice-error is-dismissible"><p><strong>Erreur lors de l’enregistrement du créneau :</strong><br>' . $message . '</p></div>';
            delete_transient("ponti_creneau_erreur_{$post->ID}");
        }
    }
});
