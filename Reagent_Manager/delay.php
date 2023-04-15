<?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddmat = new PDO('mysql:host=127.0.0.1;dbname=material;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

include_once('../Connexion/cookieconnect.php');

// Combobox filling
  if(isset($_SESSION['id']) AND $_SESSION['id'] > 0) {

    $namehtml = "";

    $selectRM = $bddmat->query('SELECT DISTINCT mat_name FROM raw_material WHERE status != "Détruit" AND status != "Non ouvert" AND status !="OK" ORDER BY mat_name');
      while ($RM = $selectRM->fetch()) {
        $namehtml .= '<option value="'.$RM['mat_name'].'">'.$RM['mat_name'].'</option>';
      }
    $selectRM->closeCursor();

    ?>
    <!DOCTYPE HTML>
    <html lang="fr">
      <head>
        <meta charset="utf-8">
        <title>Gestionnaire Biosolve</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="description" content="Onglet de prolongation de la durée de vie d'un produit pur.">
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
                  <h2>Prolongation de la durée de vie d'un produit pur</h2>
                </div>
                <div class='section--text'>
                  <p>De quel produit souhaitez-vous prolonger la durée de vie?</p>
                  <form method="POST" class="form-purity" action="">
                    <select name="name" id="name" onchange="request(this);" required>
                      <option value="none" disabled selected>Selection</option>'
                      <?php echo $namehtml ?>
                    </select>
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
        </main>
      </body>
    </html>
    <?php
  }

// purity after retest request
  if(isset($_SESSION['id'],$_POST['formPurity']) AND $_SESSION['id'] > 0){
    $mat_name = htmlspecialchars($_POST['name']);
    $id = htmlspecialchars($_POST['id']);
    $retesting_date = htmlspecialchars($_POST['retesting_date']);
    $purity = htmlspecialchars($_POST['purity']);
    $purity_retested = htmlspecialchars($_POST['purity_retested']);

    echo(substr($purity, 0, 4));

    if(substr($purity, 0, 4) == "&gt;") {
      $particle = "";
    } else {
      $particle = "de ";
    }

    if($purity <> "null" AND $purity_retested <> "null") {
      $sumup = "<p>La pureté déclarée sur le COA fournisseur de ce produit est " .$particle . $purity. " et au moins une valeur de retest a été renseignée pour ce produit (" .$purity_retested. ")</p>";
    } else if($purity <> "null" AND $purity_retested == "null") {
      $sumup = "<p>La pureté déclarée sur le COA fournisseur de ce produit est " .$particle . $purity. ".</p>";
    } else if($purity == "null" AND $purity_retested <> "null") {
      $sumup = "<p>Au moins une valeur de retest a été renseignée pour ce produit (" .$purity_retested. ")</p>";
    } else if($purity == "null" AND $purity_retested == "null") {
      $sumup = "<p>Aucune valeur de pureté n'a été renseignée pour ce produit.</p>";
    }

    ?>
    <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
    <div id="oModal" class="oModal">
      <div>
        <header>
          <h2 id="headerText">ATTENTION</h2>
          <a href="delay.php" id ="closeBtn" title="Fermer la fenêtre">X</a>
        </header>
        <section>
          <?php echo($sumup);?>
          <p id="popupText">Souhaitez-vous renseigner une valeur de retest?</p>
        </section>
        <footer>
          <div id="footerText">
            <form method="post" action="">
              <div>
                <input type="text" name="purity_retested" id="purity_retest"/>
                <input type="hidden" value="<?php echo($id)?>" name="id"/>
                <input type="hidden" value="<?php echo($retesting_date)?>" name="retesting_date"/>
                <input type="hidden" value="<?php echo($purity)?>" name="purity"/>
              </div>
              <div>
                <button type="submit" name="formDelay" class="btn">Terminer</button>
              </div>
            </form>
          </div>
        </footer>
      </div>
    </div>
    <?php
  }	

// Database insertion
  if(isset($_SESSION['id'],$_POST['formDelay']) AND $_SESSION['id'] > 0){
    $id = htmlspecialchars($_POST['id']);
    $retesting_date = htmlspecialchars($_POST['retesting_date']);
    $retesting_dateTimestamp = strtotime($retesting_date);
    $extended_date = date('Y-m-d', strtotime('+ 2 year', $retesting_dateTimestamp));
    $purity_retested = htmlspecialchars($_POST['purity_retested']);
    $purity = htmlspecialchars($_POST['purity']);

    if($purity_retested == null){
      ?>
      <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
      <div id="oModal" class="oModal">
        <div>
          <header>
            <h2 id="headerText">ATTENTION</h2>
            <a href="delay.php" id ="closeBtn" title="Fermer la fenêtre" class="right">X</a>
          </header>
          <section>
            <p id="popupText">Vous êtes sur le point de prolonger la durée de vie d'un produit pur sans indiquer de valeur de retest de la pureté. Souhaitez-vous continuer?</p>
          </section>
          <footer>
            <div id="footerText">
              <form method="post" action="">
                <input type="hidden" value="<?php echo($id)?>" name="id"/>
                <input type="hidden" value="<?php echo($retesting_date)?>" name="retesting_date"/>
                <input type="hidden" value="<?php echo($extended_date)?>" name="extended_date"/>
                <div class="modal_buttons">
                  <button type="submit" name="formDelayWithoutRetest" class="btn">Oui</button>
                  <a href="delay.php" class="btn">Non</a>
                </div>
              </form>
            </div>
          </footer>
        </div>
      </div>
      <?php
    } else {
      if($purity <> null) {

        if(substr($purity, -1) == "%") {
          $purity = substr($purity, 0, -1);
        }

        if(substr($purity_retested, -1) == "%") {
          $purity_retested_number = substr($purity_retested, 0, -1);
        } else {
          $purity_retested_number = $purity_retested;
        }

        if(substr($purity, 0, 5) == "&gt;=") {
          $purity = substr($purity, 5);
          $sign = "yes";
        }

        if(($sign == "yes" AND $purity_retested_number <= $purity) OR ($sign <> "yes" AND $purity_retested_number <= $purity * 0.95) OR ($sign == '' AND $purity_retested_number >= $purity * 1.05)) {
          echo("<div class='alert'>La pureté renseignée n'est pas conforme à la donnée fournisseur.</div>");
        } else {

          $updateDelay = $bddmat->prepare('UPDATE raw_material SET retesting_date = ?, extended_date = ? , purity_retested = ? WHERE id = ? ');
            $updateDelay->execute(array($retesting_date, $extended_date, $purity_retested, $id)) or die('Erreur SQL !'.$sql.'<br />');
          $updateDelay->closeCursor();

          ?>
          <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
          <div id="oModal" class="oModal">
            <div>
              <header>
                <h2 id="headerText">PROLONGATION</h2>
                <a href="delay.php" id ="closeBtn" title="Fermer la fenêtre" class="right">X</a>
              </header>
              <section>
              <p id="popupText">Prolongation réalisée avec succès!</p>
              </section>
              <footer>
                <div id="footerText">
                  <a href="delay.php" class="btn">Fermer</a>
                </div>
              </footer>
            </div>
          </div>
          <?php
        }
      } else {
        ?>
        <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
        <div id="oModal" class="oModal">
          <div>
            <header>
              <h2 id="headerText">ATTENTION</h2>
              <a href="delay.php" id ="closeBtn" title="Fermer la fenêtre" class="right">X</a>
            </header>
            <section>
              <p id="popupText">Vous voulez indiquer une valeur de retest alors qu'aucune donnée fournisseur n'est déclarée. Souhaitez-vous continuer?</p>
            </section>
            <footer>
              <div id="footerText">
                <form method="post" action="">
                  <input type="hidden" value="<?php echo($id)?>" name="id"/>
                  <input type="hidden" value="<?php echo($retesting_date)?>" name="retesting_date"/>
                  <input type="hidden" value="<?php echo($extended_date)?>" name="extended_date"/>
                  <input type="hidden" value="<?php echo($purity_retested)?>" name="purity_retested"/>
                  <div class="modal_buttons">
                    <button type="submit" name="formDelayWithoutRetest" class="btn">Oui</button>
                    <a href="delay.php" class="btn">Non</a>
                  </div>
                </form>
              </div>
            </footer>
          </div>
        </div>
        <?php
      }
    }
  }

// Return value to database without retest value
  if(isset($_SESSION['id'],$_POST['formDelayWithoutRetest']) AND $_SESSION['id'] > 0){
    $id = htmlspecialchars($_POST['id']);
    $retesting_date = htmlspecialchars($_POST['retesting_date']);
    $extended_date = htmlspecialchars($_POST['extended_date']);

    $updateDelay = $bddmat->prepare('UPDATE raw_material SET retesting_date = ?, extended_date = ?  WHERE id = ? ');
    $updateDelay->execute(array($retesting_date, $extended_date, $id)) or die('Erreur SQL !'.$sql.'<br />');
    $updateDelay->closeCursor();
    ?>
    <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
    <div id="oModal" class="oModal">
      <div>
        <header>
          <h2 id="headerText">PROLONGATION</h2>
          <a href="delay.php" id ="closeBtn" title="Fermer la fenêtre">X</a>
        </header>
        <section>
        <p id="popupText">Prolongation réalisée avec succès!</p>
        </section>
        <footer>
          <div id="footerText">
            <a href="delay.php" class="btn">Fermer</a>
          </div>
        </footer>
      </div>
    </div>
    <?php
  }
  ?>

<!-- Scripts -->
  <!-- <script src="../assets/js/jquery.min.js"></script> -->
  <script src="../assets/js/nav.js"></script>
  <script src="../assets/js/darkmode.js"></script>
  <script src="../assets/js/javascript functions/delay.js"></script>