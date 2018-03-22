<?php
require 'functions.php';

checkAuthentication(true);


//connessione al database
$conn = connectToDb();

//controllo validità input
$thr = getRequiredPostArgument($conn, 'thr');
$bidder = getRequiredPostArgument($conn, 'bidder');

//per evitare che avendo due pagine aperte in questo ordine: utente A, poi utente B sullo stesso browser
//se inserisco l'offerta per l'utente A non la deve prendere (dato che è scaduta) come una offerta dell'utente B
if($bidder != $_SESSION['email']){ 
	goPageError("Sessione Scaduta!");
}

$info=array();
$info=getBID($conn);
$bid= $info[1];

$test= is_numeric($thr);

//se è un numero ha senso confrontare
if($test==true){
	//validità valore thr
	if($thr > $bid){ //valido -> ricalcolo BID
		
		//inserimento del thr nella base di dati
		insertTHR($conn,$_SESSION['email'],$thr);
	
		//aggiorno BID
		updateBID($conn);
		
		closeConnection($conn);
		//  muoviti alla pagina
		goPageSuccess();
		
	}else{ //non valido -> messaggio di errore
		
		$_SESSION['error']=1;
		
		closeConnection($conn);
		//  muoviti alla pagina
		goPageSuccess();
	}
}else{
	closeConnection($conn);
	$_SESSION['error']=1;
	goPageError("Inserimento di un dato non numerico!");
	
}

?>