<?php
/**
 * items.php - Item selection. Users can select existing items.
 */
require_once __DIR__ . "/src/Data.php";
require __DIR__ . '/autoload.php';
use MK\DBT;
use MK\Logger;
use MK\Helper;
use MK\Session;
$config = include 'config.php';

Logger::$LOGFILE = $config['log'];
Logger::$DEBUG = true;

Logger::info(__LINE__ . " items.php Start -------------------------------------------------------------");
$app = $action = 'items.php';
$sess = new Session('items.php');
$user = $sess->getFullname();
$sess->loggedin();
$db = new DBT($config['sql'],$config['fetchAll'],$config['db']);

Logger::info(__LINE__ . " $app IN Session='" . $sess . "', POST=" . json_encode($_POST));

$selected = [];
$items = [];

if(!isset($_SESSION['post'])) $_SESSION['post'] = [];
$post = array_merge($_SESSION['post'],$_POST);
$_SESSION['post'] = $post;
Logger::info(__LINE__ . " $app IN Session='" . $sess . "', post=" . json_encode($post));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	// ----- from src/filter.php -----	
	/*
	if(isset($_POST['category'])) { $selected['category'] = $_POST['category']; }
	if(isset($_POST['subcategory'])) { $selected['subcategory'] = $_POST['subcategory']; }
	if(isset($_POST['in_stock'])) {
		$val = $_POST['in_stock'];
		$selected[$val] = true;
	}
	*/

	if(isset($post['category'])) { $selected['category'] = $post['category']; }
	if(isset($post['subcategory'])) { $selected['subcategory'] = $post['subcategory']; }
	if(isset($post['in_stock'])) {
		$val = $post['in_stock'];
		$selected[$val] = true;
	}

	//if(isset($_POST['filter']) && $_POST['filter'] === "search") {
	//	$val = $_POST['in_stock'];
	if(isset($post['filter']) && $post['filter'] === "search") {
		$val = $post['in_stock'];
		if($val === "stock_all") {
			$ret_all = $db->fetch('items_filter_all', [$selected['category'],$selected['subcategory']]); // Get all items
		}
		else if($val === "stock_ja") {
			$ret_all = $db->fetch('items_filter_stock', [$selected['category'],$selected['subcategory']]); // Get all items
		}
		else if($val === "stock_no") {
			$ret_all = $db->fetch('items_filter_no_stock', [$selected['category'],$selected['subcategory']]); // Get all items
		}
		$items = $ret_all['data'];
	}
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'select') {
    $item_id = (int)($_POST['item_id'] ?? 0);

    $ar['category']    = trim($_POST['category'] ?? '');
    $ar['subcategory'] = trim($_POST['subcategory'] ?? '');
    
    if ($item_id > 0) {
		$ret = $db->fetch('items_id', [$item_id]); // Check if item exists
		if ($ret['status'] === 'success') {
			$ret = $db->fetch('items_id', [$item_id,'waiting']);
			header('Location: index.php'); // Redirect to game
			exit;
		}
    }
}
/*
$ret_all = $db->fetch('items_list', []); // Get all items
$items = $ret_all['data'];
*/
$data = new Data($db);
$enums = $data->getEnums($selected['category'],$config['subcategory']);

Logger::info(__LINE__ . " $app Items: " . json_encode($items));
?>

<!--
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
	Langtext (Html)
	-----------------------------------
-->

<!-- ========================= HTML ========================= -->
<?php include 'src/head.php' ?>

<table class="content">
    <tr><td class="title" colspan=2><h1>Anzeigen</h1></td></tr>
    
	<!-- ======================== Fehlerausgabe ======================================= -->
	<?php if (!empty($error)): ?>
	<tr><td class="error"><p style="color: red;"><?= Helper::e($error) ?></p></td></tr>
	<?php endif; ?>
    
	<!-- ======================= Show ENUMs in Select-Option ============================ -->
	<tr><td class="filter">
		<?php include 'src/filter.php' ?>
	</td><td class="select">

<!-- ====================== Bestehende Items auswählen ======================= -->
<?php
$item = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	//if(isset($_POST['choose']) && $_POST['choose'] === "anzeigen" && isset($_POST['select_item'])) {
	//	$ret_item = $db->fetch('items_query', [$_POST['select_item']]); // Get one item
	if(isset($post['choose']) && $post['choose'] === "anzeigen" && isset($post['select_item'])) {
		$selected['select_item'] = $post['select_item'];
		Logger::debug(__LINE__ . " $app Selected Item: " . $selected['select_item']);
		$ret_item = $db->fetch('items_query', [$post['select_item']]); // Get one item
		$item = $ret_item['data'];
	}
}
?>

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
<!-- ======================================================== -->
</table>
<?php include 'src/foot.php' ?>

