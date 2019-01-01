
<section id="news" class="row center_elts">
    <h3 class="center col-12"><i class="fas fa-newspaper left"></i> Actualités <i class="fas fa-newspaper right"></i></h3>

    <!-- Articles catégorie actualite -->
    <?php
    if (have_posts()) {
    	while (have_posts()) {
    		the_post();
    		if (get_the_category()[0]->slug === "actualite") {
	            get_template_part( 'piece_of_news', get_post_format() );
	        }
    	}
    }
    ?>
</section>