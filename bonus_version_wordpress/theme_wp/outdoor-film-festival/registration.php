<!-- Préinscription -->
<section id="registration">
    <h3 id="registration_title" class="center col-12">
        <i class="fas fa-notes-medical left"></i> Se préinscrire <i class="fas fa-notes-medical right"></i>
    </h3>

	<?php
    // Est-ce que le formulaire est bien rempli ?
	if (checkRegistrationForm()) {
        /* On enregistre la préinscription et on envoie le mail de confirmation */
        if (registerVisitor()) {
    ?>
    <!-- Préinscription effectuée -->
    <p class="center col-12 my_message good">
        <span class="validated">🗸</span> Vous êtes préinscris au festival !
    </p>
    <p class="center col-12">
        Pour modifier vos informations, <a class="a_bad" href=<?= '"index.php?visitor_account=' . htmlspecialchars($_POST['visitor_email']) . '"' ?>>Cliquez ici.</a>
    </p>
    <?php
        } else {
    ?>
    <!-- Visiteur déjà enregistré -->
    <p class="center col-12 my_message bad">
        <span class="incomplete">❌</span> L'email entré est déjà pris ! Vous vous êtes sans doute déjà préinscrit. Si vous souhaitez vous désinscrire ou modifier votre inscription, <a class="a_bad" <?= 'href="index.php?send_account_link='. htmlspecialchars($_POST['visitor_email']) . '"'?>>cliquez ici</a> pour recevoir un lien par email.
    </p>
    <?php
        }
	} else {
        if (isset($_POST['visitor_name'])) {
    ?>
    <!-- Le formulaire est mal rempli... Boulet. -->
    <p class="center col-12 my_message bad">
        <span class="incomplete">❌</span> Vous devez impérativement remplir tous les champs dotés d'une * pour vous préinscrire !
    </p>
    <?php
        }
	?>

    <!-- Formulaire de préinscription fait maison -->
    <form id="registration_form" method="post" action="index.php#registration" class="center col-12">
        <div class="row align-items-center">
            <div id="registration_part_1" class="col-lg-6 col-12">
                <div class="row">

                    <!-- Nom et prénom -->
                    <p class="col-12">
                        <label for="visitor_name">Nom et prénom <span class="mandatory">*</span></label><br />
                        <input class="my_input" type="text" name="visitor_name" id="visitor_name" />
                    </p>

                    <!-- Email -->
                    <p class="col-12">
                        <label for="visitor_email">Email <span class="mandatory">*</span></label><br />
                        <input class="my_input" type="email" name="visitor_email" id="visitor_email" /><br />
                    </p>
                </div>
                <div class="row form_number_wrapper">

                    <!-- Dates et nombre de personnes -->
                    <p class="col-12 center my_underline">Combien de personnes ? <span class="mandatory">*</span></p>
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
                <input id="registration_btn" class="btn" type="submit" value="Je me préinscris !">
            </div>
        </div>
    </form>
    <?php
    }
    ?>
</section>