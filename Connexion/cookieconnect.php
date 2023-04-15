<?php
if(!isset($_SESSION['id'],$_COOKIE['pseudo'],$_COOKIE['passwordlabo']) AND !empty($_COOKIE['pseudo']) AND !empty($_COOKIE['passwordlabo'])) {
  $requser = $bdd->prepare("SELECT * FROM membres WHERE pseudo = ? AND motdepasse = ?");
  $requser->execute(array($_COOKIE['pseudo'], $_COOKIE['passwordlabo']));
  $userexist = $requser->rowCount();
  if($userexist == 1) {
    $userinfo = $requser->fetch();
    $_SESSION['id'] = $userinfo['id'];
    $_SESSION['pseudo'] = $userinfo['pseudo'];
    $_SESSION['mail'] = $userinfo['mail'];
  }
}
?>
