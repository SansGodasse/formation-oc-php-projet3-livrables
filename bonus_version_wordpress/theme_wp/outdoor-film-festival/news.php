
<!-- Un article dans les actualités -->
<div class="news-article">
    <!-- Titre de l'article -->
    <h2 class="blog-post-title center"><?php the_title(); ?></h2>

    <!-- Auteur et date de publication -->
    <p class="blog-post-meta form-text text-muted"><?php the_date(); ?></p>  

    <!-- Contenu de l'article -->
    <div class="my_justify">
    	<?php the_content(); ?>
    </div>

    <!-- Revenir à l'accueil -->
    <p class="center"><a <?= 'href="index.php"' ?>>Revenir à l'accueil</a></p>
</div><!-- Fin d'un article des actualités -->