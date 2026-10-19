<?php
/**
 * index.php - Item creation page.  Users can create a new item.
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

Logger::info(__LINE__ . " index.php Start -------------------------------------------------------------");
$sess = new Session('index.php',$_SESSION);
$user = $sess->getFullname();
$app  = $sess->getAppname();

$db = new DBT($config['sql'],$config['fetchAll'],$config['db']);
// Handle new item creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
	$save = trim($_POST['save'] ?? '');
    $user_id = trim($_SESSION['user_id'] ?? 0);

	Logger::debug(__LINE__ . " $app - POST: Sold: -" . $_POST['item_sold'] . "-"); 
    $name    = trim($_POST['item_name'] ?? '');
    $short   = trim($_POST['item_short'] ?? '');
    $image   = trim($_POST['item_image'] ?? '');
    $barcode = trim($_POST['item_barcode'] ?? '');
    $price   = trim($_POST['item_price'] ?? 0.00);
    $cost    = trim($_POST['item_cost'] ?? 0.00);
    $stock   = trim($_POST['item_stock'] ?? 0);
    // $sold    = trim($_POST['item_sold'] ?? '3');
    $sold    = trim($_POST['item_sold']);
	if($sold == null || empty($sold)) $sold = '0';
	// $sold = "3";
	Logger::debug(__LINE__ . " $app - POST: Sold: -" . $sold . "-"); 
    $description       = trim($_POST['item_description'] ?? '');
    $ar['category']    = trim($_POST['category'] ?? '');
    $ar['subcategory'] = trim($_POST['subcategory'] ?? '');
    $ar['gender']      = trim($_POST['gender'] ?? '');
    $ar['state']       = trim($_POST['state'] ?? '');
   
	Logger::debug(__LINE__ . " $app - Neues Item anlegen: $name ($shor, $description) $price-$cost $stock-$sold data=" . json_encode($ar)); 
	if($save === "save") {
	    if (!empty($name)) {
			try {
			$ret = $db->fetch('items_insert', 
				[$name, $user_id, $short, $description, $image, $barcode, $price, $cost, $stock, $sold, $ar['category'], $ar['subcategory'], $ar['gender'], $ar['state']]);
			} catch(Exception $e) {
				$error = __LINE__ . " index.php Exception " . $e->getMessage();
			}
			$new_item_id = $ret['data']['id'];
		}
		if(isset($_FILES['item_image'])) {
			$up = new Upload(null,$_FILES['item_image']);
			$up->upload();
		}
	}
	else if($save === "cancel") {
	}
	else {
	}
}

// ---------- Get ENUMs from Database for select options. 
$enums = [];
$enum = ['category','gender','subcategory','state'];
foreach($enum as $e) {
	// ---- Füllen der Unterkategorie ----
	if($e === 'subcategory') {
		if($_POST['category']) {
			$enums['subcategory'] = $config['subcategory'][$_POST['category']];
		} else {
			$enums['subcategory'] = $config['subcategory'][$enums['category'][0]];
		}
	} 
	else {
		$ret = $db->fetch('items_enum', [$e]);
		$str = str_replace(['(',')','\''],'',$ret['data']['enums']);
		$arr = explode(',',$str);
		Logger::debug(__LINE__ . " $app - Fetch Enums for $e: " . json_encode($arr));
		$enums[$e] = $arr;
	}
}

Logger::debug(__LINE__ . " $app - Create new FORM with $name ($description) data=" . json_encode($ar));
?>

<!-- ========================= HTML ========================= -->
<?php include 'src/head.php' ?>

<!-- ======================== Fehlerausgabe ======================================= -->
<?php if (!empty($error)): ?>
	<div class="error"><p style="color: red;"><?= Helper::e($error) ?></p></div>
<?php endif; ?>
    
<!-- ======================== Neues Item erstellen ================================= -->
<form method="post" action="index.php">
   <input type="hidden" name="action" value="create">
<table class="content">
    <tr><td class="title" colspan=4><h1>Neues Item</h1></td></tr>
        <tr><td class="input"><label for="item_name">Name * : </label></td><td>
			<input type="text" name="item_name" required placeholder="z.B. Jeanshose, blau" value="<?php echo $name; ?>">
        </td></tr>
        <tr><td class="input"><label>Kurztext * : </label></td><td>
			<input type="text" name="item_short" placeholder="z.B. Größe M, guter Zustand" value="<?php echo $short; ?>" class="short">
        	</td></tr>
        <tr><td class="input"><label>Beschreibung * (Html): </label></td><td colspan=3>
			<textarea type="text" name="item_description"
				value="<?php echo $description; ?>" class="desc" cols="65" rows="7">
			</textarea>
        </td></tr>
        <tr><td class="input"><label>Image * : </label></td><td colspan=3 style="text-align:left;">
			<input type="file" name="item_image" placeholder="Dateiname" value="<?php echo $image; ?>" required>
        </td></tr>
        <tr><td class="input"><label>Barcode: </label></td><td>
			<input type="text" name="item_barcode" placeholder="Barcode" value="<?php echo $barcode; ?>">
        </td></tr>
        <tr><td class="input"><label>Preis * (€): </label></td><td>
			<input type="text" name="item_price" placeholder="z.B. 10,20" value="<?php echo $price; ?>" required>
        	</td>
        	<td class="input"><label>Einkaufspreis (€): </label></td><td>
			<input type="text" name="item_cost" placeholder="z.B. 9,00" value="<?php echo $cost; ?>">
        </td></tr>
        <tr><td class="input"><label>Anfangsbestand * : </label></td><td>
			<input type="text" name="item_stock" placeholder="z.B. 25" value="<?php echo $stock; ?>" required>
        	</td>
        	<td class="input"><label>Verkauft: </label></td><td>
			<input type="text" name="item_sold" placeholder="z.B. 10" value="<?php echo $sold; ?>">
        </td></tr>

<!-- ======================= Show ENUMs in Select-Option ============================ -->
<?php $i=0; foreach($enum as $cat) { $i++; if($i % 2 != 0) { echo "\t<tr>"; } ?>
<td class="input"><label><?php echo ucfirst($cat); ?><?php if($cat=== 'category' || $cat === 'subcategory') echo " * "; ?>:</label></td>
		<td>
		<select 
			name="<?php echo $cat; ?>" 
			<?php if($cat === 'category') echo "onChange='this.form.submit();'"; ?>
			<?php if($cat === 'category' || $cat === 'subcategory') echo "required"; ?>
			>
	<?php foreach($enums[$cat] as $e) { ?>
		<option value="<?php echo $e; ?>" <?php if($e === $ar[$cat]) { echo "selected"; } ?>><?php echo $e; ?></option>
	<?php } ?>
	</select></td><?php if($i % 2 == 0) { echo "\n\t</tr>\n"; } ?>
<?php } ?>

<!-- ====================== Buttons ========================= -->
        <tr><td class="button" colspan=4>
            <button type="submit" name="save" value="save">Speichern</button>
            <button type="reset" name="save" value="cancel">Abbrechen</button>
        </td></tr>
</table>
</form>
(* = Pflichtfeld)

<?php include 'src/foot.php'; Logger::debug(__LINE__ . " $app - Footer: End Application."); ?>

