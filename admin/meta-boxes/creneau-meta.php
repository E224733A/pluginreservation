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
 * Fonction qui affiche le formulaire dans la meta box
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
 * Sauvegarde des champs à l'enregistrement du post
 */
add_action('save_post_creneau', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['_ponti_date'])) {
        update_post_meta($post_id, '_ponti_date', sanitize_text_field($_POST['_ponti_date']));
    }
    if (isset($_POST['_ponti_heure'])) {
        update_post_meta($post_id, '_ponti_heure', sanitize_text_field($_POST['_ponti_heure']));
    }
    if (isset($_POST['_ponti_coach'])) {
        update_post_meta($post_id, '_ponti_coach', sanitize_text_field($_POST['_ponti_coach']));
    }
    if (isset($_POST['_ponti_niveau'])) {
        update_post_meta($post_id, '_ponti_niveau', sanitize_text_field($_POST['_ponti_niveau']));
    }
    if (isset($_POST['_ponti_places'])) {
        update_post_meta($post_id, '_ponti_places', intval($_POST['_ponti_places']));
    }
});
