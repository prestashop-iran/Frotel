<?php

	// Init
	$sql = array();

	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'frotel_product` (
			  `id_frotel_product` int(10) NOT NULL AUTO_INCREMENT,
			  `id_product` int(10) NOT NULL,
			  `percent` int(10) NOT NULL,
			  UNIQUE(`id_product`),
			  PRIMARY KEY  (`id_frotel_product`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


	// orders table
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'frotel_order` (
			`id_frotel_order` int(10) NOT NULL AUTO_INCREMENT,
            `id_order` int(10) NOT NULL,
            `intercept` varchar(30) NOT NULL,
            `bill` varchar(30) NOT NULL,
            UNIQUE(`id_order`),
			PRIMARY KEY  (`id_frotel_order`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	//
    $sql[] = 'UPDATE  '._DB_PREFIX_.'tab SET  module = "frotel" WHERE  class_name = "AdminCatalog";';
    
    $sql[] = 'UPDATE  '._DB_PREFIX_.'country SET  contains_states = "1" WHERE  iso_code ="IR" LIMIT 1;' ;
    
    $sql[] = 'UPDATE  '._DB_PREFIX_.'currency SET  conversion_rate =  "10" WHERE  iso_code ="IRR" LIMIT 1 ;';
    $sql[] = 'UPDATE  '._DB_PREFIX_.'currency SET  conversion_rate =  "1.0" WHERE  iso_code ="IRT" LIMIT 1 ;';
    
    $sql[] = 'CREATE TABLE  '._DB_PREFIX_.'frotel_cache (
            id_frotel_cache INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            id_cart INT( 10 ) NOT NULL ,
            hash VARCHAR( 300 ) NOT NULL ,
            price VARCHAR( 50 ) NOT NULL ,
            UNIQUE (id_cart)) ENGINE = INNODB;';

