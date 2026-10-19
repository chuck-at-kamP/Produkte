<?php
/**
 * change.php - Item change. Users can change existing items.
 */
require_once __DIR__ . "/src/Data.php";
require_once __DIR__ . "/src/Upload.php";
require __DIR__ . '/autoload.php';
use MK\DBT;
use MK\Logger;
use MK\Helper;
use MK\Session;
$config = include 'config.php';

Logger::$LOGFILE = $config['log'];
Logger::$DEBUG = true;

Logger::info(__LINE__ . " change.php Start -------------------------------------------------------------");
$app = $action = 'change.php';
$sess = new Session('change.php');
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

	if(isset($post['category'])) { $selected['category'] = $post['category']; }
	if(isset($post['subcategory'])) { $selected['subcategory'] = $post['subcategory']; }
	if(isset($post['in_stock'])) {
		$val = $post['in_stock'];
		$selected[$val] = true;
	}
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change.php') {
    $user_id = trim($_SESSION['user_id'] ?? 0);

	Logger::debug(__LINE__ . " $app - Item ändern, ==============================================");
	Logger::debug(__LINE__ . " $app - Item ändern, POST=" . json_encode($_POST));

	$id = $_POST['id'];
	$changed_values = $_POST;
	unset($changed_values['id']);
	unset($changed_values['action']);
	unset($changed_values['submit']);
	if(!isset($_POST['image']) && isset($_FILES['image']['name'])) {
		Logger::debug(__LINE__ . " change.php Set Image in 'changed_values'");
		$changed_values['image'] = $_FILES['image']['name'];
	}

	$keys = array_keys($changed_values);
	$vals = array_values($changed_values);
	$key_str = implode("=?, ",$keys) . "=?";
	$sql = "UPDATE items SET $key_str WHERE id=$id";
	Logger::debug(__LINE__ . " $app - Item ändern, SQL=" . $sql);
	try {
		$ret = $db->query($sql,$vals); 
	} catch(Exception $e) {
		$error = __LINE__ . " change.php Exception " . $e->getMessage();
	}
	Logger::debug(__LINE__ . " $app - Item ändern, RET=" . json_encode($ret));

	Logger::debug(__LINE__ . " $app - Upload " . json_encode($_FILES['image']));
	if(isset($_FILES['image'])) {
		Logger::info(__LINE__ . " $app - Upload file " . $_FILES['image']['tmp_name']);
		try {
			Logger::debug(__LINE__ . " $app - Aufruf Upload() ");
			$up = new Upload(null,$_FILES['image']);
			$up->upload();
		} catch(Exception $e) {
			$error = __LINE__ . " change.php Exception " . $e->getMessage();
		}
	}
	Logger::debug(__LINE__ . " $app - Item ändern, ==============================================");
}
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
    <tr><td class="title" colspan=2><h1>Ändern</h1></td></tr>
    
<!-- ======================== Fehlerausgabe ======================================= -->
<?php if (!empty($error)): ?>
<tr><td class="error" colspan=2 style="text-align:center;"><p style="width:600px; color:red; text-align:justify; white-space:wrap;"><?= Helper::e($error) ?></p></td></tr>
<?php endif; ?>
    
<!-- ======================= Show ENUMs in Select-Option ============================ -->
	<tr><td>
		<?php include 'src/filter.php' ?>
	</td><td>

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
