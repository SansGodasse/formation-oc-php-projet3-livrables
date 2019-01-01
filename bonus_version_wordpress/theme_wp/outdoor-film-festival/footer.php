<?php
$contactEmail = getContactEmail();
?>
            <footer class="row">
                <p class="col-6 center">
                    <i class="fas fa-film"></i> Association Festival des Films de Plein Air<br />
                    54 rue Charlie Chaplin<br />
                    75000 Paris
                </p>
                <p class="col-6 center">
                    Présidente : Jennifer Viala<br />
                    06 69 91 73 26<br />
                    <a <?= 'href="mailto:' . $contactEmail . '"' ?>><?= $contactEmail ?></a>
                </p>
                <?php
                if ( is_active_sidebar( 'footer_map' ) ) {
                ?>
                    <div id="footer_map_wrapper" class="col-12" role="complementary">
                        <?php dynamic_sidebar( 'footer_map' ); ?>
                    </div><!-- #primary-sidebar -->
                <?php
                }
                ?>
            </footer>
        </div><!-- /.container -->
        <?php wp_footer(); ?>
    </body>
</html>