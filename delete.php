<?php
/**
 * delete.php - Item delete. Users can change existing items.
 */
require_once __DIR__ . "/src/App.php";
require __DIR__ . '/autoload.php';
use MK\Logger;
use MK\Helper;
use MK\Init;

$app = 'delete.php';
[$sess,$error,$db,$init] = Init::initialize($app);
$log = $GLOBALS['log'];

// ---- Session post: to preserve values for GUI.
if(!isset($_SESSION['post'])) $_SESSION['post'] = []; 	// Init Session post.
if($from !== $app) { $_SESSION['post'] = []; } 			// Reset preserved values.
$post = array_merge($_SESSION['post'],$_POST);			// Post for preserved values and changed values.
$_SESSION['post'] = $post;								// Preserve values in "Session Post"
Logger::info(__LINE__ . " $app IN Session='" . $sess . "'\n----\nPOST=" . json_encode($_POST) . "\n----\npost=" . json_encode($post) . "\n-----");

$user = $sess->getFullname();
$selected = [];
$items = [];

$data = new App($db,$error,$init->getConfig());
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	[$items,$selected] = $data->filter($post);
	Logger::info(__LINE__ . " $app Items: " . json_encode($items));

	$item = $data->getArtikel($post);

	if(isset($_POST['btn_submit']) && $_POST['btn_submit'] === 'delete') {
		if(isset($_POST['id'])) {
			$id = $_POST['id'];
			$ret = $db->fetch('items_delete', [$id]); // Delete Item
			Logger::debug(__LINE__ . " $app - Item delete id=" . $id . ", result=" . json_encode($ret));
		}
	}
}
$log->info(__LINE__ . " $app ---------- Build Html -----------------");
// ---------- Get ENUMs from Database for select options. 
$enums = $data->getEnums($selected['category'],$init->getConfig('subcategory'));
Logger::debug(__LINE__ . " $app Enums: " . json_encode($enums));

$enum = ['category','gender','subcategory','state'];
$item = $data->init_items($item);				// Errors "Undefined array key"
$selected = $data->init_selected($selected);
if(!isset($selected['stock_ja']) and !isset($selected['stock_no']) and !isset($selected['stock_all'])) $selected['stock_ja'] = true;

$button_name  = "Löschen";
$button_value = "delete";
?>

<!-- ========================= HTML ========================= -->
<?php include 'src/head.php' ?>
<table class="content">
    <tr><td class="title" colspan=2><h1>Löschen</h1></td></tr>
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
	<tr><td class="anzeige" colspan=2>
		<?php include 'src/list.php' ?>
	</td></tr>
<!-- ====================== Anzeige Html ========================= -->
	<tr><td class="anzeige_html" colspan=2>
		<?php include 'src/present.php' ?>
	</td></tr>
<!-- ====================== Löschen ========================= -->
	<tr><td class="button" colspan=2>
	<div class="delete_container">
	<form method="post" id="delete-form" action="<?php echo $action;?>" enctype="multipart/form-data">
   		<input type="hidden" name="action" value="<?php echo $action; ?>">
   		<input type="hidden" name="id" value="<?php echo $item['id']; ?>">
		<button type="submit" name="btn_submit" value="<?php echo $button_value;?>"><?php echo $button_name;?></button>
	</form>
	</td></tr>
<!-- ======================================================== -->
</table>
<?php include 'src/foot.php' ?>
