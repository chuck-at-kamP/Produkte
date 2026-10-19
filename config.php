<?php

return array(
	'log' => '/var/www/logs/mk-produkte.log',
	'dbuser' => array(
		'dbhost' => 'localhost',
		'dbname' => 'users',
		'dbuser' => 'mk_user',
		'dbpass' => 'mk_user'
	),
	'sqluser' => array(
		'users_query' => 'SELECT id, password, fullname FROM user WHERE username = ?'
	),
	'db' => array(
		'dbhost' => 'localhost',
		'dbname' => 'produkte',
		'dbuser' => 'mk_produkte',
		'dbpass' => 'mk_produkte'
	),
	'sql' => array(
		// ----- Query db tables -----------
		'items_filter_all'      => 'SELECT * FROM items WHERE category = ? AND subcategory = ?',
		'items_filter_stock'    => 'SELECT * FROM items WHERE category = ? AND subcategory = ? AND stock > 0',
		'items_filter_no_stock' => 'SELECT * FROM items WHERE category = ? AND subcategory = ? AND stock = 0',
		'items_query' => 'SELECT * FROM items WHERE id = ?',
		'items_id'    => 'SELECT id FROM items WHERE id = ?',
		'items_list'  => 'SELECT id, name, description, updated_at FROM items ORDER BY id DESC',
		'items_insert'=> 'INSERT INTO items (name, user_id, short, description, image, barcode, price, cost, stock, sold, category, subcategory, gender, state) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?) RETURNING id',
		'items_enum'  => "SELECT substring(column_type,5) as enums FROM information_schema.columns WHERE table_schema='produkte' AND table_name='items' and column_name=?",
		'items_delete'=> 'DELETE FROM items WHERE id = ? RETURNING id, name'
	),
	'fetchAll' => array(
		'items_list' => true,
		'items_filter_all' => true,
		'items_filter_stock' => true,
		'items_filter_no_stock' => true,
	),
	'category' => array('Kleidung','Kinder','Wohnen','Schlafen','Haushalt','Kleinmöbel','Elektro'),
	'subcategory' => array(
		'Kleidung' => array('Kleid','Rock','Pullover','Bluse','Hemd','T-Shirt','Hose','Schuhe'),
		'Kinder'   => array('Spielzeug','Möbel','Kleidung'),
		'Wohnen'   => array('Polster','Schrank','Tisch'),
		'Schlafen' => array('Bett','Matratze','Gitterrost','Nachttisch'),
		'Haushalt' => array('Töpfe','Pfannen','Besteck','Teller'),
		'Elektro'   => array('Waschmaschine','Herd','Backofen','Kühlschrank'),
		'Kleinmöbel' => array('Stuhl','Kommode','Hocker'),
	),
);
