
<!-- ====================== Anzeige Html ========================= -->
<!--
	Benötigt:
	- $item[]
-->
<div class="present_container"><table class="present">
	<tr><td colspan=2><h2><?php echo $item['name'];?></h2></td></tr>
	<tr>
		<td><img src="uploads/<?php echo $item['image'];?>" alt="Produktbild"></td>
		<td><p><?php echo $item['short'];?></p>
			Preis: <span id="price"><?php echo $item['price'];?></span><br><br>
			Bestand: <span id="stock"><?php echo $item['stock'];?></span>
		</td>
	</tr>
	<tr><td class="description"><?php echo $item['description'];?></td>
		<td class="barcode" style="text-align:right; vertical-align:bottom;">
			<figure style="text-align:center;">
			<img width=100% src="barcodes/<?php echo $item['barcode'];?>.png" alt="Barcode">
			<figcaption><?php echo $item['barcode'];?></figcaption>
			</figure>
		</td>
	</tr>
</table>
</div>

