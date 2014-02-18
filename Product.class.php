<?php

require_once( "Base.class.php" );

Class Product extends Base {

	private $name;
	private $dateCreated;
	private $description;
	private $price = 0;

	function setName( $name ) {

		$this -> name = $name;

	}

	function getName() {

		return $this -> name;

	}

	function setDateCreated( $dateCreated ) {

		$this -> dateCreated = $dateCreated;

	}

	function getDateCreated() {

		return $this -> dateCreated;

	}

	function setDescription( $description ) {

		$this -> description = $description;

	}

	function getDescription() {

		return $this -> description;

	}

	function setPrice( $price ) {

		$this -> price = $price;

	}

	function getPrice() {

		return $this -> price;

	}

	function saveToDB( $returnType = 0 ) {

		GLOBAL $dbh;

		$query = '
INSERT INTO `productDetails` (
	  `uniqueID`
	, `name`
	, `description`
)
VALUES (
	  "' .  $this -> getUniqueID() . '"
	, "' . $this -> getName() . '"
	, "' . $this -> getDescription() . '"
)';

		$queryPrice = '
INSERT INTO `productPrices` (
	  `productID`
	, `value`
) 
VALUES (
	  "' . $this -> getUniqueID() . '"
	, "' . $this -> getPrice() . '"
)';

		switch( $returnType ) {

			case "0" :
			default : {		// return a boolean result

				$returnValue = false;

				try {

					$dbh -> beginTransaction();

						$dbh -> exec( $query );
						$dbh -> exec( $queryPrice );

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
	, `description`
	, `dateCreated`
FROM
	`productDetails`
WHERE
	`uniqueID` = "' . $this -> getUniqueID() . '"';
	
		$queryPrice = '
SELECT
	`value`
FROM
	`productPrices`
WHERE
	`productID` = "' . $this -> getUniqueID() . '"
ORDER BY
	`timestamp`';

		switch( $returnType ) {

			case "0" :
			default : {		// return a boolean result

				$returnValue = false;

				try {

					$statement = $dbh -> prepare( $query );
					$statement -> execute();

					$row = $statement -> fetch();

					$this -> setName( $row[ "name" ] );
					$this -> setDescription( $row[ "description" ] );
					$this -> setDateCreated( $row[ "dateCreated" ] );

					$statement = $dbh -> prepare( $queryPrice );
					$statement -> execute();

					$row = $statement -> fetch();
					
					$this -> setPrice( $row[ "value" ] );

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
	`productDetails`
SET
	  `name` = "' . $this -> getName() . '"
	, `description` = "' . $this -> getDescription() . '"
	, `dateCreated` = "' . $this -> getDateCreated() . '"
WHERE
	`uniqueID` = "' . $this -> getUniqueID() . '"';

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
	                      $name = "",
	                      $price = 0,
	                      $description = "" ) {

		parent::__construct( $uniqueID );

		if( $uniqueID == "00000" ) {

			if( $name != "" ) {

				$this -> setName( $name );

			}

			if( is_numeric( $price ) && $price > 0 ) {

				$this -> setPrice( $price );

			}

			$this -> setDescription( $description );

		}
		else {

			$this -> loadFromDB();

		}

	}

}

function getProducts( $returnType = 0, $filter = "all" ) {

	GLOBAL $dbh;

	$query = '
SELECT
	`uniqueID`
FROM
	`productDetails`
WHERE';

	if( $filter == "all" ) {

		$query .= '
	1';

	}
	else {

		// more to come?

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

function productExists( $returnType = 0, $productID ) {

	GLOBAL $dbh;

	$query = '
SELECT
	`uniqueID`
FROM
	`productDetails`
WHERE
	`uniqueID` = "' . $productID . '"';

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
