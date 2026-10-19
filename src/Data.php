<?php
require __DIR__ . '/../autoload.php';
use MK\Logger;

class Data {

	private $db;

	public function __construct($db) {
		$this->db = $db;
	}

	// ---------- Get ENUMs from Database for select options. 
	public function getEnums($category,$subcategory) {
		$enums = [];
		$enum = ['category','gender','subcategory','state'];
		foreach($enum as $e) {
			// ---- Füllen der Unterkategorie ----
			if($e === 'subcategory') {
				if($category) {
					$enums['subcategory'] = $subcategory[$category];
				} else {
					$enums['subcategory'] = $subcategory[$enums['category'][0]];
				}
			} 
			else {
				$ret = $this->db->fetch('items_enum', [$e]);
				$str = str_replace(['(',')','\''],'',$ret['data']['enums']);
				$arr = explode(',',$str);
				Logger::debug(__LINE__ . " $app - Fetch Enums for $e: " . json_encode($arr));
				$enums[$e] = $arr;
			}
		}
	return $enums;
	}
	// -------------------------------------------------
}
