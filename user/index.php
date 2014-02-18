<?php

require_once( "../User.class.php" );
require_once( "../Product.class.php" );
require_once( "../Entry.class.php" );
require_once( "../Transaction.class.php" );

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

$pageBody = '';

$pageFooter = '
			</div>
			<div class="footer"></div>
		</div>
	</body>
</html>';


}

if( isset( $_SESSION[ "owp" ][ "user" ][ "loggedIn" ] ) ) {

	$currentUser = new User( $_SESSION[ "owp" ][ "user" ][ "loggedIn" ] );

{
	$pageBody .= '
				<div class="sideColumn">
					<ul>
						<li>
							<a href="?section=transactions">transactions</a>
							<ul>
								<li>
									<a href="?section=transactions&amp;action=list">list transactions</a>
								</li>
								<li>
									<a href="?section=transactions&amp;action=add">start new transaction</a>
								</li>
							</ul>
						</li>
						<li>
							<a href="?section=profile">profile</a>
							<ul>
								<li>
									<a href="?section=profile&amp;action=view">view profile</a>
								</li>
								<li>
									<a href="?section=access">log out</a>
								</li>
							</ul>
						</li>
					</ul>
				</div>
				<div class="mainColumn">';
}

	$section = "profile";

	if( isset( $_REQUEST[ "section" ] ) ) {

		$section = $_REQUEST[ "section" ];

	}

	switch( $section ) {

		case "access" : {

			$action = "logOut";

			if( isset( $_REQUEST[ "action" ] ) ) {

				$action = $_REQUEST[ "action" ];

			}

			switch( $action ) {

				case "logOut" :
				default : {

					unset( $_SESSION[ "owp" ][ "user" ][ "loggedIn" ] );

					if( isset( $_SESSION[ "owp" ][ "transaction" ] ) ) {

						unset( $_SESSION[ "owp" ][ "transaction" ] );

					}

					// Redirect
					$host = $_SERVER[ 'HTTP_HOST' ];
					$uri = rtrim( dirname( $_SERVER[ 'PHP_SELF' ] ), '/\\' );

					// If no headers are sent, send one
					if( !headers_sent() ) {

						header( "Location: http://" . $host . $uri . "/" );
						exit;

					}

				}
				break;

				case "unRegister" : {

					// Ask user to confirm

				}
				break;

			}

		}
		break;

		case "profile" :
		default : {

			$pageBody .= '<h2>profile</h2>';

			$action = "view";

			if( isset( $_REQUEST[ "action" ] ) ) {

				$action = $_REQUEST[ "action" ];

			}

			switch( $action ) {

				case "view" :
				default : {

					$pageBody .= '
<table>
	<tbody>
		<tr>
			<th>unique ID</th>
			<td>' . $_SESSION[ "owp" ][ "user" ][ "loggedIn" ] . '</td>
		</tr>
		<tr>
			<th>name</th>
			<td>' . $currentUser -> getName() . '</td>
		</tr>
		<tr>
			<th>user name</th>
			<td>' . $currentUser -> getScreenName() . '</td>
		</tr>
	</tbody>
</table>';

				}
				break;

				case "edit" : {

					if( isset( $_POST[ "name" ] ) && isset( $_POST[ "screenName" ] ) ) {

						//Process the data

					}
					else {

						$pageBody .= '
<div class="dialog">
	<form action="?section=profile&amp;action=edit"
	      method="post">
		<fieldset class="info">
			<legend>personal details</legend>
			<div class="row">
				<label for="screenName">username</label>
				<input type="text"
				       name="screenName"
				       value="' . $currentUser -> getScreenName() . '"
				       pattern="^[a-zA-Z][a-zA-Z0-9-_\.]{3,20}$"
				       title="must be between 3 and 20 characters long, acn only contain letters, numbers, - and _ and ." />
			</div>
			<div class="row">
				<label for="name">name</label>
				<input type="text"
				       name="name"
				       value="' . $currentUser -> getName() . '"
				       pattern="^[a-zA-Z][a-zA-Z0-9-_\.]{3,80}$"
				       title="must be between 3 and 20 characters long, acn only contain letters" />
			</div><!--
			<div class="row">
				<label for="password0">password</label>
				<input type="password"
				       name="password0"
				       placeholder=""
				       pattern="^[a-zA-Z][a-zA-Z0-9-_\.]{3,80}$"
				       title="must be between 3 and 80 characters long" />
			</div>
			<div class="row">
				<label for="password1">confirm password</label>
				<input type="password"
				       name="password1"
				       placeholder=""
				       pattern="" />
			</div> -->
		</fieldset>
		<fieldset class="buttons">
			<button type="reset">reset</button>
			<button type="submit">submit</button>
		</fieldset>
	</form>
</div>';
					}

				}
				break;

			}

		}
		break;

		case "products" : {

			$pageBody .= '<h2>products</h2>';

			$action = "list";

			if( isset( $_REQUEST[ "action" ] ) ) {

				$action = $_REQUEST[ "action" ];

			}

			switch( $action ) {

				case "list" :
				default : {

					$products = getProducts();

					if( count( $products ) > 0 ) {

						$pageBody .= '
<table>
	<thead>
		<tr>
			<th>#</th>
			<th>ID</th>
			<th>name</th>
			<th>action</th>
		</tr>
	</thead>
	<tbody>';

						$count = 1;

						foreach( $products as $productID ) {

							$product = new Product( $productID );

							$pageBody .= '
		<tr>
			<td>' . $count . '</td>
			<td>' . $product -> getUniqueID() . '</td>
			<td>' . $product -> getName() . '</td>
			<td>
				<ul>
					<li>
						<a href="?section=products&amp;action=view&amp;target=' . $productID . '">view</a>
					</li>
				</ul>
			</td>
		</tr>';

							$count++;

						}

						$pageBody .= '
	</tbody>
</table>';

					}
					else {

						$pageBody .= '
<div class="dialog">
	<p>You have no products matching that criteria</p>
</div>';

					}

				}
				break;

				case "view" : {

					if( isset( $_REQUEST[ "target" ] ) ) {

						if( productExists( 0, $_REQUEST[ "target" ] ) ) {

							$product = new Product( $_REQUEST[ "target" ] );

							$pageBody .= '
<table>
	<tbody>
		<tr>
			<th>product ID</th>
			<td>' . $product -> getUniqueID() . '</td>
		</tr>
		<tr>
			<th>name</th>
			<td>' . $product -> getName() . '</td>
		</tr>
		<tr>
			<th>description</th>
			<td>' . $product -> getDescription() . '</td>
		</tr>
		<tr>
			<th>date added</th>
			<td>' . $product -> getDateCreated() . '</td>
		</tr>
	</tbody>
</table>';

						}
						else {

							$pageBody .= '
<div class="dialog">
	<p>The product you provided is not on the system</p>
	<p>please check that you provided the correct product ID and contact the system administartor if the problem persists</p>
</div>';

						}

					}
					else {

						$pageBody .= '
<div class="dialog">
	<p>You have to specify a product</p>
</div>';

					}

				}
				break;

			}

		}
		break;

		case "transactions" : {

			$action = "list";

			if( isset( $_REQUEST[ "action" ] ) ) {

				$action = $_REQUEST[ "action" ];

			}

			switch( $action ) {

				case "new" :
				case "add" : {

					if( isset( $_SESSION[ "owp" ][ "transaction" ] ) ) {

						$transaction = new Transaction( $_SESSION[ "owp" ][ "transaction" ] );

						$pageBody .= '
<div class="dialog">
	<p>You have a <a href="?section=transaction&amp;action=view&amp;target=' . $_REQUEST[ "transaction" ] . '">pending</a> transaction, please complete or destroy it before attempting a new one</p>
</div>';

					}
					else {

						$transaction = new Transaction();

						if( $transaction -> saveToDB() ) {

							$_SESSION[ "owp" ][ "transaction" ] = $transaction -> getUniqueID();

							// Redirect
							$host = $_SERVER[ 'HTTP_HOST' ];
							$uri = rtrim( dirname( $_SERVER[ 'PHP_SELF' ] ), '/\\' );

							// If no headers are sent, send one
							if( !headers_sent() ) {

								header( "Location: http://" . $host . $uri . "/" . "?section=entries&action=new" );
								exit;

							}

						}
						else {

							$pageBody .= '
<div class="dialog">
	<p>Could not initiate transaction</p>
</div>';

						}

					}

				}
				break;

				case "end" :
				case "checkout" : {

					if( isset( $_SESSION[ "owp" ][ "transaction" ] ) ) {

						$transaction = new Transaction( $_SESSION[ "owp" ][ "transaction" ] );

						if( count( $transaction -> getEntries() ) > 0 ) {

							if( $transaction -> updateDB() ) {		// Update transaction details on the database indicating that the transaction was successfully completed

								$transaction -> setStatus( 1 );

								$pageBody .= '
<div class="dialog modal">' . $transaction -> genReport() . '</div>';

							}
							else {

								// Checkout not successfull

								$pageBody .= '
<div class="dialog modal">
	<p>There was a problem saving the transaction, please contact a system admin</p>
</div>';

							}

						}
						else {

							if( $transaction -> deleteFromDB() ) {

								// Succesfully deleted

							}
							else {

								// :( shida fulani

							}

						}

					}
					else {

						$pageBody .= '
<div class="dialog">
	<p>No transaction in progress</p>
</div>';

					}

					unset( $_SESSION[ "owp" ][ "transaction" ] );

					// redirect to home page

				}
				break;

				case "view" : {

					if( isset( $_REQUEST[ "target" ] ) ) {

						if( transactionExists( 0, $_REQUEST[ "target" ] ) ) {

							$transaction = new Transaction( $_REQUEST[ "target" ] );

							if( count( $transaction -> getEntries() ) > 0 ) {

								$pageBody .= '
<table>
	<thead>
		<tr>
			<th>#</th>
			<th>name</th>
			<th>@</th>
			<th>quantity</th>
			<th>total</th>
		</tr>
	</thead>
	<tbody>';

								$count = 1;

								foreach( $transaction -> getEntries() as $entryID ) {

									$entry = new Entry( $entryID );
									$product = new Product( $entry -> getProduct() );

									$pageBody .= '
		<tr>
			<td>' . $count . '</td>
			<td>' . $product -> getName() . '</td>
			<td>' . $product -> getPrice() . '</td>
			<td>' . $entry -> getQuantity() . '</td>
			<td>' . ( $product -> getPrice() * $entry -> getQuantity() ) . '</td>
		</tr>';

									$count++;

								}

								$pageBody .= '
	</tbody>
</table>';

							}
							else {

								$pageBody .= '
<div class="dialog">
	<p>No items in your basket</p>
</div>';

							}

						}
						else {

							$pageBody .= '
<div class="dialog">
	<p>No such transaction in our records</p>
</div>';

						}

					}
					else {

						$pageBody .= '
<div class="dialog">
	<p>you need to specify a target</p>
</div>';

					}

				}
				break;

				case "list" : {

					if( count( $currentUser -> getTransactions() ) > 0 ) {

						$pageBody .= '
<table>
	<thead>
		<tr>
			<th>#</th>
			<th>transaction d</th>
			<th>date</th>
			<th>actions</th>
		</tr>
	</thead>
	<tbody>';

						$count = 1;

						foreach( $currentUser -> getTransactions() as $transactionID ) {

							$transaction = new Transaction( $transactionID );

							$pageBody .= '
		<tr>
			<td>' . $count . '</td>
			<td>' . $transaction -> getUniqueID() . '</td>
			<td>' . $transaction -> getTimeStamp(). '</td>
			<td>
				<ul>
					<li>
						<a href="?section=transactions&amp;action=view&amp;target=' . $transactionID . '">view</a>
					</li>
				</ul>
			</td>
		</tr>';

							$count++;

						}

						$pageBody .= '
	</tbody>
</table>';

					}
					else {

						$pageBody .= '
<div class="dialog">
	<p>You have no recorded transactions</p>
</div>';

					}

				}
				break;

			}

		}
		break;

		case "entries" : {

			$action = "new";

			if( isset( $_REQUEST[ "action" ] ) ) {

				$action = $_REQUEST[ "action" ];

			}

			switch( $action ) {

				case "new" :
				case "add" :
				default : {

					$products = getProducts();

					if( !isset( $_SESSION[ "owp" ][ "transaction" ] ) ) {

						$transaction = new Transaction( "00000", $_SESSION[ "owp" ][ "user" ][ "loggedIn" ] );

						if( $_SESSION[ "owp" ][ "transaction" ] = $transaction -> getUniqueID() ) {

							$transaction -> saveToDB();

						}

					}

					$pageBody .= '
<p>Transaction ID: ' . $_SESSION[ "owp" ][ "transaction" ] .  '</p>';

					if( isset( $_REQUEST[ "productID" ] ) ) {

						$product = explode( " ", trim( $_REQUEST[ "productID" ] ) );

						$productID = $product[ 0 ];

						if( productExists( 0, $productID ) ) {

							$product = new Product( $productID );

							$quantity = 1;

							if( isset( $_POST[ "quantity" ] ) ) {

								$quantity = $_POST[ "quantity" ];

							}

							$entry = new Entry( "00000", $_SESSION[ "owp" ][ "transaction" ], $productID, $quantity );

							if( $entry -> saveToDB() ) {

								// Yay!

							}
							else {

								// :(

							}

						}
						else {

							$pageBody .= '
<div class="dialog modal">
	<p>This item is not on the system</p>
</div>';

						}

					}
					else {

						$pageBody .= '
<div class="dialog">
	<form action="?section=entries&amp;action=new"
	      method="POST">
		<fieldset class="info">
			<legend>entry details</legend>
			<div class="row">
				<label>product</label>';
/*
						$pageBody .= '
				<input type="text"
				       name="productID"
				       placeholder="product"
				       list="productList" />
				<datalist id="productList">';

						foreach( $products as $productID ) {

							$product = new Product( $productID );

							$pageBody .= '
					<option value="' . $productID . ' ' . $product -> getName() . '" />';

						}

						$pageBody .= '
				</datalist>';
*/

						$pageBody .= '
				<select name="productID">';

						foreach( $products as $productID ) {

							$product = new Product( $productID );

							$pageBody .= '
					<option value="' . $productID . '">' . $product -> getName() . '</option>';

						}

						$pageBody .= '
				</select>';

						$pageBody .= '
			</div><!--
			<div class="row">
				<label  for="quantity">quantity</label>
				<input type="text"
				       name="quantity"
				       value="1"
				       required="required" />
			</div> -->
		</fieldset>
		<fieldset class="buttons">
			<button type="reset">reset</button>
			<button type="submit">submit</button>
		</fieldset>
	</form>
</div>';

					}

				}
				break;

				case "view" : {}
				break;

				case "list" : {

					if( isset( $_SESSION[ "owp" ][ "transaction" ] ) ) {

						if( transactionExists( $_SESSION[ "owp" ][ "transaction" ] ) ) {

							$transaction = new Transaction( $_SESSION[ "owp" ][ "transaction" ] );

							// $entries = $transaction -> getEntries();

							if( count( $transaction -> getEntries() ) > 0 ) {

								$pageBody .= '
<table>
	<thead>
		<tr>
			<th>#</th>
			<th>name</th>
			<th>@</th>
			<th>quantity</th>
			<th>total</th>
		</tr>
	</thead>
	<tbody>';

									$count = 1;

									foreach( $transaction -> getEntries() as $entryID ) {

										$entry = new Entry( $entryID );
										$product = new Product( $entry -> getProduct() );

										$pageBody .= '
		<tr>
			<td>' . $count . '</td>
			<td>' . $product -> getName() . '</td>
			<td>' . $product -> getPrice() . '</td>
			<td>' . $entry -> getQuantity() . '</td>
			<td>' . ( $product -> getPrice() * $entry -> getQuantity ) . '</td>
		</tr>';

										$count++;

									}

									$pageBody .= '
	</tbody>
</table>';

								}
								else {

								$pageBody .= '
<div class="dialog modal">
	<p>This transaction "' . $_SESSION[ "owp" ][ "transaction" ] . '"has no entries</p>
</div>';

							}

						}
						else {

						$pageBody .= '
<div class="dialog modal">
	<p>This transaction "' . $_SESSION[ "owp" ][ "transaction" ] . '", is not on record</p>
</div>';

						}

					}
					else {

						$pageBody .= '
<div class="dialog">
	<p>You do not have an active transaction running</p>
</div>';

					}

				}
				break;

			}

		}
		break;

	}

	$pageBody .= '
				</div>';

}
else {

	$section = "access";

	if( isset( $_REQUEST[ "section" ] ) ) {

		$section = $_REQUEST[ "section" ];

	}

	switch( $section ) {

		case "access" : {

			$action = "logIn";

			if( isset( $_REQUEST[ "action" ] ) ) {

				$action = $_REQUEST[ "action" ];

			}

			switch( $action ) {

				case "logIn" : {

					if( isset( $_REQUEST[ "screenName" ] ) && isset( $_REQUEST[ "password" ] ) ) {

						$query = '
SELECT
	`uniqueID`
FROM
	`accountDetails`
WHERE
	`status` = "1"
AND
	`accessLevel` > 0
AND
	`screenName` = "' . $_REQUEST[ "screenName" ] . '"
AND
	`password` = MD5( "' . $_REQUEST[ "password" ] . '" )
';

						try {

							if( $result = $dbh -> query( $query ) ) {

								$results = $result -> fetchAll();

								if( count( $results ) == 1 ) {

									$_SESSION[ "owp" ][ "user" ][ "loggedIn" ] = $results[ 0 ] [ "uniqueID" ];

								}
								else {

									// More than one matching entry, something is very wrong

									$pageBody .= '<p>There were multiple entries</p>';

								}

							}
							else {

								$pageBody .= '
<div class="message">
<h4>Log In Error : 001</h4>
<p>There was an Error trying to log you in :(</p>
<p>Please contact the administrator if this persists</p>
</div>';

							}

						}
						catch( PDOException $e ) {

							print "Error!: " . $e -> getMessage() . "<br/>";

							die();

						}

						// Redirect
						$host = $_SERVER[ 'HTTP_HOST' ];
						$uri = rtrim( dirname( $_SERVER[ 'PHP_SELF' ] ), '/\\' );

						// If no headers are sent, send one
						if( !headers_sent() ) {

							header( "Location: http://" . $host . $uri . "/" );
							exit;

						}

					}
					else {

						$pageBody .= '
<div class="dialog" style="width: 30em; margin: 5em auto;">
	<form action="?section=access&amp;action=logIn"
	      method="post">
		<fieldset class="info">
			<legend>log in</legend>
			<div class="row">
				<label for="screenname">username</label>
				<input type="text"
				       name="screenName"
				       placeholder="your username"
					   required="required" />
			</div>
			<div class="row">
				<label for="password">password</label>
				<input type="password"
				       name="password"
				       placeholder="your password"
					   required="required" />
			</div>
		</fieldset>
		<fieldset class="buttons">
			<button type="reset">reset</button>
			<button type="submit">submit</button>
		</fieldset>
	</form>
</div>';

					}

				}
				break;

			}

		}
		break;

	}

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
