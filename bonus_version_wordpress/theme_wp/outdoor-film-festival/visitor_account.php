
<?php
$visitorEmail = htmlspecialchars($_GET['visitor_account']);
?>

<!-- Préinscription -->
<section id="visitor_account">

    <h3 id="visitor_account_title" class="center col-12">
        <i class="fas fa-notes-medical left"></i> Vos informations <i class="fas fa-notes-medical right"></i>
    </h3>

	<?php
    // Est-ce que le formulaire est bien rempli ?
	if (checkRegistrationForm()) {
        // On modifie les informations et on envoie le mail de confirmation 
        updateVisitor();
    ?>
    <!-- Modifications effectuées -->
    <p class="center col-12 my_message good">
        <span class="validated">🗸</span> Les modifications ont été éffectuées.
    </p>
    <?php

	} else {
        if (isset($_POST['visitor_name'])) {
    ?>
    <!-- Le formulaire est mal rempli... Boulet. -->
    <p class="center col-12 my_message bad">
        <span class="incomplete">❌</span> Vous devez impérativement remplir tous les champs dotés d'une * pour vous préinscrire !
    </p>
    <?php
        }
    }
	?>

    <!-- Formulaire de préinscription fait maison -->
    <form id="visitor_account_form" method="post" <?= 'action="index.php?visitor_account=' . $visitorEmail . '"' ?> class="center col-12">
        <div class="row align-items-center">
            <div id="visitor_account_part_1" class="col-lg-6 col-12">
                <div class="row">

                    <!-- Email -->
                    <p class="col-12">
                        Votre adresse email : <?= $visitorEmail ?>
                    </p>
                    <input type="hidden" name="visitor_email" <?= 'value="' . $visitorEmail . '"' ?>>

                    <!-- Nom et prénom -->
                    <p class="col-12">
                        <label for="visitor_name">Nom et prénom <span class="mandatory">*</span></label><br />
                        <input class="my_input" type="text" name="visitor_name" id="visitor_name" <?= 'value="' . getVisitorName($visitorEmail) . '"'  ?> />
                    </p>
                </div>
                <div class="row form_number_wrapper">

                    <!-- Dates et nombre de personnes -->
                    <p class="col-12 center my_underline">
                        Combien de personnes ? <span class="mandatory">*</span>
                    </p>
                    <?php
                    $i = 0;
                    if (have_posts()) {
                        while (have_posts()) {
                            the_post();
                            if (get_the_category()[0]->slug === "film") {
                    ?>
                    <p class="col-12 col-sm-6 center">
                        <label for=<?= '"' . PART1_DATE_ID . $i .'"' ?>><?php the_title(); ?></label><br />
                        <input type="number" name=<?= '"' . PART1_DATE_ID . $i .'"' ?> id=<?= '"' . PART1_DATE_ID . $i .'"' ?> />
                    </p>
                    <?php
                                $i++;
                            }
                        }
                    }
	                ?>
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <input id="visitor_account_btn" class="btn" type="submit" value="Mettre à jour mes informations">
            </div>
        </div>
    </form>

    <h3 class="center col-12">
        <i class="fas fa-trash-alt left"></i> Désinscription <i class="fas fa-trash-alt right"></i>
    </h3>

    <p class="center col-12">
        Pour vous désinscrire du festival, <a href=<?= '"index.php?delete_visitor=' . $visitorEmail . '"' ?>>cliquez ici.</a>
    </p>

    <!-- Retour à l'accueil -->
    <p class="center col-12">
        <a href="index.php">Revenir au site du festival</a>
    </p>
</section>