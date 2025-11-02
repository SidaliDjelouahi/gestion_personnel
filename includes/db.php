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

	/**
	 * Check if a table exists in the current database.
	 * Returns true if exists, false otherwise.
	 */
	function table_exists(PDO $pdo, string $table): bool
	{
		try {
			// Use a prepared statement to avoid injection via table name.
			$stmt = $pdo->prepare("SHOW TABLES LIKE ?");
			$stmt->execute([$table]);
			return (bool) $stmt->fetch(PDO::FETCH_NUM);
		} catch (PDOException $e) {
			return false;
		}
	}

	/**
	 * Safe count: returns integer count of rows in table, or 0 if table missing / query fails.
	 */
	function safe_count(PDO $pdo, string $table): int
	{
		try {
			if (!table_exists($pdo, $table)) {
				return 0;
			}
			$stmt = $pdo->query("SELECT COUNT(*) FROM `" . str_replace('`','', $table) . "`");
			return (int) $stmt->fetchColumn();
		} catch (PDOException $e) {
			return 0;
		}
	}

?>