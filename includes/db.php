<?php 
    $dsn = 'mysql:host=localhost;dbname=gestion_personnel;charset=utf8';
	$root = 'root';
	$password = '';
	
	try
	{
		$pdo = new PDO($dsn, $root , $password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION);
		//echo 'you are connected';
		
	}catch(PDOException $e)
	{
		print "Erreur :" . $e->getMessage() . '<br>';
	}

?>