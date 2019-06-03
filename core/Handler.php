<?php
class Handler
{
	function dumpy($data)
	{
		echo '<pre>';
		print_r($data);
		echo '</pre>'; 
		die();
	}
}
?>