<?php

	// Init
	$sql = array();
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'frotel_product`;';	
	//$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'frotel_bazaryab`;';
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'frotel_order`;';
    $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'frotel_cache`;';
    
    $sql[] = 'UPDATE  '._DB_PREFIX_.'tab SET  module = NULL WHERE  class_name = "AdminCatalog" ;';

?>
