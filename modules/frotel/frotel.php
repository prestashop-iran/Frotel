<?php

if (!defined('_PS_VERSION_'))
    exit;

class Frotel extends Module
{
    public $name    = NULL;
    
    private $_html = '';
    private $_postErrors = array();
    private $_fieldsList = array();
    private $_customerLogo = array();
    
    /**
     * Initialize
     * 
     * @return bool
     * */

     public function __construct()
    {
        $this->name = 'frotel';
        $this->tab = 'shipping_logistics';
        $this->version = 1.0;
        $this->author = 'Presta-Shop.IR';        // Author's name
        $this->limited_countries = array('ir');      // iran
        
        parent::__construct();
        
        $this->displayName = $this->l('Frotel');
        $this->description = $this->l('Frotel web service');
        
        if (self::isInstalled($this->name))         //after installing
		{
			// Loading Config Var's
			$warning = array();
			$this->loadingVar();

			// Check Soap availibility
			if (!extension_loaded('soap'))
				$warning[] = "'".$this->l('Soap extension not found')."', ";

			// Check Configuration Values
		    if (!Configuration::get('FROTEL_USERNAME') OR 
                !Configuration::get('FROTEL_PASSWORD') OR 
                !Configuration::get('FROTEL_SELLER_STATE') OR 
                !Configuration::get('FROTEL_SELLER_CITY'))
		        $warning[] = "'".$this->l('Fill all require field in Frotel module')."', ";   // warning message
            

			// Generate Warnings
			if (count($warning))
				$this->warning .= implode(' , ',$warning).$this->l('must be configured to use this module correctly').' ';
		 }
         
         //Unistalling message
         $this->confirmUninstall = $this->l('Please read the document before removing this module.');
        
    }
    
    /**
     * Set Config values
     * 
     * */
    public function loadingVar()
	{
		// Loading fields List
		$this->_fieldsList = array(
			'FROTEL_USERNAME' => '',                  // 
			'FROTEL_PASSWORD' => '',                  //
			'FROTEL_SELLER_STATE' => '',              // 
			'FROTEL_SELLER_CITY' => '',               // 
			'FROTEL_SHIP_TYPE_SEFARESHI' => 1,        // 
			'FROTEL_SHIP_TYPE_PISHTAZ' => 1,
			'FROTEL_PAY_TYPE_ONLINE' => 1,
			'FROTEL_PAY_TYPE_COD' => 1,
			'FROTEL_PAY_TYPE_HAVALE' => 0,            // Not available at this time (Frotel)
			'FROTEL_BAZARYAB_POSSIBILITY' => 0,       // Marketting Program
            'FROTEL_STATE_UPDATE' => date('Y-m-d H:i:s'),   // require for updating order status
            'FROTEL_ORDER_EXPIRE' => date('Y-m-d H:i:s')    // Not available at this time (Frotel)
		);
    }
    
