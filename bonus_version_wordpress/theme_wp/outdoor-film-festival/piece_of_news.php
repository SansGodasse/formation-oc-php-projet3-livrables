
<!-- Un article dans les actualités -->
<div class="blog-post card col-md-5 col-12">
    <!-- Titre de l'article -->
    <h2 class="blog-post-title"><?php the_title(); ?></h2>

    <!-- Auteur et date de publication -->
    <p class="blog-post-meta form-text text-muted"><?php the_date(); ?></p>  

    <!-- Contenu de l'article -->
    <div class="my_justify my_extract">
    	<?php the_content(); ?>
    </div>

    <!-- Lire la suite -->
    <p><a class="read-more" <?= 'href="index.php?news=' . get_the_ID() . '"' ?>>Lire la suite...</a></p>
</div><!-- Fin d'un article des actualités -->