<?php

require_once( "./User.class.php" );

session_start();

{ // page building variables

$pageTitle = 'Open Web App : Point Of Sale';

$pageHeader = '
<!DOCTYPE html>
<html>
	<head>
		<title>' . $pageTitle . '</title>
		<link type="text/css"
		      rel="stylesheet"
		      href="../styling/main.css.php">
	</head>
	<body>
		<div class="wrapper">
			<div class="header">
			</div>
			<div class="body">';

$pageBody = '
<p>Are you lost?you were probably looking for the <a href="./admin/">admin</a> or the <a href="./user/">user</a> portal </p>';

$pageFooter = '
			</div>
			<div class="footer"></div>
		</div>
	</body>
</html>';


}

$format = "html";

if( isset( $_REQUEST[ "format" ] ) ) {

	$format = $_REQUEST[ "format" ];

}

switch( $format ) {

	case "html" :
	default : {

		$output = $pageHeader . $pageBody . $pageFooter;

	}
	break;

	case "ajax" : {

		$output = $pageBody;

	}
	break;

}

echo $output;


?>
