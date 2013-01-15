<?php
 
include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');
include_once(_PS_MODULE_DIR_.'frotel/frotel.php');
include_once(_PS_MODULE_DIR_.'frotel/fa.php');



$frotel = new Frotel();
$errors = NULL;

/**
 * Display()
 * Print $text
 */
function Display($text){
    global $cart, $cookie;
    
    include(_PS_ROOT_DIR_.'/header.php');

	echo $text;
    
	include(_PS_ROOT_DIR_.'/footer.php');
	die ;
    
 }
 
/**
 * showForm()
 * 
 */
function showForm(){
    global $cookie, $frotel, $errors;
    
	unset($cookie->frotelToken); // unset token
    
    // generate new token
    $token = ( uniqid( hash("md5", time()), TRUE ) . time() . @$_SERVER['REMOTE_ADDR']);
    $cookie->__set('joinToken', $token); // set
    
    include(_PS_ROOT_DIR_.'/header.php');
    
    $Bazaryab = $cookie->frotel_bz_id;
    
    // From style
    echo '<style>
        input.fro_textbox {float: right; border-radius: 3px 3px 3px 3px; border: 1px solid rgb(187, 187, 187); font: 11px tahoma; padding: 2px 3px; text-align: left; color: rgb(85, 85, 85);}
        label.fro_label {margin-left: 50px; font:12px tahoma;margin-right: 50px;}
        div#jForm td { padding: 3px 10px;}
        div#jForm td span { color:#CC6600;}
        
        </style>';
    
    echo '<div id="jForm" style="border: 1px solid #FEFEff; box-shadow: 0 -65px 70px #F8F8F8 inset; width: 100%; padding-bottom: 20px; border-radius:5px 5px 5px 5px"><h1>عضويت در سرويس همكاري تبليغاتي</h1>';
     
    // Errors
    echo '<div style="padding: 10px 20px; color:#b00;">'.$errors.'</div>';
         
    // Points
    echo '<p style="margin-right:10px;">&raquo; اطلاعات این فرم در فروشگاه ذخیره نخواهد شد و مستقیما به فروتل ارسال می گردد.</p>
              <p style="margin:10px;">&raquo; به پیام هایی که بعد از ارسال فرم نمایش داده می شود دقت کنید.</p>
              <p style="margin:0 10px 15px 0;">&raquo; امکان ویرایش این اطلاعات در پنل مخصوص شما در سایت فروتل وجود دارد.</p>';
    
    
    
    ?>
    <script type="text/javascript" src="http://www.proservice.ir/js/city3.js"></script> 
    <fieldset style="padding: 12; width: 550px; margin-top:0px;border: 0px;" dir="rtl">
<form action="<?php echo __PS_BASE_URI__.'modules/'. $frotel->name.'/join.php'; ?>" method="post" name="masform" target="_self" id="masform">
    <input name="joinToken"  value="<?php echo $token; ?>" type="hidden">
    <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" dir="ltr"><tr align="right" valign="middle">
        <td width="359" height="26"><input name="moarref" type="text" class="fild" id="moarref" style="text-align:center;" dir="rtl" onFocus="this.style.borderColor='7181BD';" onBlur="this.style.borderColor='#CCCCCC';" value="<?php echo"$Bazaryab";?>" size="15" maxlength="10"<?php if($Bazaryab){echo" disabled";}?>><?php if($Bazaryab){echo"<input name=\"moarref\" type=\"hidden\" value=\"$Bazaryab\">";}?></td>
        <td height="26" align="right" class="text" dir="rtl">شناسه معرف <font color="#FF0000" size="2">*</font> :</td>
        </tr><tr align="right" valign="middle">
        <td width="359" height="26"><input name="name" value="<?php echo $cookie->customer_firstname.' '.$cookie->customer_lastname; ?>" type="text" class="fild" id="name" dir="rtl" onFocus="this.style.borderColor='7181BD';" onBlur="this.style.borderColor='#CCCCCC';" size="30" maxlength="50"></td>
        <td height="26" align="right" class="text" dir="rtl">نام و نام خانوادگي <font color="#FF0000" size="2">*</font> :</td>
        </tr><tr align="right" valign="middle"> 
                              <td height="28"><p class="text" dir="rtl">
							   مرد 
							       <input name="jens" type="radio" value="1" checked>
							   &nbsp;&nbsp;&nbsp;&nbsp;
							    زن <input name="jens" type="radio" value="0">
							  </p></td>
                    <td height="28" align="right" class="text" dir="rtl">جنسيت :</td>
                              </tr>
      <tr align="right" valign="middle">
        <td height="30"><input name="tel" type="text" class="fild" id="tel" onFocus="this.style.borderColor='7181BD';" onBlur="this.style.borderColor='#CCCCCC'" size="40" maxlength="100"></td>
        <td width="143" height="30" align="right" class="text" dir="rtl">تلفن<font color="#FF0000" size="2"> </font>:</td>
        </tr>
        <tr align="right" valign="middle">
        <td height="30"><input name="mobile" type="text" class="fild" id="mobile" onFocus="this.style.borderColor='7181BD';" onBlur="this.style.borderColor='#CCCCCC'" size="40" maxlength="100"></td>
        <td width="143" height="30" align="right" class="text" dir="rtl"> موبايل <font color="#FF0000" size="2">*</font> :</td>
        </tr>
      <tr align="right" valign="middle">
        <td height="30"><input name="email" value="<?php echo $cookie->email; ?>" type="text" class="fild" id="email" onFocus="this.style.borderColor='7181BD';" onBlur="this.style.borderColor='#CCCCCC'" size="40" maxlength="100"></td>
        <td width="143" height="30" align="right" class="text" dir="rtl">آدرس Email <font color="#FF0000" size="2">*</font> :</td>
        </tr>
        <tr align="right" valign="middle" height="5">
        <td align="right" valign="top"><font style="font: 11px tahoma; color: #922;" class="t8" dir="rtl"> ايميل شما ، نام كاربري شما خواهد بود. لطفاً صحيح وارد كنيد. </font > </td>
        <td width="143" align="right">&nbsp;</td>
        </tr>
      <tr align="right" valign="middle">
        <td height="30"><input name="pass" type="password" class="fild" id="pass" onFocus="this.style.borderColor='7181BD';" onBlur="this.style.borderColor='#CCCCCC'" size="30" maxlength="20"></td>
        <td width="143" height="30" align="right" class="text" dir="rtl">کلمه رمز <font color="#FF0000" size="2">*</font> :</td>
        </tr>
      <tr align="right" valign="middle">
        <td height="30"><input name="repass" type="password" class="fild" id="repass" onFocus="this.style.borderColor='7181BD';" onBlur="this.style.borderColor='#CCCCCC'" size="30" maxlength="20"></td>
        <td width="143" height="30" align="right" class="text" dir="rtl">تکرار کلمه رمز <font color="#FF0000" size="2">*</font> :</td>
        </tr>
      <tr align="right" valign="middle">
        <td height="30"><textarea name="url" cols="50" rows="4" class="fild" id="url" onFocus="this.style.borderColor='7181BD';" onBlur="this.style.borderColor='#CCCCCC';"></textarea></td>
        <td width="143" height="30" align="right" class="text" dir="rtl">آدرس سايتها يا وبلاگها:</td>
      </tr>
      <tr align="right" valign="middle" height="5">
        <td align="right" valign="top"><font size="-1" face="Arial, Helvetica, sans-serif" class="t8" dir="rtl">مثال :&nbsp; &nbsp;&nbsp;http://www.shahrecd.com </font > </td>
        <td width="143" align="right">&nbsp;</td>
        </tr>
      <tr align="right" valign="middle">
        <td height="30"><select name="id_ostan" class="text" onChange="ldMenu(this.selectedIndex);" dir="rtl" id="id_ostan">
                        <option value="0">لطفا استان خود را انتخاب کنید</option>
                        <option  value="41">آذربايجان شرقي</option>
                        <option  value="44">آذربايجان غربي</option>
                        <option  value="45">اردبيل</option>
                        <option  value="31">اصفهان</option>
                        <option  value="84">ايلام</option>

                        <option  value="77">بوشهر</option>
                        <option  value="26">البرز</option>
                        <option  value="21">تهران</option>
                        <option  value="38">چهارمحال بختياري</option>
                        <option  value="56">خراسان جنوبي</option>
                        <option  value="51">خراسان رضوي</option>
                        <option  value="58">خراسان شمالي</option>

                        <option  value="61">خوزستان</option>
                        <option  value="24">زنجان</option>
                        <option  value="23">سمنان</option>
                        <option  value="54">سيستان و بلوچستان</option>
                        <option  value="71">فارس</option>
                        <option  value="28">قزوين</option>

                        <option  value="25">قم</option>
                        <option  value="87">كردستان</option>
                        <option  value="34">كرمان</option>
                        <option  value="83">كرمانشاه</option>
                        <option  value="74">كهكيلويه و بويراحمد</option>
                        <option  value="17">گلستان</option>

                        <option  value="13">گيلان</option>
                        <option  value="66">لرستان</option>
                        <option  value="15">مازندران</option>
                        <option  value="86">مركزي</option>
                        <option  value="76">هرمزگان</option>
                        <option  value="81">همدان</option>
                        <option  value="35">يزد</option>
    </select></td>
        <td width="143" height="30" align="right" class="text" dir="rtl">استان  :</td>
        </tr>
      <tr align="right" valign="middle">
        <td height="30"><select name="id_shahr" id="shahr" size="1" dir="rtl" class="text">
									<option selected value="">لطفا استان خود را انتخاب کنید</option>
			    </select></td>
        <td width="143" height="30" align="right" class="text" dir="rtl">شهر  :</td>
        </tr>
      <tr align="right" valign="middle">
        <td height="30"><textarea name="address" cols="50" rows="2" class="fild" id="address" dir="rtl" onFocus="this.style.borderColor='7181BD';" onBlur="this.style.borderColor='#CCCCCC';"></textarea></td>
        <td width="143" height="30" align="right" class="text" dir="rtl">آدرس دقيق منزل يا محل كار  : </td>
        </tr>
      <tr align="right" valign="middle">
        <td height="30"><input name="bankno" type="text" class="fild" id="bankno" dir="rtl" onFocus="this.style.borderColor='7181BD';" onBlur="this.style.borderColor='#CCCCCC';" size="50" maxlength="120"></td>
        <td height="30" align="right" class="text" dir="rtl"> نام بانك و شماره حساب   <font color="#FF0000" size="2">*</font>:  </td>
        </tr><tr align="right" valign="middle" height="5">
        <td height="30" align="right" valign="top" class="t8">حساب بانكي سيبا - ملي &nbsp; يا &nbsp; سپهر - صادرات</td>
        <td height="30" align="center" valign="top">&nbsp;</td>
        </tr>
        <tr align="right" valign="middle">
        <td height="100" valign="top"><textarea name="tovzihat" cols="50" rows="6" class="fild" dir="rtl" id="tovzihat" onFocus="this.style.borderColor='7181BD';" onBlur="this.style.borderColor='#CCCCCC';"></textarea></td>
        <td width="143" height="100" align="right" class="text" dir="rtl"> ساير توضيحات و<br> بيان توانائي ها و<br>روشهاي انجام تبليغات  :<br><br> </td>
      </tr>
      <tr align="right" valign="bottom">
        <td height="45" colspan="2" align="center"><div align="center" dir="rtl" class="clo" style="width: 500px; overflow:visible; border:#CCC solid 1px; background-color:#EEF1F2; margin-top:10px; border-radius: 3px; box-shadow: -2px 2px 5px #EEE;"> <div dir="rtl" style="width:450px; padding:7px; text-align:justify; line-height:16px;"> <strong><span class="text">&nbsp;&nbsp;&nbsp; شرايط و مقررات </span></strong><br> 
          <span class="t8">تمام ايرانيان مي توانند در سيستم همكاري تبليغاتي عضو شوند به جز افراد و سايتهاي معاند جمهوري اسلامي ايران ، ضد اسلام و ضد بشري و داراي تصاوير غير اخلاقي كه حق عضويت در اين سيستم را ندارند و در صورت عضو شدن ، از سيستم  حذف خواهند گرديد . همچنين هرگونه تبليغات نامتعارف و غير مجاز  ممنوع مي باشد و بايد تمام فعاليتها طبق قوانين جاري كشور انجام گيرد.<br>سقف واريزي درآمد در سري اول 20 هزار تومان و از سري دوم واريزي به بعد سقف واريز 10 هزار تومان خواهد بود.
به ميزان 7% از درآمد همكاران  به عنوان حق سرويس و توسعه كسر مي شود كه البته تمام اين مبلغ دوباره و بلافاصله بين همكاران فعال تقسيم مي شود . </span><br>
  </div></div>
          <span class="text" dir="rtl">تمام<a onClick="rule('rule')" class="text" style="cursor:pointer;"> شرايط و مقررات</a> سيستم همكاري تبليغاتي را قبول دارم.
          <input onClick="active_submit();" tabindex=14 type="checkbox" value="on" name="accept">
		  </span><br><br>
		  <INPUT name="submit" type="submit" disabled="true" class="text" style="  padding: 4px 8px; height: 27px; border-radius: 4px; background: #86Ae12; box-shadow: 0 15px 5px #8EBE16 inset; color: #EEE; text-shadow: 0 0 1px #CCC;" tabIndex="12" dir="ltr" value="  ثبت نام  "></td>
      </tr>
      </table>
  </form><script language="JavaScript">
function active_submit()
{
    if (document.masform.submit.disabled == true)
        document.masform.submit.disabled = false;
    else
        document.masform.submit.disabled = true;
}
</script></fieldset>
    <?php
    echo '</div>';
    
    include(_PS_ROOT_DIR_.'/footer.php');
	die ;
    //Display($text);
}

