<?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddmat = new PDO('mysql:host=127.0.0.1;dbname=material;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

include_once('../Connexion/cookieconnect.php');

class Solution
{
  public string $typeOfMaterial;
  public int $solNumber;
  public string $name;
  public string $concentration;
  public string $solvent;
  public string $packaging;
  public string $preparationDate;
  public string $expirationDate;
  public string $materialUsed;
  public string $maker;
  public function __construct($typeOfMaterial, $solNumber, $name, $concentration, $solvent, $packaging, $preparationDate, $expirationDate, $materialUsed, $maker)
  {
    $this->typeOfMaterial = $typeOfMaterial;
    $this->solNumber = $solNumber;
    $this->name = $name;
    $this->concentration = $concentration;
    $this->solvent = $solvent;
    $this->packaging = $packaging;
    $this->preparationDate = $preparationDate;
    $this->expirationDate = $expirationDate;
    $this->materialUsed = $materialUsed;
    $this->maker = $maker;
  }
}

if(isset($_SESSION['id']) AND $_SESSION['id'] > 0) {
  ?>
  <!DOCTYPE HTML>
  <html lang="fr">
    <head>
      <meta charset="utf-8">
      <title>Gestionnaire Biosolve</title>
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <meta name="description" content="Onglet de préparation d'une solution.">
      <link rel="stylesheet" href="../assets/css/prefixed/main.css" media="screen"/>
      <link rel="stylesheet" href="../assets/css/print.css" media="print"/>
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
                <h2>Préparation d'une nouvelle solution</h2>
              </div>
              <div class='section--text'>
                <p>Quel type de solution souhaitez-vous créer?</p>
                <form method="POST" name="catform" action="" class="form-prep" id="catform">
                    <input class="radiobutton" type="radio" id="choice1" name="typeOfMaterial" value="reagent" onchange="selectAMaterialType('reagent')"/>
                      <label class="radiolabel" for="choice1">
                        <div class="newradio"></div>
                        <span class="radiotext">Réactif</span>
                      </label>
                    <input class="radiobutton" type="radio" id="choice2" name="typeOfMaterial" value="indicator" onchange="selectAMaterialType('indicator')"/>
                      <label class="radiolabel" for="choice2">
                        <div class="newradio"></div>
                        <span class="radiotext">Indicateur</span>
                      </label>
                    <input class="radiobutton" type="radio" id="choice3" name="typeOfMaterial" value="standard" onchange="selectAMaterialType('standard')"/>
                      <label class="radiolabel" for="choice3">
                        <div class="newradio"></div>
                        <span class="radiotext">Standard</span>
                      </label>
                    <input class="radiobutton" type="radio" id="choice4" name="typeOfMaterial" value="scale" onchange="selectAMaterialType('scale')"/>
                      <label class="radiolabel" for="choice4">
                        <div class="newradio"></div>
                        <span class="radiotext">Etalon</span>
                      </label>
                  <select name="name" id="nameList" required onchange="request(this);"></select>
                  <div name="sop" id="sop" class="SOP-prep"></div>
                  <div>
                    <div name="packsentence" id="packsentence" class="SOP-packsentence"></div>
                    <input type="text" list="pack" name="packaging" id="packcombo" oninput="selectANewPackaging()"/>
                    <datalist id="pack">
                    </datalist>
                  </div>
                  <table id="compoundtable" class="noDisplay">
                    <thead>
                      <th>Nom du composant</th>
                      <th>Détail</th>
                      <th>Quantité théorique</th>
                      <th>Quantité réelle</th>
                    </thead>
                    <tbody id="components">
                    </tbody>
                  </table>
                  <div>
                    <input type="date" name="preparation_date" value="<?php echo(date('Y-m-d'))?>" required/>
                    <button type="submit" name="formpreparation" class="btn">Envoyer</button>
                  </div>
                </form>
          </div>
        </section>
      </div>
    </body>
  </html>
  <?php
}

if(isset($_SESSION['id'],$_POST['formpreparation']) AND $_SESSION['id'] > 0) {
  $typeOfMaterial = htmlspecialchars($_POST['typeOfMaterial']);
  $solNumber = htmlspecialchars($_POST['sol_number']);
  $name = htmlspecialchars($_POST['name']);
  //concentration and solvent from name
  if($typeOfMaterial == "scale") {
    list($name, $solvent) = explode(" dans ", $name, 2);
    $concentration = '-';
  } else {
    list($name, $concentration) = explode(" à ", $name, 2);
    list($concentration, $solvent) = explode(" dans ", $concentration, 2);
  }
  //packaging
  $packaging = htmlspecialchars($_POST['packaging']);
  $theoricalPack = htmlspecialchars($_POST['theoricalpack']);
  if($packaging == "") {
      $packaging = $theoricalPack;
  } else {
    $unityExist = strrpos($packaging, "mL");
    if($unityExist === false) {
      $packaging .= "mL";
    }
  }

  $preparationDate = htmlspecialchars($_POST['preparation_date']);
  //expirationDate from preparationDate and lifetime
  $preparationDateTimestamp = strtotime($preparationDate);
  $lifetime = htmlspecialchars($_POST['lifetime']);
  $lifetime = str_replace("jour", "day", $lifetime);
  $lifetime = str_replace("mois", "month", $lifetime);
  $lifetime = str_replace("an", "year", $lifetime);
  $expirationDate = date('Y-m-d', strtotime('+'.$lifetime, $preparationDateTimestamp));
  //materialUsed
  $numberOfCompounds = htmlspecialchars($_POST['numberofcompounds']);
  $materialUsed = "";

  for($i = 0; $i < $numberOfCompounds; $i++) {
    $componentName = htmlspecialchars($_POST['compname' . $i]);
    $componentDetail = htmlspecialchars($_POST['compdetail' . $i]);
    $realQuantity = htmlspecialchars($_POST['realqty' . $i]);

    $componentDetail = str_replace("(", " _ ", $componentDetail);
    $componentDetail = str_replace(")", " _ ", $componentDetail);
    $componentDetail = str_replace("Produit pur - lot n°", "", $componentDetail);

    $materialUsed .= $componentName . $componentDetail . $realQuantity . " $ ";
  }
  $materialUsed = substr($materialUsed, 0, -3);
  //maker from session info
  $sessionid = htmlspecialchars($_SESSION['id']);
  $selectMaker = $bdd->prepare('SELECT pseudo
                                FROM membres
                                WHERE id = :memberId');
  $selectMaker->bindValue(':memberId', $sessionid, PDO::PARAM_INT);
  $selectMaker->execute();
    while ($result = $selectMaker->fetch()) {
      $maker = $result['pseudo'];
    }
  $selectMaker->closeCursor();
  $words = explode(" ", $maker);
  $maker = "";
  foreach($words as $w) {
    $maker .= $w[0];
  }

  $solution = new Solution($typeOfMaterial, $solNumber, $name, $concentration, $solvent, $packaging, $preparationDate, $expirationDate, $materialUsed, $maker);

  $solution = php2json($solution);
  ?>
  <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
  <div id="oModal" class="oModal">
    <div>
      <header>
        <h2 id="headerText">ATTENTION</h2>
        <a href="prepare_a_solution.php" id ="closeBtn" title="Fermer la fenêtre" class="right">X</a>
      </header>
      <section>
      <p id="popupText">Vous êtes sur le point de créer une nouvelle solution. Souhaitez-vous continuer?</p>
      </section>
      <footer>
        <div id="footerText">
          <form method="post" action="">
          <input type="hidden" value="<?php echo($solution);?>" name="solution"/>
            <div class="modal_buttons">
              <button type="submit" name="prepformConfirmed" class="btn">Oui</button>
              <a href="prepare_a_solution.php" class="btn">Non</a>
            </div>
          </form>
        </div>
      </footer>
    </div>
  </div>
  <?php
}

// Return value to database
  if(isset($_SESSION['id'],$_POST['prepformConfirmed']) AND $_SESSION['id'] > 0){

    var_dump($_POST['solution']);
    $solution = json2php($_POST['solution']);

    $typeOfMaterial = htmlspecialchars($solution->typeOfMaterial);
    $solNumber = htmlspecialchars($solution->solNumber);
    $name = htmlspecialchars($solution->name);
    $concentration = htmlspecialchars($solution->concentration);
    $solvent = htmlspecialchars($solution->solvent);
    $packaging = htmlspecialchars($solution->packaging);
    $preparationDate = htmlspecialchars($solution->preparationDate);
    $expirationDate = htmlspecialchars($solution->expirationDate);
    $materialUsed = htmlspecialchars($solution->materialUsed);
    $maker = htmlspecialchars($solution->maker);

    switch($typeOfMaterial) {
      case "reagent":
        $solutionName = 'reag_name';
        $solutionNumber = 'reag_number';
        break;
      case "indicator":
        $solutionName = 'ind_name';
        $solutionNumber = 'ind_number';
        break;
      case "standard":
        $solutionName = 'std_name';
        $solutionNumber = 'std_number';
        break;
      case "scale":
        $solutionName = 'sc_name';
        $solutionNumber = 'sc_number';
        break;
    }

    $selectAlreadyPrepared = $bddmat->prepare("SELECT *
                                               FROM {$typeOfMaterial}
                                               WHERE {$solutionName} = :solutionName
                                                 AND concentration = :concentration
                                                 AND solvent = :solvent
                                                 AND status != 'Détruit'");
    $selectAlreadyPrepared->bindValue(':solutionName', $name);
    $selectAlreadyPrepared->bindValue(':concentration', $concentration);
    $selectAlreadyPrepared->bindValue(':solvent', $solvent);
    $selectAlreadyPrepared->execute();
    $alreadyPrepared = count($selectAlreadyPrepared->fetchAll());
    if($alreadyPrepared != 0) {
      $closeOther = $bddmat->prepare("UPDATE {$typeOfMaterial}
                                      SET preparation_date = :preparationDate
                                      WHERE {$solutionName} = :solutionName
                                         AND concentration = :concentration
                                         AND solvent = :solvent
                                         AND status != 'Détruit'");
      $closeOther->bindValue(':preparationDate', $preparationDate);
      $closeOther->bindValue(':solutionName', $name);
      $closeOther->bindValue(':concentration', $concentration);
      $closeOther->bindValue(':solvent', $solvent);
      $closeOther->execute();
      $closeOther->closeCursor();
    }
    $insertSolution = $bddmat->prepare("INSERT INTO {$typeOfMaterial}
                                        ({$solutionNumber},
                                         {$solutionName},
                                         concentration,
                                         solvent,
                                         packaging,
                                         preparation_date,
                                         expiration_date,
                                         material_used,
                                         maker)
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insertSolution->execute(array($solNumber, $name, $concentration, $solvent, $packaging, $preparationDate, $expirationDate, $materialUsed, $maker)) or die('Erreur SQL !'.$sql.'<br />');
    $insertSolution->closeCursor();
    ?>
    <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
    <div id="oModal" class="oModal">
      <div>
        <header>
          <h2 id="headerText">Préparation d'une solution</h2>
          <a href="prepare_a_solution.php" id ="closeBtn" title="Fermer la fenêtre" class="right">X</a>
        </header>
        <section>
        <p id="popupText">Solution créée avec succès!</p>
        </section>
        <footer>
          <div id="footerText">
            <a href="prepare_a_solution.php" class="btn">Fermer</a>
          </div>
        </footer>
      </div>
    </div>
    <?php
  }
  function json2php($json) {
    $json = str_replace('&quot;', '"', $json);
    $json = str_replace('&gt;=', '>=', $json);
    $json = str_replace('/b', ' ', $json);
    return json_decode($json);
  }

  function php2json($php) {
    $json = json_encode($php);
    $json = str_replace('"', '&quot;', $json);
    $json = str_replace('>=', '&gt;=', $json);
    $json = str_replace(' ', '/b', $json);
    return $json;
  }
  ?>

<!-- Scripts -->
  <script src="../assets/js/nav.js"></script>
  <script src="../assets/js/darkmode.js"></script>
  <script src="../assets/js/javascript functions/prepare_a_solution.js"></script>