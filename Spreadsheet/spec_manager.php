<?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddspreadsheet = new PDO('mysql:host=127.0.0.1;dbname=spreadsheet;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

include_once('../Connexion/cookieconnect.php');

if(isset($_SESSION['id']) AND $_SESSION['id'] > 0 AND isset($_POST['form_selectspec'])) {
  $reference = htmlspecialchars($_POST['reference']);
  if(isset($_POST['spec'])) {
    $spec = $_POST['spec'];
  } else {
    $spec = [];
  };
  $spechtml = '';
  $selectSpecMatchingToMaterial = $bddspreadsheet->query('SELECT * FROM data_spec WHERE reference = '.$reference.' ORDER BY spec');
    while ($specification = $selectSpecMatchingToMaterial->fetch()) {
      switch($specification['rank']){
        case 'RM':
          $rank = 'Matière première';
          break;
        case 'Int':
          $rank = 'Intermédiaire';
          break;
        case 'FP':
          $rank = 'Produit fini';
          break;
      }
      if(in_array($specification['spec'],$spec)) {
        $spechtml .= '<input type="checkbox" id="spec#' .$specification['spec']. '" name="spec[]" value="' .$specification['spec']. '" class="check" checked="checked"/>';
      } else {
        $spechtml .= '<input type="checkbox" id="spec#' .$specification['spec']. '" name="spec[]" value="'.$specification['spec']. '" class="check"/>';
      }
      $spechtml .= '<label class="cbx" for="spec#'.$specification['spec']. '">
                      <span>
                        <svg width="12px" height="10px">
                          <use xlink:href="#check"></use>
                        </svg>
                      </span>
                      <span>' .$specification['spec']. ' / ' .$rank. ' / ' .$specification['grade']. '</span>
                    </label><br/>';
    }
  $selectSpecMatchingToMaterial->closeCursor();

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
              <h2>Spécification</h2>
              </div>
              <div class='section--text'>
                <p>Pour quelle(s) spécification(s) souhaitez-vous analyser cette matière?</p>
                <form method="POST" action="" class="form-selectSpec">
                  <fieldset>
                    <?php echo($spechtml);?>
                  </fieldset>
                  <input type="hidden" value="<?php echo($reference);?>" name="reference"/>
                  <button type="submit" name="form_createspec" class="btn">Nouvelle spécification</button>
                  <button type="submit" name="form_specselected" formaction="worksheet.php" class="btn">Envoyer</button>
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

if(isset($_SESSION['id']) AND $_SESSION['id'] > 0 AND isset($_POST['form_createspec'])) {
  $reference = htmlspecialchars($_POST['reference']);
  $specPHP = '';
  if(isset($_POST['spec'])) {
    $spec = $_POST['spec'];
    for($i = 0; $i < count($spec); $i++) {
      $specPHP .= '<input type="hidden" name="spec[]" value="' .$spec[$i]. '">';
    }
  }	

  $gradehtml = '';
  $testchoice = '';
  $testsPHP = '';
  if(isset($_POST['tests'])) {
    $tests = $_POST['tests'];
    for($i = 0; $i < count($tests); $i++) {
      $testsPHP .= '<input type="hidden" name="tests[]" value="' .$tests[$i]. '">';
    }
  }

  $testsumup = '';
  $testhtmlPHP = '';
  $numberOfTestsPHP = '';
  if(isset($_POST['testhtml'])) {
    $testhtml = $_POST['testhtml'];
    $numberOfTestsPHP = '<input type="hidden" name="numberOfTests" value="' .count($testhtml). '">';
    for($i = 0; $i < count($testhtml); $i++) {
      $testexplode = explode(' _ ', $testhtml[$i]);
      $testsumup .= '<tr><td>' .$testexplode[0]. '</td><td>' .$testexplode[1]. '</td><td>' .$testexplode[2]. '</td><td>' .$testexplode[3]. '</td><td>' .$testexplode[4]. '</td></tr>';
      $testhtmlPHP .= '<input type="hidden" name="testhtml[]" value="' .$testhtml[$i]. '">';
    }
  }

  $nbspecPHP = '<label for="nbpsec">Numero de la spécification :</label>
                <input type="text" id="nbspec" name="nbspec" placeholder="ex : 2017" required/>';

  if(isset($_POST['nbspec'])) {
    $nbspec = htmlspecialchars($_POST['nbspec']);
    $nbspecPHP = '<label for="nbspec">Numero de la spécification :</label>
                  <input type="text" id="nbspec" name="nbspec" value="' .$nbspec. '" required/>';
  }

  $rankPHP = '<input class="radiobutton" type="radio" id="RM" name="rank" value="RM" checked>
                <label class="radiolabel" for="RM">
                  <div class="newradio"></div>
                  <span class="radiotext">Matière première</span>
                </label>
              <input class="radiobutton" type="radio" id="Int" name="rank" value="Int">
                <label class="radiolabel" for="Int">
                  <div class="newradio"></div>
                  <span class="radiotext">Intermédiaire</span>
                </label>
              <input class="radiobutton" type="radio" id="FP" name="rank" value="FP">
                <label class="radiolabel" for="FP">
                  <div class="newradio"></div>
                  <span class="radiotext">Produit fini</span>
                </label>';

  if(isset($_POST['rank'])) {
    $rank = htmlspecialchars($_POST['rank']);
    switch ($rank) {
      case 'RM' :
        $rankPHP = '<input class="radiobutton" type="radio" id="RM" name="rank" value="RM" checked>
                      <label class="radiolabel" for="RM">
                        <div class="newradio"></div>
                        <span class="radiotext">Matière première</span>
                      </label>
                    <input class="radiobutton" type="radio" id="Int" name="rank" value="Int">
                      <label class="radiolabel" for="Int">
                        <div class="newradio"></div>
                        <span class="radiotext">Intermédiaire</span>
                      </label>
                    <input class="radiobutton" type="radio" id="FP" name="rank" value="FP">
                      <label class="radiolabel" for="FP">
                        <div class="newradio"></div>
                        <span class="radiotext">Produit fini</span>
                      </label>';
        break;
      case 'Int' :
        $rankPHP = '<input class="radiobutton" type="radio" id="RM" name="rank" value="RM">
                      <label class="radiolabel" for="RM">
                        <div class="newradio"></div>
                        <span class="radiotext">Matière première</span>
                      </label>
                    <input class="radiobutton" type="radio" id="Int" name="rank" value="Int" checked>
                      <label class="radiolabel" for="Int">
                        <div class="newradio"></div>
                        <span class="radiotext">Intermédiaire</span>
                      </label>
                    <input class="radiobutton" type="radio" id="FP" name="rank" value="FP">
                      <label class="radiolabel" for="FP">
                        <div class="newradio"></div>
                        <span class="radiotext">Produit fini</span>
                      </label>';
        break;
      case 'FP' :
        $rankPHP = '<input class="radiobutton" type="radio" id="RM" name="rank" value="RM">
                      <label class="radiolabel" for="RM">
                        <div class="newradio"></div>
                        <span class="radiotext">Matière première</span>
                      </label>
                    <input class="radiobutton" type="radio" id="Int" name="rank" value="Int">
                      <label class="radiolabel" for="Int">
                        <div class="newradio"></div>
                        <span class="radiotext">Intermédiaire</span>
                      </label>
                    <input class="radiobutton" type="radio" id="FP" name="rank" value="FP" checked>
                      <label class="radiolabel" for="FP">
                        <div class="newradio"></div>
                        <span class="radiotext">Produit fini</span>
                      </label>';
        break;
    }
  }

  $selectGrade = $bddspreadsheet->query("SELECT DISTINCT grade FROM data_spec ORDER BY grade");
    while ($grade = $selectGrade->fetch()) {
      $gradehtml .= '<option value="' .$grade['grade'].'"></option>';
    }
  $selectGrade->closeCursor();

  $gradePHP = '<label for="grade">Grade :</label><input type="text" list="gradeList" name="grade" id="grade" placeholder="ex : 06MS - HPLC" required/>
                <datalist id="gradeList">' .$gradehtml. '</datalist>';

  if(isset($_POST['grade'])) {
    $grade = htmlspecialchars($_POST['grade']);
    $gradePHP = '<label for="grade">Grade :</label><input type="text" list="gradeList" name="grade" id="grade" value ="' .$grade. '" required/>
                  <datalist id="gradeList">' .$gradehtml. '</datalist>';
  }

  $seletcTest = $bddspreadsheet->query("SELECT * FROM test_SOP ORDER BY test_ref");
    while ($test = $seletcTest->fetch()) {
      $testchoice .= '<option value="' .$test['test_id']. '">' .$test['test_ref']. ' - ' .$test['test_name']. '</option>';
    }
  $seletcTest->closeCursor();
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
              <h2>Création d'une spécification</h2>
              </div>
              <div class='section--text'>
                <form method="POST" action="" class="form-createSpec">
                  <div>
                    <div>
                      <?php echo($nbspecPHP); ?>
                    </div>
                    <div>
                      <label for="rank">Rang :</label>
                      <div id="rank">
                        <?php echo($rankPHP); ?>
                      </div>
                    </div>
                    <div>
                      <?php echo($gradePHP); ?>
                    </div>
                  </div>
                  <div>
                    <p>Veuillez renseigner les tests associés à cette spécification :</p>
                    <table>
                      <thead>
                        <tr>
                          <th>Test</th>
                          <th>Bornes</th>
                          <th>Criticité</th>
                          <th>Visibilité</th>
                        </tr>
                      </thead>
                      <tbody id="test_sumup">
                        <?php echo($testsumup); ?>
                      </tbody>
                    </table>
                  </div>
                  <div>
                    <div id="tests">
                      <label for="testSelect">Test à ajouter</label>
                      <select name="test_ref" id="testSelect" onchange="request(this);">
                        <option value="none">Selection</option>
                        <?php echo($testchoice); ?>
                      </select>
                    </div>
                    <div id="limits" class="noDisplay">
                    </div>
                    <div>
                      <input type="checkbox" class="check" id="critical" name="critical" value="critical"/>
                      <label class=" cbx" for="critical">
                        <span>
                          <svg width="12px" height="10px">
                            <use xlink:href="#check"></use>
                          </svg>
                        </span>
                        <span>Criticité</span>
                      </label><br/>
                      <input type="checkbox" class="check" id="visibility" name="visibility" value="visibility"/>
                      <label class="cbx" for="visibility">
                        <span>
                          <svg width="12px" height="10px">
                            <use xlink:href="#check"></use>
                          </svg>
                        </span>
                        <span>Visibilité</span>
                      </label><br/>
                    </div>
                    <div id="testsubmit">
                      <button type="button" id="add" class="btn" onclick="Ajouter()">Ajouter</button>
                      <button type="button" id="delete" class="btn" onclick="Supprimer()">Supprimer</button>
                      <button type="submit" name="form_createtest" formaction="create_a_test.php" class="btn">Créer un test</button>
                      <input type="hidden" name="reference" value="<?php echo($reference);?>"/>
                      <?php echo($specPHP);?>
                      <?php echo($numberOfTestsPHP);?>
                      <?php echo($testsPHP);?>
                      <?php echo($testhtmlPHP);?>
                    </div>
                  </div>
                  <div>
                    <button type="submit" name="form_speccreated" class="btn">Envoyer</button>
                  </div>
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

if(isset($_SESSION['id']) AND $_SESSION['id'] > 0 AND isset($_POST['form_speccreated'])) {
  $reference = htmlspecialchars($_POST['reference']);
  $nbspec = htmlspecialchars($_POST['nbspec']);
  $rank = htmlspecialchars($_POST['rank']);
  $grade = htmlspecialchars($_POST['grade']);
  $specPHP = '';
  if(isset($_POST['spec'])) {
    $spec = $_POST['spec'];
    for($i = 0; $i < count($spec); $i++) {
      $specPHP .= '<input type="hidden" name="spec[]" value ="' .$spec[$i]. '">';
    }
  }
  $specPHP .= '<input type="hidden" name="spec[]" value="' .$nbspec. '">';

  if(isset($_POST['tests'])) {
    $tests = $_POST['tests'];
    for($i = 0; $i < count($tests); $i++) {
      $testarray = explode(' _ ', $tests[$i]);
      $test = $testarray[0];
      $lower_limit = $testarray[1];
      $upper_limit = $testarray[2];
      $unit = $testarray[3];
      $critical = $testarray[4];
      $visible = $testarray[5];
      if($unit == 'undefined') {
        $unit = NULL;
      }
      $insertspeccontent = $bddspreadsheet -> prepare("INSERT INTO spec_content(spec, test, lower_limit, upper_limit, unit, critical, visible) VALUES(?, ?, ?, ?, ?, ?, ?)");
      $insertspeccontent -> execute(array($nbspec, $test, $lower_limit, $upper_limit, $unit, $critical, $visible));
      $insertspeccontent -> closeCursor();
    }
    $insertdataspec = $bddspreadsheet -> prepare("INSERT INTO data_spec(reference, spec, rank, grade) VALUES(?, ?, ?, ?)");
    $insertdataspec -> execute(array($reference, $nbspec, $rank, $grade));
    $insertdataspec -> closeCursor();
  }
  ?>
  <link rel="stylesheet" href="../assets/css/prefixed/main.css" />
  <div id="oModal" class="oModal">
    <div>
      <header>
        <h2 id="headerText">Création de spécification</h2>
        <a href="spec_manager.php" id ="closeBtn" title="Fermer la fenêtre">X</a>
      </header>
      <section>
      <p id="popupText">Félicitation! Vous venez d'insérer une nouvelle spécification dans la base de données. Celle-ci sera maintenant disponible dans la liste des spécifications proposées.</p>
      </section>
      <footer>
        <div id="footerText">
          <form method="post" action="">
            <input type="hidden" value="<?php echo($reference);?>" name="reference"/>
            <?php echo($specPHP);?>
            <button type="submit" name="form_selectspec" class="btn">Fermer</button>
          </form>
        </div>
      </footer>
    </div>
  </div>
<?php
}
?>

<!--SVG Sprites-->
  <svg class="inline-svg">
    <symbol id="check" viewbox="0 0 12 10">
      <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
    </symbol>
  </svg>

<!-- Scripts -->
  <!-- <script src="../assets/js/jquery.min.js"></script> -->
  <script src="../assets/js/nav.js"></script>
  <script src="../assets/js/darkmode.js"></script>
  <script src="../assets/js/javascript functions/spec_manager.js"></script>

