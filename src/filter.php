
<!-- ======================= Show ENUMs in Select-Option ============================ -->
<!--
	Benötigt:
	- $action
	- $enums['category']
	- $enums['subcategory']
	- $selected['category']
	- $selected['subcategory']
-->
<div class="filter_container">
<form method="post" action="<?php echo $action;?>">
<table class="filter">
	<tr><td class="input"><label>Category:</label></td>
		<td>
		<select name="category" onChange='this.form.submit();'>
	<?php foreach($enums['category'] as $e) { ?>
		<option value="<?php echo $e; ?>" <?php if($e === $selected['category']) { echo "selected"; } ?>><?php echo $e; ?></option>
	<?php } ?>
	</select>
	</td></tr>
	<tr><td class="input"><label>Subcategory:</label></td>
		<td>
		<select name="subcategory">
	<?php foreach($enums['subcategory'] as $e) { ?>
		<option value="<?php echo $e; ?>" <?php if($e === $selected['subcategory']) { echo "selected"; } ?>><?php echo $e; ?></option>
	<?php } ?>
	</select>
	</td></tr>

	<?php $check = 'checked="checked"';?>
	<tr><td class="input"><label>Im Bestand:</label></td><td>
		<input type="radio" id="stock_ja" name="in_stock" value="stock_ja" <?php if($selected['stock_ja']) echo $check;?>><label for="stock_ja">Ja</label>
		<input type="radio" id="stock_no" name="in_stock" value="stock_no" <?php if($selected['stock_no']) echo $check;?>><label for="stock_no">Nein</label>
		<input type="radio" id="stock_all" name="in_stock" value="stock_all" <?php if($selected['stock_all']) echo $check;?>><label for="stock_all">Alle</label> 
	</td></tr>
	<tr><td class="button" colspan=2>
		<button type="submit" name="filter" value="search">Suchen ...</button>
	</td></tr>
</table>
</form>
</div>

