<?php

$DEFAULT_UNIQUE_ID = "00000";

require_once( "DBConfig.php" );

date_default_timezone_set( "Africa/Nairobi" );

try {

   $dbh = new PDO( 'mysql:host=' . $DBHost . ';dbname=' . $DBName, $DBUser, $DBPass, array( PDO::ATTR_PERSISTENT => true ) );
   $dbh -> setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

}
catch( PDOException $e ) {

   print "Error!: " . $e -> getMessage();

   die();

}

Class Base {

	private $uniqueID;

	function setUniqueID( $uniqueID ) {

		$this -> uniqueID = $uniqueID;

	}

	function getUniqueID() {

		return $this -> uniqueID;

	}

	function genUniqueID( $length = 5, $seed = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' ) {

		$returnValue = '00000';

		for( $i = 0; $i < $length; $i++ ) {

			$returnValue[ $i ] = $seed[ rand( 0, strlen( $seed ) - 1 ) ];

		}

		$this -> setUniqueID( $returnValue );

	}

	function __construct( $uniqueID = "00000" ) {

		if( $uniqueID == "00000" ) {

			$this -> genUniqueID();

		}
		else {

			$this -> setUniqueID( $uniqueID );

		}

	}

}

function genRandomString( $length = 5, $seed = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' ) {

	$returnValue = '00000';

	for( $i = 0; $i < $length; $i++ ) {

		$returnValue[ $i ] = $seed[ rand( 0, strlen( $seed ) - 1 ) ];

	}

	return $returnValue;

}

?>
