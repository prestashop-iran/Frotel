<?php


if (!defined('_PS_VERSION_'))
	exit;

class FrotelPayment extends PaymentModule
{
	private $_html = '';
    public  $order_id = NULL;
	
	public function __construct()
	{
		$this->name = 'frotelpayment';
		$this->tab = 'payments_gateways';
		$this->version = '1.0';
        $this->author = 'Presta-Shop.IR';
		$this->limited_countries = array('ir');   // iran
		
        
		$this->currencies = true;
		$this->currencies_mode = 'checkbox';      //

        parent::__construct();

        $this->_errors = array();
		$this->page = basename(__FILE__, '.php');
        
        $this->displayName = $this->l('Frotel Payment');
        $this->description = $this->l('Frotel payment module description'); 
        
        // Uninstall message  
		$this->confirmUninstall = $this->l('plaese read the document before uninstall this module');
		
        
		if (self::isInstalled($this->name))
		{
           // Its OK!
        }
	}
    
    /**
     * Install
     * 
     * */
    public function install()
	{
        
		/* Install and register on hook */
		if (!Module::install()
			OR !$this->registerHook('payment')
			OR !$this->registerHook('paymentReturn')
			OR !$this->registerHook('shoppingCartExtra')
			OR !$this->registerHook('backBeforePayment')
			OR !$this->registerHook('paymentReturn')
			OR !$this->registerHook('cancelProduct')
            OR !$this->registerHook('newOrder')
            )
			return false;
        
		
        $orderState = array();
        
        // Order status color
		$colors = array('#fbfbfb', '#DDEEFF', '#DDEEF1', '#DDE1FF', '#D1EEFF', '#DDEE5F', '#DD5EFF', '#5DEEFF');
        // Order's status name
        $namesFa = array($this->l('montazer havale'), $this->l('moalagh'), $this->l('amade ersal'), $this->l('ersal shode'), $this->l('tozi shode'), $this->l('vosul shode'), $this->l('bargashti'), $this->l('enserafi') );
        
        // Register Statuses
        for($i=0; $i<7; $i++){
        
         if (!Configuration::get('FROTEL_ORDER_STATE_'.$i))
		  {
			$orderState[$i] = new OrderState();
			$orderState[$i]->name = array();
			foreach (Language::getLanguages() AS $language)
			{
				if (strtolower($language['iso_code']) == 'fa')
					$orderState[$i]->name[$language['id_lang']] = $this->l('frotel:: ').$namesFa[$i];
				else
					$orderState[$i]->name[$language['id_lang']] = 'Frotel'.$i;
			}
            // status Properties
            $orderState[$i]->send_email = false;
			$orderState[$i]->color = $colors[$i];
			$orderState[$i]->hidden = false;
			$orderState[$i]->delivery = false;
			$orderState[$i]->logable = true;
			$orderState[$i]->invoice = true;
			if ($orderState[$i]->add())
				//copy
			 Configuration::updateValue('FROTEL_ORDER_STATE_'.$i, (int)$orderState[$i]->id);
		 }// end if
        }// end for
		
		return true;
	}
    
    /**
     * Uninstall
     * 
     * */
    public function uninstall()
	{	
		parent::uninstall();
        return true;
	}
    
    /**
     * Hook Payment
     * Display payment options
     * */
    public function hookPayment($params)
	{
		global $smarty, $cart, $cookie, $order;


		if (!$this->active)
			return ;
		 //
         // generate new token
        $token = ( uniqid( hash("md5", time()), TRUE ) . time() . @$_SERVER['REMOTE_ADDR']);
        $cookie->__set('paymentToken', $token);
        
        if($cart->id_carrier == Configuration::get('FROTEL_PISHTAZ_O_CARRIER_ID'))
            return $this->_payitOnline('1');
        elseif($cart->id_carrier == Configuration::get('FROTEL_SEFARESHI_O_CARRIER_ID'))
            return $this->_payitOnline('2');
        elseif($cart->id_carrier == Configuration::get('FROTEL_PISHTAZ_C_CARRIER_ID'))
            return $this->_payitCod('1');
        elseif($cart->id_carrier == Configuration::get('FROTEL_SEFARESHI_C_CARRIER_ID'))
            return $this->_payitCod('2');
            
         if(isset($cookie->paymentToken)){
            $cookie->__unset('paymentToken');
        }
        
         
	}
    
