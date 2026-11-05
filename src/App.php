<?php
require __DIR__ . '/../autoload.php';
require_once __DIR__ . "/Upload.php";
use MK\Logger;
use MK\Barcode;
use MK\DBT;
use MK\Error;

class App {

	private $db;
	private $err;
	private $config;

	public function __construct($db,$err,$config) {
		$this->db     = $db;
		$this->err    = $err;
		$this->config = $config;
	}

	public function init_items($item) {
		$item['id']          = $item['id']??null;
		$item['state']       = $item['state']??null;
		$item['gender']      = $item['gender']??null;
		$item['category']    = $item['category']??null;
		$item['subcategory'] = $item['subcategory']??null;
		$item['name']        = $item['name']??null;
		$item['short']       = $item['short']??null;
		$item['description'] = $item['description']??null;
		$item['image']       = $item['image']??null;
		$item['barcode']     = $item['barcode']??null;
		$item['price']       = $item['price']??null;
		$item['cost']        = $item['cost']??null;
		$item['stock']       = $item['stock']??null;
		$item['sold']        = $item['sold']??null;
		return $item;
	}
	public function init_selected($selected) {
		$selected['stock_no']    = $selected['stock_no']??null;
		$selected['stock_ja']    = $selected['stock_ja']??null;
		$selected['stock_all']   = $selected['stock_all']??null;
		$selected['category']    = $selected['category']??null;
		$selected['subcategory'] = $selected['subcategory']??null;
		return $selected;
	}

	public function filter($post) {
		$db = $this->db;
		$error = $this->err;
		$selected = $this->init_selected([]);
		if(isset($post['category'])) { $selected['category'] = $post['category']; }
		if(isset($post['subcategory'])) { $selected['subcategory'] = $post['subcategory']; }
		if(isset($post['in_stock'])) {	// i.e. "stock_ja"
			$val = $post['in_stock'];	// $val = "stock_ja"
			$selected[$val] = true;		// $selected['stock_ja'] = true;
		} else {
			$selected['stock_ja'] = true;	// Default "stock_ja"
		}
		if(isset($post['filter']) && $post['filter'] === "search") {
			$message = "Suchen: Filter '" . $selected['category'] . "' und '" . $selected['subcategory'] . "'";
			$ret = [];
			if($selected['stock_all']) {
				$ret = $db->fetch('items_filter_all', [$selected['category'],$selected['subcategory']]); // Get all items
				$ret['message'] = $message . "!";
			}
			else if($selected['stock_ja']) {
				$ret = $db->fetch('items_filter_stock', [$selected['category'],$selected['subcategory']]); // Get all items
				$ret['message'] = $message . " mit Bestand!";
			}
			else if($selected['stock_no']) {
				$ret = $db->fetch('items_filter_no_stock', [$selected['category'],$selected['subcategory']]); // Get all items
				$ret['message'] = $message . " ohne Bestand!";
			}
			$items = $ret['data'];
			if(gettype($items) == 'array') {
				$len = count($items);
				$ret['message'] .= " Gefunden $len Artikel gefunden.";
			} else {
				Logger::error(__LINE__ . " App::filter() - Items are not array " . json_encode($items));
			}
			$error->set(__LINE__, $ret);
		}
		return [$items,$selected];
	}

	public function insertArtikel($post,$file) {
		$config = $this->config;
		Logger::debug(__LINE__ . " App::updateArtikel() - Item ändern, ==============================================");
		Logger::debug(__LINE__ . " App::updateArtikel() - Item ändern, POST=" . json_encode($post) . ", FILES=" . json_encode($file));
	
		$changed_values = $this->modifyChangedValues($post,$file);
		if($changed_values) {
			$id = $this->insertValues($changed_values);
			$this->createBarcode($id, $changed_values, $config);
		} else {
			$error->setValues(__LINE__, [false,"warning","change.php::Update","Kein Feld zum Ändern vorhanden!","",null]);
		}
		Logger::debug(__LINE__ . " App::updateArtikel() - Upload " . json_encode($_FILES['image']));
		$this->uploadImage($file);

		Logger::debug(__LINE__ . " App::updateArtikel() - Item ändern, ==============================================");
	}

	public function updateArtikel($post,$file) {
		$config = $this->config;
		Logger::debug(__LINE__ . " App::updateArtikel() - Item ändern, ==============================================");
		Logger::debug(__LINE__ . " App::updateArtikel() - Item ändern, POST=" . json_encode($post) . ", FILES=" . json_encode($file));
	
		$id = $post['id'];
		$changed_values = $this->modifyChangedValues($post,$file);
		if($changed_values) {
			$this->updateValues($id, $changed_values);
			$this->createBarcode($id, $changed_values, $config);
		} else {
			$error->setValues(__LINE__, [false,"warning","change.php::Update","Kein Feld zum Ändern vorhanden!","",null]);
		}
		Logger::debug(__LINE__ . " App::updateArtikel() - Upload " . json_encode($_FILES['image']));
		$this->uploadImage($file);

		Logger::debug(__LINE__ . " App::updateArtikel() - Item ändern, ==============================================");
	}

