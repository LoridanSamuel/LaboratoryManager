<?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddmat = new PDO('mysql:host=127.0.0.1;dbname=material;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

include_once('../Connexion/cookieconnect.php');

if(isset($_SESSION['id'],$_POST['formSOP']) AND $_SESSION['id'] > 0) {
  $typeOfMaterial = htmlspecialchars($_POST['typeOfMaterial']);
  $name = htmlspecialchars($_POST['name']);
  $concentration = htmlspecialchars($_POST['concentration']);
  $solvent = htmlspecialchars($_POST['solvent']);
  $packaging = htmlspecialchars($_POST['packaging']);
  $lifetime = htmlspecialchars($_POST['lifetime']);
  $numberOfProduct = htmlspecialchars($_POST['numberOfProduct']);

  $typeOfMaterial === "scale" ? $fullName = $name. " dans " .$solvent : $fullName = $name. " à " .$concentration. " dans " .$solvent;

  switch ($typeOfMaterial) {
    case "raw_material":
      $type = "Produit pur";
      break;
    case "reagent":
      $type = "Réactif";
      break;
    case "indicator":
      $type = "Indicateur";
      break;
    case "standard":
      $type = "Standard";
      break;
    case "scale":
      $type = "Etalon";
      break;
  }

  $materialDataBaseArray = array('raw_material', 'reagent', 'indicator', 'standard', 'scale');

  $componenthtml = "";
  $additionalcomponent = "";

  foreach($materialDataBaseArray as $materialDataBase) {
    switch ($materialDataBase) {
      case 'raw_material':
        $materialType = 'Produit pur';
        $critera = 'mat_name';
        $columnName = 'mat_name';
        break;
      case 'reagent':
        $materialType = 'Réactif';
        $critera = 'reag_name, concentration, solvent';
        $columnName = 'reag_name';
        break;
      case 'indicator':
        $materialType = 'Indicateur';
        $critera = 'ind_name, concentration, solvent';
        $columnName = 'ind_name';
        break;
      case 'standard':
        $materialType = 'Standard';
        $critera = 'std_name, concentration, solvent';
        $columnName = 'std_name';
        break;
      case 'scale':
        $materialType = 'Etalon';
        $critera = 'sc_name, concentration, solvent';
        $columnName = 'sc_name';
        break;
    }
    $selectData = $bddmat->query('SELECT DISTINCT '.$critera.' FROM '.$materialDataBase );
      while ($data = $selectData->fetch()) {
        if($materialDataBase === "raw_material") {
          $componentstr = $data[$columnName]. ' ('.$materialType.')';
        }elseif($materialDataBase === "scale") {
          $componentstr = $data[$columnName]. ' dans ' .$data['solvent']. ' ('.$materialType.')';
        }else{
          $componentstr = $data[$columnName]. ' à ' .$data['concentration']. ' dans ' .$data['solvent']. ' ('.$materialType.')';
        }
        $componenthtml .= '<option value="' .$componentstr. '">' .$componentstr. '</option>';
      }
    $selectData->closeCursor();
  }

  if($numberOfProduct > 2) {
    for ($i = 3; $i <= $numberOfProduct; $i++) {
      $additionalcomponent .= '<div class="SOP-Comp">
                                <select form="componentform" name="component' .$i. '" required>
                                  <option  disabled selected>Composant N° '.$i. '</option>'
                                  .$componenthtml. '
                                </select>
                                <input type="text" placeholder="quantité" name="quantity' .$i. '" required>
                              </div>';
    }
  }
  ?>
  <!DOCTYPE HTML>
  <html lang="fr">
    <head>
      <meta charset="utf-8">
      <title>Gestionnaire Biosolve</title>
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <meta name="description" content="Onglet de finalisation de la création d'un mode opératoire de création de solution.">
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
                <h2>Rédaction d'un mode opératoire de préparation de solution (suite)</h2>
              </div>
              <div class='section--text'>
                
              <form method="POST" action="" class="form-SOPfinal" id="componentform">
                <p>Type de solution : <?php echo($type)?></p>
                <p>Nom de la solution : <?php echo($fullName);?></p>
                <p>Volume final (mL) : <?php echo($packaging)?></p>
                <p>Durée de validité : <?php echo($lifetime)?></p>
                <div class="SOP-sumup">
                  <div class="SOP-components">
                    <div class="SOP-firstComp">
                      <select form="componentform" name="component1" required>
                        <option  disabled selected>Composant principal</option>
                        <?php echo($componenthtml);?>
                      </select>
                      <input type="text" placeholder="quantité" name="quantity1" required>
                    </div>
                    <div class="SOP-Comp">
                      <select form="componentform" name="component2" required>
                        <option  disabled selected>Solvant principal</option>
                        <?php echo($componenthtml);?>
                      </select>
                      <input type="text" placeholder="quantité" name="quantity2" required>
                    </div>
                    <?php echo($additionalcomponent);?>
                  </div>
                  <div class="SOP-InputText">
                    <label for="SOP">Mode opératoire :</label>
                    <textarea id="SOP" class="align-left" name="SOPtext" rows="10" required></textarea>
                  </div>
                </div>
                <div>
                  <input type="hidden" value="<?php echo($typeOfMaterial);?>" name="typeOfMaterial"/>
                  <input type="hidden" value="<?php echo($name);?>" name="name"/>
                  <input type="hidden" value="<?php echo($concentration);?>" name="concentration"/>
                  <input type="hidden" value="<?php echo($solvent);?>" name="solvent"/>
                  <input type="hidden" value="<?php echo($packaging);?>" name="packaging"/>
                  <input type="hidden" value="<?php echo($lifetime);?>" name="lifetime"/>
                  <input type="hidden" value="<?php echo($numberOfProduct);?>" name="numberOfProduct"/>
                  <button type="submit" name="formSOPfinal" class="btn">Envoyer</button>
                </div>
              </form>
            </section>
          </div>
        </div>
      </main>
    </body>
  </html>
  <?php
}

if(isset($_SESSION['id'],$_POST['formSOPfinal']) AND $_SESSION['id'] > 0){
  $typeOfMaterial = htmlspecialchars($_POST['typeOfMaterial']);
  $name = htmlspecialchars($_POST['name']);
  $concentration = htmlspecialchars($_POST['concentration']);
  $solvent = htmlspecialchars($_POST['solvent']);
  $packaging = htmlspecialchars($_POST['packaging']);
  $lifetime = htmlspecialchars($_POST['lifetime']);
  $numberOfProduct = htmlspecialchars($_POST['numberOfProduct']);
  $SOPtext = htmlspecialchars($_POST['SOPtext']);
  $productsUsed = "";

  for ($i = 1; $i <= $numberOfProduct; $i++){
    $componentLine = 'component'.$i;
    $componentQuantity = 'quantity'.$i;

    $$componentLine = htmlspecialchars($_POST[$componentLine]);
    $$componentQuantity = htmlspecialchars($_POST[$componentQuantity]);

    $$componentLine = str_replace("(Produit pur)", "_ RM _ ", $$componentLine);
    $$componentLine = str_replace("(Réactif)", "_ reagent _ ", $$componentLine);
    $$componentLine = str_replace("(Indicateur)", "_ indicator _ ", $$componentLine);
    $$componentLine = str_replace("(Standard)", "_ standard _ ", $$componentLine);
    $$componentLine = str_replace("(Etalon)", "_ scale _ ", $$componentLine);

    $productsUsed .= $$componentLine. $$componentQuantity. " $ ";
  }

  $productsUsed = substr($productsUsed, 0, -3);

  ?>
  <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
  <div id="oModal" class="oModal">
    <div>
      <header>
        <h2 id="headerText">ATTENTION</h2>
        <a href="create_a_SOP_final.php" id ="closeBtn" title="Fermer la fenêtre" class="right">X</a>
      </header>
      <section>
      <p id="popupText">Vous êtes sur le point de créer un nouveau mode opératoire. Souhaitez-vous continuer?</p>
      </section>
      <footer>
        <div id="footerText">
          <form method="post" action="">
            <input type="hidden" value="<?php echo($typeOfMaterial);?>" name="typeOfMaterial"/>
            <input type="hidden" value="<?php echo($name);?>" name="name"/>
            <input type="hidden" value="<?php echo($concentration);?>" name="concentration"/>
            <input type="hidden" value="<?php echo($solvent);?>" name="solvent"/>
            <input type="hidden" value="<?php echo($packaging);?>" name="packaging"/>
            <input type="hidden" value="<?php echo($lifetime);?>" name="lifetime"/>
            <input type="hidden" value="<?php echo($productsUsed);?>" name="productsUsed"/>
            <input type="hidden" value="<?php echo($SOPtext);?>" name="SOPtext"/>
            <div class="modal_buttons">
              <button type="submit" name="formConfirmed" class="btn">Oui</button>
              <a href="create_a_SOP_final.php" class="btn">Non</a>
            </div>
          </form>
        </div>
      </footer>
    </div>
  </div>
  <?php
}
?>

<?php
// Return value to database
  if(isset($_SESSION['id'],$_POST['formConfirmed']) AND $_SESSION['id'] > 0){
    $typeOfMaterial = htmlspecialchars($_POST['typeOfMaterial']);
    $name = htmlspecialchars($_POST['name']);
    $concentration = htmlspecialchars($_POST['concentration']);
    $solvent = htmlspecialchars($_POST['solvent']);
    $packaging = htmlspecialchars($_POST['packaging']);
    $lifetime = htmlspecialchars($_POST['lifetime']);
    $SOPtext = htmlspecialchars($_POST['SOPtext']);
    $productsUsed = htmlspecialchars($_POST['productsUsed']);
    $solutionNumber = "";

    $selectSolutionNumber = $bddmat->prepare('SELECT sol_number FROM sop WHERE type = ? ORDER BY sol_number DESC LIMIT 1');
    $selectSolutionNumber->execute(array($typeOfMaterial)) or die('Erreur SQL !'.$sql.'<br />');
    $lastNumber = $selectSolutionNumber->fetch(PDO::FETCH_ASSOC);

      $solutionNumber = $lastNumber['sol_number'] + 1;
    $selectSolutionNumber->closeCursor();

    $insertSOP = $bddmat->prepare('INSERT INTO sop (sol_number, name, concentration, solvent, type, packaging, productsUsed, SOPtext, lifetime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $insertSOP->execute(array($solutionNumber, $name, $concentration, $solvent, $typeOfMaterial, $packaging, $productsUsed, $SOPtext, $lifetime)) or die('Erreur SQL !'.$sql.'<br />');

    $insertSOP->closeCursor();
    ?>
    <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
    <div id="oModal" class="oModal">
      <div>
        <header>
          <h2 id="headerText">Création d'un mode opératoire</h2>
          <a href="create_a_SOP.php" id ="closeBtn" title="Fermer la fenêtre" class="right">X</a>
        </header>
        <section>
        <p id="popupText">Mode opératoire créé avec succès!</p>
        </section>
        <footer>
          <div id="footerText">
            <a href="create_a_SOP.php" class="btn">Fermer</a>
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

