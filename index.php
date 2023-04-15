<?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre;charset=utf8', 'root', '');

include_once('Connexion/cookieconnect.php');

if(isset($_SESSION['id']))
{
  header("Location: mainboard.php?id=".$_SESSION['id']);
}

?>

<!DOCTYPE HTML>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <title>Gestionnaire Biosolve</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Page d'accueil de l'aide à la gestion du laboratoire.">
    <link rel="stylesheet" href="assets/css/prefixed/main.css" />
    <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
  </head>
  <body>
    <main>
      <nav>
        <ul>
          <li>
            <a href="Connexion/inscription.php" class="nav--btn">S'inscrire</a>
          </li>
          <li>
            <a href="Connexion/connexion.php" class="nav--btn">Se connecter</a>
          </li>
        </ul>
      </nav>
      <div class='content'>
        <div id="page-wrapper">

          <?php include('header.php'); ?>

          <section class='section'>
            <div id="logo">
              <svg class="icone" width="42.5mm" height="57.5mm" viewBox="0 0 85.907257 115.38412" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
                <g transform="translate(-40.314582,-78.767128)">
                  <path class="logodraw" d="m 40.481249,181.76875 h 48.41875 c 4.355762,0 8.740508,-0.0471 13.014901,-0.88503 4.2744,-0.83797 8.42141,-2.47392 11.94706,-5.03175 3.52564,-2.55783 6.39404,-6.01576 8.2413,-9.96041 1.84726,-3.94466 2.67584,-8.345 2.51549,-12.69781 -0.2209,-5.99664 -2.32429,-11.91035 -5.93972,-16.69962 -3.61544,-4.78928 -8.72655,-8.43244 -14.4332,-10.28788 3.70967,-2.17744 6.8321,-5.34484 8.95629,-9.08525 2.12419,-3.74042 3.24488,-8.04461 3.21455,-12.346 -0.0267,-3.78646 -0.94765,-7.570478 -2.71792,-10.917741 -1.77027,-3.347264 -4.3874,-6.235278 -7.51845,-8.36473 C 103.04925,83.363077 99.430965,82.005015 95.713263,81.286187 91.99556,80.56736 88.188321,80.482504 84.402082,80.433332 l -20.372916,-0.264583 0.161922,91.648491 10.28745,-0.0149 c 0.632998,-9.2e-4 1.281896,-0.0384 1.859761,-0.2968 0.577866,-0.25838 1.057533,-0.714 1.384098,-1.25626 0.342724,-0.56909 0.529253,-1.21932 0.649896,-1.87259 0.120642,-0.65328 0.181964,-1.31585 0.249281,-1.97675 l 2.4415,-23.9701 c 0.06545,-0.64253 0.09,-1.29384 0.248854,-1.91986 0.07943,-0.31301 0.202657,-0.62048 0.406399,-0.87102 0.101871,-0.12527 0.223681,-0.23517 0.362916,-0.31693 0.139234,-0.0818 0.296208,-0.13491 0.457258,-0.14647 0.178938,-0.0128 0.360065,0.026 0.522453,0.10224 0.162388,0.0763 0.306384,0.18915 0.426548,0.32235 0.240327,0.26642 0.383892,0.60779 0.471871,0.95564 0.175956,0.69569 0.181983,1.42171 0.23768,2.13714 l 1.854346,23.81904 c 0.05765,0.74058 0.09895,1.48343 0.205189,2.21862 0.106238,0.73518 0.29444,1.47967 0.725323,2.08475 0.437041,0.61373 1.117978,1.04591 1.85712,1.192 0.215003,0.0425 0.436464,0.0613 0.65371,0.0324 0.217246,-0.0289 0.429047,-0.10744 0.606058,-0.23666 0.177011,-0.12923 0.315803,-0.30769 0.407722,-0.50664 0.09192,-0.19896 0.136597,-0.41589 0.177085,-0.63128 l 1.839756,-9.78731 c 0.03872,-0.206 0.07877,-0.41271 0.148817,-0.61027 0.07004,-0.19756 0.17064,-0.38604 0.310347,-0.54231 0.139707,-0.15626 0.318744,-0.279 0.518601,-0.3422 0.199857,-0.0632 0.419703,-0.0655 0.618513,9.3e-4 0.17139,0.0573 0.324206,0.16365 0.448303,0.295 0.124098,0.13135 0.220462,0.28719 0.295459,0.45159 0.149995,0.32881 0.21765,0.68814 0.287426,1.04274 l 1.861118,9.45823 c 0.0452,0.2297 0.09689,0.46193 0.209785,0.667 0.112899,0.20508 0.285724,0.37784 0.494805,0.48314 0.209081,0.1053 0.448168,0.14244 0.68197,0.13062 0.233803,-0.0118 0.462581,-0.0695 0.688932,-0.12929 2.575422,-0.67968 5.118562,-1.59505 7.343772,-3.05903 2.93558,-1.93134 5.23127,-4.79265 6.57753,-8.03846 1.5316,-3.69266 1.83641,-7.86478 0.96741,-11.76688 -1.17738,-5.28684 -4.6349,-10.03892 -9.35839,-12.68949 -2.36174,-1.32528 -4.989374,-2.14249 -7.666038,-2.55442 -2.676665,-0.41193 -5.39698,-0.41476 -8.105141,-0.40575 l -16.06363,0.0535" style="fill:none;stroke:#00537fff;stroke-width:3;stroke-linecap:square;"/>
                  <path class="logodraw" d="m 73.917238,90.514209 0.323843,32.141451 10.605867,-1e-5 c 2.625853,0 5.269326,-0.0287 7.845666,-0.53627 2.576339,-0.50752 5.074898,-1.49797 7.197624,-3.04366 2.122722,-1.54568 3.847942,-3.63295 4.960872,-6.01129 1.11293,-2.37834 1.61553,-5.02893 1.53142,-7.65343 -0.0738,-2.30398 -0.6,-4.60261 -1.62558,-6.667057 -1.02558,-2.064446 -2.5543,-3.878378 -4.42519,-5.225059 -1.870879,-1.34668 -4.065245,-2.211969 -6.328488,-2.649565 -2.263242,-0.437595 -4.584303,-0.450215 -6.889418,-0.436073 z" style="fill:none;stroke:#00537fff;stroke-width:3;stroke-linecap:square;"/>
                  <path class="logodraw" d="m 40.318494,192.52485 85.899436,0.24289" style="fill:none;stroke:#00537fff;stroke-width:3;stroke-linecap:square;"/>
                  <path class="letter" d="m 41.940321,171.67975 15.923453,-0.0388 0.042,-1.49783 c 0.02335,-0.83263 0.02815,-1.66943 -0.0926,-2.49359 -0.120752,-0.82415 -0.357403,-1.62927 -0.675154,-2.39923 -0.266801,-0.6465 -0.601525,-1.28817 -1.134867,-1.7406 -0.597072,-0.50649 -1.399873,-0.72916 -2.182815,-0.72344 -0.68685,0.005 -1.379268,0.18021 -1.957223,0.55136 -0.604822,0.38841 -1.063341,0.97571 -1.387693,1.61717 -0.516079,1.02063 -0.70874,2.17121 -0.846005,3.30663 -0.118362,0.97906 -0.200467,1.9625 -0.246091,2.94763 -0.08048,-0.73032 -0.162482,-1.46047 -0.246017,-2.19045 -0.125878,-1.09999 -0.266533,-2.23474 -0.822022,-3.19248 -0.288567,-0.49753 -0.685558,-0.93385 -1.163001,-1.25456 -0.450844,-0.30284 -0.980872,-0.50257 -1.523956,-0.50808 -0.733166,-0.007 -1.438396,0.34096 -1.976742,0.83873 -0.480543,0.44432 -0.844913,1.00517 -1.105563,1.60551 -0.356392,0.82085 -0.47962,1.72227 -0.521007,2.61619 -0.03942,0.85143 -0.06766,1.70337 -0.0847,2.55554 -10e-7,5e-5 -2e-6,1e-4 -2e-6,1.5e-4 0,5e-5 1e-6,1e-4 3e-6,1.5e-4 z" style="fill:none;stroke:#00537fff;stroke-width:1.8;stroke-linecap:square;"/>
                  <path class="letter" d="m 46.659883,157.99485 11.178409,-0.0499" style="fill:none;stroke:#00537fff;stroke-width:1.8;stroke-linecap:square;"/>
                  <circle class="letter" cx="41.819233" cy="157.96989" r="0.7" style="fill:none;stroke:#00537fff;stroke-width:1.2;stroke-linecap:square;"/>
                  <circle class="letter" cx="52.261562" cy="147.46521" r="6" style="fill:none;stroke:#00537fff;stroke-width:1.8;stroke-linecap:square;"/>
                  <circle class="letter" cx="52.526146" cy="121.53604" r="6" style="fill:none;stroke:#00537fff;stroke-width:1.8;stroke-linecap:square;"/>
                  <path class="letter" d="M 41.599096,111.00741 H 57.844082" style="fill:none;stroke:#00537fff;stroke-width:1.8;stroke-linecap:square;"/>
                  <path class="letter" d="m 47.349566,131.58597 c -0.544746,0.31142 -0.992008,0.79066 -1.265127,1.35558 -0.273119,0.56492 -0.370926,1.2131 -0.276659,1.83346 0.106789,0.70276 0.464827,1.36944 1.010149,1.8254 0.545321,0.45597 1.276328,0.69125 1.982672,0.6115 0.791925,-0.0894 1.502792,-0.5562 2.040445,-1.14448 0.537654,-0.58827 0.924992,-1.29532 1.304242,-1.99625 0.353969,-0.65421 0.711616,-1.31821 1.223205,-1.85818 0.255795,-0.26998 0.549385,-0.50679 0.879474,-0.67815 0.33009,-0.17135 0.697667,-0.27596 1.069563,-0.27982 0.689623,-0.007 1.357239,0.34013 1.813636,0.85717 0.456397,0.51705 0.713624,1.18942 0.8075,1.87266 0.165428,1.204 -0.172212,2.46922 -0.915534,3.43071" style="fill:none;stroke:#00537fff;stroke-width:1.8;stroke-linecap:square;"/>
                  <path class="letter" d="M 45.986168,106.71555 56.726833,101.64757 46.072554,96.349229" style="fill:none;stroke:#00537fff;stroke-width:1.8;stroke-linecap:square;"/>
                  <path  class="letter" d="m 51.96016,91.802904 -6e-6,-11.549855 c -1.481451,0.146033 -2.899913,0.859094 -3.900865,1.960965 -1.000952,1.10187 -1.5749,2.582098 -1.578394,4.070726 -0.0035,1.488627 0.563499,2.971533 1.559268,4.07809 0.995768,1.106557 2.410868,1.826269 3.891617,1.979255 1.425747,0.147304 2.89987,-0.230584 4.078923,-1.045622 1.179053,-0.815038 2.053406,-2.060569 2.419348,-3.446404 0.365942,-1.385836 0.220484,-2.900656 -0.402483,-4.191533 -0.622968,-1.290878 -1.718357,-2.347268 -3.030961,-2.92305" style="fill:none;stroke:#00537fff;stroke-width:1.8;stroke-linecap:square;"/>
                </g>
              </svg>
            </div>
            <div class='section--title'>
              <h2>Gestionnaire des réactifs et des feuilles de calcul<br />
                du laboratoire d'analyse.
              </h2>
            </div>
            <div class='section--text'>
              <a href="Connexion/connexion.php" class="btn">Se connecter</a>
            </div>
          </section>
        </div>
      </div>
    </main>
  </body>
</html>

<!-- Scripts -->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/nav.js"></script>
  <script src="assets/js/darkmode.js"></script>