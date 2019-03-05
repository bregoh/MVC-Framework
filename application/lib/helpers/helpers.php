<?php 
/*
** Dumpy is used for debugging
*/
function dumpy($data)
{
	echo '<pre>';
	var_dump($data);
	echo '</pre>'; 
	die();
}
?>