<?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddmat = new PDO('mysql:host=127.0.0.1;dbname=material;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

include_once('../Connexion/cookieconnect.php');

if(isset($_SESSION['id'],$_POST['typeOfDatabase']) AND $_SESSION['id'] > 0) {

  $typeOfDatabase = htmlspecialchars($_POST['typeOfDatabase']);

  switch ($typeOfDatabase) {
    case 'raw_material':
      $titleCompletion = "de produits purs";
      $solNumber = 'mat_number';
      $solName = 'mat_name';
      break;
    case 'reagent':
      $titleCompletion = "de réactifs";
      $solNumber = 'reag_number';
      $solName = 'reag_name';
      break;
    case 'indicator':
      $titleCompletion = "d'indicateurs";
      $solNumber = 'ind_number';
      $solName = 'ind_name';
      break;
    case 'standard':
      $titleCompletion = "de standards";
      $solNumber = 'std_number';
      $solName = 'std_name';
      break;
    case 'scale':
      $titleCompletion = "d'étalons";
      $solNumber = 'sc_number';
      $solName = 'sc_name';
      break;
  }

  $title = "<h2>Consultation de la base de données ".$titleCompletion."</h2>";
  $tableBody = '';
  $status = '';
  $selectData = $bddmat->query('SELECT * FROM '.$typeOfDatabase.' ORDER BY '.$solNumber);

  if($typeOfDatabase === 'raw_material') {
    $tableHeader = "<th>N°</th>
                    <th>Nom</th>
                    <th>Statut</th>
                    <th>Type</th>
                    <th>Fournisseur</th>
                    <th>Reference fournisseur</th>
                    <th>Conditionnement</th>
                    <th>N° de lot</th>
                    <th>Grade</th>
                    <th>Pureté fournisseur</th>
                    <th>Pureté retestée</th>
                    <th>Reçu le</th>
                    <th>Ouvert le</th>
                    <th>Périme le</th>
                    <th>Prolongé jusqu'au</th>
                    <th>Détruit le</th>";

    while ($RM = $selectData->fetch()) {
      $reception_date = date("d/m/Y", strtotime($RM['reception_date']));
      $RM['opening_date'] != null ? $opening_date = date("d/m/Y", strtotime($RM['opening_date'])) : $opening_date = null;
      $RM['perempting_date'] != null ? $perempting_date = date("d/m/Y", strtotime($RM['perempting_date'])) : $perempting_date = null;
      $RM['extended_date'] != null ? $extended_date = date("d/m/Y", strtotime($RM['extended_date'])) : $extended_date = null;
      $RM['destruction_date'] != null ? $destruction_date = date("d/m/Y", strtotime($RM['destruction_date'])) : $destruction_date = null;

      switch($RM['status']) {
        case "OK":
          $tableBody .= "<tr>
                           <td class='RM_number'>" . $RM['mat_number'] . "</td>
                           <td>" . $RM['mat_name'] . "</td>
                           <td class='status_OK'>" . $RM['status'] . "</td>";
          break;
        case "Détruit":
          $tableBody .= "<tr class='destructed'>
                           <td class='RM_number'>" . $RM['mat_number'] . "</td>
                           <td>" . $RM['mat_name'] . "</td>
                           <td class='status_destroy'>" . $RM['status'] . "</td>";
          break;
        case "Non ouvert":
          $tableBody .= "<tr>
                           <td class='RM_number'>" . $RM['mat_number'] . "</td>
                           <td>" . $RM['mat_name'] . "</td>
                           <td class='status_notOpen'>" . $RM['status'] . "</td>";
          break;
        default:
          $tableBody .= "<tr>
                           <td class='RM_number'>" . $RM['mat_number'] . "</td>
                           <td>" . $RM['mat_name'] . "</td>
                           <td class='status_Perempted'>" . $RM['status'] . "</td>";
          break;
      }

      $tableBody .= "<td>" . $RM['type'] . "</td>
                     <td>" . $RM['seller'] . "</td>
                     <td>" . $RM['reference'] . "</td>
                     <td>" . $RM['packaging'] . "</td>
                     <td>" . $RM['lot_number'] . "</td>
                     <td>" . $RM['grade'] . "</td>
                     <td>" . $RM['purity'] . "</td>
                     <td>" . $RM['purity_retested'] . "</td>
                     <td>" . $reception_date . "</td>
                     <td>" . $opening_date . "</td>
                     <td>" . $perempting_date . "</td>
                     <td>" . $extended_date . "</td>
                     <td>" . $destruction_date . "</td>
                   </tr>";
    }
    $selectData->closeCursor();
  } else {
    $tableHeader = "<th>N°</th>
                    <th>Nom</th>
                    <th>Concentration</th>
                    <th>Solvant</th>
                    <th>Statut</th>
                    <th>Volume final</th>
                    <th>Préparé le</th>
                    <th>Expire le</th>
                    <th>Détruit le</th>
                    <th>Produits utilisés</th>
                    <th>N° de lot</th>
                    <th>Quantité</th>
                    <th>Préparateur</th>
                    <th>Vérificateur</th>
                    <th>Vérifié le</th>";
    
    $line_number = 1;
    while ($data = $selectData->fetch()) {
      $preparation_date = date("d/m/Y", strtotime($data['preparation_date']));
      $expiration_date = date("d/m/Y", strtotime($data['expiration_date']));
      $data['destruction_date'] != null ? $destruction_date = date("d/m/Y", strtotime($data['destruction_date'])) : $destruction_date = null;
      $data['verification_date'] != null ? $verification_date = date("d/m/Y", strtotime($data['verification_date'])) : $verification_date = null;
      $numberofmat = count(explode(' $ ', $data['material_used']));
      $material_used1 = $data['material_used'].' $ ';

      for($i = 1; $i <= $numberofmat; $i++) {

        $first = 'material_used'.$i;
        $sec = 'material_used'.($i + 1);
        $firstlot = 'lot_number'.$i;
        $firstqty = 'quantity'.$i;

        list($$first, $$sec) = explode(' $ ', $$first, 2);
        list($$first, $$firstlot, $$firstqty) = explode(' _ ', $$first, 3);

        if($i==1){
          if($data['status']=='Détruit'){
            $tableBody .= '<tr class="destructed">';
          } else {
            $tableBody .= '<tr>';
          }
          $tableBody .= '<td class="' . $solNumber . '" rowspan=' . $numberofmat . '>' . $data[$solNumber] . '</td>
                        <td class="' . $solName . '" rowspan=' . $numberofmat . '>' . $data[$solName] . '</td>
                        <td class="concentration" rowspan=' . $numberofmat . '>' . $data['concentration'] . '</td>
                        <td class="solvent" rowspan=' . $numberofmat . '>' . $data['solvent'] . '</td>';

          switch($data['status']){
            case "OK":
              $tableBody .= '<td class="status_OK" rowspan=' . $numberofmat . '>' . $data['status'] . '</td>';
              break;
            case "Détruit":
              $tableBody .= '<td class="status_destroy" rowspan=' . $numberofmat . '>' . $data['status'] . '</td>';
              break;
            default:
              $tableBody .= '<td class="status_Perempted" rowspan=' . $numberofmat . '>' . $data['status'] . '</td>';
              break;
          }
          $tableBody .= '<td class="packaging" rowspan=' . $numberofmat . '>' . $data['packaging'] . '</td>
                        <td class="preparation_date" rowspan=' . $numberofmat . '>' . $preparation_date . '</td>
                        <td class="expiration_date" rowspan=' . $numberofmat . '>' . $expiration_date . '</td>
                        <td class="destruction_date" rowspan=' . $numberofmat . '>' . $destruction_date . '</td>
                        <td class="material_used">' . $material_used1 . '</td>
                        <td class="lot_number">' . $lot_number1 . '</td>
                        <td class="quantity">' . $quantity1 . '</td>
                        <td class="maker" rowspan=' . $numberofmat . '>' . $data['maker'] . '</td>
                        <td class="checker" rowspan=' . $numberofmat . '>' . $data['checker'] . '</td>
                        <td class="verification_date" rowspan=' . $numberofmat . '>' . $verification_date . '</td>
                      </tr>';
        } else {
          if($data['status']=='Détruit'){
            $tableBody .= '<tr class="destructed">';
          } else {
            $tableBody .= '<tr>';
          }
          $tableBody .= '<td class="' . $solNumber . ' noDisplay">' . $data[$solNumber] . '</td>
                        <td class="' . $solName . ' noDisplay">' . $data[$solName] . '</td>
                        <td class="concentration noDisplay">' . $data['concentration'] . '</td>
                        <td class="solvent noDisplay">' . $data['solvent'] . '</td>
                        <td class="bold green noDisplay">' . $data['status'] . '</td>
                        <td class="packaging noDisplay">' . $data['packaging'] . '</td>
                        <td class="preparation_date noDisplay">' . $preparation_date . '</td>
                        <td class="expiration_date noDisplay">' . $expiration_date . '</td>
                        <td class="destruction_date noDisplay">' . $destruction_date . '</td>

                        <td class="material_used">' . $$first . '</td>
                        <td class="lot_number">' . $$firstlot . '</td>
                        <td class="quantity">' . $$firstqty . '</td>

                        <td class="maker noDisplay">' . $data['maker'] . '</td>
                        <td class="checker noDisplay">' . $data['checker'] . '</td>
                        <td class="verification_date noDisplay">' . $verification_date . '</td>
                      </tr>';
        }
      }
      $line_number++;
    }
    $selectData->closeCursor();
  }

  ?>
    <!DOCTYPE HTML>
    <html>
      <head>
        <meta charset="utf-8">
        <title>Gestionnaire Biosolve</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
        <link rel="stylesheet" href="../assets/css/prefixed/main.css" media="screen"/>
        <link rel="stylesheet" href="../assets/css/print.css" media="print"/>
        <link rel="stylesheet" type="text/css" href="../assets/css/table_filter.css">
        <script LANGUAGE="JavaScript" src="../assets/js/table_filter.js"></script>

        <noscript><link rel="stylesheet" href="../assets/css/noscript.css" /></noscript>
      </head>
      <body>
        <main>
          <nav>
            <ul>
              <li>
                <a href="../mainboard.php" class="nav--btn">Retour au menu principal</a>
              </li>
              <li>
                <a href="consultation.php" class="nav--btn">Changer de tableau</a>
              </li>
              <li>
                <a href="../Connexion/editionprofil.php" class="nav--btn">Editer mon profil</a>
              </li>
              <li>
                <a href="../Connexion/deconnexion.php" class="nav--btn">Se déconnecter</a>
              </li>
            </ul>
          </nav>
          <div class="content">
            <div id="page-wrapper">
              
              <?php include('../header.php'); ?>


              <section class='section'>
                <div class='section--title'>
                  <?php	echo($title); ?>
                </div>
                <div class='section--text'>
                  <table id="tableconsult">
                    <thead id="theadConsultation">
                      <tr>
                        <?php echo($tableHeader);?>
                      </tr>
                    </thead>
                    <tbody id="tbodyConsultation">
                      <?php echo($tableBody);?>
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
} else {
  header("Location: consultation.php");
}
?>

<!-- Scripts -->
  <script src="../assets/js/nav.js"></script>
  <script src="../assets/js/darkmode.js"></script>
  <script src="../assets/js/javascript functions/consultation_table.js"></script>