    /**
     * Pay Online 
     * 
     * */
    private function _payitOnline($SendType)
    {
    global $cart, $cookie, $smarty;
    

    // check cart
	if (!$cart->getOrderTotal(true, Cart::BOTH))
		die('Empty cart');
        
    $cookie->__set('payType','online');
   //
   $firstStateId = (int)Db::getInstance()->getValue('SELECT id_state FROM '._DB_PREFIX_.'state WHERE iso_code= "AzS"');
       //
   $statesConvert = array(41,44,45,26,31,84,77,21,38,56,51,58,61,24,23,54,71,28,25,87,34,83,74,17,13,66,15,86,76,81,35);
        
   $Oaddress = new Address($cart->id_address_delivery);
    
    $Name       = $Oaddress->firstname;
    $Family     = $Oaddress->lastname;
    $Gender     = "";
    $Email      = $cookie->email;
    $IdOstan    = $statesConvert[$Oaddress->id_state - $firstStateId ];
    $IdShahr    = $Oaddress->city;
    $Address    = $Oaddress->address1.'-'.$Oaddress->address2.'-'.$Oaddress->other ;
    $PCode      = $Oaddress->postcode;
    $Telephone  = $Oaddress->phone;
    $Cellphone  = $Oaddress->phone_mobile;
    //
    $message = new MessageCore();
    $my_mes = $message->getMessageByCartId($cart->id);
    $Message    = $Oaddress->other.' / '.$my_mes['message'];//
    
    
    // Get cart currency and ..
    $Currrency     = new Currency();
    $rial = $Currrency->getCurrency($Currrency->getIdByIsoCode('IRR'));
    $currentCur = new Currency($cart->id_currency);
    $conversionRate = $this->getCartCurrencyRateOnline($currentCur, $rial);
    
    // Marjetting program
    if(isset($cookie->frotel_bz_id))
        $bazaryab = (int)$cookie->frotel_bz_id;
    else
        $bazaryab = 'fs';

    
    $myCart = $cart->getSummaryDetails();
    // Generate  product list in format
    foreach ($myCart['products'] as $product) {
        if(isset($cookie->frotel_bz_id)){
            $pur = (int)Db::getInstance()->getValue('SELECT percent FROM '._DB_PREFIX_.'frotel_product WHERE id_product='.$product['id_product']);
        }else{
            $pur = 0;
        }
	   $temp  = $product['id_product'].'^';
       $temp .= $product['name'].'^';
       $temp .= (int)$product['price_wt']* $conversionRate .'^';
       $temp .= $product['weight'].'^';
       $temp .= $product['quantity'].'^';
       $temp .= $pur.'^';
       $temp .= '';
       $RequestList[] = $temp;
       $temp = '';
    }
    
    //bank token
    $bankToken = ( uniqid( hash("md5", time()), TRUE ) . time() . @$_SERVER['REMOTE_ADDR']);
    if(isset($cookie->bankToken))
        $cookie->__unset('bankToken');
    $cookie->__set('bankToken', $bankToken);
    
    //
    $RequestList = implode(';', $RequestList);  
    $BuyType = "saman";  
    $Fish = "";
    $Bank = "";
    $Bazaryab = $Bazaryab;  //  
    
    // Verify Url
    $VerifyUrl = _PS_BASE_URL_SSL_.__PS_BASE_URI__.'modules/frotelpayment/payment/submit.php?banktoken='.$bankToken; 
    $Username = Configuration::get('FROTEL_USERNAME');
    $Password = Configuration::get('FROTEL_PASSWORD');

    $soap = new SoapClient("http://www.froservice.ir/F-W-S-L/F_Gateway.php?wsdl");
    
    // register an order in Web Service
    $Res = $soap-> FSetOrder($Name,$Family,$Gender,$Email,$IdOstan,$IdShahr,$Address,$PCode,$Telephone,$Cellphone,$Message,$SendType,$RequestList,$BuyType,$Fish,$Bank,$Bazaryab,$VerifyUrl,$Username,$Password);
    $Res = urldecode($Res);

    $ResArray = explode("^^",$Res);
    
    //
    echo '<style>div#center_column h4 {display:none;}</style>';
    
    if(($ResArray[0]!="") && ($ResArray[1]!="")){
        
        $cookie->__unset('paymentToken');
       
       // Store bill in cookie
	   $cookie->__set('frotelFactor', $ResArray[0]);    //factor
       $cookie->__set('frotelRahgiri', $ResArray[1]);   //rahgiri
	   $PM = $ResArray[2];
	   $out = "<div><p> $PM </p></div>";

    }else{
	   $out = "<div><p> $Res </p></div>";
    }
    
    echo '<style>center input{  padding: 4px 8px; height: 27px; border-radius: 4px; background: #86Ae12; box-shadow: 0 15px 5px #8EBE16 inset; color: #EEE; text-shadow: 0 0 1px #CCC; margin: 10px; cursor: pointer;}</style>';

   $smarty->assign('out', $out);
   
   return $this->display(__FILE__, 'payment/payment_online.tpl');
 }
    
