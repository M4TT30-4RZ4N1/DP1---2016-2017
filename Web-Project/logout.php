<?php
  // puliscie ritorna alla home
  session_start();
  session_unset();//libero tutte le variabili di sessione
  session_destroy();
  header('Location: ' . 'index.php');
?>
