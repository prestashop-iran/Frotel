<?php

 $useSSL = true;

include_once(dirname(__FILE__).'/../../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../../init.php');

include_once(_PS_MODULE_DIR_.'frotelpayment/fa.php');
include_once(_PS_MODULE_DIR_.'frotelpayment/frotelpayment.php');



$fPayment = new FrotelPayment();

$errors = array();

 
 /**
  * Register COD order in Web Service
  * 
  * */
 function processCod()
{
    global $cart, $cookie, $fPayment;
    
    include(_PS_ROOT_DIR_.'/header.php');
    
    if (!$cookie->isLogged(true))
	{
		header('location:../../../'); exit;
		die('Not logged');
	}
	elseif (!$cart->getOrderTotal(true, Cart::BOTH))
		die('Empty cart');
    
    elseif ($cookie->paymentToken != Tools::getValue('codtoken'))
		Tools::redirect('order.php?step=3');
  //
  $Oaddress = new Address($cart->id_address_delivery);

    //
   $firstStateId = (int)Db::getInstance()->getValue('SELECT id_state FROM '._DB_PREFIX_.'state WHERE iso_code= "AzS"');
       //
   $statesConvert = array(41,44,45,26,31,84,77,21,38,56,51,58,61,24,23,54,71,28,25,87,34,83,74,17,13,66,15,86,76,81,35);
    
    $Name = $Oaddress->firstname;
    $Family = $Oaddress->lastname;
    $Gender = " ";
    $Email = $cookie->email;
    
    
    $IdOstan    = $statesConvert[$Oaddress->id_state - $firstStateId ];
    $IdShahr    = $Oaddress->city;
    $Address = $Oaddress->address1.'-'.$Oaddress->address2.'-'.$Oaddress->other ;
    $PCode = $Oaddress->postcode;
    $Telephone = $Oaddress->phone;
    $Cellphone = $Oaddress->phone_mobile;
    //
    $message = new MessageCore();
    $my_mes = $message->getMessageByCartId($cart->id);
    $Message    = $Oaddress->other.' / '.$my_mes['message'];//
    
    if($cart->id_carrier == Configuration::get('FROTEL_PISHTAZ_C_CARRIER_ID'))
        $SendType = '1';
    elseif($cart->id_carrier == Configuration::get('FROTEL_SEFARESHI_C_CARRIER_ID'))
        $SendType = '2';
    
    //
    $Currrency     = new Currency();
    $rial = $Currrency->getCurrency($Currrency->getIdByIsoCode('IRR'));
    $currentCur = new Currency($cart->id_currency);
    $conversionRate = getCartCurrencyRate($currentCur, $rial);
    
    //
    if(isset($cookie->frotel_bz_id))
        $bazaryab = (int)$cookie->frotel_bz_id;
    else
        $bazaryab = 'fs';
    //
    
    $myCart = $cart->getSummaryDetails();
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
    
    //
    $RequestList = implode(';', $RequestList);  
    $BuyType = "posti";  
    $Fish = "";
    $Bank = "";
    $Bazaryab = $Bazaryab;  //  
    $VerifyUrl = '';
    $Username = Configuration::get('FROTEL_USERNAME');
    $Password = Configuration::get('FROTEL_PASSWORD');

    $soap = new SoapClient("http://www.froservice.ir/F-W-S-L/F_Gateway.php?wsdl");
    $Res = $soap-> FSetOrder($Name,$Family,$Gender,$Email,$IdOstan,$IdShahr,$Address,$PCode,$Telephone,$Cellphone,$Message,$SendType,$RequestList,$BuyType,$Fish,$Bank,$Bazaryab,$VerifyUrl,$Username,$Password);
    $Res = urldecode($Res);
    $ResArray = explode("^^",$Res);
	
    if(($ResArray[0]!="") && ($ResArray[1]!="")){
        
       $cookie->__unset('paymentToken');
        //
	   $cookie->__set('frotelFactor', $ResArray[0]); //factor
       $cookie->__set('frotelRahgiri', $ResArray[1]); //rahgiri
       
       /// saving
       $fPayment->validateOrder((int)($cart->id), Configuration::get('FROTEL_ORDER_STATE_1'), (float)($cart->getOrderTotal(true, Cart::BOTH)), 'Frotel::'. $cookie->payType, $result[1].'___'.$result[2], array(), $cart->id_currency, $dont_touch_amount = false, $secure_key = false);
    
        $order = new Order($fPayment->currentOrder);
        
        
        
   
         if($order)
             Db::getInstance()->ExecuteS('INSERT INTO '._DB_PREFIX_.'frotel_order (id_frotel_order ,id_order ,intercept ,bill)
                                        VALUES (NULL ,  "'.$order->id.'",  "'.$cookie->frotelRahgiri.'",  "'.$cookie->frotelFactor.'")');
               
       //
       $PM = $ResArray[2];
       echo '<div id="frotel-show" style="padding: 0;box-shadow: 0 -65px 70px #F8F8F8 inset; width: 100%; padding-bottom: 20px; border-radius:5px 5px 5px 5px">
	<h1></h1>
	<p>';
	   echo" $PM ";
       echo '</p></div>';
    }else{
        echo '<div id="frotel-show" style="padding: 0;box-shadow: 0 -65px 70px #F8F8F8 inset; width: 100%; padding-bottom: 20px; border-radius:5px 5px 5px 5px">
	<h1></h1>
	<p>';
	   echo" $Res ";
       echo '</p></div>';
    }
    
    //
    include(_PS_ROOT_DIR_.'/footer.php');
	die ;
}

/**
 * Confirm Online payment and save order
 * */
 function processOnline(){
    global $cart, $cookie, $fPayment;
    
    include(_PS_ROOT_DIR_.'/header.php');
    
    if (!$cookie->isLogged(true))
	{
		header('location:../../../'); exit;
		die('Not logged');
	}
	elseif (!$cart->getOrderTotal(true, Cart::BOTH))
		die('Empty cart');
    
    elseif ($cookie->bankToken != Tools::getValue('banktoken'))
		Tools::redirect('order.php?step=3');
  //

    $ResNum = $_REQUEST['ResNum'];
    $RefNum = $_REQUEST['RefNum'];
    $State  = $_REQUEST['State'];
    
    if(empty($ResNum) OR empty($RefNum) OR empty($State) ){
        echo $fPayment->l('data not recieved from bank');
        Tools::redirect('order.php?step=3');
        include(_PS_ROOT_DIR_.'/footer.php');
	   die ;   
    }

    $VerifyUrl = _PS_BASE_URL_SSL_.__PS_BASE_URI__.'modules/frotelpayment/payment/submit.php';
    $Username = Configuration::get('FROTEL_USERNAME');
    $Password = Configuration::get('FROTEL_PASSWORD');
    
    $soap = new SoapClient("http://www.froservice.ir/F-W-S-L/F_Gateway.php?wsdl");
    
    // Confimation in Web Service
    $Res = $soap->FVerifyEndbuy($ResNum,$RefNum,$State,$VerifyUrl,$Username,$Password);
    $Res=urldecode($Res);
    
    // Handle Errors
    switch ($Res){ 
	case 'Access Denied':
        echo $fPayment->l('online: Access Denied');
	break;

	case 'Service Not Active':
        echo $fPayment->l('online:Service Not Active'); 
	break;

	default :{
	   // saving
       $fPayment->validateOrder((int)($cart->id), Configuration::get('FROTEL_ORDER_STATE_1'), (float)($cart->getOrderTotal(true, Cart::BOTH)), 'Frotel::'. $cookie->payType, $result[1].'___'.$result[2], array(), $cart->id_currency, $dont_touch_amount = false, $secure_key = false);
    
        $order = new Order($fPayment->currentOrder);
        
        
        
   
         if($order)
             if(!Db::getInstance()->ExecuteS('INSERT INTO '._DB_PREFIX_.'frotel_order (id_frotel_order ,id_order ,intercept ,bill)
                                        VALUES (NULL ,  '.$order->id.',  '.$cookie->frotelRahgiri.',  '.$cookie->frotelFactor.')'))
                die('Database Error!');
          
        echo '<div id="frotel-show" style="padding: 0;box-shadow: 0 -65px 70px #F8F8F8 inset; width: 100%; padding-bottom: 20px; border-radius:5px 5px 5px 5px">
	       <h1></h1>
	       <p>';      
        echo $Res;
        echo '</p></div>';
        
      }
    }// end switch
    

    //
    include(_PS_ROOT_DIR_.'/footer.php');
	die ;
}
 
 
/**
 * Convert Currency
 * */
function convertC ($price, $from, $to)
{
    return (float)((int)$price * ((float)$from / (float)$to));
}
/**
 * 
 * */
function getCartCurrencyRate($id_currency_origin, $currentCurrency)
	{
		$conversionRate = 1;

		if ($currentCurrency['id_currency'] != $id_currency_origin->id_currency)
		{
			$conversionRate /= $id_currency_origin->conversion_rate;
			$conversionRate *= $currentCurrency['conversion_rate'];
		}
		return $conversionRate;
	}
/**
 * Native translate
 * 
 * */
function l($string){
    global $myNative;
        return ($myNative['<{frotelpayment}prestashop>submit_'.md5($string)])? $myNative['<{frotelpayment}prestashop>submit_'.md5($string)] : $string;
}


// Global conditions: Handling requests
if(Tools::getValue('codtoken') AND Tools::getValue('submitPayment'))
	processCod();
elseif(Tools::getValue('banktoken') AND !Tools::getValue('submitPayment'))
    processOnline();
else
    die('Try again!'); 