<?php

if (!defined('_PS_VERSION_'))
	exit;

include_once(_PS_MODULE_DIR_.'frotel/frotel.php');

class FrotelCarrier extends CarrierModule
{
	public  $id_carrier;

	private $_html = '';
	private $_postErrors = array();
	private $_moduleName = 'frotelcarrier';
    private $weightUnit = 'gr';



	public function __construct()
	{
		$this->name = 'frotelcarrier';
		$this->tab = 'shipping_logistics';
		$this->version = '1.0';
		$this->author = 'Presta-Shop.IR';
		$this->limited_countries = array('ir');   // iran

		parent::__construct ();

		$this->displayName = $this->l('Frotel post service');
		$this->description = $this->l('Frotel\'s shipping module');
        // Uninstall message
        $this->confirmUninstall = $this->l('plaese read the document before uninstall this module');   

		if (self::isInstalled($this->name))
		{
            // Its Ok!
        }
	}


	/**
	* Install 
	**/
	 public function install()
	{
	   // Shipping Carrier
		$carrierConfig = array(
			0 => array('name' => $this->l('pishtaz-online'),
				'id_tax_rules_group' => 0,
				'active' => true,
				'deleted' => 0,
				'shipping_handling' => false,
				'range_behavior' => 0,
				'delay' =>  array('en' => 'Description 1', Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')) => $this->l('Shipping in Pishtaz method anp Online payment')),
				'id_zone' => 1,
				'is_module' => true,
				'shipping_external' => true,
				'external_module_name' => $this->_moduleName,
				'need_range' => true
			),
			1 => array('name' => $this->l('sefareshi-online'),
				'id_tax_rules_group' => 0,
				'active' => true,
				'deleted' => 0,
				'shipping_handling' => false,
				'range_behavior' => 0,
				'delay' => array('en' => 'Description 1', Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')) => $this->l('Shipping in Sefareshi method and Online payment')),
				'id_zone' => 1,
				'is_module' => true,
				'shipping_external' => true,
				'external_module_name' => $this->_moduleName,
				'need_range' => true
			),
			2 => array('name' => $this->l('pishtaz-cod'),
				'id_tax_rules_group' => 0,
				'active' => true,
				'deleted' => 0,
				'shipping_handling' => false,
				'range_behavior' => 0,
				'delay' => array('en' => 'Description 1', Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')) => $this->l('Shipping in Pishtaz method and Cod payment')),
				'id_zone' => 1,
				'is_module' => true,
				'shipping_external' => true,
				'external_module_name' => $this->_moduleName,
				'need_range' => true
			),
			3 => array('name' => $this->l('sefareshi-cod'),
				'id_tax_rules_group' => 0,
				'active' => true,
				'deleted' => 0,
				'shipping_handling' => false,
				'range_behavior' => 0,
				'delay' => array('en' => 'Description 1', Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')) => $this->l('Shipping in Sefareshi method and Cod payment')),
				'id_zone' => 1,
				'is_module' => true,
				'shipping_external' => true,
				'external_module_name' => $this->_moduleName,
				'need_range' => true
			)
		);

		$id_pishtaz_online   = $this->installExternalCarrier($carrierConfig[0]);
		$id_sefareshi_online = $this->installExternalCarrier($carrierConfig[1]);
        $id_pishtaz_cod   = $this->installExternalCarrier($carrierConfig[2]);
		$id_sefareshi_cod = $this->installExternalCarrier($carrierConfig[3]);
        
        $curency = new Currency();
        // save id
		Configuration::updateValue('FROTEL_PISHTAZ_O_CARRIER_ID', (int)$id_pishtaz_online);
		Configuration::updateValue('FROTEL_SEFARESHI_O_CARRIER_ID', (int)$id_sefareshi_online);
        Configuration::updateValue('FROTEL_PISHTAZ_C_CARRIER_ID', (int)$id_pishtaz_cod);
		Configuration::updateValue('FROTEL_SEFARESHI_C_CARRIER_ID', (int)$id_sefareshi_cod);
		
        if (!parent::install() OR 
         !$this->registerHook('updateCarrier') OR 
         !$this->registerHook('cart') OR 
         !$this->registerHook('newOrder') //OR 
         //!Configuration::updateValue('PS_CURRENCY_DEFAULT', $curency->getIdByIsoCode('IRT'))
		 )
			return false;
            
		return true;
	}
	
    /**
     *  Uninstall
     * */
	public function uninstall()
	{
		// Uninstall
		if (!parent::uninstall() or !$this->unregisterHook('updateCarrier') or !$this->unregisterHook('cart') or !$this->unregisterHook('newOrder'))
			return false;
		
		// Delete External Carrier
		$pishtaz_online = new Carrier((int)(Configuration::get('FROTEL_PISHTAZ_O_CARRIER_ID')));
		$sefareshi_online = new Carrier((int)(Configuration::get('FROTEL_SEFARESHI_O_CARRIER_ID')));
        $pishtaz_cod = new Carrier((int)(Configuration::get('FROTEL_PISHTAZ_C_CARRIER_ID')));
		$sefareshi_cod = new Carrier((int)(Configuration::get('FROTEL_SEFARESHI_C_CARRIER_ID')));
        
		// If external carrier is default set other one as default
		if (Configuration::get('PS_CARRIER_DEFAULT') == (int)($pishtaz_online->id) ||
            Configuration::get('PS_CARRIER_DEFAULT') == (int)($sefareshi_online->id) ||
            Configuration::get('PS_CARRIER_DEFAULT') == (int)($pishtaz_cod->id) ||
            Configuration::get('PS_CARRIER_DEFAULT') == (int)($sefareshi_cod->id))
		{
			global $cookie;
			$carriersD = Carrier::getCarriers($cookie->id_lang, true, false, false, NULL, PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
			foreach($carriersD as $carrierD)
				if ($carrierD['active'] AND !$carrierD['deleted'] AND ($carrierD['name'] != $this->_config['name']))
					Configuration::updateValue('PS_CARRIER_DEFAULT', $carrierD['id_carrier']);
		} 

		// delete Carrier
		$pishtaz_online->deleted   = 1;
		$sefareshi_online->deleted = 1;
        $pishtaz_cod->deleted   = 1;
		$sefareshi_cod->deleted = 1;
		
        if (!$pishtaz_online->update() || !$sefareshi_online->update() ||
            !$pishtaz_cod->update() || !$sefareshi_cod->update())
			return false;

		return true;
	}
    
    /**
     *  Installing
     * */
	 public static function installExternalCarrier($config)
	{
		$carrier = new Carrier();
		$carrier->name = $config['name'];
		$carrier->id_tax_rules_group = $config['id_tax_rules_group'];
		$carrier->id_zone = $config['id_zone'];
		$carrier->active = $config['active'];
		$carrier->deleted = $config['deleted'];
		$carrier->delay = $config['delay'];
		$carrier->shipping_handling = $config['shipping_handling'];
		$carrier->range_behavior = $config['range_behavior'];
		$carrier->is_module = $config['is_module'];
		$carrier->shipping_external = $config['shipping_external'];
		$carrier->external_module_name = $config['external_module_name'];
		$carrier->need_range = $config['need_range'];

		$languages = Language::getLanguages(true);
		foreach ($languages as $language)
		{
			if ($language['iso_code'] == 'en')
				$carrier->delay[(int)$language['id_lang']] = $config['delay'][$language['iso_code']];
			if ($language['iso_code'] == Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')))
				$carrier->delay[(int)$language['id_lang']] = $config['delay'][$language['iso_code']];
		}

		if ($carrier->add())
		{
			$groups = Group::getGroups(true);
			foreach ($groups as $group)
				Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier_group', array('id_carrier' => (int)($carrier->id), 'id_group' => (int)($group['id_group'])), 'INSERT');
            // price range
			$rangePrice = new RangePrice();
			$rangePrice->id_carrier = $carrier->id;
			$rangePrice->delimiter1 = '0';           // lower bound
			$rangePrice->delimiter2 = '10000000';    // upper bound
			$rangePrice->add();
            // weight range
			$rangeWeight = new RangeWeight();
			$rangeWeight->id_carrier = $carrier->id;
			$rangeWeight->delimiter1 = '0';          // lower bound
			$rangeWeight->delimiter2 = '100000';     // ...
			$rangeWeight->add();

			$zones = Zone::getZones(true);
			foreach ($zones as $zone)
			{
				Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier_zone', array('id_carrier' => (int)($carrier->id), 'id_zone' => (int)($zone['id_zone'])), 'INSERT');
				Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_.'delivery', array('id_carrier' => (int)($carrier->id), 'id_range_price' => (int)($rangePrice->id), 'id_range_weight' => NULL, 'id_zone' => (int)($zone['id_zone']), 'price' => '0'), 'INSERT');
				Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_.'delivery', array('id_carrier' => (int)($carrier->id), 'id_range_price' => NULL, 'id_range_weight' => (int)($rangeWeight->id), 'id_zone' => (int)($zone['id_zone']), 'price' => '0'), 'INSERT');
			}

			// Copy Logo
			if (!copy(dirname(__FILE__).'/'.$carrier->name.'.jpg', _PS_SHIP_IMG_DIR_.'/'.(int)$carrier->id.'.jpg'))
				return false;

			// Return ID Carrier
			return (int)($carrier->id);
		}

		return false;
	}

	/**
	** Hook Update carrier
	**/
	 public function hookupdateCarrier($params)
	{
	   global $cookie;
       
       if(isset($cookie->payType))
            $cookie->__unset('payType');
            
		if ((int)($params['id_carrier']) == (int)(Configuration::get('FROTEL_PISHTAZ_O_CARRIER_ID')))
			Configuration::updateValue('FROTEL_PISHTAZ_O_CARRIER_ID', (int)($params['carrier']->id));
            
		if ((int)($params['id_carrier']) == (int)(Configuration::get('FROTEL_SEFARESHI_O_CARRIER_ID')))
			Configuration::updateValue('FROTEL_SEFARESHI_O_CARRIER_ID', (int)($params['carrier']->id));
            
        if ((int)($params['id_carrier']) == (int)(Configuration::get('FROTEL_PISHTAZ_C_CARRIER_ID')))
			Configuration::updateValue('FROTEL_PISHTAZ_C_CARRIER_ID', (int)($params['carrier']->id));
            
		if ((int)($params['id_carrier']) == (int)(Configuration::get('FROTEL_SEFARESHI_C_CARRIER_ID')))
			Configuration::updateValue('FROTEL_SEFARESHI_C_CARRIER_ID', (int)($params['carrier']->id));
	}

    /**
     * Hook Cart
     * if Cart get empty clear shipping cost Cache
     * 
     public function hookCart($params)
	{
	   global $cart;
       
       if($cart->nbProducts() == 0) {
            Db::getInstance()->ExecuteS('DELETE FROM  '._DB_PREFIX_.'frotel_cache 
                                        WHERE id_cart="'.$cart->id.'"');
       }
	   
    }
	*/
    
    /**
     * clear shipping cost Cache
     * 
    public function hooknewOrder($cart, $order, $customer, $currency, $orderStatus)
    {
        global $cookie;
        
        Db::getInstance()->ExecuteS('DELETE FROM  '._DB_PREFIX_.'frotel_cache 
                                        WHERE id_cart="'.$cart->id.'"');
        $cookie->__unset('payType');
    }
	*/
    
	/**
	* Generate Hash string
    **/
	public function getOrderShippingCostHash($wsParams)
	{
		$paramHash = '';
		$productHash = '';
		foreach ($wsParams['products'] as $product)
		{
			if (!empty($productHash))
				$productHash .= '|';
			$productHash .= $product['id_product'].':'.$product['id_product_attribute'].':'.$product['cart_quantity'];
		}
		foreach ($wsParams as $k => $v)
			if ($k != 'products')
			$paramHash .= '/'.$v;
		return md5($productHash.$paramHash);
	}
    
    /**
     * Get cached cost from db
     * */
    public function getOrderShippingCostCache($id, $hash)
	{
		// Get Cache
		$row = Db::getInstance()->getValue('
		SELECT price FROM '._DB_PREFIX_.'frotel_cache
		WHERE id_cart = '.(int)($id).'
		AND hash = "'.pSQL($hash).'"');
        
        return $row;
	}
    
    /**
     * Store shipping cost in db
     * */
    public function saveOrderShippingCostCache($id, $hash, $price)
	{
		
		Db::getInstance()->ExecuteS('INSERT INTO  '._DB_PREFIX_.'frotel_cache (id_frotel_cache, id_cart, hash, price)
                                     VALUES (NULL, "'.$id.'", "'.$hash.'","'.$price.'") 
                                     ON DUPLICATE KEY UPDATE price="'.$price.'",hash="'.$hash.'"' );
                                     
            
	}
    
    /**
     * Get shipping cost
     * */
	public function getOrderShippingCost($params, $shipping_cost)
	{
	   global $cart;
        // Init var
		$address = new Address($params->id_address_delivery);
		if (!Validate::isLoadedObject($address))
		{
			// If address is not loaded, we take data from shipping estimator module (if installed)
			global $cookie;
			$address->id_state = $cookie->id_state;
			$address->postcode = $cookie->postcode;
		}
        
        $carrier = ($this->id_carrier != 0 )? $this->id_carrier : Configuration::get('PS_CARRIER_DEFAULT');
        
        if(isset($cookie->payType) AND $cookie->payType == 'cod')
            $BuyStyle = '1';
        else 
            $BuyStyle = '0';
        // Webservices Params
		$wsParams = array(
			'id_cart' => $params->id,
			'id_address_delivery' => $params->id_address_delivery,
			'recipient_address1' => $address->address1,
			'recipient_address2' => $address->address2,
			'recipient_postalcode' => $address->postcode,
			'recipient_city' => $address->city,
            'shpping_type' => $carrier,
            'pay_type' => $BuyStyle,
            'currency' => $params->id_currency,
			'products' => $params->getProducts()
		);
        
        // Get Hash
		$myHash = $this->getOrderShippingCostHash($wsParams);
        
        
       // Check cache
		$cache = $this->getOrderShippingCostCache($cart->id, $myHash);
            
        if((int)$cache > 0 OR (string)$cache == '0' )
            return $cache;
        else{       //cache not found
       
       $totalPrice  =  $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
       $TotalWeight =  $cart->getTotalWeight();
       
       $Currrency     = new Currency();
       $rial = $Currrency->getCurrency($Currrency->getIdByIsoCode('IRR'));
       $currentCurrency  = $Currrency->getCurrency($params->id_currency);
       //
       $firstStateId = (int)Db::getInstance()->getValue('SELECT id_state FROM '._DB_PREFIX_.'state WHERE iso_code= "AzS"');
       //
       $state       = new Frotel();
       $OMabda      = Configuration::get('FROTEL_SELLER_STATE');
       $SMabda      = Configuration::get('FROTEL_SELLER_CITY');
       
       $address     = new Address($cart->id_address_delivery);
       $OMaghsad    = $state->frotelGetstate($address->id_state - $firstStateId );
       $SMaghsad    = $address->city;
       
         

       $conversionRate = $this->getCartCurrencyRate($currentCurrency ,$rial);
       $TotalPrice = $totalPrice * $conversionRate;//  + $cookie->frotelKhadamat;
       
       // 
       if( $TotalPrice == 0 OR 
            empty($SMaghsad) OR
            empty($OMaghsad) OR
            $TotalWeight == 0 )
          return 0; 
       
       $soap = new SoapClient("http://www.froservice.ir/F-W-S-L/F_Gateway.php?wsdl");
	
		if ($this->id_carrier == (int)(Configuration::get('FROTEL_PISHTAZ_O_CARRIER_ID'))){
		  // Get shipping cost from Web Service
          $Res = $soap-> FCalcPPrice($TotalPrice,$TotalWeight,'0','1',$OMabda,$SMabda,$OMaghsad,$SMaghsad,Configuration::get('FROTEL_USERNAME'),Configuration::get('FROTEL_PASSWORD'));
          $Res = urldecode($Res);
          if((int)$Res >= 0){
            $conversionRate = $this->getCartCurrencyRate( $rial, $currentCurrency);
            $price = ((float)$Res + (float)$cookie->frotelKhadamat) * $conversionRate;
            // Store cost as cache
            $this->saveOrderShippingCostCache($cart->id, $myHash, $price);
            return $price;  //return cost
          }
            
          else{ //If an error occured return 0
		  	 $this->saveOrderShippingCostCache($cart->id, $myHash, 0);
			 return false;
		  }
            
		}
			
		if ($this->id_carrier == (int)(Configuration::get('FROTEL_SEFARESHI_O_CARRIER_ID'))){
		  // Get shipping cost from Web Service
          $Res = $soap-> FCalcPPrice($TotalPrice,$TotalWeight,'0','2',$OMabda,$SMabda,$OMaghsad,$SMaghsad,Configuration::get('FROTEL_USERNAME'),Configuration::get('FROTEL_PASSWORD'));
          $Res = urldecode($Res);
          if((int)$Res >= 0)
            {
            $conversionRate = $this->getCartCurrencyRate( $rial, $currentCurrency);
            $price = ((float)$Res + (float)$cookie->frotelKhadamat) * $conversionRate;
            // Store cost as cache
            $this->saveOrderShippingCostCache($cart->id, $myHash, $price);
            return $price;  // return cost
          }
          else{ //If an error occured return 0
  	         $this->saveOrderShippingCostCache($cart->id, $myHash, 0);
			 return false;
		  }
            
		}
        
        if ($this->id_carrier == (int)(Configuration::get('FROTEL_PISHTAZ_C_CARRIER_ID'))){
		  // Get shipping cost from Web Service
          $Res = $soap-> FCalcPPrice($TotalPrice,$TotalWeight,'1','1',$OMabda,$SMabda,$OMaghsad,$SMaghsad,Configuration::get('FROTEL_USERNAME'),Configuration::get('FROTEL_PASSWORD'));
          $Res = urldecode($Res);
          if((int)$Res >= 0){
            $conversionRate = $this->getCartCurrencyRate( $rial, $currentCurrency);
            $price = ((float)$Res + (float)$cookie->frotelKhadamat) * $conversionRate;
            // Store cost as cache
            $this->saveOrderShippingCostCache($cart->id, $myHash, $price);
            return $price;  //return cost
          }
            
          else{ //If an error occured return 0
		  	 $this->saveOrderShippingCostCache($cart->id, $myHash, 0);
			 return false;
		  }
            
		}
			
		if ($this->id_carrier == (int)(Configuration::get('FROTEL_SEFARESHI_C_CARRIER_ID'))){
		  // Get shipping cost from Web Service
          $Res = $soap-> FCalcPPrice($TotalPrice,$TotalWeight,'1','2',$OMabda,$SMabda,$OMaghsad,$SMaghsad,Configuration::get('FROTEL_USERNAME'),Configuration::get('FROTEL_PASSWORD'));
          $Res = urldecode($Res);
          if((int)$Res >= 0)
            {
            $conversionRate = $this->getCartCurrencyRate( $rial, $currentCurrency);
            $price = ((float)$Res + (float)$cookie->frotelKhadamat) * $conversionRate;
            // Store cost as cache
            $this->saveOrderShippingCostCache($cart->id, $myHash, $price);
            return $price;  // return cost
          }
          else{ //If an error occured return 0
  	         $this->saveOrderShippingCostCache($cart->id, $myHash, 0);
			 return false;
		  }
            
		}
        
     }//end else		

		return false;
	}
	
    /**
     * */
	 public function getOrderShippingCostExternal($params)
	{
	   $this->getOrderShippingCost($params);
	}
	
     /**
     * 
     * */
    public function getCartCurrencyRate($id_currency_origin, $currentCurrency)
	{
		$conversionRate = 1;

		if ($currentCurrency['id_currency'] != $id_currency_origin['id_currency'])
		{
			$conversionRate /= $id_currency_origin['conversion_rate'];
			$conversionRate *= $currentCurrency['conversion_rate'];
		}
		return $conversionRate;
	}
}


