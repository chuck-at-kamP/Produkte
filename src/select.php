<?php
require __DIR__ . '/../autoload.php';
use MK\Helper;
use MK\Logger;
?>
<!-- ======================= Bestehende Items auswählen ======================= -->

<div class="select_container">
<form method="post" action="<?php echo $action;?>">
<table class="select"><tr><td>
	<select class="choice_list" name="select_item" size="10">
		<option class="first_element" value="none">--- Wähle ein Produkt aus! ---</option>
<?php if (!empty($items)) { ?>
		<?php foreach ($items as $i) { ?>
			<option value="<?php echo Helper::e($i['id']); ?>" <?php if($selected['select_item'] == $i['id']) echo "selected"; ?>>
				<?php echo Helper::e($i['updated_at'])." (".Helper::e($i['id']).") ".Helper::e($i['name']); ?>
				<?php Logger::debug(__LINE__ . " src/select.php Selected: " . $selected['select_item'] . ", Item: " . $i['id']);
					if($selected['select_item'] == $i['id']) echo "selected"; ?>
			</option>
		<?php } ?>
<?php } ?>
	</select>
	</td></tr>
	<!-- ====================== Buttons ========================= -->
	<tr><td class="button">
		<button type="submit" name="choose" value="anzeigen">Anzeigen</button>
	</td></tr>
</table></form></div>

