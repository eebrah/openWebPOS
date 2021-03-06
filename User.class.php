<?php

require_once( "Account.class.php" );

Class User extends Account {

	private $name;
	private $dateJoined;

	private $transactions = Array();

	function setName( $name ) {

		$this -> name = $name;

	}

	function getName() {

		return $this -> name;

	}

	function setDateJoined( $dateJoined ) {

		$this -> dateJoined = $dateJoined;

	}

	function getDateJoined() {

		return $this -> dateJoined;

	}

	function addTransaction( $transactionID ) {

		if( !in_array( $transactionID, $this -> transactions ) ) {

			array_push( $this -> transactions, $transactionID );

		}

	}

	function getTransactions() {

		return $this -> transactions;

	}

	function saveToDB( $returnType = 0 ) {

		GLOBAL $dbh;

		$query = '
INSERT INTO `userDetails` (
	  `uniqueID`
	, `name`
)
VALUES (
	  "' .  $this -> getUniqueID() . '"
	, "' . $this -> getName() . '"
)';

		switch( $returnType ) {

			case "0" :
			default : {		// return a boolean result

				$returnValue = false;

				parent::saveToDB();

				try {

					$dbh -> beginTransaction();

						$dbh -> exec( $query );

					$dbh -> commit();

					$returnValue = true;

				}
				catch( PDOException $e ) {

				   print "Error[ 101 ]: " . $e -> getMessage() . "<br/>";
				   die();

				}

			}
			break;

			case "1" : {	// return the query

				$returnValue = $query;

			}
			break;

		}

		return $returnValue;

	}

	function loadFromDB( $returnType = 0 ) {

		GLOBAL $dbh;

		$query = '
SELECT
	  `name`
	, `dateJoined`
FROM
	`userDetails`
WHERE
	`uniqueID` = "' . $this -> getUniqueID() . '"';

		$query_transactions = '
SELECT
	`uniqueID`
FROM
	`transactionDetails`
WHERE
	`userID` = "' . $this -> getUniqueID() . '"';

		switch( $returnType ) {

			case "0" :
			default : {		// return a boolean result

				$returnValue = false;

				parent::loadFromDB();

				try {

					$statement = $dbh -> prepare( $query );
					$statement -> execute();

					$row = $statement -> fetch();

					$this -> setName( $row[ "name" ] );
					$this -> setDateJoined( $row[ "dateJoined" ] );

					$statement = $dbh -> prepare( $query_transactions );
					$statement -> execute();

					$results = $statement -> fetchAll();

					foreach( $results as $result ) {

						$this -> addTransaction( $result[ "uniqueID" ] );

					}

					$returnValue = true;

				}
				catch( PDOException $e ) {

				   print "Error[ 102 ]: " . $e -> getMessage() . "<br/>";
				   die();

				}

			}
			break;

			case "1" : {	// return the query

				$returnValue = $query;

			}
			break;

		}

		return $returnValue;

	}

	function updateDB( $returnType = 0 ) {

		GLOBAL $dbh;

		$parentQuery = parent::updateDB( 1 );

		$query = '
UPDATE
	`userDetails`
SET
	  `name` = "' . $this -> getName() . '"
	, `dateJoined` = "' . $this -> getDateJoined() . '"
WHERE
	`uniqueID` = "' . $this -> getUniqueID() . '"';

		switch( $returnType ) {

			case "0" :
			default : {		// return a boolean result

				$returnValue = false;

				try {

					$statement = $dbh -> prepare( $parentQuery );
					$statement -> execute();

					$statement = $dbh -> prepare( $query );
					$statement -> execute();

					$returnValue = true;

				}
				catch( PDOException $e ) {

				   print "Error[ 103 ]: " . $e -> getMessage() . "<br/>";
				   die();

				}

			}
			break;

			case "1" : {	// return the query

				$returnValue = $query;

			}
			break;

		}

		return $returnValue;

	}

	function __construct( $uniqueID = "00000",
	                      $screenName = "",
	                      $password = "",
	                      $name = "",
	                      $email = "") {

		parent::__construct( $uniqueID, $screenName, $password, $email );

		if( $uniqueID == "00000" ) {

			if( $name != "" ) {

				$this -> setName( $name );

			}

		}
		else {

			$this -> loadFromDB();

		}

	}

}

function getUsers( $returnType = 0, $filter = "all" ) {

	GLOBAL $dbh;

	$query = '
SELECT
	  `userDetails`.`uniqueID`
FROM
	`userDetails`
INNER JOIN
	`accountDetails`
ON
	`userDetails`.`uniqueID` = `accountDetails`.`uniqueID`
WHERE';

/*
	$query = '
SELECT
	`uniqueID`
FROM
	`userDetails`
WHERE';
*/

	if( $filter == "all" ) {

		$query .= '
	1';

	}
	else {

		switch( $filter ) {

			case "pending" : {

				$query = '
	`accountDetails`.`status` = 0';

			}
			break;

			case "active" : {

				$query = '
	`accountDetails`.`status` = 1';

			}
			break;

			case "suspended" : {

				$query = '
	`accountDetails`.`status` = 2';

			}
			break;

		}

	}

	switch( $returnType ) {

		case "0" : {

			$returnValue = Array();

			try {

				$statement = $dbh -> prepare( $query );
				$statement -> execute();

				$results = $statement -> fetchAll();

				foreach( $results as $result ) {

					array_push( $returnValue, $result[ "uniqueID" ] );

				}

			}
			catch( PDOException $e ) {

			   print "Error!: " . $e -> getMessage() . "<br/>";
			   die();

			}


		}
		break;

		case "1" : {

			$returnValue = $query;

		}
		break;

	}

	return $returnValue;

}

function userExists( $returnType = 0, $userID ) {

	GLOBAL $dbh;

	$query = '
SELECT
	`uniqueID`
FROM
	`userDetails`
WHERE
	`uniqueID` = "' . $userID . '"';

	switch( $returnType ) {

		case "0" :
		default : {

			$returnValue = true;

			try {

				$statement = $dbh -> prepare( $query );
				$statement -> execute();

				$results = $statement -> fetchAll();

				if( count( $results ) > 0 ) {

					$returnValue = true;

				}

			}
			catch( PDOException $e ) {

			   print "Error!: " . $e -> getMessage() . "<br/>";
			   die();

			}


		}
		break;

		case "1" : {

			$returnValue = $query;

		}
		break;

	}

	return $returnValue;

}


?>
