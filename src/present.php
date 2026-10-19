
<!-- ====================== Anzeige Html ========================= -->
<!--
	Benötigt:
	- $item[]
-->
<div class="present_container"><table class="present">
	<tr><td colspan=2><h2><?php echo $item['name'];?></h2></td></tr>
	<tr><td><img src="uploads/<?php echo $item['image'];?>" alt="Produktbild"></td>
		<td><p><?php echo $item['short'];?></p></td>
	</tr>
	<tr><td class="description" colspan=2><?php echo $item['description'];?></td></tr>
</table>

