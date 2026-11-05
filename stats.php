<?php
/**
 * stats.php - Statistics over all products.
 */
require_once __DIR__ . "/src/App.php";
require __DIR__ . '/autoload.php';
use MK\Logger;
use MK\Helper;
use MK\Init;

$app = 'stats.php';
[$sess,$error,$db,$init] = Init::initialize($app);
$log = $GLOBALS['log'];
$user = $sess->getFullname();

// ---- Session post: to preserve values for GUI.
if(!isset($_SESSION['post'])) $_SESSION['post'] = []; 	// Init Session post.
if($from !== $app) { $_SESSION['post'] = []; } 			// Reset preserved values.
$post = array_merge($_SESSION['post'],$_POST);			// Post for preserved values and changed values.
$_SESSION['post'] = $post;								// Preserve values in "Session Post"
Logger::info(__LINE__ . " $app IN Session='" . $sess . "'\n----\nPOST=" . json_encode($_POST) . "\n----\npost=" . json_encode($post) . "\n-----");

$data = new App($db,$error,$init->getConfig());
$stats = $data->getStats();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	[$items,$selected] = $data->filter($post);
	Logger::info(__LINE__ . " $app Items: " . json_encode($items));
}
$log->info(__LINE__ . " $app ---------- Build Html -----------------");
// ---------- Get ENUMs from Database for select options. 
$enums = $data->getEnums($selected['category'],$init->getConfig('subcategory'));
Logger::debug(__LINE__ . " $app Enums: " . json_encode($enums));

$enum = ['category','gender','subcategory','state'];
$item = $data->init_items($item);				// Errors "Undefined array key"
$selected = $data->init_selected($selected);
if(!isset($selected['stock_ja']) and !isset($selected['stock_no']) and !isset($selected['stock_all'])) $selected['stock_ja'] = true;

?>
<!-- Statistik:
 - Liste der Kategorien, Unterkategorien, Anzahl, Preise, Erlöse.
 - 
 |----------------------------------------------------------------------------------------------------|
 | Kategorie | Unterkategorie | Artikel | Bestand | Verkauft | Preis | Wert | Erlös | Kosten | Gewinn |
 | Kleidung  | Hose           | 5       | 22      | 10       | 10€   | 10€  | 10€   | 10€    | 10€    |
 | Kleidung  | Rock           | ...
 | Kleidung  | -------------- | 25      | 50      | 20       | 20€   | 20€  | 20€   | 20€    | 20€    |
 | Summe     |
 |----------------------------------------------------------------------------------------------------|

 Artikel (nr)		Anzahl Artikel
 Bestand (nStock)	Anzahl Bestand
 Verkauft (nSold)	Anzahl Verkauft
 Gesamt (nSum)		Anzahl Gesamt
 -------------		---------------
 Bestand (nstock)
 - Wert (bVal)		nstock * price		Bestand * Preis
 - Kosten (bCost)	nstock * cost		Bestand * Einkaufspreis (Kosten Bestand)
 Verkauft (nsold)
 - Wert (oVal)		nsold * price		Verkauft * Preis (Umsatz)
 - Kosten (oCost)	nsold * cost		Verkauft * Einkaufspreis (Kosten)
 - Gewinn (oProfit) oVal - oCost		Umsatz - Kosten (Gewinn)
 Gesamt (nsum)
 - Wert (gVal)		nsum * price		Gesamt * Preis (Wert)
 - Kosten (gCost)	nsum * cost			Gesamt * Einkaufspreis (Kosten Gesamt)
 -------------		---------------
-->
<!-- ========================= HTML ========================= -->
<?php include 'src/head.php' ?>
<table class="content">
    <tr><td></td><td></td></tr>
    <tr><td class="title" colspan=2><h1>Statistik</h1></td></tr>
	<tr><td colspan=2><table class=data style="padding:3px; font-size:20px; text-align:right;">
	<tr style='text-align:center; font-size:small;'><th colspan=2></th><th colspan=4>Anzahl</th><th colspan=2>Bestand</th><th colspan=3>Verkauft</th><th colspan=2>Gesamt</th></tr>
	<tr style='text-align:center; font-size:small;'><th>Category</th><th>Subcategory</th>
		<th>Artikel</th><th>Bestand</th><th>Verkauft</th><th>Gesamt</th>
		<th>Wert</th><th>Kosten</th>
		<th>Wert</th><th>Kosten</th><th>Gewinn</th>
		<th>Wert</th><th>Kosten</th>
		</tr>
	<?php
	foreach($stats as $s) {
		$style="";
		if($s['sub'] === 'All') $style = "style='background-color:lightgrey;'";
		if($s['cat'] === 'All') $style = "style='background-color:grey;color:white;'";
		echo "<tr $style>";
		foreach($s as $k => $v) {
			$style2="";
			if($k === "sub") $style2 = "border-right:3px solid black;";
			if($k === "nr") $style2 = "border-right:3px solid black;";
			if($k === "nsum") $style2 = "border-right:3px solid black;";
			if($k === "bcost") $style2 = "border-right:3px solid black;";
			if($k === "oprofit") $style2 = "border-right:3px solid black;";
			echo "<td style='padding:5px;text-align:right;$style2'>$v</td>";
		}
		echo "</tr>\n";
	}
	?>
	</table></td></tr>

<!-- ======================= Show ENUMs in Select-Option ============================ -->
	<tr><td>
		<?php include 'src/filter.php' ?>
	</td></tr>
<!-- ====================== Bestehende Items auswählen ======================= -->
	<tr><td colspan=2><table class=data style="text-align:right;">
	<tr style="text-align:center;"><th>Name</th><th>Id</th><th>Category</th><th>Subcategory</th><th>Bestand</th><th>Verkauft</th><th>Preis</th><th>Einkaufspreis</th></tr>
	<?php
	foreach($items as $item) {
		echo "<tr style='text-align:right;'>";
		echo "<td>" . $item['name'] . "</td>";
		echo "<td>" . $item['id'] . "</td>";
		echo "<td>" . $item['category'] . "</td>";
		echo "<td>" . $item['subcategory'] . "</td>";
		echo "<td>" . $item['stock'] . "</td>";
		echo "<td>" . $item['sold'] . "</td>";
		echo "<td>" . $item['price'] . "</td>";
		echo "<td>" . $item['cost'] . "</td>";
		echo "</tr>";
	}
	?>
	</table></td></tr>
<!-- ======================================================== -->
</table>
<?php include 'src/foot.php' ?>


