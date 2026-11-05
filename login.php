<?php
// login.php - Handles Login.
require __DIR__ . '/autoload.php';
use MK\Helper;
use MK\Init;

$app = 'login.php';
[$sess,$error,$db] = Init::initialize($app);
$log = $GLOBALS['log'];

$log->info(__LINE__ . " $app ---------- Login -----------------");
/* Login:
	1.) Hole Benutzername und Passwort aus Datenbank.
	2.) Überprüfe Passwort und setze SESSION.
	3.) Aktuelle Logins setzen: INSERT INTO logins.
	4.) Ablauf Phase "login" in Table "active_game" setzen.
	5.) Gehe zur Hauptseite.
*/

$from = $sess->getFrom();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
	$log->debug(__LINE__ . " $app - User '$username' logging in.");

	try {
		// ---------- 1.) Hole Benutzername und Passwort aus Datenbank "user".
		$ret = $db->fetch('users_query', [$username]);
		$user = $ret['data'];
		$log->debug(__LINE__ . " $app - User '$username' in database: " . json_encode($ret));

		// ---------- 2.) Überprüfe Passwort und setze SESSION
		if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
			$sess->set($user['id'], $username, $user['fullname']);		// Set to $_SESSION and to Session().
			$log->debug(__LINE__ . " $app - User '$username' successful verified! _SESSION=" . json_encode($_SESSION));

			// ---------- 5.) Gehe zur Hauptseite.
			$log->info(__LINE__ . " $app - Zurück zu '" . $from . "'!");
			header("Location: $from");
			exit;
		} else {
			$error = 'Ungültiger Benutzername oder Passwort.';
			$log->error("$app - User '$username' not logged in. error=" . $error);
		}
	} catch(Exception $e) { 
		$error = 'Fehler beim Login: ' . $e->getMessage(); 
		$log->error("$app - $error");
	}
}
if(!isset($user)) $user = "Not logged in!"; // $user wird in src/head.php benötigt.
?>
<?php include 'src/head.php' ?>

<form method="post" action="login.php">
<table class="content">
<tr><td class=title colspan=2><h1>Login</h1></td></tr>
  <tr><td><label for="username">Benutzername: </label></td><td><input name="username" required></td></tr>
  <tr><td><label for="password">Passwort:     </label></td><td><input name="password" type="password" required></td></tr>
  <tr><td class=button colspan=2><button type="submit">Login</button></td></tr>
</table>
</form>

<?php include 'src/foot.php' ?>
