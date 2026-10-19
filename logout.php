<?php
/* logout.php:
	1.) Aktuelle Logins entfernen: DELETE FROM logins.
*/
require __DIR__ . '/autoload.php';
use MK\DBT;
use MK\Logger;
use MK\Helper;
use MK\Session;
$config = include 'config.php';

Logger::info(__LINE__ . " logout.php Start -------------------------------------------------------------");
$app = 'logout.php';
$sess = new Session($app);
$sess->loggedin();
$fullname = $sess->getFullname();

$sess->logout();
Logger::info(__LINE__ . " $app - User '$fullname' logged out.");

$user = "$fullname"; // wird in src/head.php benötigt.
?>
<?php include 'src/head.php' ?>

<table class="content">
<tr><td class=title><h1>Logout</h1></td></tr>
<tr><td class=error><?php echo '<p style="color:red;">'.e($error).'</p>'; ?></td></tr>
<tr><td class=data><div class=logout> Tschüß <br> <?php echo $fullname ?> </div></td></tr>
</table>

<?php include 'src/foot.php' ?>
