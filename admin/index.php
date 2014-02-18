<?php

require_once( "../User.class.php" );
require_once( "../Product.class.php" );

session_start();

{ // page building variables

$pageTitle = 'Open Web Point Of Sale';

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

if( isset( $_SESSION[ "owp" ][ "admin" ][ "loggedIn" ] ) ) {

	$currentUser = new User( $_SESSION[ "owp" ][ "admin" ][ "loggedIn" ] );

{
	$pageBody .= '
				<div class="sideColumn">
					<ul>
						<li>
							<a href="?section=products">products</a>
							<ul>
								<li>
									<a href="?section=products&amp;action=list">list products</a>
								</li>
								<li>
									<a href="?section=products&amp;action=add">add new product</a>
								</li>
							</ul>
						</li>
						<li>
							<a href="?section=users">users</a>
							<ul>
								<li>
									<a href="?section=users&amp;action=list">list users</a>
								</li>
								<li>
									<a href="?section=users&amp;action=add">add new user</a>
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

					unset( $_SESSION[ "owp" ][ "admin" ][ "loggedIn" ] );

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
			<td>' . $_SESSION[ "owp" ][ "admin" ][ "loggedIn" ] . '</td>
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
			<th>price</th>
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
			<td>' . $product -> getPrice() . '</td>
			<td>
				<ul>
					<li>
						<a href="?section=products&amp;action=view&amp;target=' . $productID . '">view</a>
					</li>
					<li>
						<a href="?section=products&amp;action=list&amp;target=' . $productID . '">list</a>
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

				case "add" :
				case "new" : {

					if( isset( $_POST[ "name" ] ) ) {

						$product = new Product( "00000", $_POST[ "name" ], $_POST[ "price" ], $_POST[ "description" ] );

						if( $product -> saveToDB() ) {

							$pageBody .= '
<div class="dialog">
	<p>The following product</p>
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
				<th>price</th>
				<td>' . $product -> getPrice() . '</td>
			</tr>
			<tr>
				<th>description</th>
				<td>' . $product -> getDescription() . '</td>
			</tr>
		</tbody>
	</table>
	<p>was successfully added to the system</p>
</div>';

						}
						else {

							$pageBody .= '
<div class="dialog">
	<p>Could not save the product, please contact system admin</p>
</div>';

						}

					}
					else {

						$pageBody .= '
<div class="dialog">
	<form action="?section=products&amp;action=add"
	      method="post">
		<fieldset class="info">
			<legend>product details</legend>
			<div class="row">
				<label for="name">name</label>
				<input type="text"
				       name="name"
				       placeholder="what is the product called?"
				       required="required" />
			</div>
			<div class="row">
				<label for="price">price</label>
				<input type="number"
				       name="price"
				       placeholder="how much to sell it for?"
				       required="required" />
			</div>
			<div class="row">
				<label for="description">description</label>
				<textarea name="description"
				          placeholder="any extra information"></textarea>
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
			<th>price</th>
			<td>' . $product -> getPrice() . '</td>
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

				case "edit" : {

					if( isset( $_REQUEST[ "target" ] ) ) {

						if( productExists( 0, $_REQUEST[ "target" ] ) ) {

							$product = new Product( $_REQUEST[ "target" ] );

							if( isset( $_POST[ "name" ] ) ) {

								$product -> setName( $_POST[ "name" ] );
								$product -> setDescription( $_POST[ "description" ] );

								if( $product -> updateDB() ) {

									$pageBody .= '
<div class="dialog">
	<p>The following product</p>
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
		</tbody>
	</table>
	<p>was successfully updated on the system</p>
</div>';

								}
								else {

									$pageBody .= '
<div class="dialog">
	<p>Could not update the product, please contact system admin</p>
</div>';

								}

							}
							else {

								$pageBody .= '
<div class="dialog">
	<form action="?section=products&amp;action=edit"
	      method="post">
		<fieldset class="info">
			<legend>product details</legend>
			<input type="hidden"
			       name="target"
			       value="' . $_REQUEST[ "target" ] . '" />
			<div class="row">
				<label for="name">name</label>
				<input type="text"
				       name="name"
				       value="' . $product -> getName() . '"
				       required="required" />
			</div>
			<div class="row">
				<label for="description">description</label>
				<textarea name="description"
				          placeholder="any extra information">' . $product -> getDescription() . '</textarea>
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

				case "delete" : {

					if( isset( $_REQUEST[ "target" ] ) ) {

						if( productExists( $_REQUEST[ "target" ] ) ) {

							$product = new Product( $_REQUEST[ "target" ] );

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

		case "users" : {

			$pageBody .= '<h2>users</h2>';

			$action = "list";

			if( isset( $_REQUEST[ "action" ] ) ) {

				$action = $_REQUEST[ "action" ];

			}

			switch( $action ) {

				case "list" :
				default : {

					$users = getUsers( 0 );

					if( count( $users ) > 0 ) {

						$pageBody .= '
<table>
	<thead>
		<tr>
			<th>#</th>
			<th>user ID</th>
			<th>username</th>
			<th>name</th>
			<th>action</th>
		</tr>
	</thead>
	<tbody>';

						$count = 1;

						foreach( $users as $userID ) {

							$user = new User( $userID );

							$pageBody .= '
		<tr>
			<td>' . $count . '</td>
			<td>' . $user -> getUniqueID() . '</td>
			<td>' . $user -> getScreenName() . '</td>
			<td>' . $user -> getName() . '</td>
			<td>
				<ul>
					<li>
						<a href="?section=users&amp;action=view&amp;target=' . $user -> getUniqueID() . '">view</a>
					</li>
					<li>
						<a href="?section=users&amp;action=edit&amp;target=' . $user -> getUniqueID() . '">edit</a>
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
	<p>There are no users on this system</p>
</div>';

					}

				}
				break;

				case "view" : {

					if( isset( $_REQUEST[ "target" ] ) ) {

						$target = $_REQUEST[ "target" ];

						if( userExists( 0, $target ) ) {

							$user = new User( $target );

							$pageBody .= '
<table>
	<tbody>
		<tr>
			<th>unique ID</th>
			<td>' . $user -> getUniqueID() . '</td>
		</tr>
		<tr>
			<th>name</th>
			<td>' . $user -> getName() . '</td>
		</tr>
		<tr>
			<th>user name</th>
			<td>' . $user -> getScreenName() . '</td>
		</tr>
		<tr>
			<th>member since</th>
			<td>' . substr( $user -> getDateJoined(), 0, 10 ) . '</td>
		</tr>
	</tbody>
</table>';

						}
						else {

							$pageBody .= '
	<div class="dialog">
		<p>No such user on the system</p>
	</div>';

						}

					}
					else {

						$pageBody .= '
<div class="dialog">
	<p>You have to specify a user to view</p>
</div>';

					}

				}
				break;

				case "add" :
				case "new" : {

					if( isset( $_POST[ "screenname" ] ) && isset( $_POST[ "email" ] ) && isset( $_POST[ "name" ] ) ) {

						$password = genRandomString( 10 );

						$user = new User( "00000", $_POST[ "screenname" ], $password, $_POST[ "email" ], $_POST[ "name" ] );

						if( $user -> saveToDB() ) {

							$pageBody .= '
<div class="dialog">
	<p>The following user</p>
	<table>
		<tbody>
			<tr>
				<th>user ID</th>
				<td>' . $user -> getUniqueID() . '</td>
			</tr>
			<tr>
				<th>name</th>
				<td>' . $user -> getName() . '</td>
			</tr>
			<tr>
				<th>username</th>
				<td>' . $user -> getScreenName() . '</td>
			</tr>
			<tr>
				<th>password</th>
				<td>' . $user -> getPassword() . '</td>
			</tr>
		</tbody>
	</table>
	<p>was successfully added to the system</p>
</div>';

						}
						else {

							$pageBody .= '
<div class="dialog">
	<p>Could not save the user, please contact system admin</p>
</div>';

						}

					}
					else {

						$pageBody .= '
<div>
	<form action="?section=users&amp;action=add"
	      method="post">
		<fieldset class="info">
			<legend>user details</legend>
			<div class="row">
				<label for="name">name</label>
				<input type="text"
				       name="name"
				       placeholder="what is the user called?"
				       required="required" />
			</div>
			<div class="row">
				<label for="screenname">username</label>
				<input type="text"
				       name="screenname"
				       placeholder="preffered username"
				       required="required" />
			</div>
			<div class="row">
				<label for="email">email</label>
				<input type="email"
				       name="email"
				       placeholder="users email address"
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

				case "edit" : {

					if( isset( $_REQUEST[ "target" ] ) ) {

						$target = $_REQUEST[ "target" ];

						if( userExists( 0, $target ) ) {

							$user = new User( $target );

							if( isset( $_POST[ "screenname" ] ) && isset( $_POST[ "email" ] ) && isset( $_POST[ "name" ] ) ) {

								$user -> setScreenName( $_POST[ "screenname" ] );
								$user -> setEmail( $_POST[ "email" ] );
								$user -> setName( $_POST[ "name" ] );

								if( $user -> updateDB() ) {

									$pageBody .= '
<div class="dialog">
	<p>The following user&apos;s details were succesfully updated</p>
	<table>
		<tbody>
			<tr>
				<th>user ID</th>
				<td>' . $user -> getUniqueID() . '</td>
			</tr>
			<tr>
				<th>name</th>
				<td>' . $user -> getName() . '</td>
			</tr>
			<tr>
				<th>username</th>
				<td>' . $user -> getScreenName() . '</td>
			</tr>
			<tr>
				<th>password</th>
				<td>' . $user -> getPassword() . '</td>
			</tr>
		</tbody>
	</table>
</div>';

								}
								else {

									$pageBody .= '
<div class="dialog">
	<p>Could not save the user, please contact system admin</p>
</div>';

								}

							}
							else {

								$pageBody .= '
<div class="dialog">
	<form action="?section=users&amp;action=edit"
	      method="post">
		<fieldset class="info">
			<legend>user details</legend>
			<input type="hidden"
			       name="target"
			       value="' . $_REQUEST[ "target" ] . '" />
			<div class="row">
				<label for="name">name</label>
				<input type="text"
				       name="name"
				       value="' . $user -> getName() . '"
				       required="required" />
			</div>
			<div class="row">
				<label for="screenname">username</label>
				<input type="text"
				       name="screenname"
				       placeholder="preffered username"
				       value="' . $user -> getScreenName() . '"
				       required="required" />
			</div>
			<div class="row">
				<label for="email">email</label>
				<input type="email"
				       name="email"
				       placeholder="users email address"
				       value="' . $user -> getEmail() . '"
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
						else {

							$pageBody .= '
	<div class="dialog">
		<p>No such user on the system</p>
	</div>';

						}

					}
					else {

						$pageBody .= '
<div class="dialog">
	<p>You have to specify a user whose details to edit</p>
</div>';

					}

				}
				break;

			}

		}
		break;

		case "transactions" : {}
		break;

		case "entries" : {  /* Why??? */ }
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
	`accessLevel` = "0"
AND
	`screenName` = "' . $_REQUEST[ "screenName" ] . '"
AND
	`password` = MD5( "' . $_REQUEST[ "password" ] . '" )
';

						try {

							if( $result = $dbh -> query( $query ) ) {

								$results = $result -> fetchAll();

								if( count( $results ) == 1 ) {

									$_SESSION[ "owp" ][ "admin" ][ "loggedIn" ] = $results[ 0 ] [ "uniqueID" ];

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
