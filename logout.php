<?php
/* logout.php: */
require __DIR__ . '/autoload.php';
use MK\Helper;
use MK\Init;

$app = 'logout.php';
[$sess,$error,$db] = Init::initialize($app);
$log = $GLOBALS['log'];

$log->info(__LINE__ . " $app ---------- Logout -----------------");
$fullname = $sess->logout();
$log->info(__LINE__ . " $app - User '".$sess->getFullname()."' logged out.");
if(!isset($user)) $user = "Not logged in!"; // $user wird in src/head.php benötigt.
?>
<?php include 'src/head.php' ?>
<table class="content">
<tr><td class=title><h1>Logout</h1></td></tr>
<tr><td class=data><div class=logout> Tschüß <br> <?php echo $fullname ?> </div></td></tr>
</table>
<?php include 'src/foot.php' ?>

