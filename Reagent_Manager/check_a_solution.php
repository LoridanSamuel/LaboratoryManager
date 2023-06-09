<?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddmat = new PDO('mysql:host=127.0.0.1;dbname=material;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

include_once('../Connexion/cookieconnect.php');

if(isset($_SESSION['id']) AND $_SESSION['id'] > 0) {
  $solhtml = '';

  $materialType = ['reagent', 'indicator', 'standard'];

  foreach ($materialType as $type) {
    switch ($type) {
      case 'reagent':
        $material_name = 'reag_name';
        $brackets = '(Réactif)';
        break;
      case 'indicator':
        $material_name = 'ind_name';
        $brackets = '(Indicateur)';
        break;
      case 'standard':
        $material_name = 'std_name';
        $brackets = '(Standard)';
        break;
    }

    $selectMaterial = $bddmat -> query('SELECT * FROM '.$type.' WHERE verification_date IS null AND destruction_date IS null');
      while($material = $selectMaterial -> fetch()) {
        $solutionInfo = $material[$material_name].' à '.$material['concentration'].' dans '.$material['solvent'].' '.$brackets.' préparé le '.$material['preparation_date'].' par '.$material['maker'];
        $solhtml .= '<option value = "'.$solutionInfo.'">'.$solutionInfo.'</option>';
      }
    $selectMaterial -> closeCursor();
  }

  $selectScale = $bddmat -> query('SELECT * FROM scale WHERE verification_date IS null AND destruction_date IS null ');
    while ($scale = $selectScale -> fetch()) {
      $solutionInfo = $scale['sc_name'].' dans '.$scale['solvent'].' (Etalon) préparé le '.$scale['preparation_date'].' par '.$scale['maker'];
      $solhtml .= '<option value = "'.$solutionInfo.'">'. $solutionInfo.'</option>';
    }
  $selectScale->closeCursor();

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
                <h2>Vérification d'une solution</h2>
              </div>
              <div class='section--text'>
                <p>Quel produit souhaitez-vous vérifier?</p>
                <form method="POST" class="form-checking" action="">
                  <select name="mat_name" id="mat_name" required onchange="selectAMaterial()">
                    <option  disabled selected>Nom de la solution</option>
                    <?php echo($solhtml) ?>
                  </select>

                  <span name="sop" id="sop"></span>
                  <span name="pack" id="pack"></span>
                  <span name="componentsTable" id="componentsTable"></span>
                  <div id="hidden"></div>
                  <input type="date" name="verification_date" value="<?php echo date('Y-m-d')?>" required/>
                  <button type="submit" name="formChecking" class="btn">Envoyer</button>
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

if(isset($_SESSION['id'],$_POST['formChecking']) AND $_SESSION['id'] > 0) {
  $typeOfMaterial = htmlspecialchars($_POST['typeOfMaterial']);
  $matName = htmlspecialchars($_POST['matName']);
  $concentration = htmlspecialchars($_POST['concentration']);
  $solvent = htmlspecialchars($_POST['solvent']);
  $preparation_date = htmlspecialchars($_POST['preparation_date']);
  $maker = htmlspecialchars($_POST['maker']);
  $verification_date = htmlspecialchars($_POST['verification_date']);

  // $checker from session table
    $sessionid = htmlspecialchars($_SESSION['id']);
    $selectChecker = $bdd->prepare('SELECT pseudo FROM membres WHERE id = ?');
    $selectChecker->execute(array($sessionid));
      while ($result = $selectChecker->fetch()) {
        $checker = $result['pseudo'];
      }
    $selectChecker->closeCursor();

    $words = explode(" ", $checker);
    $checker = "";
    foreach($words as $w) {
      $checker .= $w[0];
    }

  ?>
  <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
  <div id="oModal" class="oModal">
    <div>
      <header>
        <h2 id="headerText">ATTENTION</h2>
        <a href="check_a_solution.php" id ="closeBtn" title="Fermer la fenêtre">X</a>
      </header>
      <section>
      <p id="popupText">Vous êtes sur le point de déclarer cette solution comme étant conforme. Souhaitez-vous continuer?</p>
      </section>
      <footer>
        <div id="footerText">
          <form method="post" action="">
            <input type="hidden" value="<?php echo($typeOfMaterial);?>" name="typeOfMaterial"/>
            <input type="hidden" value="<?php echo($matName);?>" name="matName"/>
            <input type="hidden" value="<?php echo($concentration);?>" name="concentration"/>
            <input type="hidden" value="<?php echo($solvent);?>" name="solvent"/>
            <input type="hidden" value="<?php echo($preparation_date);?>" name="preparation_date"/>
            <input type="hidden" value="<?php echo($maker);?>" name="maker"/>
            <input type="hidden" value="<?php echo($verification_date);?>" name="verification_date"/>
            <input type="hidden" value="<?php echo($checker);?>" name="checker"/>
            <div class="modal_buttons">
              <button type="submit" name="checkformConfirmed" class="btn">Oui</button>
              <a href="check_a_solution.php" class="btn">Non</a>
            </div>
          </form>
          </div>
      </footer>
    </div>
  </div>
  <?php
}

// Return value to database
  if(isset($_SESSION['id'],$_POST['checkformConfirmed']) AND $_SESSION['id'] > 0){
    $typeOfMaterial = htmlspecialchars($_POST['typeOfMaterial']);
    $matName = htmlspecialchars($_POST['matName']);
    $concentration = htmlspecialchars($_POST['concentration']);
    $solvent = htmlspecialchars($_POST['solvent']);
    $preparation_date = htmlspecialchars($_POST['preparation_date']);
    $maker = htmlspecialchars($_POST['maker']);
    $verification_date = htmlspecialchars($_POST['verification_date']);
    $checker = htmlspecialchars($_POST['checker']);

    switch ($typeOfMaterial) {
      case "reagent":
        $sol_name = "reag_name";
        break;
      case "indicator":
        $sol_name = "ind_name";
        break;
      case "standard":
        $sol_name = "std_name";
        break;
      case "scale":
        $concentration = "-";
        $sol_name = "sc_name";
    }

    $updateSolutionChecked = $bddmat->prepare('UPDATE ' . $typeOfMaterial . ' SET verification_date = ?, checker = ? WHERE ' . $sol_name . ' = ? 
                                                                                                                       AND concentration = ? 
                                                                                                                       AND solvent = ? 
                                                                                                                       AND preparation_date = ? 
                                                                                                                       AND maker = ? 
                                                                                                                       AND destruction_date IS null');
    $updateSolutionChecked->execute(array($verification_date, $checker, $matName, $concentration, $solvent, $preparation_date, $maker)) or die('Erreur SQL !'.$sql.'<br />');
    $updateSolutionChecked->closeCursor();

    ?>
    <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
    <div id="oModal" class="oModal">
      <div>
        <header>
          <h2 id="headerText">Vérification d'une solution</h2>
          <a href="check_a_solution.php" id ="closeBtn" title="Fermer la fenêtre">X</a>
        </header>
        <section>
        <p id="popupText">Solution déclarée conforme!</p>
        </section>
        <footer>
          <div id="footerText">
            <div class="modal_buttons">
              <a href="check_a_solution.php" class="btn">Fermer</a>
            </div>
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
  <script src="../assets/js//javascript functions/check_a_solution.js"></script>