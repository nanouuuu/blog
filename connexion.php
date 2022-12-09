<?php
include 'inc/init.inc.php'; // initialisation du site
include 'inc/fonctions.inc.php'; // des fonctions utiles

    // Déconnexion utilisateur
    // session_destroy est lu en fin de page, donc déconnexion en fin de page en étant redirigé vers profil. Là, le if dit que l'utilisateur n'es pas connecté donc redirigé vers connexion, et ne reste pas sur profil.
    if( isset($_GET['action']) && $_GET['action'] == 'deconnexion' ) {
        session_destroy();
    }

    // Si l'utilisateur est connecté on le redirige sur profil.php
    if(user_is_connected()) {
        header('location: profil.php');
    }


    // Traitements PHP
    // Connexion utilisateur
    if( isset($_POST['pseudo']) && isset($_POST['mdp'])) {
        $pseudo = trim($_POST['pseudo']);
        $mdp = trim($_POST['mdp']);

        $connexion = $pdo->prepare("SELECT id_utilisateur, pseudo, mdp, email, statut, date_format(date_inscription, '%d/%m/%Y') AS date_inscription FROM utilisateur WHERE pseudo = :pseudo");
        $connexion->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $connexion->execute();

        if($connexion->rowCount() > 0) {
            // on a récupéré une ligne donc le pseudo est ok
            // vérification du mdp // https://www.php.net/manual/fr/function.password-verify.php // https://phptherightway.com/#password_hashing
            $infos = $connexion->fetch(PDO::FETCH_ASSOC);
            if(password_verify($mdp, $infos['mdp'])) {

                // on enregistre les informations uitilisateurs dans la session
                $_SESSION['utilisateur'] = array();
                $_SESSION['utilisateur']['pseudo'] = $infos['pseudo'];
                $_SESSION['utilisateur']['email'] = $infos['email'];
                $_SESSION['utilisateur']['date_inscription'] = $infos['date_inscription'];
                $_SESSION['utilisateur']['statut'] = $infos['statut'];
                $_SESSION['utilisateur']['id_utilisateur'] = $infos['id_utilisateur'];

                /*
                $_SESSION['utilisateur'] = array();  équivaut aux lignes 32 à 38
                foreach($infos AS $indice => $valeur) {
                    if($indice != 'mdp') {
                        $_SESSION['utilisateur'][$indice] = $valeur;
                    }
                }
                */
                // redirection vers la page profil.php // attention, JAMAIS d'affichage avant, sinon, cela marche en local, mais cela plante en ligne !!!
                header('location: profil.php');

            } else {
                // erreur sur le mdp
                $msg .= '<div class="alert alert-danger mb-3">Attention, <br>Erreur sur le pseudo et/ou le mot de passe.</div>';
            }

        } else {
            // erreur sur le pseudo
            $msg .= '<div class="alert alert-danger mb-3">Attention, <br>Erreur sur le pseudo et/ou le mot de passe.</div>';
        }

    }


// début des affichages
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
?>


        <div class="bg-light p-5 rounded">
            <h1 class="text-center border-bottom pb-3">Blog | connexion</h1>
            <p class="lead text-center">Bienvenue sur notre blog</p>
            <?php echo $msg; ?>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-sm-6 mx-auto">
                    <form method="post" class="border mt-5 p-3">
                        <div class="mb-3">
                            <label for="pseudo">Pseudo</label>
                            <input type="text" name="pseudo" id="pseudo" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="mdp">Mot de passe</label>
                            <input type="text" name="mdp" id="mdp" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-outline-dark w-100">Connexion</button>
                    </form>
                </div>
            </div>
        </div>

<?php
include 'inc/footer.inc.php';
