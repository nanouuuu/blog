<?php 
    include 'inc/init.inc.php'; // initialisation du site
    include 'inc/fonctions.inc.php'; // des fonctions utiles

    // Si l'utilisateur est connecté on le redirige sur profil.php
    if(user_is_connected()) {
        header('location: profil.php');
    }

    // Traitement PHP
    // Inscription utilisateur :
    if( isset($_POST['pseudo']) && isset($_POST['email']) && isset($_POST['mdp']) ) {
        $pseudo = trim($_POST['pseudo']);
        $email = trim($_POST['email']);
        $mdp = trim($_POST['mdp']);

    // Variable de contrôle
    $erreur = false;
    
    // Contrôles :
    // Taille du pseudo : entre 4 et 16
    $taille_pseudo = iconv_strlen($pseudo);
    if($taille_pseudo < 4 || $taille_pseudo > 16) {
        $msg .= '<div class="alert alert-danger mb-3 text-center col-sm-6 mx-auto">Attention, <br>le pseudo doit avoir entre 4 et 16 caractères.</div>';
        $erreur = true;
    }

    // caractères dans le pseudo :
    // on teste le pseudo avec une expression régulière (voir wikipédia ou "regex" sur stackoverflow)
        $verif_caracteres = preg_match('#^[A-Za-z0-9]+$#', $pseudo);
        /*
        Expression régulière :
        ----------------------
        - dans les crochets [], tous les caractères autorisés (ici, les lettres majuscules et minuscules et les chiffres)
        - les # permettent de préciser le début et la fin de l'expression (sinon, il est possible d'uliser les / à la place)
        - le + permet de dire que l'on peut avoir plusieurs fois le même caractère dans la chaîne
        - le ^ permet de dire que la chaîne doit obligatoirement commencer par les caractères proposés
        - le $ permet de dire que la chaîne doit obligatoirement finir par les caractères proposés
        - lorsque l'on bloque le début et la fin, toute la chaîne ne peut contenir que ces caractères
        */

        // if($verif_caracteres != true) équivaut à la ligne suivante
            if( ! $verif_caracteres) {
                $msg .= '<div class="alert alert-danger mb-3 text-center col-sm-6 mx-auto">Attention, <br>le pseudo ne peut contenir que des caractères alphanumériques.</div>';
                $erreur = true;

        }

        // disponibilité du pseudo (pseudo en index unique dans la BDD)
        $verif_pseudo = $pdo->prepare("SELECT * FROM utilisateur WHERE pseudo = :pseudo");
        $verif_pseudo->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $verif_pseudo->execute();
        if($verif_pseudo->rowCount() > 0) { // si on a plus de 0 ligne
            $msg .= '<div class="alert alert-danger mb-3 text-center col-sm-6 mx-auto">Attention, <br>Pseudo indisponible.</div>';
            $erreur = true;
        }

        // format du mail
        if( ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg .= '<div class="alert alert-danger mb-3 text-center col-sm-6 mx-auto">Attention, <br>Le format du mail est incorect</div>';
            $erreur = true;
        }


        // disponibilité du mail
        $verif_mail = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email");
        $verif_mail->bindParam(':email', $email, PDO::PARAM_STR);
        $verif_mail->execute();
        if($verif_mail->rowCount() > 0) {
            $msg .= '<div class="alert alert-danger mb-3 text-center col-sm-6 mx-auto">Attention, <br>mail indisponible.</div>';
            $erreur = true;
        }

        // le mot de passe doit avoir au moins 4 caractères
        if(iconv_strlen($mdp) < 4) {
            $msg .= '<div class="alert alert-danger mb-3 text-center col-sm-6 mx-auto">Attention, <br>le mot de passe doit avoir au minimum 4 caractères</div>';
            $erreur = true;
        }

        // enregistrement
        if($erreur == false) {
            // cryptage du mot de passe : bonne pratique : password_hash($mdp, PASSWORD_DEFAULT);
            $mdp = password_hash($mdp, PASSWORD_DEFAULT);

            /*
            Pour le statut :
            ----------------
            1 = membre
            2 = admin
            3 = référenceur
            ...
            */
            $enregistrement = $pdo->prepare("INSERT INTO utilisateur (pseudo, mdp, email, date_inscription, statut) VALUES (:pseudo, :mdp, :email, CURDATE(), 1)");
            $enregistrement->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
            $enregistrement->bindParam(':mdp', $mdp, PDO::PARAM_STR);
            $enregistrement->bindParam(':email', $email, PDO::PARAM_STR);
            $enregistrement->execute();
        }






    }

    // début des affichages
    include 'inc/header.inc.php';
    include 'inc/nav.inc.php';
?>  

    
        <div class="bg-light p-5 rounded">
            <h1 class="text-center border-bottom pb-3">Blog | Inscription</h1>
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
                            <label for="email">Email</label>
                            <input type="text" name="email" id="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="mdp">Mot de passe</label>
                            <input type="text" name="mdp" id="mdp" class="form-control">
                        </div>  
                        <button type="submit" class="btn btn-outline-dark w-100">Inscription</button>                      
                    </form>
                </div>            
            </div>
        </div>

<?php
include 'inc/footer.inc.php';
    