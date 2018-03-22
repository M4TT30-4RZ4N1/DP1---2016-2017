<?php
  require 'functions.php';
  // non necessità autenticazione
  checkAuthentication(false);
  //se sono autenticato vai alla homepage
  if($authenticated) {
    moveToPage($homePage);
  }
  
  if(isset($_COOKIE['format'])){
  	
  	if($_COOKIE['format']==0){
  		alert("Errore Formato Password: Utilizza solo caratteri alfanumerici.");
  	}
  	else if($_COOKIE['format']==1){
  		alert("Errore Formato Password: Inserisci almeno una lettera");
  	}
  	else if($_COOKIE['format']==2){
  		alert("Errore Formato Password: Inserisci almeno un numero");
  	}
  	
  	setcookie('format', '', time()-3600);
  }
?>
<!DOCTYPE html>
<html>

<head>
  <title>Asta Online - Login</title>
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
    <h1><font face="SansSerif">Asta Online- LogIn/Registrazione</font> </h1>
  </div>
  <div class="w3-sidenav w3-display-topleft w3-indigo w3-card-8 w3-animate-left" style="width:25%">
      <?php dynamicBar() ; ?>
  </div>
  <div class="w3-padding-medium">
    <div class="w3-row" style="margin-left:25%">
     
        <div class="w3-half w3-animate-left w3-padding-small">
          <div class="w3-card-8 ">
            <h1 class="w3-container w3-light-blue">Login</h1>
            <form action="login_validate.php" method="post" class="w3-padding-medium">
              <input type="text" value="login" hidden="hidden" name="type" />
              <p>
                <input type="email" maxlength="50" required="required" name="email" placeholder="your email" class="w3-input w3-hover-light-grey" />
                <label class="w3-label w3-validate">Username (email)</label>
              </p>
              <p>
                <input type="password" maxlength="50" required="required" name="password" placeholder="password" class="w3-input w3-hover-light-grey" />
                <label class="w3-label w3-validate">Password</label>
              </p>
              <p>
                <input class="w3-btn w3-button w3-round-xxlarge w3-blue" type="submit" value="Login" />
              </p>
            </form>
          </div>
        </div>
        <div class="w3-half w3-animate-right w3-padding-small">
          <div class="w3-card-8">
            <h1 class="w3-container w3-light-blue">Registrazione</h1>
            <form action="login_validate.php" method="post" class="w3-padding-medium">
              <input type="text" value="register" hidden="hidden" name="type" />
              <p>
                <input type="email" maxlength="50" required="required" name="email" placeholder="your email" class="w3-input w3-hover-light-grey" />
                <label class="w3-label w3-validate">Email</label>
              </p>
              <p>
                <input type="password" maxlength="50" required="required" name="password" placeholder="your new password" class="w3-input w3-hover-light-grey" />
                <label class="w3-label w3-validate">Password</label>
              </p>
              <p>
                <input class="w3-btn w3-button w3-round-xxlarge w3-blue" type="submit" value="Registrazione" />
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
 

</body>

</html>
