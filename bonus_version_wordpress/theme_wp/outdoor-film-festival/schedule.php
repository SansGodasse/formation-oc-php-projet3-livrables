<section id="films" class="row center_elts">
    <h3 class="center col-12"><i class="fas fa-film left"></i> Programme des films projetés <i class="fas fa-film right"></i></h3>

    <!-- Articles catégorie film -->
    <?php
    if ( have_posts() ) : while ( have_posts() ) : the_post();
        if (get_the_category()[0]->slug === "film") {
            get_template_part( 'film', get_post_format() );
        }
    endwhile; endif;
    ?>
</section>