<?php
// login.php - Handles Login.
require __DIR__ . '/autoload.php';
use MK\DBT;
use MK\Logger;
use MK\Helper;
use MK\Session;
$config = include 'config.php';

Logger::$LOGFILE = $config['log'];
Logger::$DEBUG = true;

/* Login:
	1.) Hole Benutzername und Passwort aus Datenbank.
	2.) Überprüfe Passwort und setze SESSION.
	3.) Aktuelle Logins setzen: INSERT INTO logins.
	4.) Ablauf Phase "login" in Table "active_game" setzen.
	5.) Gehe zur Hauptseite.
*/

Logger::info(__LINE__ . " login.php Start -------------------------------------------------------------");
$app = 'login.php';
$sess = new Session('login.php');
$from = $sess->getFrom();

$db = new DBT($config['sqluser'],null,$config['dbuser']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
	Logger::debug(__LINE__ . " $app - User '$username' logging in.");

	try {
		// ---------- 1.) Hole Benutzername und Passwort aus Datenbank "user".
		$ret = $db->fetch('users_query', [$username]);
		$user = $ret['data'];
		Logger::debug(__LINE__ . " $app - User '$username' in database: " . json_encode($ret));

		// ---------- 2.) Überprüfe Passwort und setze SESSION
		if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
			$sess->set($user['id'], $username, $user['fullname']);		// Set to $_SESSION and to Session().
			Logger::debug(__LINE__ . " $app - User '$username' successful verified! _SESSION=" . json_encode($_SESSION));

			// ---------- 3.) Aktuelle Logins setzen: INSERT INTO logins.
			$ret = $db->fetch('logins_insert', [$user['id'], $username, $user['fullname']]);
			Logger::debug(__LINE__ . " $app - User '$username' (" . json_encode($ret) . ") logged in.");

			// ---------- 4.) Ablauf Phase "login" in Table "active_game" setzen.
			$ret = $db->fetch('active_game_init', [$user['id'], 'login']);
			Logger::debug(__LINE__ . " $app - User '$username' Ablauf Phase (" . json_encode($ret) . ")");

			// ---------- 5.) Gehe zur Hauptseite.
			Logger::info(__LINE__ . " $app - Zurück zu '" . $from . "'!");
			header("Location: $from");
			exit;
		} else {
			$error = 'Ungültiger Benutzername oder Passwort.';
			Logger::error("$app - User '$username' not logged in. error=" . $error);
		}
	} catch(Exception $e) { 
		$error = 'Fehler beim Login: ' . $e->getMessage(); 
		Logger::error("$app - $error");
	}
}
if(!$user) $user = "Not logged in!";
?>
<?php include 'src/head.php' ?>

<table class="content">
<tr><td class=title colspan=2><h1>Login</h1></td></tr>
<?php echo '<p style="color:red;">'.Helper::e($error).'</p>'; ?>
<form method="post" action="login.php">
  <tr><td><label for="username">Benutzername: </label</td><td><input name="username" required></td></tr>
  <tr><td><label for="password">Passwort:     </label</td><td><input name="password" type="password" required></td></tr>
  <tr><td class=button colspan=2><button type="submit">Login</button></td></tr>
</form>
</table>

<?php include 'src/foot.php' ?>
