<!-- En-tête -->
<?php get_header(); ?>

    <div class="container">

    <?php
    if (isset($_GET['news'])) {
        // Affichage d'une actualité en totalité
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                if (get_the_category()[0]->slug === "actualite") {
                    if (get_the_ID() == $_GET['news']) {
                        // Actualité sélectionnée
                        get_template_part( 'news' );
                        break;
                    }
                }
            }
        }
        
    } elseif (isset($_GET['delete_visitor'])) {
        // Suppression d'un visiteur inscrit
        get_template_part( 'delete_visitor' );

    } elseif (isset($_GET['send_account_link'])) {
        // Envoi du lien de modification par email
        get_template_part( 'send_account_link' );

    } elseif (isset($_GET['visitor_account'])) {
        // Affichage des infos d'un visiteur
        get_template_part( 'visitor_account' );

    } else {
        // Page d'accueil
    ?>
        <!-- Présentation -->
        <?php get_template_part( 'presentation' ); ?>

        <!-- Films -->
        <?php get_template_part( 'schedule' ); ?>

        <!-- Préinscription -->
        <?php get_template_part( 'registration' ); ?>

        <!-- Actualités -->
        <?php get_template_part( 'news_list' ); ?>

    <?php
    }
    ?>

<!-- Pied de page -->
<?php get_footer(); ?>
