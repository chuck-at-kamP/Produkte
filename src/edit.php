
<form method="post" action="<?php echo $action;?>" onSubmit="beforeSubmit()" enctype="multipart/form-data">
   	<input type="hidden" name="action" value="<?php echo $action; ?>">
   	<input type="hidden" name="id" value="<?php echo $item['id']; ?>">
<table class="edit">
        <tr><td class="input"><label>Name * : </label></td><td>
			<input type="text" name="name" required placeholder="z.B. Jeanshose, blau" 
				value="<?php echo $item['name']; ?>" onChange="add('name')">
        </td></tr>
        <tr><td class="input"><label>Kurztext * : </label></td><td>
			<input type="text" name="short" placeholder="z.B. Größe M, guter Zustand" value="<?php echo $item['short']; ?>" class="short" onChange="add('short')">
        	</td></tr>
        <tr><td class="input"><label>Beschreibung * (Html): </label></td><td colspan=3>
			<textarea type="text" name="description"
				value="" class="desc" cols="65" rows="7" onChange="add('description')"><?php echo $item['description']; ?></textarea>
        </td></tr>
        <tr><td class="input"><label>Image * : </label></td><td colspan=3 style="text-align:left;">
			<input type="file" id="item_image" name="image" onchange="add('image');" onclick="this.value='';"/>
			<span style="font-size:20px; color:darkgreen; margin-left:10px;">File: <?php echo $item['image']; ?></span>
        </td></tr>
        <tr><td class="input"><label>Barcode: </label></td><td>
			<input type="text" name="barcode" placeholder="Barcode" value="<?php echo $item['barcode']; ?>" onChange="add('barcode')">
        </td></tr>
        <tr><td class="input"><label>Preis * (€): </label></td><td>
			<input type="text" name="price" placeholder="z.B. 10,20" value="<?php echo $item['price']; ?>" onChange="add('price')" required>
        	</td>
        	<td class="input"><label>Einkaufspreis (€): </label></td><td>
			<input type="text" name="cost" placeholder="z.B. 9,00" value="<?php echo $item['cost']; ?>" onChange="add('cost')">
        </td></tr>
        <tr><td class="input"><label>Bestand * : </label></td><td>
			<input type="text" name="stock" placeholder="z.B. 25" value="<?php echo $item['stock']; ?>" onChange="add('stock')" required>
        	</td>
        	<td class="input"><label>Verkauft: </label></td><td>
			<input type="text" name="sold" placeholder="z.B. 10" value="<?php echo $item['sold']; ?>" onChange="add('sold')">
        </td></tr>

<!-- ======================= Show ENUMs in Select-Option ============================ -->
<?php $i=0; foreach($enum as $cat) { $i++; if($i % 2 != 0) { echo "\t<tr>"; } ?>
<td class="input"><label><?php echo ucfirst($cat); ?><?php if($cat=== 'category' || $cat === 'subcategory') echo " * "; ?>:</label></td>
		<td>
		<select 
			name="<?php echo $cat; ?>" 
			<?php if($cat === 'category') echo "onChange='this.form.submit();'"; ?>
			<?php if($cat === 'category' || $cat === 'subcategory') echo "required"; ?>
			 onChange="add('<?php echo $cat;?>')">
	<?php foreach($enums[$cat] as $e) { ?>
		<option value="<?php echo $e; ?>" <?php if($e === $item[$cat]) { echo "selected"; } ?>><?php echo $e; ?></option>
	<?php } ?>
	</select></td><?php if($i % 2 == 0) { echo "\n\t</tr>\n"; } ?>
<?php } ?>
	<tr><td class="button" colspan=4>
		<button type="submit" name="submit" value="change">Ändern ...</button>
	</td></tr>
</table>
</form>


