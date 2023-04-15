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
      <meta name="description" content="Onglet de création d'un mode opératoire de préparation de solution.">
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
                <h2>Rédaction d'un mode opératoire de préparation de solution</h2>
              </div>
              <div class='section--text'>
                <p>Quel type de solution souhaitez-vous créer?</p>
                <form method="POST" name="catform" class="form-SOP" action="create_a_SOP_final.php">
                  <div id="radioB">
                    <input class="radiobutton" type="radio" id="reagent" name="typeOfMaterial" value="reagent" onchange="selectAType('reagent')"/>
                      <label class="radiolabel" for="reagent">
                        <div class="newradio"></div>
                        <span class="radiotext">Réactif</span>
                      </label>
                    <input class="radiobutton" type="radio" id="indicator" name="typeOfMaterial" value="indicator" onchange="selectAType('indicator')"/>
                      <label class="radiolabel" for="indicator">
                        <div class="newradio"></div>
                        <span class="radiotext">Indicateur</span>
                      </label>
                    <input class="radiobutton" type="radio" id="standard" name="typeOfMaterial" value="standard" onchange="selectAType('standard')"/>
                      <label class="radiolabel" for="standard">
                        <div class="newradio"></div>
                        <span class="radiotext">Standard</span>
                      </label>
                    <input class="radiobutton" type="radio" id="scale" name="typeOfMaterial" value="scale" onchange="selectAType('scale')"/>
                      <label class="radiolabel" for="scale">
                        <div class="newradio"></div>
                        <span class="radiotext">Etalon</span>
                      </label>
                  </div>
                  <div>
                    <label for="name">Nom de la solution :</label>
                    <input type="text" list="nameList" placeholder="Nom de la solution" name="name" id="name" required/>
                    <datalist id="nameList"></datalist>
                  </div>
                  <div>
                    <label for="concentration">Concentration :</label>
                    <input type="text" list="concentrationList" placeholder="Concentration" name="concentration" id="concentration" required/>
                    <datalist id="concentrationList"></datalist>
                  </div>
                  <div>
                    <label for="solvent">Solvant utilisé :</label>
                    <input type="text" list="solventList" placeholder="Solvant" name="solvent" id="solvent" required/>
                    <datalist id="solventList"></datalist>
                  </div>
                  <div>
                    <label for="packaging">Volume final en mL :</label>
                    <input type="text" list="packagingList" placeholder="Volume final (mL)" name="packaging" id="packaging" required/>
                    <datalist id="packagingList"></datalist>
                  </div>
                  <div>
                    <label for="number_of_product">Nombre de produits utilisés (solvant compris) :</label>
                    <input type="text" placeholder="Nombre de produits" name="numberOfProduct" id="numberOfProduct" required/>
                  </div>
                  <div>
                    <label for="lifetime">Durée de validité de la solution :</label>
                    <input type="text" list="lifetimeList" placeholder="Durée de validité" name="lifetime" id="lifetime" required/>
                    <datalist id="lifetimeList"></datalist>
                  </div>
                  <div>
                    <button type="submit" name="formSOP" class="btn">Envoyer</button>
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
  <!-- <script src="../assets/js/jquery.min.js"></script> -->
  <script src="../assets/js/nav.js"></script>
  <script src="../assets/js/darkmode.js"></script>
  <Script src="../assets/js/javascript functions/create_a_SOP.js"></Script>
