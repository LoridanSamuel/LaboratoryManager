<?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddspreadsheet = new PDO('mysql:host=127.0.0.1;dbname=spreadsheet;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddmat = new PDO('mysql:host=127.0.0.1;dbname=material;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

include_once('../Connexion/cookieconnect.php');

//if the spec selection form is validated :
  if(isset($_SESSION['id']) AND $_SESSION['id'] > 0 AND isset($_POST['form_specselected'])) {

    // $spec is an array of the spec(s) selected
      $reference = htmlspecialchars($_POST['reference']);
      $spec = $_POST['spec'];

    //for each spec selected, look in the database the tests concerned
      $testlist = [];
      for($i = 0; $i < count($spec); $i++) {
        $reqtests = $bddspreadsheet->query('SELECT * FROM spec_content WHERE spec = '.$spec[$i]);
          while ($tests = $reqtests->fetch()) {
            array_splice($tests, 1, 1);
            array_splice($tests, 2, 1);
            array_splice($tests, 3, 1);
            array_splice($tests, 4, 1);
            array_splice($tests, 5, 1);
            array_splice($tests, 6, 1);
            array_splice($tests, 7, 1);
            $tobecopied = 'yes';

            //look in $testlist if the test we want to insert is already in it
            //if yes, it won't be copied but the limits are changed in order to have the biggest lower limit and the smallest upper limit (same for visible and critical)
              for($j = 0; $j < count($testlist); $j++) {
                if($tests['test'] == $testlist[$j]['test']) {
                  $tobecopied = 'no';
                  if($tests['lower_limit'] <> 'PT') {
                    if($tests['lower_limit'] > $testlist[$j]['lower_limit']) {
                      $testlist[$j]['lower_limit'] = $tests['lower_limit'];
                    }
                    if($tests['upper_limit'] < $testlist[$j]['upper_limit']) {
                      $testlist[$j]['upper_limit'] = $tests['upper_limit'];
                    }
                  }
                  if($tests['critical'] == 'yes') {
                    $testlist[$j]['critical'] = 'yes';
                  }
                  if($tests['visible'] == 'yes') {
                    $testlist[$j]['visible'] = 'yes';
                  }
                }
              }
              if($tobecopied == 'yes') {
                $testlist[] = $tests;
              }
          }
        $reqtests->closeCursor();
      }

    //resort the list in alphanumerical order
      $testcolumn = array_column($testlist, 'test');
      array_multisort($testcolumn, SORT_ASC, $testlist);

    //creation of the tests sumup according to the array created before
      $testsumup = "";
      for($i = 0; $i < count($testlist); $i++) {
        $test = $testlist[$i]['test'];
        $lower = $testlist[$i]['lower_limit'];
        $upper = $testlist[$i]['upper_limit'];
        $unit = $testlist[$i]['unit'];
        if($testlist[$i]['critical'] == 'yes') {
          $crit = 'CRITIQUE';
        } else {
          $crit = 'non critique';
        }
        $vis = $testlist[$i]['visible'];

        if($lower == 'PT') {
          $limit = 'Passes Test';
        } else {
          $limit = $lower. ' - ' .$upper. ' ' .$unit;
        }

        $inputId = 'test' .$i;
        $inputValue = $test. ' _ ' .$lower. ' _ ' .$upper;
        $inputLabel = $test. ' / ' .$limit. ' / ' .$crit;

        if($vis == 'yes') {
          $testsumup .= '<input type="checkbox" class="check" id="' .$inputId. '" name="visibletests[]" value="' .$inputValue. '" checked = "checked"/>';
        } else {
          $testsumup .= '<input type="checkbox" class="check" id="' .$inputId. '" name="visibletests[]" value="' .$inputValue. '"/>';
        }
        $testsumup .= '<label class="cbx" for="' .$inputId. '">
                        <span>
                          <svg width="12px" height="10px">
                            <use xlink:href="#check"></use>
                          </svg>
                        </span>
                        <span>' .$inputLabel. '</span>
                      </label><br/>';
      }

    //management of what will be seen in the tests list and the information form about the lot number and the number of sample
      if(count($spec)<>1) {
        $lot_number = '<label for="lot_number_input">Numéro de lot : </label>
                       <input type="text" name="lot_number" id="lot_number_input"/>';
        $ref_cat = '<label for="ref_cat_input">Référence catalogue : </label>
                    <input type="text" name="ref_cat" id="ref_cat_input" value="Multiple"/>';
      } else {
        $reqSpecInfo = $bddspreadsheet->query('SELECT * FROM data_spec WHERE spec = '.$spec[0]);
        $infoSpec = $reqSpecInfo->fetch();

        $gradeInfo = explode(' - ', $infoSpec['grade']);
        $gradeClient = $gradeInfo[0];
        $gradeSimple = substr($gradeClient, 0, 2);
        $client = substr($gradeClient, 2);
        if($infoSpec['rank'] == 'Int') {
          $lot_number = '<label for="lot_number_input">Numéro de lot : </label>
                         <input type="text" name="lot_number" id="lot_number_input" value="0402"/>';
          $ref_cat = '<label for="ref_cat_input">Référence catalogue : </label>
                      <input type="text" name="ref_cat" id="ref_cat_input" value="30' .$reference. ' - ' .$gradeClient. '"/>';
        } else if($infoSpec['rank'] == 'FP') {
          $lot_number = '<label for="lot_number_input">Numéro de lot : </label>
                         <input type="text" name="lot_number" id="lot_number_input"/>';
          $ref_cat = '<label for="ref_cat_input">Référence catalogue : </label>
                      <input type="text" name="ref_cat" id="ref_cat_input" value="00' .$reference . $gradeSimple. '"/>';
        } else if($infoSpec['rank'] == 'RM') {
          $lot_number = '<label for="lot_number_input">Numéro de lot : </label>
                         <input type="text" name="lot_number" id="lot_number_input" value="100"/>';
          $ref_cat = '<label for="ref_cat_input">Référence catalogue : </label>
                      <input type="text" name="ref_cat" id="ref_cat_input" value="10' .$reference. '"/>';
        }
      }
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
                  <h2>Mise en forme de la feuille de calcul</h2>
                </div>
                <div class='section--text'>
                  
                  <form method="POST" action="" class="form-selectTest">
                    <div id="visibleTest">
                      <p>Vérifiez que les analyses visibles sont bien configurées.</p>
                      <?php echo($testsumup); ?>
                      <button type="button" onclick="goToHeader()" class="btn">Suivant</button>
                    </div>
                    <div id="worksheetHeader" class="noDisplay">
                      <p>Complétez les informations suivantes :</p>
                      <div>
                        <div id="lot_number">
                          <?php echo($lot_number); ?>
                        </div>
                        <div id="ref_cat">
                          <?php echo($ref_cat); ?>
                        </div>
                        <div id="numberOfSample">
                          <label for="number_sample_input">Nombre d'échantillons : </label>
                          <input type="text" name="numberOfSample" id="number_sample_input" value="1"/>
                        </div>
                        <input type="hidden" name="reference" value="<?php echo($reference);?>">
                        <button type="submit" name="form_worksheetCreation" class="btn">Valider</button>
                        <button type="button" onclick="backFromHeader()" class="btn">Retour</button>
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

//If the information about the tests that can be seen and lot number and number of samples are validated
  if(isset($_SESSION['id']) AND $_SESSION['id'] > 0 AND isset($_POST['form_worksheetCreation'])) {

    // $visibleTests is an array of the tests (and limits, etc...) that will be visible
      $visibleTests = $_POST['visibletests'];
      $lot_number = htmlspecialchars($_POST['lot_number']);
      $ref_cat = htmlspecialchars($_POST['ref_cat']);
      $numberOfSample = htmlspecialchars($_POST['numberOfSample']);
      $reference = htmlspecialchars($_POST['reference']);
      $sessionid = htmlspecialchars($_SESSION['id']);
      $today = date("d/m/Y");

    //Look in the database to find the name of the operator in order to create the sign to be put at the bottom of the worksheet
      $reqoperator = $bdd->query('SELECT pseudo FROM membres WHERE id=' .$sessionid);
        $result = $reqoperator->fetch();
        $operator = $result['pseudo'];
        $str = explode(' ', $operator);
        $nickname = substr($str[0], 0);
        $surname = substr($str[1], 0);
        $sign = $nickname[0] . $surname[0];
      $reqoperator->closeCursor();

    //Look in the database to find the material name and its theorical density
      $reqmatinfo = $bddspreadsheet->query('SELECT * FROM material_specificity WHERE reference=' .$reference);
        $result = $reqmatinfo->fetch();
        $mat_name = $result['name'];
        $density = $result['density'];
      $reqmatinfo->closeCursor();

    //Look in the database to find the last version number in use of the worksheet generator program and its date
      $reqversion = $bddspreadsheet->query('SELECT * FROM version ORDER BY id DESC LIMIT 1');
        $result = $reqversion->fetch();
        $QA_name = $result['QA_name'];
        $version = $result['version'];
        $version_date = $result['version_date'];
        $version_date = date("d/m/Y", strtotime($version_date));
      $reqversion->closeCursor();

    $bodyHTML = '';
    $hiddenCalculHTML = '';

    $reagentsAlert = [];
   
    //for each test in visibleTests
      for($i = 0; $i < count($visibleTests); $i++) {

        //creation of an array in order to split the information of the line in visibleTests
        //$visibleTests[$i] = ref - name _ lower limit _ upper limit  --> $test_ref  $test_name  $lower_limit  $upper_limit
          $teststr = $visibleTests[$i];
          $teststr = explode(' _ ', $teststr);

          $test_refname = $teststr[0];
          $lower_limit = $teststr[1];
          $upper_limit = $teststr[2];

          $test_refname = explode(' - ', $test_refname);
          $test_ref = $test_refname[0];
          $test_ref_nospace = str_replace(' ', '_', $test_ref);
          $test_name = $test_refname[1];

        //Look in the database to collect all the information about the SOP of the test
          $reqtest = $bddspreadsheet->query('SELECT * FROM test_sop WHERE test_ref = "' .$test_ref. '"');
            $res = $reqtest->fetch();
            $method = $res['method'];
            $unit = $res['unit'];
            $reagents = $res['reagents'];
            $SOP = $res['SOP'];
            $limits = $res['limits'];
            $result = $res['result'];
            $calcul_description = $res['calcul_description'];
            $operandes = $res['operandes'];
            $alcoholimetric_assay = $res['alcoholimetric_assay'];
          $reqtest->closeCursor();

        //Preparation of the display of the calculation description (formula and operandes) --> $calc_descHTML
          //if there is a calcul description
            if($calcul_description <> NULL) {

              //The '_' are replaced by a space in the formula in order to be displayed
                $calcul_descriptionSTR = str_replace(' _ ', ' ', $calcul_description);

              //The operandes informations are split and put in an array ($operandesSTR = name _ desc _ type _ value   the presence of value is there if the type isn't 'unknown')	
                $operandesSTR = explode(' $ ', $operandes);

                $operandesHTML = '';

              //For each operandes in $operandesSTR
                for($j = 0; $j < count($operandesSTR); $j++) {

                  //The operandes information are split
                    $opeSTR = explode(' _ ', $operandesSTR[$j]);

                    $ope_name = $opeSTR[0];
                    $ope_desc = $opeSTR[1];
                    $ope_type = $opeSTR[2];

                    if(count($opeSTR) == 4) {
                      $ope_val = $opeSTR[3];
                    } else {
                      $ope_val = '';
                    }

                  //if the type is 'editable', the value display of the operande will be an input type="text", else, it will just be a div
                    $inputId = $test_ref_nospace . $ope_type;

                    if(strpos($ope_type, "editable") !== false) {
                      $ope_val = '<input type="text" id="' .$inputId. '" value="' .$ope_val. '" oninput="calculation(`Basic`, this);">';
                    } else if(strpos($ope_type, "notEditable") !== false) {
                      $ope_val = '<div id="' .$inputId. '">' .$ope_val. '</div>';
                    }

                  //The line created for an operande will be composed of 2 div : opDesc with the name and the description and opeVal with the value
                    $operandesHTML .= '<div>
                                          <div>' .$ope_name. ' = ' .$ope_desc. '</div>
                                          <div>' .$ope_val. '</div>
                                        </div>';
                }

              //Preparation of the calcul description pseudo table that will be displayed in the worksheet
                $calc_descHTML = '<div class="WStest--calculTable">
                                    <div class="pseudoHeader">Calcul :</div>
                                    <div>' .$calcul_descriptionSTR. '</div>'
                                    .$operandesHTML. 
                                  '</div>';
          //if there isn't a calcul description, $calc_descHTML is empty
            } else {
              $calc_descHTML = '<div class="WStest--calculTable"></div>';
            }

        //Preparation of the display of the limits --> $limitsHTML
          if($limits == 'yes') {
            $limitsHTML = '<div class="WStest--Limits">
                              <div class="pseudoHeader">Bornes :</div>
                              <div id="' .$test_ref_nospace. 'limits">' .$lower_limit. ' - ' .$upper_limit. ' ' .$unit. '</div>
                            </div>';
          } else {
            $limitsHTML = '';
          }

        //Preparation of the display of the SOP text --> $SOPHTML

          if($SOP <> NULL) {
            $SOP = explode('&lt;br/&gt;', $SOP);
            $SOPHTML = '<div class="WStest--SOPtext">' .$SOP[0];
            for($j = 1; $j < count($SOP); $j++) {
              $SOPHTML .= '<br/>' .$SOP[$j];
            }
            $SOPHTML .= '</div>';
          } else {
            $SOPHTML = '';
          }

        //Preparation of the display of the reagents used --> $reagentsHTML
          //If there is reagents
            if($reagents <> NULL) {
              $reagentsHTML = '<div class="WStest--reagent">';

              //creation of an array in order to split the different reagents
              //$reagentslist[$j] = name - type
                $reagentslist = explode(' $ ', $reagents);

              //for each reagent in reagentslist
                for($j = 0; $j < count($reagentslist); $j++) {

                  //the reagent name and type are split
                    $reagentstr = explode(' _ ', $reagentslist[$j]);
                    $reagent_name = $reagentstr[0];
                    $reagent_type = $reagentstr[1];
                    $reagent_status = '';

                  //if the reagent type is 'RM' (color will be white)
                    if($reagent_type == 'RM') {

                      //Look in the database to collect the material number and the status of the reagent --> $reagent_number and $reagant_status
                        $reqreagent = $bddmat->query('SELECT mat_number, status FROM raw_material WHERE mat_name = "' .$reagent_name. '"');
                          //look for a line where the status of the reagent concerned isn't 'Détruit' (destroyed) or 'Non ouvert' (Not opened yet)
                            while ($res = $reqreagent->fetch()) {
                              if($res['status'] <> 'Détruit' && $res['status'] <> 'Non ouvert') {
                                $reagent_status = $res['status'];
                              }
                              $reagent_number = $res['mat_number'];
                              $soltype = 'RM';
                            }
                        $reqreagent->closeCursor();

                  //if the reagent type isn't 'RM'
                    } else {
                      //the reagent name is split --> $reagent_name   $concentration   $solvent
                        $reagent_str = explode(' dans ', $reagent_name);
                        if(count($reagent_str) > 2) {
                          $solvent = $reagent_str[1]. ' dans ' .$reagent_str[2];
                        // } else if(count($reagent_str) < 2) {
                        //   $solvent = '-';
                        } else {
                          $solvent = $reagent_str[1];
                        }

                        $reagent_str = explode(' à ', $reagent_str[0]);
                        if(count($reagent_str) == 1) {
                          $reagent_name = $reagent_str;
                          $concentration = '-';
                        } else {
                          $reagent_name = $reagent_str[0];
                          $concentration = $reagent_str[1];
                        }

                    //switch between the different type of reagent (that is not a 'RM')
                    //look in the good database table to find the reagent number, a line with a status that is not 'détruit' or 'Non ouvert' (see above) and set $color
                      switch($reagent_type) {
                        case 'reagent':
                          $reqreagent = $bddmat->query('SELECT reag_number, status FROM reagent WHERE reag_name = "' .$reagent_name. '" AND concentration = "' .$concentration. '" AND solvent = "' .$solvent. '"');
                            While($res = $reqreagent->fetch()) {
                              if($res['status'] <> 'Détruit' && $res['status'] <> 'Non ouvert') {
                                $reagent_status = $res['status'];
                              }
                              $reagent_number = $res['reag_number'];
                              $soltype = 'Reagent';
                            }
                          $reqreagent->closeCursor();
                          break;
                        case 'indicator':
                          $reqreagent = $bddmat->query('SELECT ind_number, status FROM indicator WHERE ind_name = "' .$reagent_name. '" AND concentration = "' .$concentration. '" AND solvent = "' .$solvent. '"');
                            While($res = $reqreagent->fetch()) {
                              if($res['status'] <> 'Détruit' && $res['status'] <> 'Non ouvert') {
                                $reagent_status = $res['status'];
                              }
                              $reagent_number = $res['ind_number'];
                              $soltype = 'Indicator';
                            }
                          $reqreagent->closeCursor();
                          break;
                        case 'standard':
                          $reqreagent = $bddmat->query('SELECT std_number, status FROM standard WHERE std_name = "' .$reagent_name. '" AND concentration = "' .$concentration. '" AND solvent = "' .$solvent. '"');
                            While($res = $reqreagent->fetch()) {
                              if($res['status'] <> 'Détruit' && $res['status'] <> 'Non ouvert') {
                                $reagent_status = $res['status'];
                              }
                              $reagent_number = $res['std_number'];
                              $soltype = 'Standard';
                            }
                          $reqreagent->closeCursor();
                          break;
                        case 'scale':
                          $reqreagent = $bddmat->query('SELECT sc_number, status FROM scale WHERE sc_name = "' .$reagent_name. '" AND concentration = "' .$concentration. '" AND solvent = "' .$solvent. '"');
                            While($res = $reqreagent->fetch()) {
                              if($res['status'] <> 'Détruit' && $res['status'] <> 'Non ouvert') {
                                $reagent_status = $res['status'];
                              }
                              $reagent_number = $res['sc_number'];
                              $soltype = 'Scale';
                            }
                          $reqreagent->closeCursor();
                          break;
                      }
                    }

                  //if the reagent isn't declared 'OK', it will be put in an array ($reagentsAlert) in order to be displayed in a pop up
                    if($reagent_status != 'OK') {
                      $reagentsAlert[] = [$reagentstr[0], $reagent_number, $soltype, $reagent_status];
                    }

                  //a line in $reagentsHTML is created in order to fill the 'reagents zone' for this test in the worksheet
                    $reagentsHTML .= '<div class="solution">
                                      <div>' .$reagentstr[0].'</div>
                                      <div class="' .$soltype. '">' .$reagent_number. '</div>
                                    </div>';
                }

              $reagentsHTML .= '</div>';

          //if there isn't any reagent, $reagentsHTML is empty
            } else {
              $reagentsHTML = '';
            }

        //Preparation of the display of the calculation zone --> $unknownFromTestHTML and $calcTableHTML
          //if the test is an alcolholimetric assay
            if($alcoholimetric_assay <> NULL) {

              $unknownFromTestHTML = '';

              $calcul_descriptionSTR_EP = array('density',
                                                '=',
                                                '',
                                                $test_ref_nospace. 'unknownSample1',
                                                '/',
                                                $test_ref_nospace. 'editable2',
                                               );
              $calcul_descriptionSTR_USP = array('rel.density',
                                                 '=',
                                                 '',
                                                 $test_ref_nospace. 'unknownSample3',
                                                 '/',
                                                 $test_ref_nospace. 'editable2',
                                                 '/',
                                                 $test_ref_nospace. 'notEditable3',
                                                );

              $calcTableHTML_EP =  '<table class="calcTable">
                                      <thead>
                                        <tr>
                                          <th  class="tableFirstCol"></th>
                                          <th>W(20 °C)</th>
                                          <th>= (% v / v)</th>
                                        </tr>
                                      </thead>
                                      <tbody>';
              $calcTableHTML_USP = '<table class="calcTable">
                                      <thead>
                                        <tr>
                                          <th  class="tableFirstCol"></th>
                                          <th>W(15.6 °C)</th>
                                          <th>= (% v / v)</th>
                                        </tr>
                                      </thead>
                                      <tbody>';
              $calcTableHTML_A =   '<table class="calcTable">
                                      <thead>
                                        <tr>
                                          <th  class="tableFirstCol"></th>
                                          <th>W(15.6 °C)</th>
                                          <th>= (% v / v)</th>
                                        </tr>
                                      </thead>
                                      <tbody>';
              $calcTableHTML_B = 	 '<table class="calcTable">
                                      <thead>
                                        <tr>
                                          <th  class="tableFirstCol"></th>
                                          <th>W(20 °C)</th>
                                          <th>= (% v / v)</th>
                                        </tr>
                                      </thead>
                                      <tbody>';

              if($numberOfSample > 1) {

                $alphaUpper = range('A', 'Z');
                $alphaLower = range('a', 'z');


                for($k = 0; $k < $numberOfSample; $k++) {

                  $line_EP = $alphaUpper[$k];
                  $line_USP = $alphaLower[$k];

                  $calcTableHTML_EP .= '<tr>
                                          <td>Ech. ' .($k + 1). '</td>
                                          <td>
                                            <input type="text" id="'.$test_ref_nospace. 'unknownSample1' .$line_EP. '" oninput="calculation(`Assay_EP`, this);">
                                          </td>
                                          <td id="'.$test_ref_nospace. 'result' .$line_EP. '"></td>
                                        </tr>';
                  $calcTableHTML_USP .='<tr>
                                          <td>Ech. ' . ($k + 1) . '</td>
                                          <td>
                                            <input type="text" id="'.$test_ref_nospace. 'unknownSample3' .$line_USP. '" oninput="calculation(`Assay_USP`, this);">
                                          </td>
                                          <td id="'.$test_ref_nospace. 'result' .$line_USP. '"></td>
                                        </tr>';
                  $calcTableHTML_A .=  '<tr>
                                          <td>Ech. ' . ($k + 1) . '</td>
                                          <td>
                                            <input type="text" id="'.$test_ref_nospace. 'unknownSample3' .$line_USP. '" oninput="calculation(`Assay_all`, this);">
                                          </td>
                                          <td id="'.$test_ref_nospace. 'result' .$line_USP. '"></td>
                                        </tr>';
                  $calcTableHTML_B .=  '<tr>
                                          <td>Ech. ' . ($k + 1) . '</td>
                                          <td>
                                            <input type="text" id="'.$test_ref_nospace. 'unknownSample1' .$line_EP. '" oninput="calculation(`Assay_all`, this);">
                                          </td>
                                          <td id="'.$test_ref_nospace. 'result' .$line_EP. '"></td>
                                        </tr>';
                }
              } else {
                $calcTableHTML_EP .= 	 '<tr>
                                          <td>Ech.</td>
                                          <td>
                                            <input type="text" id="'.$test_ref_nospace. 'unknownSample1A" oninput="calculation(`Assay_EP`, this);">
                                          </td>
                                          <td id="'.$test_ref_nospace. 'resultA"></td>
                                        </tr>';
                $calcTableHTML_USP .=  '<tr>
                                          <td>Ech.</td>
                                          <td>
                                            <input type="text" id="'.$test_ref_nospace. 'unknownSample3a" oninput="calculation(`Assay_USP`, this);">
                                          </td>
                                          <td id="'.$test_ref_nospace. 'resulta"></td>
                                        </tr>';
                $calcTableHTML_A .= 	 '<tr>
                                          <td>Ech.</td>
                                          <td>
                                            <input type="text" id="'.$test_ref_nospace. 'unknownSample3a" oninput="calculation(`Assay_all`, this);">
                                          </td>
                                          <td id="'.$test_ref_nospace. 'resulta"></td>
                                        </tr>';
                $calcTableHTML_B .= 	 '<tr>
                                          <td>Ech.</td>
                                          <td>
                                            <input type="text" id="'.$test_ref_nospace. 'unknownSample1A" oninput="calculation(`Assay_all`, this);">
                                          </td>
                                          <td id="'.$test_ref_nospace. 'resultA"></td>
                                        </tr>';
              }

              $calcTableHTML_EP .= '</tbody></table>';
              $calcTableHTML_USP .= '</tbody></table>';
              $calcTableHTML_A .= '</tbody></table>';
              $calcTableHTML_B .= '</tbody></table>';

              switch ($alcoholimetric_assay) {
                case 'EP':
                  $calcTableHTML = $calcTableHTML_EP;
                  $calc_descHTML = '<div class="WStest--calculTable">
                                      <div class="pseudoHeader">Calcul :</div>
                                      <div>Density = W(20 °C) / V</div>
                                      <div>
                                        <div>W(20 °C) = Poids de l\'échantillon contenu dans le pycnomètre (en g)</div>
                                        <div></div>
                                      </div>
                                      <div>
                                        <div>V = Volume d\'échantillon contenu dans le pycnomètre (en mL)</div>
                                        <div>
                                          <input type="text" id="' .$test_ref_nospace. 'editable2" value="25.062" oninput="calculation(`Assay_EP`, this);">
                                        </div>
                                      </div>
                                    </div>';

                  $hiddenValue_EP = '';
                  for($k = 2; $k < count($calcul_descriptionSTR_EP); $k++) {
                    $hiddenValue_EP .= $calcul_descriptionSTR_EP[$k];
                  }
                  $hiddenCalculHTML = '<input type="hidden" id="' .$test_ref_nospace. 'hiddenCalcul_EP" value="' .$hiddenValue_EP. '"/>
                                       <input type="hidden" id="' .$test_ref_nospace. 'numberOfSample" value="' .$numberOfSample. '"/>';
                  break;
                case 'USP':
                  $calcTableHTML = $calcTableHTML_USP;
                  $calc_descHTML = '<div class="WStest--calculTable">
                                      <div class="pseudoHeader">Calcul :</div>
                                      <div>rel. density = W(15.6 °C) / V / d(eau 15.6 °C)</div>
                                      <div>
                                        <div>W(15.6 °C) = Poids de l\'échantillon contenu dans le pycnomètre (en g)</div>
                                        <div></div>
                                      </div>
                                      <div>
                                        <div>V = Volume d\'échantillon contenu dans le pycnomètre (en mL)</div>
                                        <div>
                                          <input type="text" id="' .$test_ref_nospace. 'editable2" value="25.062" oninput="calculation(`Assay_USP`, this);">
                                        </div>
                                      </div>
                                      <div>
                                        <div>d(eau 15.6 °C) = densité de l\'eau à 15 °C (en g / mL)</div>
                                        <div>
                                          <div id="' .$test_ref_nospace. 'notEditable3">0.999073</div>
                                        </div>
                                      </div>
                                    </div>';
                  $hiddenValue_USP = '';
                  for($k = 2; $k < count($calcul_descriptionSTR_USP); $k++) {
                    $hiddenValue_USP .= $calcul_descriptionSTR_USP[$k];
                  }
                  $hiddenCalculHTML = '<input type="hidden" id="' .$test_ref_nospace. 'hiddenCalcul_USP" value="' .$hiddenValue_USP. '"/>
                                       <input type="hidden" id="' .$test_ref_nospace. 'numberOfSample" value="' .$numberOfSample. '"/>';
                  break;
                case 'EP_USP':
                  $calcTableHTML = $calcTableHTML_A . $calcTableHTML_B;
                  $calc_descHTML = '<div class="WStest--calculTable">
                                      <div class="pseudoHeader">Calcul :</div>
                                      <div>rel. density = W(15.6 °C) / V / d(eau 15.6 °C)</div>
                                      <div>Density = W(20 °C) / V</div>
                                      <div>
                                        <div>W(15.6 °C) = Poids de l\'échantillon contenu dans le pycnomètre (en g)</div>
                                        <div></div>
                                      </div>
                                      <div>
                                        <div>W(20 °C) = Poids de l\'échantillon contenu dans le pycnomètre (en g)</div>
                                        <div></div>
                                      </div>
                                      <div>
                                        <div>V = Volume d\'échantillon contenu dans le pycnomètre (en mL)</div>
                                        <div>
                                          <input type="text" id="' .$test_ref_nospace. 'editable2" value="25.062" oninput="calculation(`Assay_all`, this);">
                                        </div>
                                      </div>
                                      <div>
                                        <div>d(eau 15.6 °C) = densité de l\'eau à 15 °C (en g / mL)</div>
                                        <div>
                                          <div id="' .$test_ref_nospace. 'notEditable3">0.999073</div>
                                        </div>
                                      </div>
                                    </div>';
                  $hiddenValue_EP = '';
                  $hiddenValue_USP = '';
                  for($k = 2; $k < count($calcul_descriptionSTR_EP); $k++) {
                    $hiddenValue_EP .= $calcul_descriptionSTR_EP[$k];
                  }
                  for($k = 2; $k < count($calcul_descriptionSTR_USP); $k++) {
                    $hiddenValue_USP .= $calcul_descriptionSTR_USP[$k];
                  }
                  $hiddenCalculHTML = '<input type="hidden" id="' .$test_ref_nospace. 'hiddenCalcul_USP" value="' .$hiddenValue_USP. '"/>
                                       <input type="hidden" id="' .$test_ref_nospace. 'hiddenCalcul_EP" value="' .$hiddenValue_EP. '"/>
                                       <input type="hidden" id="' .$test_ref_nospace. 'numberOfSample" value="' .$numberOfSample. '"/>';
                  break;
              }
            }

          //if there is a calcul description
            if($calcul_description <> NULL && $alcoholimetric_assay == NULL) {

              //creation of an array in order to split the operandes and the calculation signs in the calcul description --> $calcul_descriptionSTR
                $calcul_descriptionSTR = explode(' _ ', $calcul_description);

              //creation of an array in order to split the differents operandes for this test
                $operandesSTR = explode(' $ ', $operandes);

              $unknownFromTestHTML = '';
              $tableHeader = '';
              $tableBody = '';
              $calcTableHTML = '<table class="calcTable">
                                  <thead>
                                    <tr>
                                      <th  class="tableFirstCol"></th>';
              
              //if the number of sample is not 1, an extra line has to be added for each additionnal sample in the array $tableLine
                if($numberOfSample > 1) {
                  for($k = 0; $k < $numberOfSample; $k++) {
                    $tableLine[$k] = '<td>Ech. ' .($k + 1). '</td>';
                  }
                } else {
                  $tableLine[0] = '<td>Ech.</td>';
                }

              //for each operande in $operandesSTR[]
                for($j = 0; $j < count($operandesSTR); $j++) {
                  //the operande is split in order to split the name, desc, and type of the operande
                    $opeSTR = explode(' _ ', $operandesSTR[$j]);

                    $ope_name = $opeSTR[0];
                    $ope_desc = $opeSTR[1];
                    $ope_type = $opeSTR[2];
                    if(count($opeSTR) == 4) {
                      $ope_val = $opeSTR[3];
                    } else {
                      $ope_val = '';
                    }

                  //replacement in the calculation formula of the operande names with the concatenation of the test reference (ex: 121_défaut) and the operande type (ex: unknownTest1)
                    $calcul_descriptionSTR[(2*$j) + 3] = $test_ref_nospace . $ope_type;

                  //if the operande type is 'unknownTest', creation of a line in $unknownFromTestHTML
                  //input with an 'oniput' rule that allow the calculation to be done when we put something in the input --> calculation(this)
                    if(strpos($ope_type, "unknownTest") !== false) {
                      $unknownFromTestHTML .=  '<div class="WStest--Unknown">
                                                  <label for="' . $test_ref_nospace . $ope_type . '">' . $ope_name . ' = </label>
                                                  <input type="text" id="' . $test_ref_nospace . $ope_type . '" oninput="calculation(`Basic`, this);">
                                                </div>';
                    }

                  //if the operande type is 'unknownSample'
                    if(strpos($ope_type, "unknownSample") !== false) {

                      //creation of a column header in the calculation table with the name of the operande on it
                        $tableHeader .= '<th>' . $ope_name . '</th>';

                        $alphas =range('A', 'Z');

                      //for each sample
                        for($k = 0; $k < $numberOfSample; $k++) {

                          // link between the numeric order and alphabetic order --> 1 = A, 2 = B, ...

                            $line = $alphas[$k];

                          //creation of the other frames in the operande column ($tableLine is an array with a number of entries that match with the number of samples.)
                          //input with an 'oniput' rule that allow the calculation to be done when we put something in the input --> calculation(this)
                            $tableLine[$k] .=  '<td>
                                                  <input type="text" id="'. $test_ref_nospace . $ope_type . $line . '" oninput="calculation(`Basic`, this);">
                                                </td>';
                        }
                    }
                }

                $alphas =range('A', 'Z');
              
              //for each sample line (that correspond an entry in the array $tableLine)
                for($k = 0; $k < count($tableLine); $k++) {

                  // link between the numeric order and alphabetic order --> 1 = A, 2 = B, ...

                    $line = $alphas[$k];

                  //insertion in $tableBody of a table line with the input(s) with the operande(s) and a frame for the result of the calculation for this line
                    $tableBody .= '<tr>' . $tableLine[$k] . '<td id="'.$test_ref_nospace. 'result' .$line. '"></td></tr>';
                }
              
              //check if a unit is declared
                if($unit == '') {
                  $unitSTR = '';
                } else {
                  $unitSTR = '(' .$unit. ')';
                }

              //all the data that concern the creation of the calculation table are compiled in $calcTableHTML (with addition of a frame with ' = ' + units)
                $calcTableHTML .= $tableHeader. '<th> = ' .$unitSTR. '</th></tr></thead><tbody>' .$tableBody. '</tbody></table>';

              //creation of $hiddenValue that keep in memory the calcul_description
                $hiddenValue = '';
                for($k = 2; $k < count($calcul_descriptionSTR); $k++) {
                  $hiddenValue .= $calcul_descriptionSTR[$k];
                }

              //creation of $hiddenCalculHTML that allow us the keep the number of samples and the calculation formula
                $hiddenCalculHTML = '<input type="hidden" id="' .$test_ref_nospace. 'hiddenCalcul" value="' . $hiddenValue . '"/>
                                     <input type="hidden" id="' .$test_ref_nospace. 'numberOfSample" value="' . $numberOfSample . '"/>';

          //if there isn't a calcul description
            } else if($calcul_description == NULL && $alcoholimetric_assay == NULL) {
              $hiddenCalculHTML = '';
            }

        //Preparation of the display of the result line --> $resultHTML
          //if the result type isn't declared as the default type, the sentence in $result is passed in the div, else, the défault sentence is passed
            if($result <> 'default') {
              $resultHTML = '<div id="' .$test_ref_nospace. 'resultSentence" class="ResultOK">' .$result. '</div>';
            } else {
              $resultHTML = "<div id='" .$test_ref_nospace. "resultSentence' class='ResultnotOK'>Le calcul ne peut s'effectuer car des données sont manquantes.</div>";
            }

        //concatenation of all the data in $bodyHTML
          $bodyHTML .= '<div class="WStest">';

          //Header
            $testHTML =  '<div class="WStest--header pseudoHeader">
                            <div class="testName">' .$test_name. '</div>
                            <div class="method">' .$method. '</div>
                          </div>';

          //calculation description with operandes and limits
            if($limitsHTML <>'') {
              $testHTML .= '<div class="WStest--numbers">' . $calc_descHTML . $limitsHTML . '</div>';
            }

          //SOP + reagents
            $testHTML .= '<div class="WStest--SOP">' . $SOPHTML . $reagentsHTML . '</div>';

          //calculation form
            if($calcul_description <> NULL) {
              $testHTML .= '<div class="WStest--calcTable">';
              if($unknownFromTestHTML <> '') {
                $testHTML .= '<div>' . $unknownFromTestHTML . '</div>';
              }
              $testHTML .= $calcTableHTML . '</div>';
            }

          //result line
            $testHTML .= '<div class="WStest--Result">' . $resultHTML . '</div>';

          $testHTML .= $hiddenCalculHTML;

          $bodyHTML .= $testHTML . '</div>';
      }

    ?>
    <!DOCTYPE HTML>
    <html>
      <head>
        <meta charset="utf-8">
        <title>Gestionnaire Biosolve</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
        <link rel="stylesheet" href="../assets/css/prefixed/main.css" media="screen"/>
        <link rel="stylesheet" href="../assets/css/printworksheet.css" media="print"/>
        <noscript><link rel="stylesheet" href="../assets/css/noscript.css" /></noscript>
      </head>
      <body>
        <main>
          <?php
          if($reagentsAlert) {
            $reagentsAlertList = '';
            for($i = 0; $i < count($reagentsAlert); $i++) {
              $reagentsAlertList .=  '<div>
                                        <span class="' . $reagentsAlert[$i][2] . '">' . $reagentsAlert[$i][1] . '</span>
                                        <span> - ' . $reagentsAlert[$i][0] . ' - </span>
                                        <span class="reagentAlert">' . $reagentsAlert[$i][3] . '</span>
                                      </div>';
            }
            ?>
            <div id="oModal" class="oModal">
              <div>
                <header>
                  <h2 id="headerText">ATTENTION</h2>
                  <a id ="closeBtn" title="Fermer la fenêtre" onclick="closeModal()">X</a>
                </header>
                <section>
                <p id="popupText">Ces réactifs sont expirés ou vont l'être prochainement :</p>
                <p><?php echo($reagentsAlertList); ?></p>
                </section>
                <footer>
                  <div id="footerText">
                    <button type="button" onclick="closeModal()" class="btn">Continuer</button>
                  </div>
                </footer>
              </div>
            </div>
            <?php
          }
          ?>

          <?php include('../nav.php'); ?>

          <div class="content">
            <div id="page-wrapper">
              
              <?php include('../header.php'); ?>

              <section class="section">
                <div class='section--text'>
                  <div class="WSHeader">
                    <div id="matheader" class="matHeader">
                      <div id="Biosolve">
                        <img src="../images/logo.gif" alt="Biosolve - Imprimer" height="95" onclick="printPDF();">
                      </div>
                      <h2>FEUILLE DE CALCUL</h2>
                      <div id="versionInfo" class="versionInfo">
                        <span id="QA_name"><?php echo($QA_name); ?></span>
                        <span id="version_number">Version <?php echo($version); ?></span>
                        <span id="version_date"><?php echo($version_date); ?></span>
                      </div>
                    </div>
                    <div id="matInfo" class="matInfo">
                      <div>
                        <span class="matInfo--ref">Référence catalogue : <?php echo($ref_cat); ?></span>
                        <span id="matName" class="matInfo--name">Nom du produit / matériaux : <?php echo($mat_name); ?></span>
                      </div>
                      <div>
                        <span id="lotNumber" class="matInfo--lot">Numéro de lot : <?php echo($lot_number); ?></span>
                        <span class="matInfo--density" id="density">Densité : <?php echo($density); ?> g/mL</span>
                      </div>
                    </div>
                  </div>
                  <div id="WSBody" class="WSBody">
                    <?php echo($bodyHTML); ?>
                  </div>
                  <div id="WSFooter" class="WSFooter">
                    <span>Date : <?php echo($today); ?></span>
                    <span>Analysé par : <?php echo($operator); ?></span>
                    <span>Signature : <?php echo($sign); ?></span>
                  </div>
                </div>
              </section>
            </div>
          </div>
        </main>
      </body>
    </html>
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
  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/nav.js"></script>
  <script src="../assets/js/darkmode.js"></script>
  <script src="../assets/js/javascript functions/worksheet.js"></script>