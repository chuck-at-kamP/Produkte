<table class="title">
<tr>
	<td class=menu><button id="menu">&#9776;</button></td> 
	<td class="title"><h1><span id="device"> Produkte </span></h1></td>
	<td> (<?php echo $_SESSION['fullname'];?>) </td>
	<td class="image"><img class="title_image" src=""></td>
</tr>
</table>

<nav id="sidebar">
	<a id="menu_item" href="/Produkte/index.php"> Einrichten </a>
	<a id="menu_item" href="/Produkte/items.php"> Anzeigen </a>
	<a id="menu_item" href="/Produkte/change.php"> Ändern </a>
	<a id="menu_item" href="/Produkte/delete.php"> Löschen </a>
	<hr>
	<a id="menu_item" href="javascript:stop(); javascript:closeSidebar('sidebar')"> Reset </a>
	<hr>
	<a id="menu_item" href="/Produkte/login.php"> Login </a>
	<a id="menu_item" href="/Produkte/logout.php"> Logout </a>
</nav>

<!-- Ansonsten ist Sidebar einen kleinen Augenblick sichtbar -->
<script> javascript:closeSidebar('sidebar'); </script>

