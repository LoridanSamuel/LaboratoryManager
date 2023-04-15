<?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddmat = new PDO('mysql:host=127.0.0.1;dbname=material;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

include_once('../Connexion/cookieconnect.php');

// Combobox filling
  if(isset($_SESSION['id']) AND $_SESSION['id'] > 0) {

  $namehtml = "";

  $selectRM = $bddmat->query("SELECT DISTINCT mat_name FROM raw_material WHERE opening_date <=> null ORDER BY mat_name");
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
      <meta name="description" content="Onglet d'ouverture d'un produit pur.">
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
                <h2>Ouverture d'un nouveau lot de produit pur</h2>
              </div>
              <div class='section--text'>
                <p>Quel produit souhaitez-vous ouvrir?</p>
                <form method="POST" class="form-open" action="">
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
        </div>
      </main>
    </body>
  </html>
  <?php
  }

// Database insertion
  if(isset($_SESSION['id'],$_POST['formOpen']) AND $_SESSION['id'] > 0){

  $mat_name = htmlspecialchars($_POST['name']);
  $id = htmlspecialchars($_POST['id']);
  $openingDate = htmlspecialchars($_POST['opening_date']);
  $openingDateTimestamp = strtotime($openingDate);
  $peremptingDate = date('Y-m-d', strtotime('+ 5 year', $openingDateTimestamp));

  $updateOpening = $bddmat->prepare('UPDATE raw_material SET opening_date = ?, perempting_date = ? WHERE id = ? ');
  $updateOpening->execute(array($openingDate, $peremptingDate, $id)) or die('Erreur SQL !'.$sql.'<br />');
  $updateOpening->closeCursor();

  // Check if another lot is already open and ask if the user want to close it.
    $selectAlreadyOpen = $bddmat->prepare('SELECT * FROM raw_material WHERE mat_name = ? AND opening_date IS NOT null AND destruction_date IS null AND id <> ?');
    $selectAlreadyOpen->execute(array($mat_name, $id)) or die('Erreur SQL !'.$sql.'<br />');
    $selectAlreadyOpen->closeCursor();

    $openExist = $selectAlreadyOpen->rowCount();

    if($openExist <> 0) {
    ?>
    <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
    <div id="oModal" class="oModal">
      <div>
        <header>
          <h2 id="headerText">ATTENTION</h2>
          <a href="open_a_raw_material.php" id ="closeBtn" title="Fermer la fenêtre">X</a>
        </header>
        <section>
          <p id="popupText">Ouverture réalisée avec succès! Au moins un autre lot de ce produit est actuellement ouvert. Souhaitez-vous déclarer la fin de ce/ces lots?</p>
        </section>
        <footer>
          <div id="footerText">
            <form method="post" action="close_a_raw_material.php">
              <input type="hidden" value="<?php echo($mat_name);?>" name="mat_name"/>
              <input type="hidden" value="<?php echo($id);?>" name="id"/>
              <div class="modal_buttons">
                <button type="submit" name="formClosable" class="btn">Oui</button>
                <a href="open_a_raw_material.php" class="btn">Non</a>
              </div>
            </form>
          </div>
        </footer>
      </div>
    </div>
    <?php
    } else {
    ?>
    <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
    <div id="oModal" class="oModal">
      <div>
        <header>
          <h2 id="headerText">OUVERTURE</h2>
          <a href="open_a_raw_material.php" id ="closeBtn" title="Fermer la fenêtre">X</a>
        </header>
        <section>
          <p id="popupText">Ouverture réalisée avec succès!</p>
        </section>
        <footer>
          <div id="footerText">
            <a href="open_a_raw_material.php" class="btn">Fermer</a>
          </div>
        </footer>
      </div>
    </div>
    <?php
    }
  }
  ?>

<!-- Scripts -->
  <!-- <script src="../assets/js/jquery.min.js"></script> -->
  <script src="../assets/js/nav.js"></script>
  <script src="../assets/js/darkmode.js"></script>
  <script src="../assets/js/javascript functions/open_a_raw_material.js"></script>
