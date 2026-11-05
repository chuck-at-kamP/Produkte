<?php
/**
 * index.php - Item creation page.  Users can create a new item.
 */
require_once __DIR__ . "/src/App.php";
require_once __DIR__ . "/src/Upload.php";

require __DIR__ . '/autoload.php';
use MK\Logger;
use MK\Helper;
use MK\Init;

$app = 'index.php';
[$sess,$error,$db,$init] = Init::initialize($app);
$log = $GLOBALS['log'];

$log->info(__LINE__ . " $app ---------- Post Handling -----------------");
$user = $sess->getFullname();
$from = $sess->getFrom();
$action = $app;

// ---- Session post: to preserve values for GUI.
if(!isset($_SESSION['post'])) $_SESSION['post'] = []; 	// Init Session post.
if($from !== $app) {
	$_SESSION['post'] = [];								// Reset preserved values.
}
$post = array_merge($_SESSION['post'],$_POST);			// Post for preserved values and changed values.
$_SESSION['post'] = $post;								// Preserve values in "Session Post"
Logger::info(__LINE__ . " $app IN Session='" . $sess . "'\n----\nPOST=" . json_encode($_POST) . "\n----\npost=" . json_encode($post) . "\n-----");

// ---- Submit wird im POST in index.php ausgewertet ----
// action "index.php"
// submit_category "changed"  - Category geändert, Subcategory anpassen.
// submit_edit "save"         - Daten sichern.
// ------------------------------------------------------

// Init Vars
$item = [];		// New Item
$selected = []; // Selected from lists.

$log->info(__LINE__ . " $app ---------- Application -----------------");
$data = new App($db,$error,$init->getConfig());
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Handle new item creation

	if(isset($_POST['submit_edit']) && $_POST['submit_edit'] === 'save') {	// Save Item
		$data->insertArtikel($post,$_FILES);
	}
	if(isset($_POST['submit_category']) && $_POST['submit_category'] === 'changed') { // Change Category
		$selected['category'] = $_POST['category'];
		$item = $data->modifyChangedValues($_POST,$_FILES);
		$item['category'] = $_POST['category'];
	}
}

$log->info(__LINE__ . " $app ---------- Build Html -----------------");

// ---------- Get ENUMs from Database for select options. 
$enums = $data->getEnums($selected['category'],$init->getConfig('subcategory'));
Logger::debug(__LINE__ . " $app Enums: " . json_encode($enums));

$enum = ['category','gender','subcategory','state'];
$item = $data->init_items($item);	// Errors "Undefined array key"

$button_name  = "Sichern";
$button_value = "save";
?>

<!-- ========================= HTML ========================= -->
<?php include 'src/head.php' ?>
<table class="content">
    <tr><td class="title" colspan=2><h1>Einrichten</h1></td></tr>
<!-- ======================== Fehlerausgabe ======================================= -->
	<tr><td class="error" colspan=2 style="text-align:center;"><?php echo $error->getMessages(); ?></td></tr>
	<tr><td class="error" colspan=2 style="text-align:center;"><?php echo $error->getTable(); ?></td></tr>
<!-- ====================== Eingabe ========================= -->
	<tr><td colspan=2>
		<?php include 'src/edit.php' ?>
	</td></tr>
<!-- ======================================================== -->
</table>
<?php include 'src/foot.php' ?>

