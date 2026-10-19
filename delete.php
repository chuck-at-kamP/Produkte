<?php
/**
 * delete.php - Item delete. Users can change existing items.
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

Logger::info(__LINE__ . " delete.php Start -------------------------------------------------------------");
$app = $action = 'delete.php';
$sess = new Session('delete.php');
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete.php') {
    $user_id = trim($_SESSION['user_id'] ?? 0);

	if(isset($_POST['id'])) {
		$id = $_POST['id'];
		$ret = $db->fetch('items_delete', [$id]); // Delete Item
		Logger::debug(__LINE__ . " $app - Item delete id=" . $id . ", result=" . json_encode($ret));
	}
}
$data = new Data($db);
$enums = $data->getEnums($selected['category'],$config['subcategory']);

Logger::info(__LINE__ . " $app Items: " . json_encode($items));
?>

<!-- ========================= HTML ========================= -->
<?php include 'src/head.php' ?>

<table class="content">
    <tr><td class="title" colspan=2><h1>Löschen</h1></td></tr>
    
<!-- ======================== Fehlerausgabe ======================================= -->
<?php if (!empty($error)): ?>
<tr><td class="error"><p style="color: red;"><?= Helper::e($error) ?></p></td></tr>
<?php endif; ?>
    
<!-- ======================= Show ENUMs in Select-Option ============================ -->
	<tr><td>
		<?php include 'src/filter.php' ?>
	</td><td>

<!-- ====================== Bestehende Items auswählen ======================= -->
<?php
$item = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
	<!-- ====================== Löschen ========================= -->
	<tr><td class="delete" colspan=2>
	<div class="delete_container">
	<form method="post" action="<?php echo $action;?>">
   		<input type="hidden" name="action" value="<?php echo $action; ?>">
   		<input type="hidden" name="id" value="<?php echo $item['id']; ?>">
		<button type="submit" name="delete" value="loeschen">Löschen</button>
	</form>
	</td></tr>
<!-- ======================================================== -->
</table>

<?php include 'src/foot.php' ?>
