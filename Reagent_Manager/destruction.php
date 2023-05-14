<?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddmat = new PDO('mysql:host=127.0.0.1;dbname=material;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

include_once('../Connexion/cookieconnect.php');

if(isset($_SESSION['id']) AND $_SESSION['id'] > 0) {

  ?>
  <!DOCTYPE HTML>
  <html lang="fr">
    <head>
      <meta charset="utf-8">
      <title>Gestionnaire Biosolve</title>
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <meta name="description" content="Onglet de destruction d'un produit pur ou d'un réactif.">
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
                <h2>Destruction d'un produit</h2>
              </div>
              <div class='section--text'>
                <p>Quel produit souhaitez-vous détruire?</p>
                <form method="POST" action="" id="formtypeofmaterial" name="catform" class="form-destruction">
                  <input class="radiobutton" type="radio" id="choice1" name="typeOfMaterial" value="raw_material" onchange="selectAMaterialType('raw_material')"/>
                    <label class="radiolabel" for="choice1">
                      <div class="newradio"></div>
                      <span class="radiotext">Produit pur</span>
                    </label>
                  <input class="radiobutton" type="radio" id="choice2" name="typeOfMaterial" value="reagent" onchange="selectAMaterialType('reagent')"/>
                    <label class="radiolabel" for="choice2">
                      <div class="newradio"></div>
                      <span class="radiotext">Réactif</span>
                    </label>
                  <input class="radiobutton" type="radio" id="choice3" name="typeOfMaterial" value="indicator" onchange="selectAMaterialType('indicator')"/>
                    <label class="radiolabel" for="choice3">
                      <div class="newradio"></div>
                      <span class="radiotext">Indicateur</span>
                    </label>
                  <input class="radiobutton" type="radio" id="choice4" name="typeOfMaterial" value="standard" onchange="selectAMaterialType('standard')"/>
                    <label class="radiolabel" for="choice4">
                      <div class="newradio"></div>
                      <span class="radiotext">Standard</span>
                    </label>
                  <input class="radiobutton" type="radio" id="choice5" name="typeOfMaterial" value="scale" onchange="selectAMaterialType('scale')"/>
                    <label class="radiolabel" for="choice5">
                      <div class="newradio"></div>
                      <span class="radiotext">Etalon</span>
                    </label>
                  <select name="nameList" id="nameList" onchange="selectMaterial(this)"></select>
                  <table>
                    <thead id="tableHeader">
                    </thead>
                    <tbody id="tableBody">
                    </tbody>
                  </table>
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

// Return value to database
  if(isset($_SESSION['id'],$_POST['formDestruct']) AND $_SESSION['id'] > 0){
    $typeOfMaterial = htmlspecialchars($_POST['typeOfMaterial']);
    $id = htmlspecialchars($_POST['id']);
    $destructionDate = htmlspecialchars($_POST['destruction_date']);

    $updateDestructed = $bddmat->prepare("UPDATE {$typeOfMaterial}
                                          SET destruction_date = :destructionDate,
                                              status = 'Détruit'
                                          WHERE id = ? ");
    $updateDestructed->bindValue(':destructionDate', $destructionDate);
    $updateDestructed->execute();

    $updateDestructed->closeCursor();
    ?>
    <div id="oModal" class="oModal">
      <div>
        <header>
          <h2 id="headerText">Destruction</h2>
          <a href="destruction.php" id ="closeBtn" title="Fermer la fenêtre" class="right">X</a>
        </header>
        <section>
        <p id="popupText">Destruction réalisée avec succès!</p>
        </section>
        <footer>
          <div id="footerText">
            <a href="destruction.php" class="btn">Fermer</a>
          </div>
        </footer>
      </div>
    </div>
    <?php
  }
  ?>

<!-- Scripts -->
  <script src="../assets/js/nav.js"></script>
  <script src="../assets/js/darkmode.js"></script>
  <script src="../assets/js/javascript functions/destruction.js"></script>