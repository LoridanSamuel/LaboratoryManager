<?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddmat = new PDO('mysql:host=127.0.0.1;dbname=material;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

include_once('../Connexion/cookieconnect.php');

// Combobox filling
  if(isset($_SESSION['id']) AND $_SESSION['id'] > 0) {

    $namehtml = "";
    $lothtml = "";
    $sellerhtml = "";
    $refhtml = "";
    $packhtml = "";
    $gradehtml = "";
    $typehtml = "";

    $materialColumn = array('mat_name', 'lot_number', 'seller', 'reference', 'packaging', 'grade', 'type');

    foreach($materialColumn as $column) {
      switch($column) {
        case "mat_name" :
          $html = &$namehtml;
          break;
        case "lot_number" :
          $html = &$lothtml;
          break;
        case "seller" :
          $html = &$sellerhtml;
          break;
        case "reference" :
          $html = &$refhtml;
          break;
        case "packaging" :
          $html = &$packhtml;
          break;
        case "grade" :
          $html = &$gradehtml;
          break;
        case "type" :
          $html = &$typehtml;
          break;
      }
      $selection = $bddmat -> query('SELECT DISTINCT '.$column.' FROM raw_material ORDER BY '.$column);
        while ($result = $selection -> fetch()) {
          $html .= '<option value="' . $result[$column].'">';
        }
      $selection->closecursor();
    }

    ?>
    <!DOCTYPE HTML>
    <html lang="fr">
      <head>
        <meta charset="utf-8">
        <title>Gestionnaire Biosolve</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="description" content="Onglet de réception d'un produit pur.">
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
                  <h2>Réception d'un nouveau lot de produit pur</h2>
                </div>
                <div class='section--text'>
                  <form method="POST" class="form-reception" action="">
                    <div>
                      <label for="name">Nom du produit :</label>
                      <input type="text" list="name" placeholder="Nom du produit" name="mat_name" required/>
                      <datalist id="name"><?php echo($namehtml) ?></datalist>
                    </div>
                    <div>
                      <label for="lotNumber">N° de lot :</label>
                      <input type="text" list="lotNumber" placeholder="N° de lot" name="lot_number" required/>
                      <datalist id="lotNumber"><?php echo($lothtml) ?></datalist>
                    </div>
                    <div>
                      <label for="seller">Fournisseur :</label>
                      <input type="text" list="seller" placeholder="Fournisseur" name="seller" required/>
                      <datalist id="seller"><?php echo($sellerhtml) ?></datalist>
                    </div>
                    <div>
                      <label for="sellerRef">Réference fournisseur :</label>
                      <input type="text" list="sellerRef" placeholder="réference fournisseur" name="reference" required/>
                      <datalist id="sellerRef"><?php echo($refhtml) ?></datalist>
                    </div>
                    <div>
                      <label for="packaging">Conditionnement :</label>
                      <input type="text" list="packaging" placeholder="Conditionnement" name="packaging" required/>
                      <datalist id="packaging"><?php echo($packhtml) ?></datalist>
                    </div>
                    <div>
                      <label for="grade">Grade (facultatif):</label>
                      <input type="text" list="grade" placeholder="Grade" name="grade" />
                      <datalist id="grade"><?php echo($gradehtml) ?></datalist>
                    </div>
                    <div>
                      <label for="type">Usage :</label>
                      <input type="text" list="type" placeholder="Usage" name="type"  required/>
                      <datalist id="type"><?php echo($typehtml) ?></datalist>
                    </div>
                    <div>
                      <label for="purity">Pureté (facultatif):</label>
                      <input type="text" placeholder="Pureté" id="purity" name="purity" />
                    </div>
                    <div>
                      <label for="reception_date">Date de réception :</label>
                      <input type="date" value="<?php echo date('Y-m-d')?>" id="reception_date" name="reception_date" required/>
                    </div>
                    <div>
                      <button type="submit" name="formreception" class="btn">Envoyer</button>
                    </div>
                  </form>
                  <?php
                  if(isset($erreur)) {
                    echo '<font color="bold red">'.$erreur.'</font>';
                  }
                  ?>
                </div>
              </section>
            </div>
          </div>
        </main>
      </body>
    </html>
    <?php
  }

// Database insertion
  if(isset($_SESSION['id'],$_POST['formreception']) AND $_SESSION['id'] > 0) {
    $mat_name = htmlspecialchars($_POST['mat_name']);
    $lot_number = htmlspecialchars($_POST['lot_number']);
    $seller = htmlspecialchars($_POST['seller']);
    $reference = htmlspecialchars($_POST['reference']);
    $packaging = htmlspecialchars($_POST['packaging']);
    $grade = htmlspecialchars($_POST['grade']);
    $type = htmlspecialchars($_POST['type']);
    $purity = $_POST['purity'];
    $reception_date = htmlspecialchars($_POST['reception_date']);

    //Check if the raw material already has an entry in the Data Base. If yes, keep the same material number, else give the last material number known and increment it

    $reqdouble = $bddmat->prepare('SELECT mat_number FROM raw_material WHERE mat_name = ?');
    $reqdouble->execute(array($mat_name));
    $doubleexist = $reqdouble->rowCount();

    if($doubleexist == 0) {
      $reqdouble -> closeCursor();
      $reqmatnumber = $bddmat->query('SELECT mat_number FROM raw_material ORDER BY mat_number DESC');
      $lastmatnumber = $reqmatnumber->fetch();
      $mat_number = $lastmatnumber['mat_number'] + 1;
      $reqmatnumber -> closeCursor();
    } else {
      $matinfo = $reqdouble->fetch();
      $mat_number = $matinfo['mat_number'];
      $reqdouble -> closeCursor();
    };

    if($grade == '') {
      $grade = null;
    }

    if($purity == '') {
      $purity = null;
    }

    $mat_name_length = strlen($mat_name);
    $lot_number_length = strlen($lot_number);
    $seller_length = strlen($seller);
    $reference_length = strlen($reference);
    $packaging_length = strlen($packaging);
    $grade_length = strlen($grade);
    $type_length = strlen($type);
    $purity_length = strlen($purity);

    $insertmat = $bddmat->prepare("INSERT INTO raw_material(mat_number, mat_name, type, seller, reference, packaging, lot_number, grade, purity, reception_date) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insertmat->execute(array($mat_number, $mat_name, $type, $seller, $reference, $packaging, $lot_number, $grade, $purity, $reception_date));
    $insertmat->closeCursor();
    ?>
    <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
    <div id="oModal" class="oModal">
      <div>
        <header>
          <h2 id="headerText">Reception</h2>
          <a href="receive_a_raw_material.php" id ="closeBtn" title="Fermer la fenêtre">X</a>
        </header>
        <section>
        <p id="popupText">Réception réalisée avec succès!</p>
        </section>
        <footer>
          <div id="footerText">
            <a href="receive_a_raw_material.php" class="btn">Fermer</a>
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
