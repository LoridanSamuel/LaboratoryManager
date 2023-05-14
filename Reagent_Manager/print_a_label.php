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
      <meta name="description" content="Onglet pour impression d'étiquettes.">
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
              <h2>Impression d'étiquettes</h2>
              </div>
              <div class='section--text'>
              <p>Pour quel produit souhaitez-vous imprimer une étiquette?</p>
              <form method="POST" action="" id="formtypeofmaterial" name="catform" class="form-print">
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
                <select name="nameList" id="nameList" onchange="selectAMaterial(this);"></select>
                <table>
                  <thead id="tableHeader">
                  </thead>
                  <tbody id="tableBody">
                  </tbody>
                </table>
              </form>
            </section>
          </div>
        </div>
      </main>
    </body>
  </html>
  <?php
}

//Choose a format and a position to print
  if(isset($_SESSION['id'],$_POST['formPrint']) AND $_SESSION['id'] >0){
    $typeOfMaterial = htmlspecialchars($_POST['typeOfMaterial']);
    $id = htmlspecialchars($_POST['id']);

    ?>
    <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
    <div id="oModal" class="oModal">
      <div>
        <header>
          <h2 id="headerText">Mise en page</h2>
          <a href="print_a_label.php" id ="closeBtn" title="Fermer la fenêtre">X</a>
        </header>
        <section>
          <form method="POST" name="format" action="printpdf.php" TARGET=_BLANK>
            <p>De quelle taille est l'étiquette à imprimer?</p>
            <div>
              <input class="radiobutton" type="radio" id="small" name="labelformat" value="small" onchange="selectAFormat('small')"/>
                <label class="radiolabel" for="small">
                  <div class="newradio"></div>
                  <span class="radiotext">Petite (63 x 23,25mm)</span>
                </label>
            </div>
            <div>
              <input class="radiobutton" type="radio" id="big" name="labelformat" value="big" onchange="selectAFormat('big')"/>
                <label class="radiolabel" for="big">
                  <div class="newradio"></div>
                  <span class="radiotext">Grande (63 x 46,5mm)</span>
                </label>
            </div>
            <div id="labeltable"></div>
            <input type="hidden" value="<?php echo($typeOfMaterial);?>" name="typeOfMaterial"/>
            <input type="hidden" value="<?php echo($id);?>" name="id"/>
            <div class="modal_buttons">
              <button type="submit" name="formPrintFinal" class="btn">Envoyer</button>
              <a href="print_a_label.php" class="btn" title="Annuler">Annuler</a>
            </div>
          </form>
        </section>
      </div>
    </div>
    <?php
  }
    ?>

<!-- Scripts -->
  <script src="../assets/js/nav.js"></script>
  <script src="../assets/js/darkmode.js"></script>
  <script src="../assets/js/javascript functions/print_a_label.js"></script>