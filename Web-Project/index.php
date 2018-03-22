<?php
  require 'functions.php';
  // pagina principale non autenticato
  checkAuthentication(false);
  
  
  //messaggio di errore inserimento offerta
  if(isset($_SESSION['error']) && $_SESSION['error']==1){
  	alert("Errore: Offerta non valida !!!");
  	$_SESSION['error']=0; //dopo che ho visualizzato pulisco l'errore
  }
 
 

?>
<!DOCTYPE html>
<html>

<head>
  <title> Aste Online - s236462 Matteo Arzani - Politecnico di Torino </title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="w3.css" />
  <link rel="stylesheet" type="text/css" href="myCSS.css" />

</head>

<body>
  <div class="w3-container w3-red w3-center topbar w3-animate-top" style="margin-left:25%">
    <noscript>
    <h2>Funzionalità limitata: JavaScript Disabilitato.</h2>
    </noscript>
  </div>
 <div class="w3-container w3-orange w3-center topbar w3-animate-right" style="margin-left:25%">
    <h1><font face="SansSerif">Asta Online</font> </h1>
  </div>
  <div class="w3-sidenav w3-display-topleft w3-indigo w3-card-8 w3-animate-left" style="width:25%">
      <?php dynamicBar() ; ?>
  </div>

   
  <div class="w3-animate-right w3-padding-medium" style="margin-left:25%" id="content">
  
    <br>
    <br>
    <br>
	<div align="center">
    <?php showBID()?>
	</div>
</div>

</body>


</html>
