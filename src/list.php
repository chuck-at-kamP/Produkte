<?php
require __DIR__ . '/../autoload.php';
use MK\Helper;
?>
<!-- ====================== Anzeige ========================= -->
<!-- Benötigt: - $item[] -->
<div class="list_container">
	<table class="data">
	<tr><td>Id:</td><td class="val"><?php echo Helper::e($item['id']); ?></td>
		<td>Name:</td><td class="val"><?php echo Helper::e($item['name']); ?></td></tr>
	<tr><td>User:</td><td class="val"><?php echo Helper::e($item['user_id']); ?></td>
		<td>Kurztext:</td><td class="val"><?php echo Helper::e($item['short']); ?></td></tr>
	<tr><td>Bild:</td><td class="val"><?php echo Helper::e($item['image']); ?></td>
		<td>Barcode:</td><td class="val"><?php echo Helper::e($item['barcode']); ?></td></tr>
	<tr><td>Preis:</td><td class="val"><?php echo Helper::e($item['price']); ?></td>
		<td>Einkaufspreis:</td><td class="val"><?php echo Helper::e($item['cost']); ?></td></tr>
	<tr><td>Bestand:</td><td class="val"><?php echo Helper::e($item['stock']); ?></td>
		<td>Verkauft:</td><td class="val"><?php echo Helper::e($item['sold']); ?></td></tr>
	<tr><td>Kategorie:</td><td class="val"><?php echo Helper::e($item['category']); ?></td>
		<td>Unter-Kategorie:</td><td class="val"><?php echo Helper::e($item['subcategory']); ?></td></tr>
	<tr><td>Gender:</td><td class="val"><?php echo Helper::e($item['gender']); ?></td>
		<td>Zustand:</td><td class="val"><?php echo Helper::e($item['state']); ?></td></tr>
	<tr><td>Angelegt:</td><td class="val"><?php echo Helper::e($item['created_at']); ?></td>
		<td>Geändert:</td><td class="val"><?php echo Helper::e($item['updated_at']); ?></td></tr>
</table>
</div>

