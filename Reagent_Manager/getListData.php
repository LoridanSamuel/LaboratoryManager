<?php

header("Content-Type: application/json");

session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddmat = new PDO('mysql:host=127.0.0.1;dbname=material;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

include_once('../Connexion/cookieconnect.php');

if(isset($_GET['checkName'])){
  $typeOfMaterial = htmlspecialchars($_GET['typeOfMaterial']);
  $name = htmlspecialchars($_GET['checkName']);
  $concentration = htmlspecialchars($_GET['concentration']);
  $solvent = htmlspecialchars($_GET['solvent']);
  if($typeOfMaterial == "scale") {
    $query = $bddmat->query('SELECT * FROM sop WHERE name = "' . $name . '" 
                                                 AND solvent = "' . $solvent . '"');
    $data = $query->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode($data);
    echo $json;
  } else {
    $query = $bddmat->query('SELECT * FROM sop WHERE name = "' . $name . '" 
                                                 AND concentration = "' . $concentration . '" 
                                                 AND solvent = "' . $solvent . '"');
    $data = $query->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode($data);
    echo $json;
  }
}

if(isset($_GET['checkSol'])){
  $typeOfMaterial = htmlspecialchars($_GET['typeOfMaterial']);
  $name = htmlspecialchars($_GET['checkSol']);
  $concentration = htmlspecialchars($_GET['concentration']);
  $solvent = htmlspecialchars($_GET['solvent']);
  $preparation_date = htmlspecialchars($_GET['prepDate']);
  $maker = htmlspecialchars($_GET['maker']);

  switch ($typeOfMaterial) {
    case "reagent" :
      $type = "reag_name";
      break;
    case "indicator" :
      $type = "ind_name";
      break;
    case "standard" :
      $type = "std_name";
      break;
    case "scale" :
      $type = "sc_name";
      break;
  }

  if($typeOfMaterial == "scale") {
    $query = $bddmat->query('SELECT * FROM scale WHERE sc_name = "' . $name . '" 
                                                    AND solvent = "' . $solvent . '"
                                                    AND preparation_date = "' . $preparation_date . '"
                                                    AND maker = "' . $maker . '"
                                                    AND status = "OK"');
    $data = $query->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode($data);
    echo $json;
  } else {
    $query = $bddmat->query('SELECT * FROM ' . $typeOfMaterial . ' WHERE ' . $type . ' = "' . $name . '" 
                                                                      AND concentration = "' . $concentration . '" 
                                                                      AND solvent = "' . $solvent . '"
                                                                      AND preparation_date = "' . $preparation_date . '"
                                                                      AND maker = "' . $maker . '"
                                                                      AND status = "OK"');
    $data = $query->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode($data);
    echo $json;
  }
}

if(isset($_GET['name_Open'])){
  $name = htmlspecialchars($_GET['name_Open']);
  $query = $bddmat->query('SELECT * FROM raw_material WHERE mat_name = "' . $name . '" AND status <> "Détruit" AND opening_date IS NULL');
  $data = $query->fetchAll(PDO::FETCH_ASSOC);
  $json = json_encode($data);
  echo $json;
}

if(isset($_GET['name_Delay'])){
  $name = htmlspecialchars($_GET['name_Delay']);
  $query = $bddmat->query('SELECT * FROM raw_material WHERE mat_name = "' . $name . '" AND destruction_date <=> NULL');
  $data = $query->fetchAll(PDO::FETCH_ASSOC);
  $json = json_encode($data);
  echo $json;
}

if(isset($_GET['typeOfMaterial_Destruction'])){
  $typeOfMaterial = htmlspecialchars($_GET['typeOfMaterial_Destruction']);

  switch ($typeOfMaterial) {
    case 'raw_material':
      $query = $bddmat->query('SELECT DISTINCT mat_name FROM raw_material WHERE status <> "Détruit" ORDER BY mat_name');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
    case 'reagent':
      $query = $bddmat->query('SELECT DISTINCT reag_name, concentration, solvent FROM reagent WHERE status <> "Détruit" ORDER BY reag_name');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
    case 'indicator':
      $query = $bddmat->query('SELECT DISTINCT ind_name, concentration, solvent FROM indicator WHERE status <> "Détruit" ORDER BY ind_name');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
    case 'standard':
      $query = $bddmat->query('SELECT DISTINCT std_name, concentration, solvent FROM standard WHERE status <> "Détruit" ORDER BY std_name');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
    case 'scale':
      $query = $bddmat->query('SELECT DISTINCT sc_name, solvent FROM scale WHERE status <> "Détruit" ORDER BY sc_name');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
  }
}

if(isset($_GET['typeOfMaterial_SOP'])){
  $typeOfMaterial = htmlspecialchars($_GET['typeOfMaterial_SOP']);

  $data = [];

  $query = $bddmat->query('SELECT DISTINCT name FROM sop WHERE type = "' . $typeOfMaterial . '" ORDER BY name');
  $data_name = $query->fetchAll(PDO::FETCH_ASSOC);
    $data[0] = $data_name;
  $query->closeCursor();

  $query = $bddmat->query('SELECT DISTINCT concentration FROM sop WHERE type = "' . $typeOfMaterial . '" ORDER BY concentration');
  $data_concentration = $query->fetchAll(PDO::FETCH_ASSOC);
    $data[1] = $data_concentration;
  $query->closeCursor();

  $query = $bddmat->query('SELECT DISTINCT solvent FROM sop WHERE type = "' . $typeOfMaterial . '" ORDER BY solvent');
  $data_solvent = $query->fetchAll(PDO::FETCH_ASSOC);
    $data[2] = $data_solvent;
  $query->closeCursor();

  $query = $bddmat->query('SELECT DISTINCT packaging FROM sop WHERE type = "' . $typeOfMaterial . '" ORDER BY packaging');
  $data_packaging = $query->fetchAll(PDO::FETCH_ASSOC);
    $data[3] = $data_packaging;
  $query->closeCursor();

  $query = $bddmat->query('SELECT DISTINCT lifetime FROM sop WHERE type = "' . $typeOfMaterial . '" ORDER BY lifetime');
  $data_lifetime = $query->fetchAll(PDO::FETCH_ASSOC);
    $data[4] = $data_lifetime;
  $query->closeCursor();

  $json = json_encode($data);
  echo $json;	
}

if(isset($_GET['typeOfMaterial_Prepare'])){
  $typeOfMaterial = htmlspecialchars($_GET['typeOfMaterial_Prepare']);
  $query = $bddmat->query('SELECT DISTINCT name, concentration, solvent FROM sop WHERE type = "' . $typeOfMaterial . '" ORDER BY name');
  $data = $query->fetchAll(PDO::FETCH_ASSOC);
  $json = json_encode($data);
  echo $json;	
}

if(isset($_GET['typeOfMaterial_Label'])){
  $typeOfMaterial = htmlspecialchars($_GET['typeOfMaterial_Label']);

  switch ($typeOfMaterial) {
    case 'raw_material':
      $query = $bddmat->query('SELECT DISTINCT mat_name FROM raw_material WHERE status <> "Détruit" ORDER BY mat_name');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
    case 'reagent':
      $query = $bddmat->query('SELECT DISTINCT reag_name, concentration, solvent FROM reagent WHERE status <> "Détruit" ORDER BY reag_name');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
    case 'indicator':
      $query = $bddmat->query('SELECT DISTINCT ind_name, concentration, solvent FROM indicator WHERE status <> "Détruit" ORDER BY ind_name');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
    case 'standard':
      $query = $bddmat->query('SELECT DISTINCT std_name, concentration, solvent FROM standard WHERE status <> "Détruit" ORDER BY std_name');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
    case 'scale':
      $query = $bddmat->query('SELECT DISTINCT sc_name, solvent FROM scale WHERE status <> "Détruit" ORDER BY sc_name');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
  }
}

if(isset($_GET['typeOfMat_Destruction'])){
  $typeOfMaterial = htmlspecialchars($_GET['typeOfMat_Destruction']);
  $name = htmlspecialchars($_GET['name']);
  if(isset($_GET['concentration'])){$concentration = htmlspecialchars($_GET['concentration']);}
  if(isset($_GET['solvent'])){$solvent = htmlspecialchars($_GET['solvent']);}

  switch ($typeOfMaterial) {
    case 'raw_material':
      $query = $bddmat->query('SELECT id, lot_number, seller, reference, reception_date, opening_date FROM raw_material WHERE mat_name = "' . $name . '" AND opening_date IS NOT null AND destruction_date IS null');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
    case 'reagent':
      $query = $bddmat->query('SELECT id, reag_name, concentration, solvent, packaging, preparation_date, maker FROM reagent WHERE reag_name = "' . $name . '" AND concentration = "' . $concentration . '" AND solvent = "' . $solvent . '" AND destruction_date IS null');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
    case 'indicator':
      $query = $bddmat->query('SELECT id, ind_name, concentration, solvent, packaging, preparation_date, maker FROM indicator WHERE ind_name = "' . $name . '" AND concentration = "' . $concentration . '" AND solvent = "' . $solvent . '" AND destruction_date IS null');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
    case 'standard':
      $query = $bddmat->query('SELECT id, std_name, concentration, solvent, packaging, preparation_date, maker FROM standard WHERE std_name = "' . $name . '" AND concentration = "' . $concentration . '" AND solvent = "' . $solvent . '" AND destruction_date IS null');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
    case 'scale':
      $query = $bddmat->query('SELECT id, sc_name, solvent, packaging, preparation_date, maker FROM scale WHERE sc_name = "' . $name . '" AND solvent = "' . $solvent . '" AND destruction_date IS null');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
  }
}

if(isset($_GET['typeOfMat_Prepare'])){
  $typeOfMaterial = htmlspecialchars($_GET['typeOfMat_Prepare']);
  $name = htmlspecialchars($_GET['name']);
  if(isset($_GET['concentration'])){$concentration = htmlspecialchars($_GET['concentration']);}
  if(isset($_GET['solvent'])){$solvent = htmlspecialchars($_GET['solvent']);}

  switch ($typeOfMaterial) {
    case 'scale':
      $query = $bddmat->query('SELECT * FROM sop WHERE name = "' . $name . '" AND  solvent = "' . $solvent . '"');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      $json = str_replace("\\r\\n", "<br/>", $json);
      echo $json;
      break;
    default:
      $query = $bddmat->query('SELECT * FROM sop WHERE name = "' . $name . '" AND concentration = "' . $concentration . '" AND solvent = "' . $solvent . '"');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      $json = str_replace("\\r\\n", "<br/>", $json);
      echo $json;
      break;
  }
}

if(isset($_GET['typeOfMat_Label'])){
  $typeOfMaterial = htmlspecialchars($_GET['typeOfMat_Label']);
  $name = htmlspecialchars($_GET['name']);
  if(isset($_GET['concentration'])){$concentration = htmlspecialchars($_GET['concentration']);}
  if(isset($_GET['solvent'])){$solvent = htmlspecialchars($_GET['solvent']);}

  switch ($typeOfMaterial) {
    case 'raw_material':
      $query = $bddmat->query('SELECT id, lot_number, seller, reference, reception_date, opening_date FROM raw_material WHERE mat_name = "' . $name . '" AND destruction_date IS null');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
    case 'reagent':
      $query = $bddmat->query('SELECT id, reag_name, concentration, solvent, packaging, preparation_date, maker FROM reagent WHERE reag_name = "' . $name . '" AND concentration = "' . $concentration . '" AND solvent = "' . $solvent . '" AND destruction_date IS null');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
    case 'indicator':
      $query = $bddmat->query('SELECT id, ind_name, concentration, solvent, packaging, preparation_date, maker FROM indicator WHERE ind_name = "' . $name . '" AND concentration = "' . $concentration . '" AND solvent = "' . $solvent . '" AND destruction_date IS null');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
    case 'standard':
      $query = $bddmat->query('SELECT id, std_name, concentration, solvent, packaging, preparation_date, maker FROM standard WHERE std_name = "' . $name . '" AND concentration = "' . $concentration . '" AND solvent = "' . $solvent . '" AND destruction_date IS null');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
    case 'scale':
      $query = $bddmat->query('SELECT id, sc_name, solvent, packaging, preparation_date, maker FROM scale WHERE sc_name = "' . $name . '" AND solvent = "' . $solvent . '" AND destruction_date IS null');
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($data);
      echo $json;
      break;
  }
}

if(isset($_GET['RM_compound'])){
  $RM_compound = htmlspecialchars($_GET['RM_compound']);

  $query = $bddmat->query('SELECT lot_number FROM raw_material WHERE mat_name = "' . $RM_compound . '" AND  opening_date IS NOT NULL AND destruction_date IS NULL');
  $data = $query->fetchAll(PDO::FETCH_ASSOC);
  $json = json_encode($data);
  echo $json;
}

if(isset($_GET['Sc_compound'])){
  $name = htmlspecialchars($_GET['Sc_compound']);
  $solvent = htmlspecialchars($_GET['solvent']);

  $query = $bddmat->query('SELECT COUNT(*) AS nb FROM scale WHERE sc_name = "' . $name . '" AND  solvent = "' . $solvent . '" AND (status LIKE "OK" OR status LIKE "Expire dans %" ) AND destruction_date IS NULL');
  $data = $query->fetch();
  $nb = $data['nb'];
  $json = json_encode($nb);
  echo $json;
}

if(isset($_GET['Sol_compound'])){
  $name = htmlspecialchars($_GET['Sol_compound']);
  $concentration = htmlspecialchars($_GET['concentration']);
  $solvent = htmlspecialchars($_GET['solvent']);
  $type = htmlspecialchars($_GET['type']);

  switch($type) {
    case 'reagent':
      $table = "reag_name";
      break;
    case 'indicator':
      $table = "ind_name";
      break;
    case 'standard':
      $table = "std_name";
      break;
  }

  $query = $bddmat->query('SELECT COUNT(*) AS nb FROM ' . $type . ' WHERE ' . $table . ' = "' . $name . '" AND  concentration = "' . $concentration . '" AND solvent = "' . $solvent . '" AND (status LIKE "OK" OR status LIKE "Expire dans %" ) AND destruction_date IS NULL');
  $data = $query->fetch();
  $nb = $data['nb'];
  $json = json_encode($nb);
  echo $json;
}

?>