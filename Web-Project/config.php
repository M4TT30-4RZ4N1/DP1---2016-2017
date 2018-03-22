<?php
//set del configuration option
ini_set('display_errors', 1);

//VARIABILI GLOBALI:
// timeout per inattività (2 minuti)
$maxInactiveTime = 60 * 2;
// pagine importanti
$loginPage = 'login.php';
$homePage = 'index.php';


// array di coppie di pagine: successo/errore
// viene usato per capire quale percorso prendere ad ogni decisione
$redirections = array(
		'login_validate.php' => array(
				'success' => 'index.php',
				'error' => 'login.php'
		),
		'functions.php' => array(
				'success' => 'index.php',
				'error' => 'login.php'
		),
		'config.php' => array(
				'success' => 'index.php',
				'error' => 'login.php'
		),
		'insert_thr.php' => array(
				'success' => 'index.php',
				'error' => 'index.php'
		)
);


// forza HTTPS
if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
  header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], TRUE, 301);
  die();
}

// controllo utilizzo cookies
function checkCookies() {
	
	if (isset($_COOKIE['test'])) {
		
	} else {
		if (isset($_GET['reload'])) {
			die('<link rel="stylesheet" type="text/css" href="w3.css" />
	<div class="w3-animate-bottom w3-padding-small">
	<div class="w3-card-8 ">
	<h1 class="w3-container w3-center w3-cyan">Abilitare i Cookies per accedere al sito.</h1>
	</div>
	</div>');
		} else {
			setcookie('test', '1', time() + 86400);
			header('Location: ' . $_SERVER['PHP_SELF'] . '?reload');
			exit();
		}
	}

}

// test dei cookie
if (!isset($_COOKIE['test'])) {
	checkCookies();
}


// ridirezione pagine web a cui non si può accedere (non principali)
//per inser_thr.php gestita diversamente (messaggio: devi essere autenticato)
//lo stesso vale per login_validate.php
switch (basename($_SERVER['SCRIPT_FILENAME'])) {
  case 'config.php':
  case 'functions.php':
    header("Location: $homePage");
    break;
  default:
    // nulla
    break;
}

// impostazioni database
$database = 'labinf';

if ($database === 'local') {
  $host = 'localhost';
  $user = 'root';
  $pwd = '';
  $db = 'testdb';
} 
else {//per caricamento al labInf
  $host = 'localhost';
  $user = 's236462';
  $pwd = 'nissioni';
  $db = 's236462';
}



?>
