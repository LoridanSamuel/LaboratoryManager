<?php

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '');

if(isset($_POST['forminscription'])) {
  $pseudo = htmlspecialchars($_POST['pseudo']);
  $mail = htmlspecialchars($_POST['mail']);
  $mail2 = htmlspecialchars($_POST['mail2']);
  $mdp = sha1($_POST['mdp']);
  $mdp2 = sha1($_POST['mdp2']);

  if(!empty($_POST['pseudo']) AND !empty($_POST['mail']) AND !empty($_POST['mail2']) AND !empty($_POST['mdp']) AND !empty($_POST['mdp2'])) {
    $pseudolength = strlen($pseudo);
    if($pseudolength <= 255) {
      if($mail == $mail2) {
        if(filter_var($mail, FILTER_VALIDATE_EMAIL)) {
          $reqmail = $bdd->prepare("SELECT * FROM membres WHERE mail = ?");
          $reqmail->execute(array($mail));
          $mailexist = $reqmail->rowCount();
          if($mailexist == 0) {
            $reqpseudo = $bdd->prepare("SELECT * FROM membres WHERE pseudo = ?");
            $reqpseudo->execute(array($pseudo));
            $pseudoexist = $reqpseudo->rowCount();
            if($pseudoexist == 0) {
              if($mdp == $mdp2) {
                $insertmbr = $bdd->prepare("INSERT INTO membres(pseudo, mail, motdepasse) VALUES(?, ?, ?)");
                $insertmbr->execute(array($pseudo, $mail, $mdp));
                $erreur = "Votre compte a bien été créé ! <a href=\"connexion.php\">Me connecter</a>";
              } else {
                $erreur = "Vos deux mdp ne correspondent pas !";
              }
            } else {
              $erreur = "Cet identifiant existe déjà !";
            }
          } else {
            $erreur = "Adresse mail déjà utilisée !";
          }
        } else {
          $erreur = "Votre adresse mail n'est pas valide !";
        }
      } else {
        $erreur = "vos adresses mail ne correspondent pas !";
      }
    } else {
      $erreur = "Votre identifiant ne doit pas dépasser 255 caractères !";
    }
  } else {
    $erreur = "Tous les champs doivent être complétés !";
  }
}
?>

<!DOCTYPE HTML>
<html>
  <head>
    <meta charset="utf-8">
    <title>Gestionnaire Biosolve</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
    <noscript><link rel="stylesheet" href="../assets/css/noscript.css" /></noscript>
  </head>
  <body>
    <main>
      <nav>
        <ul>
          <li>
            <a href="../index.php" class="nav--btn">Retour à l'accueil</a>
          </li>
        </ul>
      </nav>
      <div class="content">
        <div id="page-wrapper">

          <?php include('../header.php'); ?>

          <section class="section">
            <div class="section--title">
              <h2>Inscription</h2>
            </div>
            <div class='section--text'>
              <form method="POST" class="form-inscription" action="">
                <div>
                  <label for="pseudo">Identifiant :</label>
                  <input type="text" placeholder="Votre identifiant" id="pseudo" name="pseudo" value="<?php if(isset($pseudo)) { echo $pseudo; } ?>"/>
                </div>
                <div>
                  <label for="mail">Mail :</label>
                  <input type="email" placeholder="Votre mail" id="mail" name="mail" value="<?php if(isset($mail)) { echo $mail; } ?>"/>
                </div>
                <div>
                  <label for="mail2">Confirmation du Mail :</label>
                  <input type="email" placeholder="Confirmez votre mail" id="mail2" name="mail2" value="<?php if(isset($mail2)) { echo $mail2; } ?>"/>
                </div>
                <div>
                  <label for="mdp">Mot de passe :</label>
                  <input type="password" placeholder="Votre mot de passe" id="mdp" name="mdp" />
                </div>
                <div>
                  <label for="mdp2">Confirmation du mot de passe :</label>
                  <input type="password" placeholder="Confirmez votre mdp" id="mdp2" name="mdp2" />
                </div>
                <div>
                  <button type="submit" name="forminscription" class="btn">Je m'inscris</button>
                </div>
              </form>
              <?php
              if(isset($erreur))
              {
                echo '<font color="red">'.$erreur.'</font>';
              }
              ?>
            </div>
          </section>
      </div>
    </div>
  </body>
</html>

<!-- Scripts -->
  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/nav.js"></script>
  <script src="../assets/js/darkmode.js"></script>