    /**
     * Pay Cod
     * 
     * */
    private function _payitCod($sendType)
    {
        global $cart, $cookie, $smarty;
    
        $Currrency        = new Currency();
        $rial             = $Currrency->getCurrency($Currrency->getIdByIsoCode('IRR'));
        $currentCurrency  = $Currrency->getCurrency($cart->id_currency);
        $conversionRate = $this->getCartCurrencyRate($rial ,$currentCurrency);
        
        // check cart
	    if (!$cart->getOrderTotal(true, Cart::BOTH))
		  die('Empty cart');
    
        $cookie->__set('payType','cod');
        //
        $currency = new Currency($cart->id_currency);
        $total = $cart->getOrderTotal(true, Cart::BOTH);
        
        $smarty->assign('amount', Tools::displayPrice($total+((int)($cookie->frotelKhadamat)* $conversionRate), $currency));
        $smarty->assign('action', __PS_BASE_URI__.'modules/'. $this->name.'/payment/submit.php');
        $smarty->assign('token', $cookie->paymentToken);
        
        echo '<style>
                input.fro_textbox {float: right; border-radius: 3px 3px 3px 3px; border: 1px solid rgb(187, 187, 187); font: 11px tahoma; padding: 5px 10px; text-align: left; color: rgb(85, 85, 85);}
                div#pForm td { padding: 3px 10px;}
                div#center_column h4 {display:none;}
                </style>';
        return $this->display(__FILE__, 'payment/payment_cod.tpl');
    
    }
 
    private function _convertC ($price, $from, $to)
    {
        return (float)((int)$price * ((float)$from / (float)$to));
    }
    
    public function hookShoppingCartExtra($params)
	{
	   //
	}
    
    public function hookPaymentReturn($params)
	{
	   //
	}
    
    /**
     * Get shop Domain whth SSL (if used)
     * 
     * */
    public static function getShopDomainSsl($http = false, $entities = false)
	{
		if (!($domain = Configuration::get('PS_SHOP_DOMAIN_SSL')))
			$domain = Tools::getHttpHost();
		if ($entities)
			$domain = htmlspecialchars($domain, ENT_COMPAT, 'UTF-8');
		if ($http)
			$domain = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$domain;
		return $domain;
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
    /*
    */
    public function getCartCurrencyRateOnline($id_currency_origin, $currentCurrency)
	{
		$conversionRate = 1;

		if ($currentCurrency['id_currency'] != $id_currency_origin->id_currency)
		{
			$conversionRate /= $id_currency_origin->conversion_rate;
			$conversionRate *= $currentCurrency['conversion_rate'];
		}
		return $conversionRate;
	}
    

}