	public function modifyChangedValues($post,$file) {
		$changed_values = $post;
		unset($changed_values['id']);
		unset($changed_values['action']);
		unset($changed_values['submit_edit']);
		unset($changed_values['submit_category']);
		Logger::debug(__LINE__ . " App::modifyChangedValues() sold=" . $post['sold']);
		if(isset($post['sold'])) Logger::debug("isset() OK"); else Logger::debug("isset() NOK");
		if(empty($post['sold'])) Logger::debug("empty() OK"); else Logger::debug("empty() NOK");
		if(is_null($post['sold'])) Logger::debug("is_null() OK"); else Logger::debug("is_null() NOK");
		if(isset($post['sold']) && empty($post['sold'])) $changed_values['sold'] = "0";
		if (isset($file['image']) && $file['image']['error'] === UPLOAD_ERR_OK) {
			$changed_values['image'] = basename($file['image']['name']);
		}
		return $changed_values;
	}
	private function insertValues($changed_values) {
		$db = $this->db;
		$error = $this->err;
		$ret = $db->insert("items",$changed_values); // ------ Datenbankabfrage -------
		$id = $ret['data']['id'];
		$ret['message'] = "Einrichten Produkt mit Id '$id': " . implode("-",$changed_values);
		$error->set(__LINE__, $ret);
		return $id;
	}
	private function updateValues($id,$changed_values) {
		$db = $this->db;
		$error = $this->err;
		$ret = $db->update($id,"items",$changed_values); // ------ Datenbankabfrage -------
		$ret['message'] = "Änderung Produkt mit Id '$id': " . implode("-",$changed_values);
		$error->set(__LINE__, $ret);
	}

	private function createBarcode($id, $changed_values, $config) {
		$db = $this->db;
		$error = $this->err;
		if(isset($changed_values['category']) || isset($changed_values['subcategory'])) {
			// $barcode = $changed_values['barcode'];
			$ret1 = $db->fetch("items_barcode",[$id]);
			$error->set(__LINE__, $ret1);
			$array = $ret1['data'];

			// $bc = new Barcode($barcode);
			$bc = Barcode::unique_barcode($array,['category'=>$config['category'],'subcategory'=>$config['subcategory']]);
			$ret2 = $bc->createPng($config['bar']);
			$error->set(__LINE__, $ret2);

			$ret3 = $db->fetch("items_barcode_update",[$bc->getCode(),$id]);
			$error->set(__LINE__, $ret3);
		}
	}
	private function uploadImage($file) {
		$error = $this->err;
		if(isset($file['image']) && $file['image']['error'] === UPLOAD_ERR_OK) {
			Logger::info(__LINE__ . " $app - Upload file " . $file['image']['tmp_name']);
			try {
				Logger::debug(__LINE__ . " App::uploadImage() - Aufruf Upload() ");
				$up = new Upload(null,$file['image']);
				$up->upload();
			} catch(Exception $e) {
				$error = __LINE__ . " App::uploadImage() Exception " . $e->getMessage();
				$error->setValues(__LINE__, [false,"error","App::uploadImage","Upload Bild",$e->getMessage(),null]);
			}
		}

	}

	public function getArtikel($post) {
		$db = $this->db;
		$error = $this->err;
		if(isset($post['choose']) && $post['choose'] === "anzeigen" && isset($post['select_item'])) {
			$selected['select_item'] = $post['select_item'];
			Logger::debug(__LINE__ . " App::getArtikel() Selected Item: " . $selected['select_item']);
			$ret = $db->fetch('items_query', [$post['select_item']]); // Get one item
			$item = $ret['data'];
			$ret['message'] = "Anzeigen: Artikel (id=" . $item['id'] . ") '" . $item['name'] . "'";
			$error->set(__LINE__,$ret);
		}
		return $item;
	}

	// ---------- Get ENUMs from Database for select options. 
	public function getEnums($category,$subcategory) {
		$enums = [];
		$enum = ['category','gender','subcategory','state'];
		foreach($enum as $e) {
			// ---- Füllen der Unterkategorie aus Config entsprechend Category ----
			if($e === 'subcategory') {
				if($category) {
					$enums['subcategory'] = $subcategory[$category];
				} else {
					$enums['subcategory'] = $subcategory[$enums['category'][0]];
				}
			} 
			// ---- Füllen Category,Gender,State aus Datenbank ----
			else {
				$ret = $this->db->fetch('items_enum', [$e]);
				$str = str_replace(['(',')','\''],'',$ret['data']['enums']);
				$arr = explode(',',$str);
				Logger::debug(__LINE__ . " Data::getEnums() - Fetch Enums for $e: " . json_encode($arr));
				$enums[$e] = $arr;
			}
		}
		return $enums;
	}

	public function getStats() {
		$result = [];
		$ret1 = $this->db->fetch('stats_sub', []);
		$res_sub = $ret1['data'];
		$ret2 = $this->db->fetch('stats_cat', []);
		$res_cat = $ret2['data'];
		$ret3 = $this->db->fetch('stats_all', []);
		$res_all = $ret3['data'];

		$i = 0;
		$old = $res_sub[0]['cat'];
		Logger::debug(__LINE__ . " App::getStats() OLD Category $old");
		foreach($res_sub as $rsub) {
			$cat = $rsub['cat']; 			// get Category;
			Logger::debug(__LINE__ . " App::getStats() Category $cat");
			if($cat !== $old) {
				Logger::debug(__LINE__ . " App::getStats() Cat " . $res_cat[$i]['cat']);
				array_push($result,$res_cat[$i]);
				Logger::debug(__LINE__ . " App::getStats() PUSH " . $res_cat[$i]['cat'] . "/" . $res_cat[$i]['sub']);
				$i++;
			}
			array_push($result,$rsub);
			Logger::debug(__LINE__ . " App::getStats() PUSH " . $rsub['cat'] . "/" . $rsub['sub']);
			$old = $cat;
		}
		array_push($result,$res_cat[$i]);
		array_push($result,$res_all);
		Logger::debug(__LINE__ . " App::getStats() PUSH " . $res_all['cat'] . "/" . $res_all['sub']);
		return $result;
	}
	// -------------------------------------------------
}
