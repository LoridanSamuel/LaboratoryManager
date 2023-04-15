<?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddmat = new PDO('mysql:host=127.0.0.1;dbname=material;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

include_once('Connexion/cookieconnect.php');

if(isset($_SESSION['id']) AND $_SESSION['id'] > 0) {

  //Updating status of destroyed products
    $updateStatusOfDestroyedRM = $bddmat->query('UPDATE raw_material SET status = "Détruit" WHERE destruction_date IS NOT null');
    $updateStatusOfDestroyedRM->closeCursor();
    $updateStatusOfDestroyedInd = $bddmat->query('UPDATE indicator SET status = "Détruit" WHERE destruction_date IS NOT null');
    $updateStatusOfDestroyedInd->closeCursor();
    $updateStatusOfDestroyedReagent = $bddmat->query('UPDATE reagent SET status = "Détruit" WHERE destruction_date IS NOT null');
    $updateStatusOfDestroyedReagent->closeCursor();
    $updateStatusOfDestroyedScale = $bddmat->query('UPDATE scale SET status = "Détruit" WHERE destruction_date IS NOT null');
    $updateStatusOfDestroyedScale->closeCursor();
    $updateStatusOfDestroyedStandard = $bddmat->query('UPDATE standard SET status = "Détruit" WHERE destruction_date IS NOT null');
    $updateStatusOfDestroyedStandard->closeCursor();

  //Updating status of non opened raw material
    $updateStatusOfNonOpenedRM = $bddmat->query('UPDATE raw_material SET status = "Non ouvert" WHERE opening_date IS null');
    $updateStatusOfNonOpenedRM->closeCursor();

  //Updating status of opened products
    $dateToday = strtotime(date('Y-m-d'));
    $RMtoOrder = false;
    $SoltoPrepare = false;

    //Raw_material
      $selectOpenedRM = $bddmat->query('SELECT * FROM raw_material WHERE destruction_date IS null AND opening_date IS NOT null');
        while ($openedRM = $selectOpenedRM->fetch()) {

          $openedRM['extended_date'] == null ? $PeremptingDate = strtotime($openedRM['perempting_date']) : $PeremptingDate = strtotime($openedRM['extended_date']);
          
          $interval = (abs($dateToday - $PeremptingDate))/86400;
          $updateStatusOfOpenedRM = $bddmat->prepare('UPDATE raw_material SET status = ? WHERE id = ' . $openedRM['id']);
          if($dateToday <= $PeremptingDate && $interval >= 30) {
            $updateStatusOfOpenedRM->execute(array('OK'));
          } else if ($dateToday <= $PeremptingDate && $interval < 30) {
            $updateStatusOfOpenedRM->execute(array('Reste ' . $interval . ' jours'));
            $RMtoOrder = true;
          } else if ($dateToday >= $PeremptingDate) {
            $updateStatusOfOpenedRM->execute(array('Expiré depuis ' . $interval . ' jours'));
            $RMtoOrder = true;
          }
        }
      $selectOpenedRM->closeCursor();

    //Indicator
      $selectOpenedInd = $bddmat->query('SELECT * FROM indicator WHERE destruction_date IS null');
      while ($openedInd = $selectOpenedInd->fetch()) {
        $PeremptingDate = strtotime($openedInd['expiration_date']);
        $interval = (abs($dateToday - $PeremptingDate))/86400;
        $updateStatusOfOpenedInd = $bddmat->prepare('UPDATE indicator SET status = ? WHERE id = ' . $openedInd['id']);
        if($dateToday <= $PeremptingDate && $interval >= 30) {
          $updateStatusOfOpenedInd->execute(array('OK'));
        } else if ($dateToday <= $PeremptingDate && $interval < 30) {
          $updateStatusOfOpenedInd->execute(array('Reste ' . $interval . ' jours'));
          $SoltoPrepare = true;
        } else if ($dateToday >= $PeremptingDate) {
          $updateStatusOfOpenedInd->execute(array('Expiré depuis ' . $interval . ' jours'));
          $SoltoPrepare = true;
        }
      }
      $selectOpenedInd->closeCursor();

    //Reagent
      $selectOpenedRgt = $bddmat->query('SELECT * FROM reagent WHERE destruction_date IS null');
      while ($openedRgt = $selectOpenedRgt->fetch()) {
        $PeremptingDate = strtotime($openedRgt['expiration_date']);
        $interval = (abs($dateToday - $PeremptingDate))/86400;
        $updateStatusOfOpenedRgt = $bddmat->prepare('UPDATE reagent SET status = ? WHERE id = ' . $openedRgt['id']);
        if($dateToday <= $PeremptingDate AND $interval >= 30) {
          $updateStatusOfOpenedRgt->execute(array('OK'));
        } else if ($dateToday <= $PeremptingDate AND $interval < 30) {
          $updateStatusOfOpenedRgt->execute(array('Reste ' . $interval . ' jours'));
          $SoltoPrepare = true;
        } else if ($dateToday >= $PeremptingDate) {
          $updateStatusOfOpenedRgt->execute(array('Expiré depuis ' . $interval . ' jours'));
          $SoltoPrepare = true;
        }
      }
      $selectOpenedRgt->closeCursor();

    //Scale
      $selectOpenedSca = $bddmat->query('SELECT * FROM scale WHERE destruction_date IS null');
      while ($openedSca = $selectOpenedSca->fetch()) {
        $PeremptingDate = strtotime($openedSca['expiration_date']);
        $interval = (abs($dateToday - $PeremptingDate))/86400;
        $updateStatusOfOpenedSca = $bddmat->prepare('UPDATE scale SET status = ? WHERE id = ' . $openedSca['id']);
        if($dateToday <= $PeremptingDate AND $interval >= 30) {
          $updateStatusOfOpenedSca->execute(array('OK'));
        } else if ($dateToday <= $PeremptingDate AND $interval < 30) {
          $updateStatusOfOpenedSca->execute(array('Reste ' . $interval . ' jours'));
          $SoltoPrepare = true;
        } else if ($dateToday >= $PeremptingDate) {
          $updateStatusOfOpenedSca->execute(array('Expiré depuis ' . $interval . ' jours'));
          $SoltoPrepare = true;
        }
      }
      $selectOpenedSca->closeCursor();

    //Standard
      $selectOpenedStd = $bddmat->query('SELECT * FROM standard WHERE destruction_date IS null');
      while ($openedStd = $selectOpenedStd->fetch()) {
        $PeremptingDate = strtotime($openedStd['expiration_date']);
        $interval = (abs($dateToday - $PeremptingDate))/86400;
        $updateStatusOfOpenedStd = $bddmat->prepare('UPDATE standard SET status = ? WHERE id = ' . $openedStd['id']);
        if($dateToday <= $PeremptingDate AND $interval >= 30) {
          $updateStatusOfOpenedStd->execute(array('OK'));
        } else if ($dateToday <= $PeremptingDate AND $interval < 30) {
          $updateStatusOfOpenedStd->execute(array('Reste ' . $interval . ' jours'));
          $SoltoPrepare = true;
        } else if ($dateToday >= $PeremptingDate) {
          $updateStatusOfOpenedStd->execute(array('Expiré depuis ' . $interval . ' jours'));
          $SoltoPrepare = true;
        }
      }
      $selectOpenedStd->closeCursor();

  //Manage de button "ouvrir un nouveau lot de produit pur" (open a new batch of raw material)
    $selectOpenableRM = $bddmat->query('SELECT opening_date FROM raw_material WHERE opening_date IS null');
      $openableRMexist = $selectOpenableRM->rowCount();
    $selectOpenableRM->closeCursor();

  //Manage de button "prolonger la durée de vie" (delay the lifetime of a raw material)
    $selectDelayableRM = $bddmat->query('SELECT status FROM raw_material WHERE status IS NOT null || status != "detruit"');
      $delayableRMexist = $selectDelayableRM->rowCount();
    $selectDelayableRM->closeCursor();

  //Manage de button "vérifier la préparation d'une solution" (check the preparation of a solution)
    $selectUncheckedReagent = $bddmat->query('SELECT verification_date FROM reagent WHERE verification_date IS null');
      $checkableReagent = $selectUncheckedReagent->rowCount();
    $selectUncheckedReagent->closeCursor();

    $selectUncheckedInd = $bddmat->query('SELECT verification_date FROM indicator WHERE verification_date IS null');
      $checkableInd = $selectUncheckedInd->rowCount();
    $selectUncheckedInd->closeCursor();

    $selectUncheckedStd = $bddmat->query('SELECT verification_date FROM standard WHERE verification_date IS null');
      $checkableStd = $selectUncheckedStd->rowCount();
    $selectUncheckedStd->closeCursor();

    $selectUncheckedScale = $bddmat->query('SELECT verification_date FROM scale WHERE verification_date IS null');
      $checkableScale = $selectUncheckedScale->rowCount();
    $selectUncheckedScale->closeCursor();

  //Préparation de la mailinglist
    $mailinglist = "";
    $reqmail = $bdd->query('SELECT id, mail FROM membres');
      while($mail = $reqmail->fetch()) {
        if ($mail['id'] != $_SESSION['id']){
          $mailinglist .= $mail['mail']. ", ";
        }
      }
      $mailinglist = substr($mailinglist, 0, -2);
    $reqmail->closeCursor();
  ?>

  <!DOCTYPE HTML>
  <html lang="fr">
    <head>
      <meta charset="utf-8">
      <title>Gestionnaire Biosolve</title>
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <meta name="description" content="Page pricipale du document d'aide du laboratoire d'analyse de Biosolve.">
      <link rel="stylesheet" href="assets/css/prefixed/main.css" />
      <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
    </head>
    <body>
      <main>
        <nav>
          <ul>
            <li>
              <a href="Connexion/editionprofil.php" class="nav--btn">Editer mon profil</a>
            </li>
            <li>
              <a href="Connexion/deconnexion.php" class="nav--btn">Se déconnecter</a>
            </li>
          </ul>
        </nav>
        <div class='content'>
          <div id='page-wrapper'>
        
            <?php include('header.php'); ?>

            <section class='section'>
              <div class='section--title'>
                <h2>Tableau de bord principal</h2>
              </div>
              <div class='section--text'>
                <ul class="icons">
                  <li>
                    <a href="Spreadsheet/spreadsheet_index.php">
                      <i class="fa-solid fa-book" id="spreadsheet"></i>
                      <label for="spreadsheet">Feuille de calcul</label>
                    </a>
                  </li>
                </ul>
                <ul class="icons">
                  <li>
                    <a href="Reagent_Manager/receive_a_raw_material.php">
                      <i class="fa-solid fa-circle-plus" id="receiveNewRM"></i>
                      <label for="receiveNewRM">Réceptionner un nouveau lot</label>
                    </a>
                  </li>

                  <?php
                  if($openableRMexist != 0) {
                  ?>
                  <li>
                    <a href="Reagent_Manager/open_a_raw_material.php">
                      <i class="fa-solid fa-flask" id="openNewRM"></i>
                      <label for="openNewRM">Ouvrir un nouveau lot</label>
                    </a>
                  </li>
                  <?php
                  }

                  if($delayableRMexist != 0) {
                  ?>
                  <li>
                    <a href="Reagent_Manager/delay.php">
                      <i class="fa-solid fa-clock" id="delayRM"></i>
                      <label for="delayRM">Prolonger la durée de vie d'un lot</label>
                    </a>
                  </li>
                  <?php
                  }
                  ?>

                  <li>
                    <a href="Reagent_Manager/destruction.php">
                      <i class="fa-solid fa-trash-can" id="destroy"></i>
                      <label for="destroy">Détruire un lot ou une solution</label>
                    </a>
                  </li>
                  <li>
                    <a href="Reagent_Manager/create_a_SOP.php">
                      <i class="fa-solid fa-file-circle-plus" id="NewSOP"></i>
                      <label for="NewSOP">Créer un mode opératoire</label>
                    </a>
                  </li>
                  <li>
                    <a href="Reagent_Manager/prepare_a_solution.php">
                      <i class="fa-solid fa-vial" id="NewReagent"></i>
                      <label for="NewReagent">Préparer une solution</label>
                    </a>
                  </li>

                  <?php
                  if($checkableReagent != 0 || $checkableInd != 0 || $checkableStd != 0 || $checkableScale != 0) {
                  ?>
                  <li>
                    <a href="Reagent_Manager/check_a_solution.php">
                      <i class="fa-solid fa-circle-check" id="checkReagent"></i>
                      <label for="checkReagent">Vérifier une solution</label>
                    </a>
                  </li>
                  <?php
                  }
                  ?>
                </ul>
                <ul class="icons">
                  <li>
                    <a href="Reagent_Manager/print_a_label.php">
                      <i class="fa-solid fa-print" id="printLabel"></i>
                      <label for="printLabel">Imprimer une étiquette</label>
                    </a>
                  </li>
                  <li>
                    <a href="Reagent_Manager/consultation.php">
                      <i class="fa-solid fa-magnifying-glass" id="consultDB"></i>
                      <label for="consultDB">Consulter la base de données</label>
                    </a>
                  </li>
                </ul>
              </div>
            </section>
            <section id="one">
              <header>
                <h2>Voici les tâches en cours<br />
                à prendre en considération</h2>
              </header>
              <ul class="icons">
                <?php
                if($checkableReagent != 0 || $checkableInd != 0 || $checkableStd != 0 || $checkableScale != 0) {
                ?>
                <li>
                  <a href="mailto:<?php echo($mailinglist)?>?subject=Vérification de solutions&body=Des solutions viennent d'être préparées et doivent être vérifier. Peux-tu t'en occuper s'il te plait?">
                    <i class="fa-solid fa-at" id="checkReagent"></i>
                    <label for="checkReagent">Envoyer un mail pour vérification</label>
                  </a>
                </li>
                <?php
                }

                if($RMtoOrder) {
                ?>
                <li>
                  <a href="mailto:<?php echo($mailinglist)?>?subject=Commande de produits de laboratoire&body=Des produits de laboratoire sont périmés ou vont l'être prochainement. Peux-tu t'en occuper s'il te plait?">
                    <i class="fa-solid fa-at" id="OrderRM"></i>
                    <label for="OrderRM">Commande de produit pur</label>
                  </a>
                </li>
                <?php
                }

                if($SoltoPrepare) {
                ?>
                <li>
                  <a href="mailto:<?php echo($mailinglist)?>?subject=Préparation de solutions&body=Des solutions sont périmées ou vont l'être prochainement. Peux-tu t'en occuper s'il te plait?">
                    <i class="fa-solid fa-at" id="PreapareSol"></i>
                    <label for="PrepareSol">Préparation de solution</label>
                  </a>
                </li>
                <?php
                }
                ?>
              </ul>
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
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/nav.js"></script>
  <script src="assets/js/darkmode.js"></script>
