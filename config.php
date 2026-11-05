<?php

return array(
	'log' => '/var/www/logs/mk-produkte.log',
	'debug' => true,
	'bar' => '/var/www/html/Produkte/barcodes',
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
		'items_barcode'    => 'SELECT id, category, subcategory FROM items WHERE id = ?',
		'items_barcode_update'    => 'UPDATE items SET barcode=? WHERE id = ?',
		'items_list'  => 'SELECT id, name, description, updated_at FROM items ORDER BY id DESC',
		'items_insert'=> 'INSERT INTO items (name, user_id, short, description, image, barcode, price, cost, stock, sold, category, subcategory, gender, state) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?) RETURNING id',
		'items_enum'  => "SELECT substring(column_type,5) as enums FROM information_schema.columns WHERE table_schema='produkte' AND table_name='items' and column_name=?",
		'items_delete'=> 'DELETE FROM items WHERE id = ? RETURNING id, name',
		'stats_sub'=> 'SELECT category as cat, subcategory as sub, COUNT(id) as nr, SUM(stock) as nstock, SUM(sold) as nsold, SUM(stock+sold) as nsum, SUM(stock*price) as bval, SUM(stock*cost) as bcost, SUM(sold*price) as oval, SUM(sold*cost) as ocost, (SUM(sold*price))-(SUM(sold*cost)) as oprofit, SUM((stock+sold)*price) as gval, SUM((stock+sold)*cost) as gcost FROM items GROUP BY subcategory ORDER BY category',
		'stats_cat'   => 'SELECT category as cat,"All" as sub, COUNT(id) as nr, SUM(stock) as nstock, SUM(sold) as nsold, SUM(stock+sold) as nsum, SUM(stock*price) as bval, SUM(stock*cost) as bcost, SUM(sold*price) as oval, SUM(sold*cost) as ocost, (SUM(sold*price))-(SUM(sold*cost)) as oprofit, SUM((stock+sold)*price) as gval, SUM((stock+sold)*cost) as gcost FROM items GROUP BY category ORDER BY category',
		'stats_all'   => 'SELECT "All" as cat,"All" as sub, COUNT(id) as nr, SUM(stock) as nstock, SUM(sold) as nsold, SUM(stock+sold) as nsum, SUM(stock*price) as bval, SUM(stock*cost) as bcost, SUM(sold*price) as oval, SUM(sold*cost) as ocost, (SUM(sold*price))-(SUM(sold*cost)) as oprofit, SUM((stock+sold)*price) as gval, SUM((stock+sold)*cost) as gcost FROM items'
	),
	'fetchAll' => array(
		'items_list' => true,
		'items_filter_all' => true,
		'items_filter_stock' => true,
		'items_filter_no_stock' => true,
		'stats_sub' => true,
		'stats_cat' => true,
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
	'gender' => array('Herren','Damen','Kinder','Sonstiges'),
	'state' => array('sehr gut','gut','mittel','schlecht','sehr schlecht'),
);
