<?php
  session_start();
  // rimuovo tutte le session: causato dal nuovo login
  session_unset();
  require 'functions.php';
  
  //setto un flag per l'offerta-> per la gestione del messaggio di errore del thr
  //quando il suo valore è ==1 indica che l'utente ha fatto almeno una offerta
  $_SESSION['flag']=0;
  //errore inserimento offerta
  $_SESSION['error']=0;
  
  if(!isset($_POST['type'])) {//type mi indica se sono login/registrazione
    goPageError('Richiesta Non Valida');
  }
  
  //connessione al database
  $conn = connectToDb();
  
  //uso metodo POST per scambio di credenziali
  if($_POST['type'] === 'login') {
  	
  	//controllo di sicurezza
  	//controllo email: html escape
    $email = getRequiredPostArgument($conn, 'email');
    // controllo password: sql injection, no html escape perchè l'html non è mostrato: *****
    $password = md5(getRequiredPostArgument($conn, 'password', false));
       
    // prova login
    login($conn, $email, $password);
    
  } else if($_POST['type'] === 'register') {
  	
  	
  	//controllo di sicurezza
  	//controllo email: html escape
    $email = getRequiredPostArgument($conn, 'email');
    // controllo password: sql injection, no html escape perchè l'html non è mostrato: *****
    // crittografia : sh1 -> Secure Hash Algorithm 1 -> output 160 bit
    $password = md5(getRequiredPostArgument($conn, 'password', false));
    // controllo email, funzione che ne valida il formato
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      goPageError('Email Non Valida');

    }
    
    //per evitare debolezze su ATTACCO HASHING INVERSO MD5
    //aggiungo caratteri alla password CRIPTATA 4(varchar)+ password + 4(varchar)
    $password=preventReverseMD5in($password);
    
    
    // prova a registrarmi
    signup($conn, $email, $password);
    
  } else {
  	session_unset();
  	session_destroy();
    goPageError('Richiesta Non Valida');
  }

?>

