<?php
require 'config.php';

$authenticated = false;
//  controllo autenticazione (se il timeout è scattato)
//  $redirect è un booleano:
//  true autenticazione non valida: vai al login
//  false autenticazione non necessaria per questa pagina
function checkAuthentication($redirect) {
	
	global $maxInactiveTime, $loginPage, $authenticated;
	session_start();
	// controlla timeout non settato o terminato
	if(!isset($_SESSION['timeout']) || $_SESSION['timeout'] + $maxInactiveTime < time()) {
		// fine sessione
		if(!isset($_SESSION['timeout'])) {
			$message = 'Per accedere alla pagina devi essere autenticato';
		} else {
			$message = 'Sessione Terminata. Ripetere il Login';
		}
		
		session_unset();
		session_destroy();
		
		if($redirect) {
			// vai al login
			moveToPage("$loginPage?error=$message");
			die();
		}
	} else {
		// sessione valida, aggiorno il timeout
		$_SESSION['timeout'] = time();
		$authenticated = true;
	}
}
// connessione al database e disabilitazione autocommit
function connectToDb() {
	
	global $host, $user, $pwd, $db;
	
	$conn = @new mysqli($host, $user, $pwd, $db);
	if($conn->connect_error) {
		die('<link rel="stylesheet" type="text/css" href="w3.css" />
	<div class="w3-animate-bottom w3-padding-small">
	<div class="w3-card-8 ">
	<h1 class="w3-container w3-center w3-cyan">Connessione al database fallita: contattare il System Administrator.</h1>
	</div>
	</div>');
	}
	else if(!$conn) {
		die('<link rel="stylesheet" type="text/css" href="w3.css" />
	<div class="w3-animate-bottom w3-padding-small">
	<div class="w3-card-8 ">
	<h1 class="w3-container w3-center w3-cyan">Connessione al database non possibile.</h1>
	</div>
	</div>');
	}
	else if(!mysqli_set_charset($conn, "utf8")){//controllo codifica
		die('<link rel="stylesheet" type="text/css" href="w3.css" />
	<div class="w3-animate-bottom w3-padding-small">
	<div class="w3-card-8 ">
	<h1 class="w3-container w3-center w3-cyan">Errore codifica caratteri Database: impostare UTF8.</h1>
	</div>
	</div>');
	}
	
	// per ragioni di sicurezza faccio unset dati connessione database
	unset($host);
	unset($user);
	unset($pwd);
	unset($db);
	$conn->autocommit(false); //disabilito autocommit per permettere il rollback, e no fare subito lo store su disk
	
	return $conn;
}

// dynamicPage permette il caricamento pagina di log_in o  di registrazione
// nel caso abbia già effettuato l'accesso accedo al profilo direttamente
function dynamicBar() {
  global $authenticated, $loginPage, $homePage;
 
  //script per la barra: evento open/close
  echo '<script>
function w3_open() {
    document.getElementById("mySidebar").style.display = "block";
}
function w3_close() {
    document.getElementById("mySidebar").style.display = "none";
}
</script>';
  
  // controllo se ero già autenticato
  if($authenticated) {
    // autenticato
    echo '<h1 class="w3-padding-medium">Benvenuto <br>'.$_SESSION['email'].'</h1>';
    echo '<div class="w3-sidebar  w3-indigo  w3-animate-left" style="display:none; z-index:5; width:25%" id="mySidebar">
  <button class="w3-btn w3-block w3-bar-item w3-button w3-large w3-indigo "  onclick="w3_close() ">Close &times;</button>';
    echo '<a href="index.php" class="w3-bar-item w3-button w3-indigo ">Home Page</a>';
    echo '<a href="logout.php" class="w3-bar-item w3-button w3-indigo ">LogOut</a>';
    echo '</div>';
    echo '
  <div class="w3-panel w3-panel w3-indigo">
  <button class="w3-btn w3-block w3-indigo" onclick="w3_open()">&#9776; MENU </button>
  </div>';
    
   //javascript diabilitato
    echo '<noscript>';
    echo '<a href="index.php" class="w3-bar-item w3-button w3-indigo">Home Page</a>';
    echo '<a href="logout.php" class="w3-bar-item w3-button w3-indigo">LogOut</a>';
    echo '</noscript>';
     
  } else {
    //non autenticato
  	echo '<div class="w3-sidebar w3-indigo  w3-animate-left" style="display:none; z-index:5; width:25%" id="mySidebar">
  <button class="w3-btn w3-block w3-bar-item w3-button w3-large w3-indigo   "  onclick="w3_close() ">Close &times;</button>';
  	echo "<a href=\"$homePage\" class=\"w3-bar-item w3-button w3-indigo  \">Home Page</a>";
    echo "<a href=\"$loginPage\" class=\"w3-bar-item w3-button w3-indigo \">Login o Registrazione</a>";
    echo '</div>';    
    echo '
  <div class="w3-panel w3-panel w3-indigo">
  <button class="w3-btn w3-block w3-indigo" onclick="w3_open()">&#9776; MENU </button>
  </div>';
    
    //javascript diabilitato
    echo '<noscript>';
    echo "<a href=\"$homePage\" class=\"w3-bar-item w3-button w3-indigo\">Home Page</a>";
    echo "<a href=\"$loginPage\" class=\"w3-bar-item w3-button w3-indigo\">Login o Registrazione</a>"; 
    echo '</noscript>';

  }
 

}

// ridirezione verso una pagina specifica
function moveToPage($destination) {
	header("Location: $destination");
	die();
}

//ritorno il nome della pagina
function getCurrentPageName() {
  return basename($_SERVER['SCRIPT_FILENAME']);
}

// ridirezione: caso successo
function getPageSuccess() {
  global $redirections;//array mappatura ridirezioni di tutte le pagine
  return $redirections[getCurrentPageName()]['success'];
}

// ridirezione: caso errore
function getPageError() {
  global $redirections;//array mappatura ridirezioni di tutte le pagine
  return $redirections[getCurrentPageName()]['error'];
}

// ridirezione: caso errore
function goPageError($error) {
	header('Location: '.getPageError()."?error=$error");//get method
	die();
}

// ridirezione: caso successo
function goPageSuccess() {
	header('Location: '.getPageSuccess());
	die();
}

// sicurezza: $tocontrol può essere email o password o thr -> dati inseriti nel database
function getRequiredPostArgument($conn, $tocontrol, $escape = true) {//se escape non messo assert true
  //non presente
	if(!isset($_POST[$tocontrol]) || $_POST[$tocontrol] === '') {
    goPageError("Dati Mancanti: $tocontrol");
    die();
  }
  // previene utilizzo maligno di codice HTML nell'username(email)/thr
  if ($escape) {
  	//trim — Rimuove gli spazi (ed altri caratteri) all'inizio e alla fine di un testo
  	//mysql_real_escape_string — prevenzione sql injection
  	//stripslashes — Rimuove gli slash
  	$result = mysqli_real_escape_string($conn,(htmlentities(stripslashes(trim($_POST[$tocontrol])))));
  } else {
    // prevenzione SQL injection, non escape HTML characters
    // usato per le passwords
    // le passwords non vengono visualizzate , il controllo di escaping html non ha significato
  	$result = mysqli_real_escape_string($conn,$_POST[$tocontrol]);
  	
  	
  	//controllo formato con regex
  	
  	//la mia password non può contenere caratteri speciali: solo numeri e lettere
  	$format_special= preg_match("/\W|_/", $result);
  	
  	if($format_special==1){
  		
  		if(!isset($_COOKIE['format']))
  			setcookie('format',0);	
  			goPageError("Errore Formato Password: Inserisci solo lettere e numeri");
  			
  	}
  	
  	//lettera: format=1 ho una lettera 
  	$format_low= preg_match("/[a-z]/", $result);
  	$format_upp= preg_match("/[A-Z]/", $result);
  	
  	if($format_low==0 && $format_upp==0){
  		
  		if(!isset($_COOKIE['format']))
  			setcookie('format',1);
  		
  		goPageError("Errore Formato Password: Inserisci almeno una lettera");
  		
  	}
  	
  	//numero format=1 ho un numero nella password
  	$format_num= preg_match("/[0-9]/", $result);
  	
  	if($format_num==0){
  		
  		if(!isset($_COOKIE['format']))
  			setcookie('format',2);
  		
  		goPageError("Errore Formato Password: Inserisci almeno un numero");
  		
  	}
  	
  	
  }
  return $result; //ritorno email/password/thr filtrati in maniera sicura
}

// login 
// user= email password= ***
function login($conn, $email, $password) {
	
	// Validation
	
	$sql = "SELECT password, thr FROM testtable WHERE user= '". $email . "'";
	
	if(! $risposta = mysqli_query($conn,$sql)){
		mysqli_close($conn);
		goPageError('200-Impossibile creare la query!');
	}
	if (mysqli_num_rows($risposta) == 0) {
		mysqli_close($conn);
		goPageError('Nome Utente Non Valido');
	}
	
	$riga = mysqli_fetch_array($risposta,MYSQLI_NUM);
	$p= $riga[0];
	$mythr= $riga[1];
	
	mysqli_free_result($risposta);
	//p una password CRIPTATA precedentemente in fase di signup: 4(varchar)+ password + 4(varchar)
	//per evitare debolezze su ATTACCO HASHING INVERSO MD5
	//rimuovo caratteri aggiunti alla password 
	$p=preventReverseMD5out($p);
	
	// $p 		: password ritornata dalla Query -> on SERVER (DataBase)
	// $password: password inserita da utente ->    on CLIENT
	if($password!=$p){ 
		mysqli_close($conn);
		goPageError("'".$password."'");
	}
	
  // salva info utili
  $_SESSION['timeout'] = time();
  $_SESSION['email'] = $email;
  $_SESSION['password'] = $password;
  
  //setto il flag delle offerte a zero se thr è NULL, a 1 se thr è diverso da NULL
  //serve per visualizzare messaggio Stato offerta per un utente che ha fatto almeno una offerta
  if($mythr == NULL){
  	
  	$_SESSION['flag'] = 0;
  	
  }else{
  	
  	$_SESSION['flag'] = 1;
  	
  }
 
  closeConnection($conn);
  // muoviti alla pagina
  goPageSuccess();
  
 
}

//registrazione nuovo utente
function signup($conn, $email, $password) {
	
  //nb: la password a questo punto è criptata con sha1
	
  $result = mysqli_query($conn, "INSERT INTO testtable (user, password, thr) VALUES ('".$email."','".$password."', NULL)");
 
  if(!$result) {
  	mysqli_close($conn);
    goPageError("247-Impossibile creare l'account. Probabilmente email gia in uso.");
  }
  
  mysqli_free_result($result);
 
  if(!$conn->commit()) {//avevo disabilitato l'autocommit per la transazione
  	closeConnection($conn);
    goPageError('Commit fallito. Riprovare.');
  }
  // salva info utili
  $_SESSION['timeout'] = time();
  $_SESSION['email'] = $email;
  $_SESSION['password'] = $password;
  
  //nell signup non posso aver fatto un'offerta: mi sono appena registrato
  //setto il flag delle offerte a zero
  $_SESSION['flag'] = 0;
  

  closeConnection($conn);
  //  muoviti alla pagina
  goPageSuccess();
 
 
}

//funzione per aggiornare valore thr di un utente
function insertTHR($conn,$email,$thr){
	
	$date= date("Y-m-d h:i:s");
	$result = mysqli_query($conn, "UPDATE testtable SET thr=".$thr.", date='".$date."' WHERE user='".$email."'");
	
	if(!$result) {
		goPageError("Impossibile procedere con l'inserimento.");
	}
	
	mysqli_free_result($result);
	
	if(!$conn->commit()) {//avevo disabilitato l'autocommit per la transazione
		goPageError('Commit fallito. Riprovare.');
	}
	
	//avendo un thr è necessario informare il sistema che ho fatto una offerta
	//per poi visualizzare un messaggio di offerta vinta/persa
	$_SESSION['flag'] = 1;
	
	
}

function getBID($conn){
	
	//operazione atomica
	//se sto leggendo il BID che potrebbe essere modificato
	$sql = "SELECT user,bid FROM offer FOR UPDATE";
	
	if(!$risposta = mysqli_query($conn,$sql)){
		mysqli_close($conn);
		goPageError('290-Impossibile creare la query!');
	}
	if (mysqli_num_rows($risposta) == 0) {
		mysqli_close($conn);
		goPageError('Bid non trovato!');
	}
	
	$riga = mysqli_fetch_array($risposta,MYSQLI_NUM);
	$best= $riga[0];
	$bid= $riga[1];
	
	mysqli_free_result($risposta);
	
	//connessione non chiusa a questo livello ma dopo essere ritornata
	
	//ritorno la coppia valori
	return array(0=>$best,1=>$bid);
	
}

function getTHR($conn,$myemail){
	
	$sql = "SELECT thr FROM testtable WHERE user='".$myemail."'";
	
	if(!$risposta = mysqli_query($conn,$sql)){
		goPageError('310-Impossibile creare la query!');
	}
	if (mysqli_num_rows($risposta) == 0) {
		goPageError('Ricerca THR fallita');
	}
	
	$riga = mysqli_fetch_array($risposta,MYSQLI_NUM);
	$thr= $riga[0];
	
	mysqli_free_result($risposta);

	if($thr==NULL){
		$thr="-";
	}
	//ritorno thr
	return $thr;
	
}

//controlla il valore del bid
//Uso SELECT ... FOR UPDATE
function updateBID($conn){

	//calcolo max thr
	$query ="SELECT MAX(thr) FROM testtable FOR UPDATE";
	$ris = mysqli_query($conn,$query);
	
	if(!$ris){
		goPageError('334-Impossibile creare la query - Max Thr !');
	}
	if (mysqli_num_rows($ris) == 0) {
		//non ho offerte
		$bid=1;
		$user="nessuno";
		
	}else{
	
		$riga = mysqli_fetch_array($ris,MYSQLI_NUM);
		$max= $riga[0];
		
		mysqli_free_result($ris);
		
		$query ="SELECT user, thr FROM testtable WHERE thr IS NOT NULL ORDER BY thr DESC,date ASC LIMIT 2 FOR UPDATE" ;
		$ris = mysqli_query($conn,$query);
		
		if(!$ris){
			goPageError('350-Impossibile creare la query - Find User non pari');
		}
		if (mysqli_num_rows($ris) == 0) {
			//non ho offerte
			$bid=1.00;
			$user="nessuno";
		}else if (mysqli_num_rows($ris) == 1) {
			//unico offerente
			$riga = mysqli_fetch_array($ris,MYSQLI_NUM);
			$user= $riga[0];
			$bid= 1;
		}else if (mysqli_num_rows($ris) == 2) {
			//più di una offerta
			$riga = mysqli_fetch_array($ris,MYSQLI_NUM);
			$user= $riga[0];
			$thr1=$riga[1];
			$riga = mysqli_fetch_array($ris,MYSQLI_NUM);
			$thr2=$riga[1];
			
			if($thr1 > $thr2){//no parimerito
				$bid= $thr2+0.01;
			}else{ //parimerito
				$bid=$riga[1];
		    }
		}
		
	}
	
	mysqli_free_result($ris);
	
	//operazione di update: user e bid
	$query="UPDATE offer SET user='".$user."', bid=".$bid;
	$ris=mysqli_query($conn, $query);
	
	if(!$ris){
		goPageError('384-Impossibile creare la query - Update BID');
	}
	
	if(!$conn->commit()) {//avevo disabilitato l'autocommit per la transazione
		goPageError('Commit fallito. Riprovare.');
	}

	
}



// mostra nella pagina principale, sia autenticato/non autenticato
// i valori del BID e dell'utente associato (html)
// e se autenticato lo stato dell'offerta dell'utente + la sua ultima offerta
function showBID() { 
	
	global  $authenticated;
	
	$conn= connectToDb();
	
	//update del BID 
	updateBID($conn);
	
	//struttura user-bid ritornata da getBID
	$info= array();
	$info= getBID($conn);
	
	closeConnection($conn);
	
	$best= $info[0];
	$bid= $info[1];
	
	
	//STAMPA LA MIGLIORE OFFERTA
	echo'
    <div class="w3-animate-bottom w3-padding-small">
	<div class="w3-card-8 ">
	<h1 class="w3-container w3-cyan">Migliore Offerta</h1>
	<form>
	<table>
	<tr><td align="right">User:</td><td><Input type = "text" id="biduser" value ='.$best.' style=”text-align:right” readonly></td></tr>
	<tr><td align="right">BID (€):</td><td><Input type = "text" id="bid" value ='.$bid.' style=”text-align:right” readonly></td></tr>
	</table>
	</form>
	</div>
	</div>';
	
	//contenuto PLUS - INSERIMENTO THR
	if($authenticated){ 
		
		$conn= connectToDb();
		
		$myemail=$_SESSION['email'];//informazione di sessione!
			if($_SESSION['email']==$best){
				$_SESSION['flag']=0;
		}
		
		$thr=getTHR($conn, $myemail);
		
		closeConnection($conn);
		
		$test=is_numeric($thr);
		
		//STAMPA L'ULTIMO THR DELL'UTENTE
		echo'
	    <div class="w3-animate-bottom w3-padding-small">
		<div class="w3-card-8 ">
		<h1 class="w3-container w3-cyan">THR</h1>
		<form>
		<table>
		<tr><td align="right">THR(€):</td><td><Input type = "text" id="thr" value ='.$thr.' style=”text-align:right” readonly></td></tr>
		</table>
		</form>
		</div>
		</div>';
		
		//OFFERTA - PUNTO(2):" il valore di THR_i può essere modificato in qualsiasi momento"
		// possibilità ad un utente che sta vincendo l'asta di alzare il prezzo a cui è disposto arrivare
		// in modo che il sistema faccia offerte per suo conto
		//il campo nascosto bidder, passato col metodo POST serve a capire se sono ancora in sessione o sono scaduti i due minuti
		echo'
			<div class="w3-animate-bottom w3-padding-small">
			<div class="w3-card-8 ">
			<h1 class="w3-container w3-light-blue">Offri</h1>
			<form action="insert_thr.php" method="post" class="w3-padding-medium" >
			<table>
			<tr><td align="right">THR (€):</td><td><input type = "text" required="required" id="thr" name="thr" value ="0" style=”text-align:right” placeholder="your offer" class="w3-input w3-hover-light-grey"></td>
			<td><input class="w3-btn w3-button w3-round-xxlarge w3-blue" type="submit" value="Offri" ></td></tr>
			</table>
			<input type="hidden" name="bidder" value='.$myemail.'>
			</form>
			</div>
			</div>';
		
		$myemail=$_SESSION['email'];//mi salvo questa info per quando aprirò nuove tab e farò login con altri account
		
	
		//permetto all'utente di inserire una nuova offerta
		//sulla base delle info del migliore offerente e della sua ultima offerta
		if(($best != $myemail) ){//non sono il miglior offerente || ($test==true && $thr<=$bid)
		
			//REAL TIME NOTIFY
			//ad ogni refresh notifico il messaggio se in tempo reale qualcuno ha offerto
			
			if($_SESSION['flag']==1 || ($test==true && $thr<=$bid)){ //ho fatto un'offerta ma è stata superata
				
				echo' <div class="w3-animate-bottom w3-padding-small">
				<div class="w3-card-8 ">
				<h1 class="w3-container w3-red">Stato Offerta</h1>
				<p  id="superata" style="font-size:36px;"> Offerta superata !!!</p>
				</div>
				</div>';
					
			}
			
			}else{ //sono il miglior offerente
				
				echo'
				<div class="w3-animate-bottom w3-padding-small">
				<div class="w3-card-8 ">
				<h1 class="w3-container w3-green">Stato Offerta</h1>
				<p id="massima" style="font-size:36px;"> Sei il massimo offerente!!!</p>
				</div>
				</div>';
				
			}
	}
		
	
}



//funzione per gestire il messaggio di errore - popup
function alert($msg) {
	echo "<script type='text/javascript'>alert('$msg');</script>";
}

//CRYPT
//funzione che cripta la password con 8 caratteri random in inserimento nel database
function preventReverseMD5in($pwd){
	function generateRandomString($length = 4) {
		return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyz', ceil($length/strlen($x)) )),1,$length);
	}
	
	$pwd=generateRandomString().$pwd.generateRandomString();
	
	return $pwd;
}

//DECRYPT
//funzione che decripta la password con 8 caratteri random in estrazione dal database
function preventReverseMD5out($pwd){
	
	$new="";
	
	for($i=0;$i< strlen($pwd);$i++){
		
		if($i>=4 && $i<=35){
			$c= $pwd[$i];
			$new.= $c;
		}
	}
	
	return $new;
}

function closeConnection($conn) {
	
	if(!mysqli_close($conn)){
		goPageError('610-Impossibile chiudere connessione con DB.');
	}
}


?>