/**
 * process()
 * Validate data and sending to Web service 
 */
function process(){
    global $cookie, $frotel, $errors;
    
   if( Tools::getValue('joinToken') != $cookie->joinToken){ // check token
	   header('location:../../'); exit;
		die('Token expired! Go back and try again');
	}
	
    // validating
    if(!Tools::getValue('name') OR !Validate::isString(Tools::getValue('name')))
        $errors .= '<p><li>نام و نام خانوادگی خود را بدرستی وارد کنید</li></p>';
        
    if(!Tools::getValue('mobile') OR !Validate::isPhoneNumber(Tools::getValue('mobile')))
        $errors .= '<p><li>شماره موبایل شما نامعتبر است</li></p>';
        
    if(!Tools::getValue('email') OR !Validate::isEmail(Tools::getValue('email')))
        $errors .= '<p><li>ایمیل شما نامعتبر است</li></p>';
        
    if(!Tools::getValue('pass') OR !Validate::isPasswd(Tools::getValue('pass')) OR
        !Tools::getValue('repass') OR !Validate::isPasswd(Tools::getValue('repass')) OR
        Tools::getValue('repass') != Tools::getValue('pass'))
        $errors .= '<p><li>رمز عبور خود را دوباره بررسی کنید</li></p>';
        
    if(!Tools::getValue('bankno'))
        $errors .= '<p><li>اطلاعات حساب خود را وارد کنید</li></p>';
    
    if(!empty($errors)){    
        $temp = '<ul>'.$errors.'</ul>';
        showForm();
        return ;
    }
    
    $Moarref = trim($_POST['moarref']);
    $Name = trim($_POST['name']);
    $Jens = trim($_POST['jens']);
    $Tel = trim($_POST['tel']);
    $Mobile = trim($_POST['mobile']);
    $Email = trim($_POST['email']);
    $Pass = trim($_POST['pass']);
    $Repass = trim($_POST['repass']);
	   if($Pass!=$Repass){
	       	$Pass="";
	   }
    $Url = trim($_POST['url']);
    $Ostan = trim($_POST['id_ostan']);
    $Shahr = trim($_POST['id_shahr']);
    $Address = trim($_POST['address']);
    $Bankno = trim($_POST['bankno']);
    $Tovzihat = trim($_POST['tovzihat']);


$soap = new SoapClient("http://www.froservice.ir/F-W-S-L/F_Gateway.php?wsdl");
$Res = $soap-> FRegWM($Moarref,$Name,$Jens,$Tel,$Mobile,$Email,$Pass,$Url,$Ostan,$Shahr,$Address,$Bankno,$Tovzihat,Configuration::get('FROTEL_USERNAME'), Configuration::get('FROTEL_PASSWORD'));
$Res = urldecode($Res);

    
    
    // handle result
    switch ($Res){ 
	case 'Access Denied':
        $temp = 'دسترسی به سیستم فروتل هم اکنون امکان پذیر نیست، لطفن پس از چند لحظه مجددا تلاش کنید.';
        unset($cookie->joinToken);
	break;

	case 'Service Not Active':
        $temp = 'سرویس بازاریابی و تبلیغات هم اکنون فعال نیست، برای کسب اطلاعات بیشتر با پشتیبانی تماس بگیرید';
        unset($cookie->joinToken);
	break;

	default :
        $temp = $Res;
        unset($cookie->joinToken);
    }
    
    Display($temp);
    return ;
        
    
}


/**
 * Native translate
 */
function l($string){
    global $_MODULE;
        return ($_MODULE['<{frotel}prestashop>join_'.md5($string)])? $_MODULE['<{frotel}prestashop>join_'.md5($string)] : $string;
}



// handle requests
	
 if(!Tools::isSubmit('submit') OR !Tools::getValue('joinToken')) // display form
    showForm();
 elseif(Tools::isSubmit('submit') AND Tools::getValue('joinToken')) // process data
    process();
 else
    die('Permission error!');
    
