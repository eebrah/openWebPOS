<?php

require_once( "Base.class.php" );

Class Transaction extends Base {

	private $timeStamp;
	private $userID;

	private $entries = Array();

	private $status = 0;

	function setTimeStamp( $timeStamp ) {

		$this -> timeStamp = $timeStamp;

	}

	function getTimeStamp() {

		return $this -> timeStamp;

	}

	function setUser( $userID ) {

		$this -> userID = $userID;

	}

	function getUser() {

		return $this -> userID;

	}

	function addEntry( $entryID ) {

		if( !in_array( $entryID, $this -> entries ) ) {

			array_push( $this -> entries, $entryID );

		}

	}

	function getEntries() {

		return $this -> entries;

	}

	function setStatus( $status ) {

		$this -> status = $status;

	}

	function getStatus() {

		return $this -> status;

	}

	function saveToDB( $returnType = 0 ) {

		GLOBAL $dbh;

		$query = '
INSERT INTO `transactionDetails` (
	  `uniqueID`
	, `userID`
)
VALUES (
	  "' . $this -> getUniqueID() . '"
	, "' . $this -> getUser() . '"
)';

		switch( $returnType ) {

			case "0" :
			default : {		// return a boolean result

				$returnValue = false;

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
	  `userID`
	, `status`
	, `timestamp`
FROM
	`transactionDetails`
WHERE
	`uniqueID` = "' .  $this -> getUniqueID() . '"';

		$fetchEntries = '
SELECT
	`uniqueID`
FROM
	`entryDetails`
WHERE
	`transactionID` = "' .  $this -> getUniqueID() . '"';

		switch( $returnType ) {

			case "0" :
			default : {		// return a boolean result

				$returnValue = false;

				try {

					$statement = $dbh -> prepare( $query );
					$statement -> execute();

					$row = $statement -> fetch();

					$this -> setUser( $row[ "userID" ] );
					$this -> setTimeStamp( $row[ "timestamp" ] );
					$this -> setStatus( $row[ "status" ] );

					$statement = $dbh -> prepare( $fetchEntries );
					$statement -> execute();

					$results = $statement -> fetchAll();

					foreach( $results as $result ) {

						$this -> addEntry( $result[ "uniqueID" ] );

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

		$query = '
UPDATE
	`transactionDetails`
SET
	  `userID` = "' .  $this -> getUser() . '"
	, `timestamp` = "' .  $this -> getTimeStamp() . '"
	, `status` = "' .  $this -> getStatus() . '"
WHERE
	`uniqueID` = "' .  $this -> getUniqueID() . '"';

		switch( $returnType ) {

			case "0" :
			default : {		// return a boolean result

				$returnValue = false;

				try {

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
	                      $userID = "" ) {

		parent::__construct( $uniqueID );

		if( $uniqueID == "00000" ) {

			if( $userID != "" ) {

				$this -> setUser( $userID );

			}

		}
		else {

			$this -> loadFromDB();

		}

	}

}

function getTransactions( $returnType = 0 ) {

	GLOBAL $dbh;

	$query = '
SELECT
	`uniqueID`
FROM
	`transactionDetails`
WHERE
	1
ORDER BY
	`timestamp` DESC';

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

function transactionExists( $returnType = 0, $target ) {

	GLOBAL $dbh;

	$query = '
SELECT
	COUNT( * ) as "count"
FROM
	`transactionDetails`
WHERE
	`uniqueID` = "' . $target . '"';

	switch( $returnType ) {

		case "0" : {

			$returnValue = false;

			try {

				$statement = $dbh -> prepare( $query );
				$statement -> execute();

				$row = $statement -> fetch();

				if( $row[ "count" ] == 1 ) {

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
