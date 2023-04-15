<?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddspreadsheet = new PDO('mysql:host=127.0.0.1;dbname=spreadsheet;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

include_once('../Connexion/cookieconnect.php');

if(isset($_SESSION['id'],$_POST['form_createtest']) AND $_SESSION['id'] > 0) {
  $reference = htmlspecialchars($_POST['reference']);

  $specPHP = '';
  if(isset($_POST['spec'])) {
    $spec = $_POST['spec'];
    for($i = 0; $i < count($spec); $i++) {
      $specPHP .= '<input type="hidden" name="spec[]" value ="' .$spec[$i]. '">';
    }
  }
  
  $testsPHP = '';
  if(isset($_POST['tests'])) {
    $tests = $_POST['tests'];
    for($i = 0; $i < count($tests); $i++) {
      $testsPHP .= '<input type="hidden" name="tests[]" value ="' .$tests[$i]. '">';
    }
  }
  
  $testhtmlPHP = '';
  if(isset($_POST['testhtml'])) {
    $testhtml = $_POST['testhtml'];
    for($i = 0; $i < count($testhtml); $i++) {
      $testhtmlPHP .= '<input type="hidden" name="testhtml[]" value ="' .$testhtml[$i]. '">';
    }
  }
  
  $nbspec = htmlspecialchars($_POST['nbspec']);
  $rank = htmlspecialchars($_POST['rank']);
  $grade = htmlspecialchars($_POST['grade']);

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

            <section class="section">
              <div class='section--title'>
              <h2>Création d'un test</h2>
              </div>
              <div class='section--text'>
                <div id="headerTest" class="headerTest">
                  <div>
                    <label for="test_ref">Référence du test :</label>
                    <input type="text" id="test_ref" placeholder="ex : 91 Acetone" required/>
                  </div>
                  <div>
                    <label for="test_name">Nom du test :</label>
                    <input type="text" id="test_name" placeholder="ex : Identification" required/>
                  </div>
                  <div>
                    <label for="method">Méthode :</label>
                    <input type="text" id="method" placeholder="ex : EP/BP" required/>
                  </div>
                  <div>
                    <button type="button" onclick="addHeader()" class="btn">Suivant</button>
                  </div>
                </div>
                <div id="SOPTest" class="SOPTest">
                  <div>
                    <Label for="SOP">Mode opératoire :</Label>
                    <textarea id="SOP" rows="30" placeholder="Texte du test"></textarea>
                  </div>
                  <div>
                    <button type="button" onclick="addSOP()" class="btn">Suivant</button>
                    <button type="button" onclick="backFromSOP()" class="btn">Retour</button>
                  </div>
                </div>
                <div id="solTest" class="solTest">
                  <div>
                    <label for="solution">Réactifs utilisés :</label>
                    <div id="solution">
                      <input class="radiobutton" type="radio" id="RM" name="typeOfSolution" value="RM" onchange="selectASolutionType('RM')"/>
                        <label class="radiolabel" for="RM">
                          <div class="newradio"></div>
                          <span class="radiotext">Produit pur</span>
                        </label>
                      <input class="radiobutton" type="radio" id="reagent" name="typeOfSolution" value="reagent" onchange="selectASolutionType('reagent')"/>
                        <label class="radiolabel" for="reagent">
                          <div class="newradio"></div>
                          <span class="radiotext">Réactif</span>
                        </label>
                      <input class="radiobutton" type="radio" id="indicator" name="typeOfSolution" value="indicator" onchange="selectASolutionType('indicator')"/>
                        <label class="radiolabel" for="indicator">
                          <div class="newradio"></div>
                          <span class="radiotext">Indicateur</span>
                        </label>
                      <input class="radiobutton" type="radio" id="standard" name="typeOfSolution" value="standard"  onchange="selectASolutionType('standard')"/>
                        <label class="radiolabel" for="standard">
                          <div class="newradio"></div>
                          <span class="radiotext">Standard</span>
                        </label>
                      <input class="radiobutton" type="radio" id="scale" name="typeOfSolution" value="scale" onchange="selectASolutionType('scale')"/>
                        <label class="radiolabel" for="scale">
                          <div class="newradio"></div>
                          <span class="radiotext">Etalon</span>
                        </label>
                      <div id="selectSolutions">
                        <select name="selectSolution" id="sol">
                        </select>
                        <br/>
                        <button type="button" onclick="addASol()" class="btn">Ajouter</button>
                        <button type="button" onclick="deleteASol()" class="btn">Supprimer</button>
                      </div>
                    </div>
                  </div>
                  <div>
                    <button type="button" onclick="addSol()" class="btn">Suivant</button>
                    <button type="button" onclick="backFromSol()" class="btn">Retour</button>
                  </div>
                </div>
                <div id="limitsTest" class="limitsTest">
                  <div>
                    <div>
                      <label for="lim">Analyse bornée :</label>
                      <div id="lim">
                        <input class="radiobutton" type="radio" id="limited" name="limitsCB" value="limited"/>
                          <label class="radiolabel" for="limited">
                            <div class="newradio"></div>
                            <span class="radiotext">Oui</span>
                          </label>
                        <input class="radiobutton" type="radio" id="notLimited" name="limitsCB" value="notLimited"/>
                          <label class="radiolabel" for="notLimited">
                            <div class="newradio"></div>
                            <span class="radiotext">Non</span>
                          </label>
                      </div>
                    </div>
                    <div>
                      <label for="calc">avec calcul :</label>
                      <div id="calc">
                        <input class="radiobutton" type="radio" id="calculation" name="calculCB" value="calculation"/>
                          <label class="radiolabel" for="calculation">
                            <div class="newradio"></div>
                            <span class="radiotext">Oui</span>
                          </label>
                        <input class="radiobutton" type="radio" id="noCalculation" name="calculCB" value="noCalculation"/>
                          <label class="radiolabel" for="noCalculation">
                            <div class="newradio"></div>
                            <span class="radiotext">Non</span>
                          </label>
                      </div>
                    </div>
                    <div>
                      <label for="testUnit">Unité :</label>
                      <input type="text" id="testUnit" placeholder="ex : % w/w" name="testUnit"/>
                    </div>
                    <div>
                      <button type="button" onclick="addLimits()" class="btn">Suivant</button>
                      <button type="button" onclick="backFromLimits()" class="btn">Retour</button>
                    </div>
                  </div>
                </div>
                <div id="calculTest" class="calculTest">
                  <div class="WScalculTemplate">
                    <div>
                      <p>De combien d'opérandes est constitué le calcul?</p>
                      <button type="button" onclick="removeOperande()" class="btn">-</button>
                      <button type="button" onclick="addOperande()" class="btn">+</button>
                      <p>Valeur: <span id="number">2</span>
                    </div>
                    <div id="formula">
                      <label for="oform">Complétez le calcul suivant avec des signes de calcul et éventuellement des parenthèses :</label>
                      <div id="oform">
                        <input type="text" id="resultData" onInput="addResultName()"/>
                        <span id='equal'>=</span>
                        <input type="text" id="blank1" onInput="addSign(1)"/>
                        <span id="op1">A</span>
                        <input type="text" id="blank2" onInput="addSign(2)"/>
                        <span id="op2">B</span>
                        <input type="text" id="blank3" onInput="addSign(3)"/>
                      </div>
                    </div>
                    <div>
                      <button type="button" onclick="addCalcul()" class="btn">Suivant</button>
                      <button type="button" onclick="backFromCalcul()" class="btn">Retour</button>
                    </div>
                  </div>
                  <div id="operandeSelection">
                    <div id="operande1" class="WSoperande">
                      <label for="type1">L'opérande A est :</label>
                      <!-- <div id="type1" onclick="addOptionFromOperandeType(1)"> -->
                      <div id="type1">
                        <input class="radiobutton" type="radio" name="type1" id="unknown1" value="unknown1" onchange="addOptionFromOperandeType(1, 'unknown1')"/>
                          <label class="radiolabel" for="unknown1">
                            <div class="newradio"></div>
                            <span class="radiotext">une inconnue</span>
                          </label>
                        <input class="radiobutton" type="radio" name="type1" id="constante1" value="constante1" onchange="addOptionFromOperandeType(1, 'constante1')"/>
                          <label class="radiolabel" for="constante1">
                            <div class="newradio"></div>
                            <span class="radiotext">une constante</span>
                          </label>
                        <input class="radiobutton" type="radio" name="type1" id="density1" value="density1" onchange="addOptionFromOperandeType(1, 'density1')"/>
                          <label class="radiolabel" for="density1">
                            <div class="newradio"></div>
                            <span class="radiotext">une densité</span>
                          </label>
                      </div>
                      <div id="info1">
                      </div>
                    </div>
                    <div id="operande2" class="WSoperande">
                      <label for="type2">L'opérande B est :</label>
                      <!-- <div id="type2" onclick="addOptionFromOperandeType(2)"> -->
                      <div id="type2">
                        <input class="radiobutton" type="radio" name="type2" id="unknown2" value="unknown2" onchange="addOptionFromOperandeType(2, 'unknown2')"/>
                          <label class="radiolabel" for="unknown2"><div class="newradio"></div>
                            <span class="radiotext">une inconnue</span>
                          </label>
                        <input class="radiobutton" type="radio" name="type2" id="constante2" value="constante2" onchange="addOptionFromOperandeType(2, 'constante2')"/>
                          <label class="radiolabel" for="constante2"><div class="newradio"></div>
                            <span class="radiotext">une constante</span>
                          </label>
                        <input class="radiobutton" type="radio" name="type2" id="density2" value="density2" onchange="addOptionFromOperandeType(2, 'density2')"/>
                          <label class="radiolabel" for="density2"><div class="newradio"></div>
                            <span class="radiotext">une densité</span>
                          </label>
                      </div>
                      <div id="info2">
                      </div>
                    </div>
                  </div>
                </div>
                <div id="resultTest" class="resultTest">
                  <div>
                    <div id="resultSentence"></div>
                    <div>
                      <button type="button" onclick="addResult()" class="btn">Suivant</button>
                      <button type="button" onclick="backFromResult()" class="btn">Retour</button>
                    </div>
                  </div>
                </div>
                <div id="confirmation" class="confirmation">
                  <div>
                    <div>
                      <p>Confirmation :</p>
                      <p>Voici un résumé de ce que vous avez indiqué comme données pour ce test. Merci de vérifier si tout est correct et validez pour insérer le test dans la base de données.</p>
                    </div>
                    <div>
                      <form method="POST" id="testCreationForm">
                        <input type="hidden" name="reference" value="<?php echo($reference); ?>">
                        <input type="hidden" name="nbspec" value="<?php echo($nbspec); ?>">
                        <input type="hidden" name="rank" value="<?php echo($rank); ?>">
                        <input type="hidden" name="grade" value="<?php echo($grade); ?>">
                        <?php echo($specPHP); ?>
                        <?php echo($testsPHP); ?>
                        <?php echo($testhtmlPHP); ?>
                      </form>
                        <button type="submit" name="form_testcreated" class="btn">Envoyer</button>
                        <button type="button" onclick="backFromValidation()" class="btn">Retour</button>
                    </div>
                  </div>
                </div>
                <p id="testReference"></p>
                <table id="sumup">
                  <thead>
                    <tr id="testHeader" class="noDisplay"></tr>
                  </thead>
                  <tbody>
                    <tr id="testNumbers"></tr>
                    <tr id="testText"></tr>
                    <tr id="testCalcul"></tr>
                    <tr id="testConclusion" class="noDisplay">
                      <td colspan="2" id="testResult" class="ResultOK"></td>
                    </tr>
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

if(isset($_SESSION['id']) AND $_SESSION['id'] > 0 AND isset($_POST['form_testcreated'])) {
  $reference = htmlspecialchars($_POST['reference']);

  $specPHP = '';
  if(isset($_POST['spec'])) {
    $spec = $_POST['spec'];
    for($i = 0; $i < count($spec); $i++) {
      $specPHP .= '<input type="hidden" name="spec[]" value ="' .$spec[$i]. '">';
    }
  }

  $testsPHP = '';
  if(isset($_POST['tests'])) {
    $tests = $_POST['tests'];
    for($i = 0; $i < count($tests); $i++) {
      $testsPHP .= '<input type="hidden" name="tests[]" value ="' .$tests[$i]. '">';
    }
  }

  $testhtmlPHP = '';
  if(isset($_POST['testhtml'])) {
    $testhtml = $_POST['testhtml'];
    for($i = 0; $i < count($testhtml); $i++) {
      $testhtmlPHP .= '<input type="hidden" name="testhtml[]" value ="' .$testhtml[$i]. '">';
    }
  }

  $nbspec = htmlspecialchars($_POST['nbspec']);
  $rank = htmlspecialchars($_POST['rank']);
  $grade = htmlspecialchars($_POST['grade']);
  
  $test_ref = htmlspecialchars($_POST['test_ref']);
  $test_name = htmlspecialchars($_POST['test_name']);
  $method = htmlspecialchars($_POST['method']);
  $passes_test = htmlspecialchars($_POST['passes_test']);

  isset($_POST['unit']) ? $unit = htmlspecialchars($_POST['unit']) : $unit = NULL;
  isset($_POST['reagents']) ? $reagents = htmlspecialchars($_POST['reagents']) : $reagents = NULL;
  isset($_POST['SOP']) ? $SOP = htmlspecialchars($_POST['SOP']) : $SOP = NULL;
  isset($_POST['calcul_description']) ? $calcul_description = htmlspecialchars($_POST['calcul_description']) : $calcul_description = NULL;
  isset($_POST['operandes']) ? $operandes = htmlspecialchars($_POST['operandes']) : $operandes = NULL;
  isset($_POST['limits']) ? $limits = htmlspecialchars($_POST['limits']) : $limits = NULL;

  $result = htmlspecialchars($_POST['result']);

  $inserttest = $bddspreadsheet -> prepare("INSERT INTO test_sop(test_ref, test_name, method, passes_test, unit, reagents, SOP, calcul_description, operandes, limits, result) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $inserttest -> execute(array($test_ref, $test_name, $method, $passes_test, $unit, $reagents, $SOP, $calcul_description, $operandes, $limits, $result));
  $inserttest -> closeCursor();

  ?>
  <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
  <div id="oModal" class="oModal">
    <div>
      <header>
        <h2 id="headerText">Création de test</h2>
        <a href="spec_manager.php" id ="closeBtn" title="Fermer la fenêtre">X</a>
      </header>
      <section>
      <p id="popupText">Félicitation! Vous venez d'insérer un nouveau test dans la base de données. Celle-ci sera maintenant disponible dans la liste des tests proposés.</p>
      </section>
      <footer>
        <div id="footerText">
          <form method="post">
            <input type="hidden" value="<?php echo($reference);?>" name="reference"/>
            <?php echo($specPHP);?>
            <input type="hidden" value="<?php echo($nbspec);?>" name="nbspec"/>
            <input type="hidden" value="<?php echo($rank);?>" name="rank"/>
            <input type="hidden" value="<?php echo($grade);?>" name="grade"/>
            <?php echo($testsPHP);?>
            <?php echo($testhtmlPHP);?>
            <button type="submit" name="form_createspec" formaction="spec_manager.php" class="btn">Fermer</button>
          </form>
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
  <script src="../assets/js/javascript functions/create_a_test.js"></script>
