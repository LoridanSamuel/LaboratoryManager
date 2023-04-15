<?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '');

include_once('cookieconnect.php');

if(isset($_SESSION['id'])) {
  $requser = $bdd->prepare("SELECT * FROM membres WHERE id = ?");
  $requser->execute(array($_SESSION['id']));
  $user = $requser->fetch();
  if(isset($_POST['newpseudo']) AND !empty($_POST['newpseudo']) AND $_POST['newpseudo'] != $user['pseudo']) {
    $newpseudo = htmlspecialchars($_POST['newpseudo']);
    $insertpseudo = $bdd->prepare("UPDATE membres SET pseudo = ? WHERE id = ?");
    $insertpseudo->execute(array($newpseudo, $_SESSION['id']));
    header('Location: ../index.php?id='.$_SESSION['id']);
  }
  if(isset($_POST['newmail']) AND !empty($_POST['newmail']) AND $_POST['newmail'] != $user['mail']) {
    $newmail = htmlspecialchars($_POST['newmail']);
    $insertmail = $bdd->prepare("UPDATE membres SET mail = ? WHERE id = ?");
    $insertmail->execute(array($newmail, $_SESSION['id']));
    header('Location: ../index.php?id='.$_SESSION['id']);
  }
  if(isset($_POST['newmdp1'],$_POST['newmdp2']) AND !empty($_POST['newmdp1']) AND !empty($_POST['newmdp2'])) {
    $mdp1 = sha1($_POST['newmdp1']);
    $mdp2 = sha1($_POST['newmdp2']);
    if($mdp1 == $mdp2) {
      $insertmdp = $bdd->prepare("UPDATE membres SET motdepasse = ? WHERE id = ?");
      $insertmdp->execute(array($mdp1, $_SESSION['id']));
      header('Location: ../index.php?id='.$_SESSION['id']);
    } else {
      $msg = "Vos deux mdp ne correspondent pas !";
    }
  }
  if(isset($_POST['newpseudo']) AND $_POST['newpseudo'] == $user['pseudo']) {
    header('Location: ../index.php?id='.$_SESSION['id']);
  }
  ?>

  <!DOCTYPE HTML>
  <html lang="fr">
    <head>
      <meta charset="utf-8">
      <title>Gestionnaire Biosolve</title>
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <meta name="description" content="Edition du profil d'utilisateur.">
      <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
      <noscript><link rel="stylesheet" href="../assets/css/noscript.css" /></noscript>
    </head>
    <body>
      <main>
        <nav>
          <ul>
            <li>
              <a href="../mainboard.php" class="nav--btn">Retour au menu principal</a>
            </li>
          </ul>
        </nav>
        <div class="content">
          <div id="page-wrapper">
      
            <?php include('../header.php'); ?>

            <section class="section">
              <div class="section--title">
                <h2>Edition de mon profil</h2>
              </div>
              <div class='section--text'>
                <form method="POST" class="form-inscription" action="">
                  <div class="flex width50 paddingAll borderTop borderBottom">
                    <label for="newpseudo" class="align-right width50 noMargin paddingAll">Identifiant :</label>
                    <input type="text" name="newpseudo" id="newpseudo" placeholder="Votre identifiant" value="<?php echo $user['pseudo']; ?>"/>
                  </div>
                  <div class="flex width50 paddingAll borderBottom">
                    <label for="newmail" class="align-right width50 noMargin paddingAll">Mail :</label>
                    <input type="text" name="newmail" id="newmail" placeholder="Mail" value="<?php echo $user['mail']; ?>"/>
                  </div>
                  <div class="flex width50 paddingAll borderBottom">
                    <label for="newmdp1" class="align-right width50 noMargin paddingAll">Mot de passe :</label>
                    <input type="password" name="newmdp1" id="newmdp1" placeholder="Mot de passe"/>
                  </div>
                  <div class="flex width50 paddingAll borderBottom">
                    <label for="newmdp2" class="align-right width50 noMargin paddingAll">Confirmation - Mot de passe :</label>
                    <input type="password" name="newmdp2" id="newmdp2" placeholder="Confirmation du mot de passe"/>
                  </div>
                  <div class="width50 marginAll">
                    <button type="submit"  name="forminscription" class="btn">Mettre à jour mon profil!</button>
                  </div>
                </form>
                <?php if(isset($msg)) { echo $msg; } ?>
              </div>
            </section>
          </div>
        </div>
      </main>
    </body>
  </html>
  <?php
} else {
  header("Location: connexion.php");}
?>

<!-- Scripts -->
  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/nav.js"></script>
  <script src="../assets/js/darkmode.js"></script>