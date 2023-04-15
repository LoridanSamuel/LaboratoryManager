<?php

header("Content-Type: application/json");

session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddspreadsheet = new PDO('mysql:host=127.0.0.1;dbname=spreadsheet;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddmat = new PDO('mysql:host=127.0.0.1;dbname=material;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

include_once('../Connexion/cookieconnect.php');

if(isset($_GET['test_id'])){
  $test_id = htmlspecialchars($_GET['test_id']);

  $query = $bddspreadsheet->query('SELECT * FROM test_sop WHERE test_id=' . $test_id);
  $data = $query->fetchAll(PDO::FETCH_ASSOC);
  $json = json_encode($data);

  echo $json;
}

if(isset($_GET['typeOfSolution'])){
  $typeOfSolution = htmlspecialchars($_GET['typeOfSolution']);

  if($typeOfSolution == 'RM') {
    $query = $bddmat->query("SELECT DISTINCT mat_number, mat_name FROM raw_material");
    $data = $query->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode($data);

    echo $json;
  } else if ($typeOfSolution == 'scale'){
    $query = $bddmat->query('SELECT sol_number, name, solvent FROM sop WHERE type = "scale"');
    $data = $query->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode($data);

    echo $json;
  } else {
    $query = $bddmat->query('SELECT DISTINCT sol_number, name, concentration, solvent FROM sop WHERE type="' . $typeOfSolution . '"');
    $data = $query->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode($data);

    echo $json;
  }
}

if(isset($_GET['density_calculated_EP'])){
  $density_calculated_EP = htmlspecialchars($_GET['density_calculated_EP']);

  $query = $bddspreadsheet->query('SELECT * FROM alcoholimetric_tables_ep WHERE density_20 <= "' . $density_calculated_EP . '"');
  $match = $query->fetch(PDO::FETCH_ASSOC);

  $data[0] = $match['density_20'];
  $data[1] = $match['percent_v'];

  $query2 = $bddspreadsheet->query('SELECT * FROM alcoholimetric_tables_ep WHERE density_20 >= "' . $density_calculated_EP . '" ORDER BY density_20');
  $matching = $query2->fetch(PDO::FETCH_ASSOC);

  $data[2] = $matching['density_20'];
  $data[3] = $matching['percent_v'];

  $data[4] = $density_calculated_EP;

  $json = json_encode($data);

  echo $json;
}

if(isset($_GET['density_calculated_USP'])){
  $density_calculated_USP = htmlspecialchars($_GET['density_calculated_USP']);

  $query = $bddspreadsheet->query('SELECT * FROM alcoholimetric_tables_usp WHERE specific_gravity_15 <= "' . $density_calculated_USP . '"');
  $match = $query->fetch(PDO::FETCH_ASSOC);

  $data[0] = $match['specific_gravity_15'];
  $data[1] = $match['percent_v'];

  $query2 = $bddspreadsheet->query('SELECT * FROM alcoholimetric_tables_usp WHERE specific_gravity_15 >= "' . $density_calculated_USP . '" ORDER BY specific_gravity_15');
  $matching = $query2->fetch(PDO::FETCH_ASSOC);

  $data[2] = $matching['specific_gravity_15'];
  $data[3] = $matching['percent_v'];

  $data[4] = $density_calculated_USP;

  $json = json_encode($data);

  echo $json;
}
?>