    /**
     * Installing
     * */
     public function install()
	{
		// Install SQL
		include(dirname(__FILE__).'/sql-install.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->Execute($s))
				return false;
        
        // Config
        $this->loadingVar();
        foreach ($this->_fieldsList as $keyConfiguration => $name)
			if (!Configuration::updateValue($keyConfiguration, $name))
                return false;
        if(!Configuration::updateValue('PS_WEIGHT_UNIT', $this->l('gr')))
            return false;

		// Install Module and Register Hook's
		if (!parent::install() 
            OR !$this->registerHook('header') 
            OR !$this->registerHook('addproduct') 
		    OR !$this->registerHook('updateproduct')
            OR !$this->registerHook('deleteproduct') 
            OR !$this->registerHook('myAccountBlock')   // account block
            OR !$this->registerHook('customerAccount')  // account page
            OR !$this->registerHook('invoice')          // Order review in backoffice
            OR !$this->registerHook('leftcolumn')
            OR !$this->registerHook('rightcolumn'))         
			return false;

		return true;
	}
    
    /**
     * Uninstall
     * */
     public function uninstall()
	{
		
		// Uninstall Config
        $this->loadingVar();
		foreach ($this->_fieldsList as $keyConfiguration => $name)
			if (!Configuration::deleteByName($keyConfiguration))
				return false;

		// Uninstall SQL
		include(dirname(__FILE__).'/sql-uninstall.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->Execute($s))
				return false;

		// Uninstall Module and Hook's
		if (!parent::uninstall() 
            OR !$this->unregisterHook('header') 
            OR !$this->unregisterHook('addproduct') 
		    OR !$this->unregisterHook('updateproduct') 
            OR !$this->unregisterHook('myAccountBlock')
            OR !$this->unregisterHook('deleteproduct')
            OR !$this->unregisterHook('customerAccount')
            OR !$this->unregisterHook('invoice')
            OR !$this->unregisterHook('leftcolumn')
            OR !$this->unregisterHook('rightcolumn'))
			return false;

		return true;
	}
    
    /**
     * Module Configuration Form
     * 
     * */
     public function getContent()
	{
		$this->_html .= '<h2>' . $this->l('Frotel').'</h2>';
		
        // On submit
        if (!empty($_POST) AND Tools::isSubmit('submitSave'))
		{
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors AS $err)
					$this->_html .= '<div class="alert error"><img src="'._PS_IMG_.'admin/forbbiden.gif" alt="nok" />&nbsp;'.$err.'</div>';
		} // end if
        
		$this->_displayForm();
		return $this->_html;
	}
     
     /**
     * Config Form Validation
     * 
     * */
     private function _postValidation()
	{
		// Check configuration values
		if (!Tools::getValue('username') OR !Validate::isCleanHtml(Tools::getValue('username')))
			$this->_postErrors[] = $this->l('username not valid');
            
		if (!Tools::getValue('password') OR !Validate::isCleanHtml(Tools::getValue('password')))
			$this->_postErrors[] = $this->l('password not valid');
            
		if (!Tools::getValue('id_ostan'))
			$this->_postErrors[] =  $this->l('you must select state');
            
		if (!Tools::getValue('id_shahr'))
			$this->_postErrors[] =  $this->l('you must select city');
            
		/*if (!Tools::getValue('shiptype_sef') AND !Tools::getValue('shiptype_pish'))
			$this->_postErrors[] =  $this->l('you must select at least one ship sys');
            
		if (!Tools::getValue('paytype_online') AND !Tools::getValue('paytype_cod'))
			$this->_postErrors[] =  $this->l('you must select at least one pay sys');*/
		
	}
    
    /**
     * Store configuration
     * 
     * */
     private function _postProcess()
	{
		// Saving new configurations
		if (Configuration::updateValue('FROTEL_USERNAME', Tools::getValue('username')) AND
			Configuration::updateValue('FROTEL_PASSWORD', Tools::getValue('password')) AND
			Configuration::updateValue('FROTEL_SELLER_STATE', Tools::getValue('id_ostan')) AND
			Configuration::updateValue('FROTEL_SELLER_CITY', Tools::getValue('id_shahr')) AND
			/*Configuration::updateValue('FROTEL_SHIP_TYPE_SEFARESHI', Tools::getValue('shiptype_sef')) AND
			Configuration::updateValue('FROTEL_SHIP_TYPE_PISHTAZ', Tools::getValue('shiptype_pish')) AND
			Configuration::updateValue('FROTEL_PAY_TYPE_ONLINE', Tools::getValue('paytype_online')) AND
			Configuration::updateValue('FROTEL_PAY_TYPE_COD', Tools::getValue('paytype_cod')) AND*/
			Configuration::updateValue('FROTEL_BAZARYAB_POSSIBILITY', Tools::getValue('bazaryab')))
			  $this->_html .= $this->displayConfirmation($this->l('Settings updated')); // It's Ok
		else
			  $this->_html .= $this->displayErrors($this->l('Settings failed in updating')); // an Error occured
            
       // Clear shipping cost Cache
       Db::getInstance()->ExecuteS('TRUNCATE TABLE  '._DB_PREFIX_.'frotel_cache');
            
	}
    
    /**
     * Hook Header
     * Update Order Status and Set some var
     * */
     public function hookHeader()
    {
        global $cookie;

        //Marketing plan: Get mrketer's ID from URI
        if(Configuration::get('FROTEL_BAZARYAB_POSSIBILITY')==1 AND Validate::isUnsignedInt(Tools::getValue('bz'))){
            if(empty($cookie->frotel_bz_id)){
                $cookie->__set('frotel_bz_id', (int)Tools::getValue('bz'));
            }
        }
        
        // Update Order Status every 3600 sec
        if (strtotime("now")- strtotime(Configuration::get('FROTEL_STATE_UPDATE')) > 3600){
            
            $this->_updateStates(); // call update function
            Configuration::updateValue('FROTEL_STATE_UPDATE',date('Y-m-d H:i:s'));  // reset db time
            
            $cookie->__unset('frotelKhadamat'); // unset Services cost
            Db::getInstance()->ExecuteS('TRUNCATE TABLE  '._DB_PREFIX_.'frotel_cache'); // clear shipping cost Cache
            
        }
        
        // get services cost from Web-Service
        if(!$cookie->frotelKhadamat){
        
            $soap = new SoapClient("http://www.froservice.ir/F-W-S-L/F_Gateway.php?wsdl");
            $res = $soap-> FKhadamat();
            $res = urldecode($res);
            $cookie->__set('frotelKhadamat', $res); // store in cookie
        }
		
		Tools::addJS(($this->_path).'frotel.js');
        
    }
    
    /**
     * Hook Invoice
     * display order ID and intercept
     * */
      public function hookInvoice($params)
     {
        
        $result = Db::getInstance()->ExecuteS('SELECT intercept, bill FROM '. _DB_PREFIX_.'frotel_order WHERE id_order="'.$params['id_order'].'" LIMIT 1');
        
        //if($result)
        echo '<fieldset style="width: 400px; float: left; margin: 0 0 20px 30px;">
	       <legend>'.$this->l('etelate sefaresh dar frotel').'</legend>
 
	       <div id="info" border:="" solid="" red="" 1px;"="">
	       <table>
	           <tbody>
                    <tr><td>'.$this->l('code rahgiri: ').'</td> <td><b>'.$result[0]['intercept'].'</b></td></tr>
	                <tr><td>'.$this->l('factor num: ').'</td> <td><b>'.$result[0]['bill'].'</b></td></tr>
	           </tbody></table>
	       </div>
 
            </fieldset>';
        
     }
     
    /**
     * Hook Add Product
     * store product's percent in db
     * */
     public function hookaddproduct($params)
	{
		if (!isset($params['product']->id))
			return false;
		$id_product = $params['product']->id;
		if ((int)$id_product < 1)
			return false;
         
        $percent = (int)Tools::getValue('fro_percent'); // 
         
        $data = Db::getInstance()->ExecuteS('INSERT INTO '._DB_PREFIX_.'frotel_product (id_product, percent) 
                                             values('.$id_product.', '.$percent.') 
                                             ON DUPLICATE KEY UPDATE percent='.$percent);
        if(!$data)
            return false;
        
    }
    
    /**
     * Hook Update Product
     * store product's percent in db
     * */
     public function hookupdateproduct($params) { $this->hookaddproduct($params); }
    
    /**
     * Hook Account Block
     * Customer links
     * */
     public function hookmyAccountBlock()
    {
        // This hook not necessary at this time (frotel)
        
        return ;
        
        global $cookie;
        
        $temp = '';
        
        // Token not necessary here, but its better than ...
        if(isset($cookie->frotelToken)){
            $cookie->__unset('frotelToken');
        }
        
        $token = ( uniqid( hash("md5", time()), TRUE ) . time() . @$_SERVER['REMOTE_ADDR']);
        $cookie->__set('frotelToken', $token);
         
         
         //   display Link
        if(Configuration::get('FROTEL_BAZARYAB_POSSIBILITY') == 1){
            $temp .= '<li><a href="'.__PS_BASE_URI__.'modules/'. $this->name.'/join.php?token='.$token.'" title="">';
            // if logo exist
            if(isset($this->_customerLogo['marketing']))
                $temp .= '<img src="'.$this->_customerLogo['marketing'].'" alt="'.$this->l('Start marketing program').'" class="icon" />';
            $temp .= $this->l('Start marketing program').'</a></li>';
        }
            
        
        return $temp;
    }
    
    /**
     * Hook Customer Account
     * Customer links
     * */
    public function hookcustomerAccount()
    {
        // set logo
        $this->_customerLogo['marketing'] = __PS_BASE_URI__.'modules/'. $this->name.'/marketing.gif';
        
        return $this->hookmyAccountBlock();
    }
    
    /**
     * Display Marketting program link
     * 
     * */
     function hookLeftColumn($params)
    {
		global $smarty, $link, $cookie;
		
        if(Configuration::get('FROTEL_BAZARYAB_POSSIBILITY') != 1)
            return;
        
        // Token not necessary here, but its better than ...
        if(isset($cookie->frotelToken)){
            $cookie->__unset('frotelToken');
        }
        
        $token = ( uniqid( hash("md5", time()), TRUE ) . time() . @$_SERVER['REMOTE_ADDR']);
        $cookie->__set('frotelToken', $token);
        
        $temp  = '';
        
        $temp .= '<div id="frotel_block_left" class="block">
	           <div class="block_content" style="padding:10px 10px 5px 5px"><ul>';
                
        $temp .= '<li><a href="'.__PS_BASE_URI__.'modules/'. $this->name.'/join.php?token='.$token.'" title="">';
        
        $temp .= '<img src="'.__PS_BASE_URI__.'modules/'. $this->name.'/bazaryabi.png" alt="'.$this->l('Start marketing program').'" class="icon" style="margin:2px 30px;" />';
        
        $temp .= '<img src="'.__PS_BASE_URI__.'modules/'. $this->name.'/marketing.gif" alt="'.$this->l('Start marketing program').'" class="icon" style="margin: 0 10px;" />';
            $temp .= $this->l('Start marketing program').'</a></li>';
            
        $temp .='</ul></div>
                </div>';
        
        //echo $temp;
        $smarty->assign(array('temp' => $temp));
		return $this->display(__FILE__, 'blockfrotelmarketting.tpl');
		
	}
	
    /**
     * a Mirror for Hook Left column
     * */
	function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}
    
    /**
     * Hook Delete Product
     * Remove product's percent from db
     * */
    public function hookdeleteproduct($params) 
    { 
        if (!isset($params['product']->id))
			return false;
		$id_product = $params['product']->id;
		if ((int)$id_product < 1)
			return false;
            
        // call remove function
        if(!$this->_hookRemove($id_product))
            return false;
        
    }
    
    /**
     * Remove percent from db
     * */
    private function _hookRemove($id)
     {
        $data = Db::getInstance()->ExecuteS('DELETE FROM '._DB_PREFIX_.'frotel_product  
                                             WHERE id_product = '.$id);
        if(!$data)
            return false;
        else
            return true;
     }
     
    /**
     * Update order's status
     * */
     private function _updateStates()
    {
        
        $orders = Db::getInstance()->ExecuteS('
			SELECT f.*, h.id_order orderId, h.id_order_state state
			FROM '._DB_PREFIX_.'frotel_order f
			LEFT JOIN '._DB_PREFIX_.'order_history  h ON (h.id_order = f.id_order)
			WHERE h.id_order_state >='.Configuration::get('FROTEL_ORDER_STATE_1').' AND h.id_order_state <= '.Configuration::get('FROTEL_ORDER_STATE_5'));   // Only active orders
            
            $ides = array();
            $factors = array();
            $initials = array();
            $j = 1;
            
        foreach ($orders AS &$order){
            $ides[]     = $order['orderId']; 
            $factors[]  = $order['bill'];
            $initials[] = $order['state'];
            $j++;
            if($j == 49)    // 50 per each request
                exit;
        }
        
        $soap = new SoapClient("http://www.froservice.ir/F-W-S-L/F_Gateway.php?wsdl");
        // get status from Web-Service
        $Res = $soap->FGetStatus(implode(';', $factors ), Configuration::get('FROTEL_USERNAME'), Configuration::get('FROTEL_PASSWORD'));
        
        $Res = urldecode($Res);
        $reses = array();
        
        if(!empty($Res))
            $reses = explode(';', $Res);
        else
            return false;
            
        $result = NULL;
        
        // Web Service Error
        if($Res == 'Access Denied' OR $Res == 'empty' OR $Res == 'not found'){
            return ;
            
        }elseif(!empty($reses)){    // Update new status
            
            for($i=0; $i<count($ides); $i++){
                if(Configuration::get('FROTEL_ORDER_STATE_'.(int)$reses[$i]) != $initials[$i] ){
                    
                    $result = Db::getInstance()->ExecuteS('INSERT INTO '._DB_PREFIX_.'order_history  
                            (id_order_history, id_employee, id_order, id_order_state, date_add)
							VALUES (NULL, "1", "'.$ides[$i].'", "'.Configuration::get('FROTEL_ORDER_STATE_'.(int)$reses[$i]).'", NOW())');
                }
                
            }
            
        }
        return ;
        
    }
   
   /**
     * Config form generator
     * */
     private function _displayForm()
	{
	   $this->_html .= '<style>
            input.fro_textbox {float: left; border-radius: 3px 3px 3px 3px; border: 1px solid rgb(187, 187, 187); font: 11px tahoma; padding: 2px 3px; text-align: left; color: rgb(85, 85, 85);}
            input.fro_textbox:focus {border: 1px solid #AA6537;}
            fieldset.fro_fset {width: 250px; border-radius: 5px 5px 5px 5px; font: 11px tahoma; border: 1px solid #839CA9; padding: 10px; direction:rtl;background: #FEFEFE;margin: 0 20px; box-shadow:0 -40px 35px #F8F8F8 inset}
            legend.fro_legend {font: 11px tahoma; color: #4A98B5; border: 1px solid;background: #F2F2F8;}
            label.fro_label {margin-left: 50px;float: none;}
            select.fro_select {border: 1px solid #BBBBBB;border-radius: 3px 3px 3px 3px;float: left;width: 125px;}
            fieldset#opt div { margin-right: 30px; }
            fieldset.fro_fset span { color:#CC6600;}
            form#frotel > div { margin-top: 30px; }
            </style>';
        // js
        $this->_html .= '<script type="text/javascript" src="http://www.proservice.ir/js/city3.js"></script> ';
        // main legend
        $this->_html .= '<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" /> '.$this->l('Frotel configuration').'</legend>';
        // 
        $this->_html .= '<div style="width: 630px; margin:0 20px;">
        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post" id="frotel">
            <div style="float: right;margin-bottom: 15px;"><fieldset class="fro_fset"><legend class="fro_legend">'.$this->l('Your account data') . '</legend>
	         <p>
                <label class="fro_label" for="username">' . $this->l('username') .'<span>*</span></label>
                <input class="fro_textbox" name="username" id="" value="' . Tools::safeOutput(Tools::getValue('username',Configuration::get('FROTEL_USERNAME'))) . '" type="text">
             </p>
	         <p>
                <label class="fro_label" for="password">' . $this->l('password') .' <span>*</span></label>
	            <input class="fro_textbox" name="password" id="" value="' . Tools::safeOutput(Tools::getValue('password',Configuration::get('FROTEL_PASSWORD'))) . '" type="text">
             </p>
            </fieldset></div>'.
            /* state an city */

        '<div style="float: left;"><fieldset class="fro_fset"><legend class="fro_legend">' .$this->l('initial place data') . '</legend>
	       <p><label class="fro_label" for="ostan">' . $this->l('state') .' <span>*</span></label>
	           
               <select name="id_ostan" class="fro_select" onchange="ldMenu(this.selectedIndex);" id="id_ostan">
                        <option value="0">لطفا استان خود را انتخاب کنيد</option>
                        <option value="41">آذربايجان شرقي</option>
                        <option value="44">آذربايجان غربي</option>
                        <option value="45">اردبيل</option>
                        <option value="31">اصفهان</option>
                        <option value="84">ايلام</option>

                        <option value="77">بوشهر</option>
                        <option value="26">البرز</option>
                        <option value="21">تهران</option>
                        <option value="38">چهارمحال بختياري</option>
                        <option value="56">خراسان جنوبي</option>
                        <option value="51">خراسان رضوي</option>
                        <option value="58">خراسان شمالي</option>

                        <option value="61">خوزستان</option>
                        <option value="24">زنجان</option>
                        <option value="23">سمنان</option>
                        <option value="54">سيستان و بلوچستان</option>
                        <option value="71">فارس</option>
                        <option value="28">قزوين</option>

                        <option value="25">قم</option>
                        <option value="87">كردستان</option>
                        <option value="34">كرمان</option>
                        <option value="83">كرمانشاه</option>
                        <option value="74">كهكيلويه و بويراحمد</option>
                        <option value="17">گلستان</option>

                        <option value="13">گيلان</option>
                        <option value="66">لرستان</option>
                        <option value="15">مازندران</option>
                        <option value="86">مركزي</option>
                        <option value="76">هرمزگان</option>
                        <option value="81">همدان</option>
                        <option value="35">يزد</option>
                </select>
               
               </p>
	               <p><label class="fro_label" for="city">' . $this->l('city') .' <span>*</span></label>
	                   <select class="fro_select" name="id_shahr" id="shahr"></select></p>
                </fieldset></div>';
        // again js
        $this->_html .= '<script>
                    document.getElementById("id_ostan").value="'.Tools::getValue('id_ostan', Configuration::get('FROTEL_SELLER_STATE')).'";
                    
                    
                        for(var x=0; x< document.getElementById("id_ostan").length; x++){
                            if(document.getElementById("id_ostan")[x].value == "'.Tools::getValue('id_ostan', Configuration::get('FROTEL_SELLER_STATE')).'"){
                               ldMenu(x);
                             }
                            }
                            
                    document.getElementById("shahr").value="'.Tools::getValue('id_shahr', Configuration::get('FROTEL_SELLER_CITY')).'";
            </script>';
        //
        /*$this->_html .= '<div style="float: right;"><fieldset class="fro_fset" id="opt" style="margin-top: 17px;"><legend class="fro_legend">' .$this->l('select types') . '</legend>
	       <p><label class="fro_label" for="shiptype">' . $this->l('shipping types') .' </label>
	           <div><input name="shiptype_sef" type="checkbox" value="1"'.((Tools::getValue('shiptype_sef', Configuration::get('FROTEL_SHIP_TYPE_SEFARESHI')) == 1) ? 'checked="checked"' : '').'/> ' . $this->l('post sefareshi') . ' </div>
	           <div><input name="shiptype_pish" type="checkbox" value="1" '.((Tools::getValue('shiptype_pish', Configuration::get('FROTEL_SHIP_TYPE_PISHTAZ')) == 1) ? 'checked="checked"' : '').' /> ' .$this->l('post pishtaz') . ' </div>
           </p>
	       <p style="border-top: 1px dashed #AAAAAA; padding-top:15px;"><label class="fro_label" for="paytype">' .$this->l('payment types') . ' </label>
	           <div><input name="paytype_online" type="checkbox" value="1" '.((Tools::getValue('paytype_online', Configuration::get('FROTEL_PAY_TYPE_ONLINE')) == 1) ? 'checked="checked"' : '').' /> ' . $this->l('naghdy online') . ' </div>
	           <div><input name="paytype_cod" type="checkbox" value="1" '.((Tools::getValue('paytype_cod', Configuration::get('FROTEL_PAY_TYPE_COD')) == 1) ? 'checked="checked"' : '').' /> ' . $this->l('cod') . ' </div>
	           
           </p>
        </fieldset></div>';*/
        //
        $this->_html .= '<div style=""><fieldset style="height: 75px;" class="fro_fset"><legend class="fro_legend">' .$this->l('bazaryabi config') . '</legend>
	       <p><label class="fro_label" for="bazaryab">' . $this->l('is bazaryabi enable') .' <span>*</span></label>
	           <div><input name="bazaryab" type="radio" value="0" '.(!Tools::getValue('bazaryab', Configuration::get('FROTEL_BAZARYAB_POSSIBILITY')) ? 'checked="checked" ' : '').'/> ' .$this->l('disable') . ' </div>
	           <div><input name="bazaryab" type="radio" value="1" '.(Tools::getValue('bazaryab', Configuration::get('FROTEL_BAZARYAB_POSSIBILITY')) ? 'checked="checked" ' : '').' /> ' .$this->l('enable') . ' </div>
           </p>
        </fieldset></div>';
        //
        $this->_html .= '<input  class="button" name="submitSave" type="submit" value="'.$this->l('submit configures').'" style="margin: 30px 170px 20px 0; border-radius: 3px; border: 1px solid #cbcb99; padding: 4px 7px; box-shadow: 0 0 5px #eee;"></form></div>';
        //
        $this->_html .= '</fieldset><div class="clear">&nbsp;</div>';
        
        
    }
    
    /**
     * Get states (Not necessary at this time)
     * */
     public function frotelGetstate($key){
        
        $statesConvert = array(41,44,45,26,31,84,77,21,38,56,51,58,61,24,23,54,71,28,25,87,34,83,74,17,13,66,15,86,76,81,35);
        
        return $statesConvert[$key];
     }
    
    
    
}

