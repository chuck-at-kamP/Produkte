<?php
/**
 * change.php - Item change. Users can change existing items.
 */
require_once __DIR__ . "/src/App.php";
require_once __DIR__ . "/src/Upload.php";

require __DIR__ . '/autoload.php';
use MK\Logger;
use MK\Helper;
use MK\Init;

$app = 'change.php';
[$sess,$error,$db,$init] = Init::initialize($app);
$log = $GLOBALS['log'];

$log->info(__LINE__ . " $app ---------- Post Handling -----------------");
$user = $sess->getFullname();
$action = $app;
$selected = [];	// Filter selected
$items = [];	// Items List
$item = [];		// Choosen Item

// ---- Session post: to preserve values for GUI.
if(!isset($_SESSION['post'])) $_SESSION['post'] = []; 	// Init Session post.
if($from !== $app) { $_SESSION['post'] = []; } 			// Reset preserved values.
$post = array_merge($_SESSION['post'],$_POST);			// Post for preserved values and changed values.
$_SESSION['post'] = $post;								// Preserve values in "Session Post"
Logger::info(__LINE__ . " $app IN Session='" . $sess . "'\n----\nPOST=" . json_encode($_POST) . "\n----\npost=" . json_encode($post) . "\n-----");

$data = new App($db,$error,$init->getConfig());
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	[$items,$selected] = $data->filter($post);
	Logger::info(__LINE__ . " $app Items: " . json_encode($items));

	if(isset($_POST['btn_submit']) && $_POST['btn_submit'] === 'change') {
		$data->updateArtikel($_POST,$_FILES);
	}
	$item = $data->getArtikel($post);
}

$log->info(__LINE__ . " $app ---------- Build Html -----------------");
// ---------- Get ENUMs from Database for select options. 
$enums = $data->getEnums($selected['category'],$init->getConfig('subcategory'));
Logger::debug(__LINE__ . " $app Enums: " . json_encode($enums));

$enum = ['category','gender','subcategory','state'];
$item = $data->init_items($item);				// Errors "Undefined array key"
$selected = $data->init_selected($selected);
if(!isset($selected['stock_ja']) and !isset($selected['stock_no']) and !isset($selected['stock_all'])) $selected['stock_ja'] = true;

$button_name  = "Ändern";
$button_value = "change";
?>

<!-- Aussehen der Oberfläche:
	-----------------------------------
	Category:       | Item 1
	Subcategory:    | Item 2
	In Bestand:     | Item 3
	-----------------------------------
	Data (Darstellung wie "Einrichten")
	-----------------------------------
	Html
	--------------------------
	Bild            | Kurztext
	--------------------------
	Langtext (Html) | Barcode
	-----------------------------------
-->
<!-- ========================= HTML ========================= -->
<?php include 'src/head.php' ?>
<table class="content">
    <tr><td class="title" colspan=2><h1>Ändern</h1></td></tr>
<!-- ======================== Fehlerausgabe ======================================= -->
	<tr><td class="error" colspan=2 style="text-align:center;"><?php echo $error->getMessages(); ?></td></tr>
	<tr><td class="error" colspan=2 style="text-align:center;"><?php echo $error->getTable(); ?></td></tr>
<!-- ======================= Show ENUMs in Select-Option ============================ -->
	<tr><td>
		<?php include 'src/filter.php' ?>
	</td><td>
<!-- ====================== Bestehende Items auswählen ======================= -->
		<?php include 'src/select.php' ?>
	</td></tr>
<!-- ====================== Anzeige ========================= -->
	<tr><td colspan=2>
		<?php include 'src/edit.php' ?>
	</td></tr>
<!-- ====================== Anzeige Html ========================= -->
	<tr><td colspan=2>
		<?php include 'src/present.php' ?>
	</td></tr>
<!-- ======================================================== -->
</table>
<?php include 'src/foot.php' ?>

