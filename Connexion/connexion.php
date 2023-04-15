<?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '');

include_once('cookieconnect.php');

if(isset($_POST['formconnexion'])) {
  $pseudoconnect = htmlspecialchars($_POST['pseudoconnect']);
  $mdpconnect = sha1($_POST['mdpconnect']);
  if(!empty($pseudoconnect) AND !empty($mdpconnect)) {
    $requser = $bdd->prepare("SELECT * FROM membres WHERE pseudo = ? AND motdepasse = ?");
    $requser->execute(array($pseudoconnect, $mdpconnect));
    $userexist = $requser->rowCount();
    if($userexist == 1) {
      if(isset($_POST['rememberme'])) {
        setcookie('pseudo',$pseudoconnect,time()+365*24*3600,null,null,false,true);
        setcookie('passwordlabo',$mdpconnect,time()+365*24*3600,null,null,false,true);
      }
      $userinfo = $requser->fetch();
      $_SESSION['id'] = $userinfo['id'];
      $_SESSION['pseudo'] = $userinfo['pseudo'];
      $_SESSION['mail'] = $userinfo['mail'];
      header("Location: ../mainboard.php?id=".$_SESSION['id']);
    } else {
      $erreur = "Mauvais pseudo ou mot de passe !";
    }
    $requser->closeCursor();
  } else {
    $erreur = "Tous les champs doivent être complétés !";
  }
}

?>
<!DOCTYPE HTML>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <title>Gestionnaire Biosolve</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Page de connexion.">
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
              <h2>Connexion</h2>
            </div>
            <div class='section--text'>
              <form class="form-connexion" method="POST" action="">
                <div>
                  <input type="text" name="pseudoconnect" placeholder="Identifiant" />
                </div>
                <div>
                  <input type="password" name="mdpconnect" placeholder="Mot de Passe" />
                </div>
                <div>
                  <input type="checkbox" class="check" name="rememberme" id="remembercheckbox" />
                  <label class="cbx" for="remembercheckbox">
                    <span>
                      <svg width="12px" height="10px">
                        <use xlink:href="#check"></use>
                      </svg>
                    </span>
                    <span>
                      Se souvenir de moi
                    </span>
                  </label>
                </div>
                <div>
                  <button type="submit"  name="formconnexion" class="btn">Se connecter</button>
                </div>
                <a href="inscription.php">Pas encore inscrit ?</a>
                <?php
                if(isset($erreur)) {
                  echo '<font color="red">'.$erreur."</font>";
                }
                ?>
              </form>
            </div>
          </section>
        </div>
      </div>
    </main>
  </body>
</html>

<!--SVG Sprites-->
  <svg class="inline-svg">
    <symbol id="check" viewbox="0 0 12 10">
      <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
    </symbol>
  </svg>

<!-- Scripts -->
  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/nav.js"></script>
  <script src="../assets/js/darkmode.js"></script>