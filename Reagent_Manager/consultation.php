<?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddmat = new PDO('mysql:host=127.0.0.1;dbname=material;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

include_once('../Connexion/cookieconnect.php');

if(isset($_SESSION['id']) AND $_SESSION['id'] > 0) {
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
                        
        <?php include('../nav.php'); ?>
        
        <div class="content">
          <div id="page-wrapper">
            
            <?php include('../header.php'); ?>


            <section class='section'>
              <div class='section--title'>
                <h2>Consultation de la base de données</h2>
              </div>
              <div class='section--text'>
                <p>Quel base de données souhaitez-vous consulter?</p>
                <form method="POST" action="consultation_table.php" id="formtypeofdatabase">
                  <div>
                    <input class="radiobutton" type="radio" id="choice1" name="typeOfDatabase" value="raw_material"/>
                      <label class="radiolabel" for="choice1">
                        <div class="newradio"></div>
                        <span class="radiotext">Produit pur</span>
                      </label>
                    <input class="radiobutton" type="radio" id="choice2" name="typeOfDatabase" value="reagent"/>
                      <label class="radiolabel" for="choice2">
                        <div class="newradio"></div>
                        <span class="radiotext">Réactif</span>
                      </label>
                    <input class="radiobutton" type="radio" id="choice3" name="typeOfDatabase" value="indicator"/>
                      <label class="radiolabel" for="choice3">
                        <div class="newradio"></div>
                        <span class="radiotext">Indicateur</span>
                      </label>
                    <input class="radiobutton" type="radio" id="choice4" name="typeOfDatabase" value="standard"/>
                      <label class="radiolabel" for="choice4">
                        <div class="newradio"></div>
                        <span class="radiotext">Standard</span>
                      </label>
                    <input class="radiobutton" type="radio" id="choice5" name="typeOfDatabase" value="scale"/>
                      <label class="radiolabel" for="choice5">
                        <div class="newradio"></div>
                        <span class="radiotext">Etalon</span>
                      </label>
                  </div>
                  <div>
                    <button type="submit" name="formtypeofdatabase" class="btn">Envoyer</button>
                  </div>
                </form>
              </div>
            </section>
          </div>
        </div>
      </main>
    </body>
  </html>
  <?php
}
?>

<!-- Scripts -->
  <script src="../assets/js/nav.js"></script>
  <script src="../assets/js/darkmode.js"></script>