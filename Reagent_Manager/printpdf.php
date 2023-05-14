<?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bddmat = new PDO('mysql:host=127.0.0.1;dbname=material;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

include_once('../Connexion/cookieconnect.php');

if(isset($_SESSION['id'],$_POST['formPrintFinal']) AND $_SESSION['id'] > 0) {
  $typeOfMaterial = htmlspecialchars($_POST['typeOfMaterial']);
  $id = htmlspecialchars($_POST['id']);
  $labelPosition = htmlspecialchars($_POST['labelposition']);
  $matNumber = "";
  $colorR = "";
  $colorV = "";
  $colorB = "";
  $name = "";
  $complement = "";
  $firstLine = "";
  $secondLine = "";
  $thirdLine = "";
  $fourthLine = "";

  if (strlen($labelPosition == 4)) {
    list($size, $col, $row, $secrow) = str_split($labelPosition);
    $row .= $secrow;
  } else {
    list($size, $col, $row) = str_split($labelPosition);
  }

  if ($size == 'S') {
    $ratio = 2;
    $fontSize = 1.5;
  } else {
    $ratio = 1;
    $fontSize = 1;
  }

  switch ($col) {
    case "A":
      $margingLeft = "8";
      break;
    case "B":
      $margingLeft = "74";
      break;
    case "C":
      $margingLeft = "140";
      break;
  }

  $margingTop = "8" + (($row - 1) * ("46.5" / $ratio));

  switch ($typeOfMaterial) {
    case "raw_material" :
      $request = $bddmat->prepare('SELECT *
                                   FROM raw_material
                                   WHERE id = :matId');
      $request->bindValue(':matId', $id, PDO::PARAM_INT);
      $request->execute();
      while ($result = $request->fetch()) {
        $matNumber = $result['mat_number'];
        $colorR = 0;
        $colorV = 0;
        $colorB = 0;
        $name = $result['mat_name'];
        $firstLine = "Reçu le " .date("d/m/Y", strtotime($result['reception_date']));
        $secondLine = "Ouvert le " .date("d/m/Y", strtotime($result['opening_date']));
        if ($result['extended_date'] == null) {
          $thirdLine = "Périme le " .date("d/m/Y", strtotime($result['perempting_date']));;
        } else {
          $thirdLine = "Retesté le " .date("d/m/Y", strtotime($result['retesting_date']));
          $fourthLine = "Périme le " .date("d/m/Y", strtotime($result['extended_date']));
        };
      };
      $request->closeCursor();
      break;
    case "reagent" :
      $request = $bddmat->prepare('SELECT *
                                   FROM reagent
                                   WHERE id = :matId');
      $request->bindValue(':matId', $id, PDO::PARAM_INT);
      $request->execute();
      while ($result = $request->fetch()) {
        $matNumber = $result['reag_number'];
        $colorR = 255;
        $colorV = 0;
        $colorB = 0;
        $name = $result['reag_name'];
        $complement = "à " .$result['concentration']. " dans " .$result['solvent'];
        $firstLine = "Préparé le " .date("d/m/Y", strtotime($result['preparation_date'])). " par " .$result['maker'];
        if ($result['verification_date'] != null) {
          $secondLine = "Vérifié le " .date("d/m/Y", strtotime($result['verification_date'])). " par " .$result['checker'];
        } else {
          $secondLine = "PAS ENCORE VERIFIE";
        }
        $thirdLine = "Périme le " .date("d/m/Y", strtotime($result['expiration_date']));
      };
      $request->closeCursor();
      break;
    case "indicator" :
      $request = $bddmat->prepare('SELECT *
                                   FROM indicator
                                   WHERE id = :matID');
      $request->bindValue(':matId', $id, PDO::PARAM_INT);
      $request->execute();
      while ($result = $request->fetch()) {
        $matNumber = $result['ind_number'];
        $colorR = 22;
        $colorV = 184;
        $colorB = 78;
        $name = $result['ind_name'];
        $complement = "à " .$result['concentration']. " dans " .$result['solvent'];
        $firstLine = "Préparé le " .date("d/m/Y", strtotime($result['preparation_date'])). " par " .$result['maker'];
        if ($result['verification_date'] != null) {
          $secondLine = "Vérifié le " .date("d/m/Y", strtotime($result['verification_date'])). " par " .$result['checker'];
        } else {
          $secondLine = "PAS ENCORE VERIFIE";
        }
        $thirdLine = "Périme le " .date("d/m/Y", strtotime($result['expiration_date']));
      };
      $request->closeCursor();
      break;
    case "standard" :
      $request = $bddmat->prepare('SELECT *
                                   FROM standard
                                   WHERE id = :matId');
      $request->bindValue(':matId', $id, PDO::PARAM_INT);
      $request->execute();
      while ($result = $request->fetch()) {
        $matNumber = $result['std_number'];
        $colorR = 38;
        $colorV = 196;
        $colorB = 236;
        $name = $result['std_name'];
        $complement = "à " .$result['concentration']. " dans " .$result['solvent'];
        $firstLine = "Préparé le " .date("d/m/Y", strtotime($result['preparation_date'])). " par " .$result['maker'];
        if ($result['verification_date'] != null) {
          $secondLine = "Vérifié le " .date("d/m/Y", strtotime($result['verification_date'])). " par " .$result['checker'];
        } else {
          $secondLine = "PAS ENCORE VERIFIE";
        }
        $thirdLine = "Périme le " .date("d/m/Y", strtotime($result['expiration_date']));
      };
      $request->closeCursor();
      break;
    case "scale" :
      $request = $bddmat->prepare('SELECT *
                                   FROM scale
                                   WHERE id = :matId');
      $request->bindValue(':matId', $id, PDO::PARAM_INT);
      $request->execute();
      while ($result = $request->fetch()) {
        $matNumber = $result['sc_number'];
        $colorR = 255;
        $colorV = 128;
        $colorB = 0;
        $name = $result['sc_name'];
        $complement = "dans " .$result['solvent'];
        $firstLine = "Préparé le " .date("d/m/Y", strtotime($result['preparation_date'])). " par " .$result['maker'];
        if ($result['verification_date'] != null) {
          $secondLine = "Vérifié le " .date("d/m/Y", strtotime($result['verification_date'])). " par " .$result['checker'];
        } else {
          $secondLine = "PAS ENCORE VERIFIE";
        }
        $thirdLine = "Périme le " .date("d/m/Y", strtotime($result['expiration_date']));
      };
      $request->closeCursor();
      break;
  };

  require('..\fpdf\fpdf.php');

  $pdf = new FPDF();

  $pdf->SetAutoPageBreak(false);
  
  $pdf->SetMargins($margingLeft, $margingTop);
  $pdf->AddPage();
  $pdf->SetFont('Arial', 'B', 15);
  $pdf->SetTextColor($colorR, $colorV, $colorB);
  $pdf->Cell(13, 16 / $ratio, mb_convert_encoding($matNumber, 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');
  // $pdf->Cell(13, 16 / $ratio, utf8_decode($matNumber), 0, 0, 'C');
  $pdf->SetFont('Arial', 'B', 8 / $fontSize);
  $pdf->SetTextColor(0, 0, 0);
  if(strlen($name) >30) {
    $pdf->MultiCell(50, 8 / $ratio ,mb_convert_encoding($name, 'ISO-8859-1', 'UTF-8'), 0, 'L', 0);
    // $pdf->MultiCell(50, 8 / $ratio ,utf8_decode($name), 0, 'L', 0);
  } else {
    $pdf->Cell(50, 16 / $ratio ,mb_convert_encoding($name, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
    // $pdf->Cell(50, 16 / $ratio ,utf8_decode($name), 0, 1, 'L');
  }
  if(strlen($complement) > 30) {
    $pdf->SetFont('Arial','B',7 / $fontSize);
  } else {
    $pdf->SetFont('Arial','B',8 / $fontSize);
  }
  $pdf->Cell(63,6.13 / $ratio, mb_convert_encoding($complement, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
  $pdf->SetFont('Arial','',8 / $fontSize);
  $pdf->Cell(63,6.13 / $ratio, mb_convert_encoding($firstLine, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
  $pdf->Cell(63,6.13 / $ratio, mb_convert_encoding($secondLine, 'ISO-8859-1', 'UTF-8'),0 ,1 , 'L');
  $pdf->Cell(63,6.13 / $ratio, mb_convert_encoding($thirdLine, 'ISO-8859-1', 'UTF-8'),0 ,1 , 'L');
  $pdf->Cell(63,6.13 / $ratio, mb_convert_encoding($fourthLine, 'ISO-8859-1', 'UTF-8'),0 ,1 , 'L');
  // $pdf->Cell(63,6.13 / $ratio, mb_convert_encoding($complement, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
  // $pdf->SetFont('Arial','',8 / $fontSize);
  // $pdf->Cell(63,6.13 / $ratio, utf8_decode($firstLine), 0, 1, 'L');
  // $pdf->Cell(63,6.13 / $ratio, utf8_decode($secondLine),0 ,1 , 'L');
  // $pdf->Cell(63,6.13 / $ratio, utf8_decode($thirdLine),0 ,1 , 'L');
  // $pdf->Cell(63,6.13 / $ratio, utf8_decode($fourthLine),0 ,1 , 'L');

  $pdf->Output();
}
?>