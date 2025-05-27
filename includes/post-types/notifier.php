<?php
if (!defined('ABSPATH')) exit;

/**
 * Envoi d’un e-mail après réservation
 */
add_action('ponti_apres_reservation', function ($user_id, $creneau_id) {
    $user = get_user_by('id', $user_id);
    if (!$user || !is_email($user->user_email)) return;

    $to = $user->user_email;
    $subject = "🎯 Confirmation de votre réservation";
    
    $date   = get_post_meta($creneau_id, '_ponti_date', true);
    $heure  = get_post_meta($creneau_id, '_ponti_heure', true);
    $coach  = get_post_meta($creneau_id, '_ponti_coach', true);
    $niveau = get_post_meta($creneau_id, '_ponti_niveau', true);

    $message = "Bonjour {$user->display_name},\n\n";
    $message .= "Votre réservation est confirmée ✅\n\n";
    $message .= "🗓️ Date : {$date}\n";
    $message .= "🕒 Heure : {$heure}\n";
    $message .= "👩‍🏫 Coach : {$coach}\n";
    $message .= "⭐ Niveau : {$niveau}\n\n";
    $message .= "Merci pour votre confiance,\nL’équipe Pole Dance Guyane.";

    wp_mail($to, $subject, $message);
}, 10, 2);
