 <?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddspreadsheet = new PDO('mysql:host=127.0.0.1;dbname=spreadsheet;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

include_once('../Connexion/cookieconnect.php');

if(isset($_SESSION['id']) AND $_SESSION['id'] > 0) {
  
  $namehtml = "";

    $selectMaterial = $bddspreadsheet->query("SELECT * FROM material_specificity");
      while ($name = $selectMaterial->fetch()) {
        $optionValue = $name['reference']. ' - ' .$name['name'];
        $namehtml .= '<option value="' .$optionValue. '">' .$optionValue. '</option>';
      }
    $selectMaterial->closeCursor();

  ?>
  <!DOCTYPE HTML>
  <html>
    <head>
      <meta charset="utf-8">
      <title>Gestionnaire Biosolve</title>
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <meta name="description" content="Selection de la matière pour laquelle on souhaite éditer une feuille de calcul.">
      <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
      <noscript><link rel="stylesheet" href="../assets/css/noscript.css" /></noscript>
    </head>
    <body>
      <main>
        
        <?php include('../nav.php'); ?>

        <div class="content">
          <div id="page-wrapper">
            
            <?php include('../header.php'); ?>

            <section class="section">
              <div class='section--title'>
                <h2>Matière</h2>
              </div>
              <div class='section--text'>
                <p>Quel est le n° de la matière que vous souhaitez analyser?</p>
                <form method="POST" class="form-selectmatname" action="">
                  <div>
                    <input type="text" list="name" placeholder="N° - Nom de la matière" name="mat_name" required/>
                    <datalist id="name"><?php echo($namehtml) ?></datalist>
                    <button type="submit"  name="form_selectmatname" class="btn">Envoyer</button>
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


if(isset($_SESSION['id']) AND $_SESSION['id'] > 0 AND isset($_POST['form_selectmatname'])) {
  $mat_name = htmlspecialchars($_POST['mat_name']);

  //If there is a ref n° AND a name.
    if(strpos($mat_name, ' - ') == true){
      list($reference, $name) = explode(" - ", $mat_name, 2);

      //If the ref n° is a 4 digits number
        if(ctype_digit($reference) AND strlen($reference) == 4) {

          //check in the database if that ref n° is already known
            $reqref = $bddspreadsheet->query('SELECT * FROM material_specificity WHERE reference = '.$reference);
            $refexist = $reqref->rowCount();

            //If the ref n° is known
              if($refexist == 1) {
                while ($mat = $reqref->fetch()) {
                
                  //If the name linked to the ref n° is not the good one --> Error
                    if($mat['name'] !== $name) {
                      ?>
                        <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
                        <div id="oModal" class="oModal">
                          <div>
                            <header>
                              <h2 id="headerText">Selection</h2>
                              <a href="spreadsheet_index.php" id ="closeBtn" title="Fermer la fenêtre">X</a>
                            </header>
                            <section>
                              <p id="popupText">La référence indiquée (<?php echo($reference);?>) ne correspond pas au nom de matière (<?php echo($name)?>).</p>
                            </section>
                            <footer>
                              <div id="footerText">
                                <a href="spreadsheet_index.php" class="btn">Fermer</a>
                              </div>
                            </footer>
                          </div>
                        </div>
                      <?php

                  //If the name linked to the ref n° is the good one --> go to select a spec
                    } else {
                      ?>
                        <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
                        <div id="oModal" class="oModal">
                          <div>
                            <header>
                              <h2 id="headerText">Selection</h2>
                              <a href="spreadsheet_index.php" id ="closeBtn" title="Fermer la fenêtre">X</a>
                            </header>
                            <section>
                            <p id="popupText">Vous avez selectionné la matière <?php echo($reference);?> - <?php echo($name)?>. Voulez-vous continuer?</p>
                            </section>
                            <footer>
                              <form method="post">
                                <input type="hidden" value="<?php echo($reference);?>" name="reference"/>
                                <div class="modal_buttons">
                                  <button type="submit" name="form_selectspec" formaction="spec_manager.php" class="btn">Oui</button>
                                  <a href="spreadsheet_index.php" class="btn">Non</a>
                                </div>
                              </form>
                            </footer>
                          </div>
                        </div>
                      <?php
                    }
                }
                $reqref->closeCursor();

            //If the ref n° is unknown --> open a form windows to indicate the density and update the database
              } else {
                ?>
                  <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
                  <div id="oModal" class="oModal">
                    <div>
                      <header>
                        <h2 id="headerText">Attention</h2>
                        <a href="spreadsheet_index.php" id ="closeBtn" title="Fermer la fenêtre">X</a>
                      </header>
                      <section>
                      <p id="popupText">La matière <?php echo($reference);?> - <?php echo($name)?> n'est pas encore renseignée dans la base de données. Voulez-vous continuer?</p>
                      </section>
                      <footer>
                        <form method="post" action="">
                          <label for ="density">Densité théorique (à 20°C) : </label>
                          <input type="text" placeholder="Densité" id="density" name="density" required/>
                          <input type="hidden" value="<?php echo($reference);?>" name="reference"/>
                          <input type="hidden" value="<?php echo($name);?>" name="name"/>
                          <br/><br/>
                          <div class="modal_buttons">
                            <button type="submit" name="form_updatebdd" class="btn">Oui</button>
                            <a href="spreadsheet_index.php" class="btn">Non</a>
                          </div>
                        </form>
                      </footer>
                    </div>
                  </div>
                <?php
              }

      //If the ref n° is not a 4 digits number --> Error				
        } else {
          ?>
            <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
            <div id="oModal" class="oModal">
              <div>
                <header>
                  <h2 id="headerText">ATTENTION</h2>
                  <a href="spreadsheet_index.php" id ="closeBtn" title="Fermer la fenêtre" class="right">X</a>
                </header>
                <section>
                <p id="popupText">Le n° de référence doit uniquement comporter 4 chiffres!</p>
                </section>
                <footer>
                  <div id="footerText">
                    <a href="spreadsheet_index.php" class="btn">Fermer</a>
                  </div>
                </footer>
              </div>
            </div>
          <?php
        }

  //If the ref n° or the name is missing (no "-") --> Error
    } else {
      ?>
      <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
      <div id="oModal" class="oModal">
        <div>
          <header>
            <h2 id="headerText">ATTENTION</h2>
            <a href="spreadsheet_index.php" id ="closeBtn" title="Fermer la fenêtre" class="right">X</a>
          </header>
          <section>
          <p id="popupText">Merci de renseigner un n° de référence et un nom de matière.</p>
          </section>
          <footer>
            <div id="footerText">
              <a href="spreadsheet_index.php" class="btn">Fermer</a>
            </div>
          </footer>
        </div>
      </div>
      <?php
    }	
}

// Database insertion
  if(isset($_POST['form_updatebdd']) AND isset($_SESSION['id']) AND $_SESSION['id'] > 0){

    $reference = htmlspecialchars($_POST['reference']);
    $name = htmlspecialchars($_POST['name']);
    $density = htmlspecialchars($_POST['density']);

    $insertmat = $bddspreadsheet->prepare("INSERT INTO material_specificity(reference, name, density) VALUES(?, ?, ?)");
    $insertmat->execute(array($reference, $name, $density));
    $insertmat->closeCursor();

    ?>
      <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
      <div id="oModal" class="oModal">
        <div>
          <header>
            <h2 id="headerText">Création de matière</h2>
            <a href="spreadsheet_index.php" id ="closeBtn" title="Fermer la fenêtre" class="right">X</a>
          </header>
          <section>
          <p id="popupText">La matière a bien été insérée dans la base de données!</p>
          </section>
          <footer>
            <div id="footerText">
              <a href="spreadsheet_index.php" class="btn">Fermer</a>
            </div>
          </footer>
        </div>
      </div>
    <?php
  }
?>

<!-- Scripts -->
  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/nav.js"></script>
  <script src="../assets/js/darkmode.js"></script>