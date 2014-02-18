<?php

require_once( "Base.class.php" );

Class Entry extends Base {

	private $transactionID;
	private $productID;

	private $quantity = 1;

	function setTransaction( $transactionID ) {

		$this -> transactionID = $transactionID;

	}

	function getTransaction() {

		return $this -> transactionID;

	}

	function setProduct( $productID ) {

		$this -> productID = $productID;

	}

	function getProduct() {

		return $this -> productID;

	}

	function setQuantity( $quantity ) {

		$this -> quantity = $quantity;

	}

	function getQuantity() {

		return $this -> quantity;

	}

	function saveToDB( $returnType = 0 ) {

		GLOBAL $dbh;

		$query = '
INSERT INTO `entryDetails` (
	  `uniqueID`
	, `transactionID`
	, `productID`
	, `quantity`
)
VALUES (
	  "' . $this -> getUniqueID() . '"
	, "' . $this -> getTransaction() . '"
	, "' . $this -> getProduct() . '"
	, "' . $this -> getQuantity() . '"
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
	  `transactionID`
	, `productID`
	, `quantity`
FROM
	`entryDetails`
WHERE
	`uniqueID` = "' .  $this -> getUniqueID() . '"';

		switch( $returnType ) {

			case "0" :
			default : {		// return a boolean result

				$returnValue = false;

				try {

					$statement = $dbh -> prepare( $query );
					$statement -> execute();

					$row = $statement -> fetch();

					$this -> setTransaction( $row[ "transactionID" ] );
					$this -> setProduct( $row[ "productID" ] );
					$this -> setQuantity( $row[ "quantity" ] );

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
	`entryDetails`
SET
	  `transactionID` = "' .  $this -> getUser() . '"
	, `productID` = "' .  $this -> getTimeStamp() . '"
	, `quantity` = "' .  $this -> getStatus() . '"
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
	                      $transactionID = "",
	                      $productID = "",
	                      $quantity = 0 ) {

		parent::__construct( $uniqueID );

		if( $uniqueID == "00000" ) {

			if( $transactionID != "" ) {

				$this -> setTransaction( $transactionID );

			}

			if( $productID != "" ) {

				$this -> setProduct( $productID );

			}

			if( $quantity != 0 ) {

				$this -> setQuantity( $quantity );

			}

		}
		else {

			$this -> loadFromDB();

		}

	}

}


?>
