<?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddmat = new PDO('mysql:host=127.0.0.1;dbname=material;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

include_once('../Connexion/cookieconnect.php');

// Table filling
  if(isset($_SESSION['id'],$_POST['formClosable']) AND $_SESSION['id'] > 0) {

    $openedSolution = htmlspecialchars($_POST['solution']);
    $openedSolution = str_replace('&quot;', '"', $openedSolution);
    $openedSolution = json_decode($openedSolution);
    $mat_name = $openedSolution->matName;
    $id = $openedSolution->matId;
    
    $itemhtmlTotal = "";
    $i = 1;

    $selectClosableMaterial = $bddmat->prepare('SELECT id,
                                                       lot_number,
                                                       seller,
                                                       reference,
                                                       reception_date,
                                                       opening_date
                                                FROM raw_material
                                                WHERE mat_name = :matName 
                                                  AND id <> :matId 
                                                  AND opening_date IS NOT null 
                                                  AND destruction_date IS null');
    $selectClosableMaterial->bindValue(':matName', $mat_name);
    $selectClosableMaterial->bindValue(':matId', $id, PDO::PARAM_INT);
    $selectClosableMaterial->execute();
    while ($closableMaterial = $selectClosableMaterial->fetch()) {
      $reception_date = date("d/m/Y", strtotime($closableMaterial['reception_date']));
      $date = date('Y-m-d');
      $closableSolution = new stdCLass();
      $closableSolution->matName = $mat_name;
      $closableSolution->matId = $closableMaterial['id'];
      $closableSolution = json_encode($closableSolution);
      $closableSolution = htmlspecialchars($closableSolution);

      $itemhtml = '<tr>
                    <td>'.$mat_name.'</td>
                    <td>'.$closableMaterial['lot_number'].'</td>
                    <td>'.$closableMaterial['seller'].'</td>
                    <td>'.$closableMaterial['reference'].'</td>
                    <td>'.$reception_date.'</td>
                    <td nowrap="nowrap">
                      <form method="post">
                        <input type="hidden" value="'.htmlspecialchars($_POST['solution']).'" name="solution"/>
                        <input type="hidden" value="'.$closableSolution.'" name="closableSolution"/>
                        <div class="table_button">
                          <input type="date" name="destruction_date" value="'.$date.'" required/>
                          <button type="submit" name="formClose" class="table--btn">Fermer</button>
                        </div>
                      </form>
                    </td>
                  </tr>';

      $itemhtmlTotal .= $itemhtml;
      $i++;
    }
    $selectClosableMaterial->closeCursor();
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
                <h2>Fermeture d'un lot de produit pur</h2>
                </div>
                <div class='section--text'>
                  <p>Quel lot souhaitez-vous fermer?</p>
                  <table>
                    <thead>
                      <tr>
                        <th>Nom</th>
                        <th>N° de lot</th>
                        <th>Fournisseur</th>
                        <th>Réference fournisseur</th>
                        <th>Date de réception</th>
                        <th>Date de fermeture</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php echo $itemhtmlTotal; ?>
                    </tbody>
                  </table>
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
  if(isset($_SESSION['id'],$_POST['formClose']) AND $_SESSION['id'] > 0){

    $openedSolution = htmlspecialchars($_POST['solution']);
    $closableSolution = htmlspecialchars(($_POST['closableSolution']));
    $closableSolution = str_replace('&quot;', '"', $closableSolution);
    $closableSolution = json_decode($closableSolution);
    $mat_name = $closableSolution->matName;
    $id = $closableSolution->matId;
    $destruction_date = htmlspecialchars($_POST['destruction_date']);

    $updateDestructionDate = $bddmat->prepare('UPDATE raw_material
                                               SET destruction_date = :destructionDate, status = "Détruit"
                                               WHERE id = :matId');
    $updateDestructionDate->bindValue(':destructionDate', $destruction_date);
    $updateDestructionDate->bindValue(':matId', $id, PDO::PARAM_INT);
    $updateDestructionDate->execute();
    $updateDestructionDate->closeCursor();

    // Check if another lot is already open and ask if the user want to close it.
      $selectAlreadyOpened = $bddmat->prepare('SELECT * FROM raw_material
                                               WHERE mat_name = :matName
                                                 AND opening_date IS NOT null
                                                 AND destruction_date IS null');
      $selectAlreadyOpened->bindValue(':matName', $mat_name);
      $selectAlreadyOpened->execute();
      $openExist = count($selectAlreadyOpened->fetchAll());
      $selectAlreadyOpened->closeCursor();

      if($openExist > 1) {
        ?>
        <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
        <div id="oModal" class="oModal">
          <div>
            <header>
              <h2 id="headerText">ATTENTION</h2>
              <a href="close_a_raw_material.php" id ="closeBtn" title="Fermer la fenêtre">X</a>
            </header>
            <section>
            <p id="popupText">Fermeture réalisée avec succès! Au moins un autre lot de ce produit est actuellement ouvert. Souhaitez-vous déclarer la fin de ce/ces lots?</p>
            </section>
            <footer>
              <div id="footerText">
                <form method="post" action="close_a_raw_material.php">
                  <input type="hidden" value="<?php echo($openedSolution);?>" name="solution"/>
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
              <h2 id="headerText">FERMETURE</h2>
              <a href="open_a_raw_material.php" id ="closeBtn" title="Fermer la fenêtre">X</a>
            </header>
            <section>
            <p id="popupText">Fermeture réalisée avec succès!</p>
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
  <script src="../assets/js/nav.js"></script>
  <script src="../assets/js/darkmode.js"></script>