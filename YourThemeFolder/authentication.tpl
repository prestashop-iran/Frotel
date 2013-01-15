{*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 9376 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{*
** Compatibility code for Prestashop older than 1.4.2 using a recent theme
** Ignore list isn't require here
** $address exist in every PrestaShop version
*}

{* Will be deleted for 1.5 version and more *}
{* Smarty code compatibility v2 *}
{* If ordered_adr_fields doesn't exist, it's a PrestaShop older than 1.4.2 *}
{if !isset($dlv_all_fields)}
		{$dlv_all_fields.0 = 'company'}
		{$dlv_all_fields.1 = 'firstname'}
		{$dlv_all_fields.2 = 'lastname'}
		{$dlv_all_fields.3 = 'address1'}
		{$dlv_all_fields.4 = 'address2'}
		{$dlv_all_fields.5 = 'postcode'}
		{$dlv_all_fields.6 = 'city'}
		{$dlv_all_fields.7 = 'country'}
		{$dlv_all_fields.8 = 'state'}
{/if}

{capture name=path}{l s='Login'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<script type="text/javascript">
// <![CDATA[
idSelectedCountry = {if isset($smarty.post.id_state)}{$smarty.post.id_state|intval}{else}false{/if};
countries = new Array();
countriesNeedIDNumber = new Array();
countriesNeedZipCode = new Array();
{if isset($countries)}
	{foreach from=$countries item='country'}
		{if isset($country.states) && $country.contains_states}
			countries[{$country.id_country|intval}] = new Array();
			{foreach from=$country.states item='state' name='states'}
				countries[{$country.id_country|intval}].push({ldelim}'id' : '{$state.id_state}','iso' : '{$state.iso_code}', 'name' : '{$state.name|escape:'htmlall':'UTF-8'}'{rdelim});
			{/foreach}
		{/if}
		{if $country.need_identification_number}
			countriesNeedIDNumber.push({$country.id_country|intval});
		{/if}
		{if isset($country.need_zip_code)}
			countriesNeedZipCode[{$country.id_country|intval}] = {$country.need_zip_code};
		{/if}
	{/foreach}
{/if}
$(function(){ldelim}
	$('.id_state option[value={if isset($smarty.post.id_state)}{$smarty.post.id_state}{else}{if isset($address)}{$address->id_state|escape:'htmlall':'UTF-8'}{/if}{/if}]').attr('selected', 'selected');
{rdelim});
//]]>
{if $vat_management}
	{literal}
	$(document).ready(function() {
		$('#company').blur(function(){
			vat_number();
		});
		vat_number();
		function vat_number()
		{
			if ($('#company').val() != '')
				$('#vat_number').show();
			else
				$('#vat_number').hide();
		}
	});
	{/literal}
{/if}
</script>
<h1>{if !isset($email_create)}{l s='Log in'}{else}{l s='Create your account'}{/if}</h1>
{assign var='current_step' value='login'}
{include file="$tpl_dir./order-steps.tpl"}

{include file="$tpl_dir./errors.tpl"}
{assign var='stateExist' value=false}
{if !isset($email_create)}
	<form action="{$link->getPageLink('authentication.php', true)}" method="post" id="create-account_form" class="std">
		<fieldset>
			<h3>{l s='Create your account'}</h3>
			<h4>{l s='Enter your e-mail address to create an account'}.</h4>
			<p class="text">
				<label for="email_create">{l s='E-mail address'}</label>
				<span><input type="text" id="email_create" name="email_create" value="{if isset($smarty.post.email_create)}{$smarty.post.email_create|escape:'htmlall':'UTF-8'|stripslashes}{/if}" class="account_input" /></span>
			</p>
			<p class="submit">
			{if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}" />{/if}
				<input type="submit" id="SubmitCreate" name="SubmitCreate" class="button_large" value="{l s='Create your account'}" />
				<input type="hidden" class="hidden" name="SubmitCreate" value="{l s='Create your account'}" />
			</p>
		</fieldset>
	</form>









                <script>
                //me
                $(document).ready(function(){
                    if($('#id_country').val() == 112){
            
                        $('#id_state').change(function() {
                             ldMenu($(this).find(':selected')[0].id);
                
                            //alert($('#id_state').val());
                        });
                     }// end if
                     
                     function ldMenu(mySubject){
var Indx=mySubject;
with (document.getElementById("city")) 
{
options.length=0;
if (Indx==0)
{
options[0]=new Option("لطفا استان خود را انتخاب کنيد","");
}
if (Indx=='AzS')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" آذر شهر","12-آذر شهر");
options[2]=new Option(" اسكو","14-اسكو");
options[3]=new Option(" اهر","13-اهر");
options[4]=new Option(" بستان آباد","17-بستان آباد");
options[5]=new Option(" بناب","15-بناب");
options[6]=new Option(" بندر شرفخانه","16-بندر شرفخانه");
options[7]=new Option(" تبريز","18-تبريز");
options[8]=new Option(" تسوج","19-تسوج");
options[9]=new Option(" جلفا","20-جلفا");
options[10]=new Option(" سراب","21-سراب");
options[11]=new Option(" شبستر","22-شبستر");
options[12]=new Option(" عجبشير","23-عجبشير");
options[13]=new Option(" قره آغاج","1-قره آغاج");
options[14]=new Option(" كليبر","2-كليبر");
options[15]=new Option(" كندوان","3-كندوان");
options[16]=new Option(" مرند","6-مرند");
options[17]=new Option(" ملكان","4-ملكان");
options[18]=new Option(" ميانه","5-ميانه");
options[19]=new Option(" مراغه","7-مراغه");
options[20]=new Option(" هاديشهر","8-هاديشهر");
options[21]=new Option(" هشترود","10-هشترود");
options[22]=new Option(" هريس","9-هريس");
options[23]=new Option(" ورزقان","11-ورزقان");
}
if (Indx=='AzQ')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" اروميه","7-اروميه");
options[2]=new Option(" اشنويه","8-اشنويه");
options[3]=new Option(" بوكان","9-بوكان");
options[4]=new Option(" تكاب","10-تكاب");
options[5]=new Option(" پيرانشهر","6-پيرانشهر");
options[6]=new Option(" پلدشت","17-پلدشت");
options[7]=new Option(" چالدران","5-چالدران");
options[8]=new Option(" خوي","11-خوي");
options[9]=new Option(" سر دشت","14-سر دشت");
options[10]=new Option(" سلماس","12-سلماس");
options[11]=new Option(" شاهين دژ","15-شاهين دژ");
options[12]=new Option(" شوط","16-شوط");
options[13]=new Option(" چايپاره","18-چايپاره");
options[14]=new Option(" ماكو","3-ماكو");
options[15]=new Option(" مهاباد","1-مهاباد");
options[16]=new Option(" مياندوآب","2-مياندوآب");
options[17]=new Option(" نقده","4-نقده");
}
if (Indx=='ADB')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" اردبيل","9-اردبيل");
options[2]=new Option(" بيله سوار","10-بيله سوار");
options[3]=new Option(" پارس آباد","8-پارس آباد");
options[4]=new Option(" خلخال","11-خلخال");
options[5]=new Option(" سرعين","12-سرعين");
options[6]=new Option(" كوثر","1-كوثر");
options[7]=new Option(" كيوي","2-كيوي");
options[8]=new Option(" گرمي","7-گرمي");
options[9]=new Option(" مشگين شهر","3-مشگين شهر");
options[10]=new Option(" مغان","4-مغان");
options[11]=new Option(" نمين","5-نمين");
options[12]=new Option(" نير","6-نير");
}
if (Indx=='IFH')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" آران و بيدگل","11-آران و بيدگل");
options[2]=new Option(" اردستان","12-اردستان");
options[3]=new Option(" اصفهان","13-اصفهان");
options[4]=new Option(" باغ بهادران","14-باغ بهادران");
options[5]=new Option(" تيران","15-تيران");
options[6]=new Option(" خميني شهر","16-خميني شهر");
options[7]=new Option(" خوانسار","17-خوانسار");
options[8]=new Option(" زرين شهر","19-زرين شهر");
options[9]=new Option(" سميرم","20-سميرم");
options[10]=new Option(" شاهين شهر","22-شاهين شهر");
options[11]=new Option(" شهرضا","21-شهرضا");
options[12]=new Option(" فريدن","3-فريدن");
options[13]=new Option(" فريدون شهر","4-فريدون شهر");
options[14]=new Option(" فلاورجان","1-فلاورجان");
options[15]=new Option(" فولاد شهر","2-فولاد شهر");
options[16]=new Option(" كاشان","5-كاشان");
options[17]=new Option(" گلپايگان","10-گلپايگان");
options[18]=new Option(" مباركه","6-مباركه");
options[19]=new Option(" نايين","7-نايين");
options[20]=new Option(" نجف آباد","8-نجف آباد");
options[21]=new Option(" نطنز","9-نطنز");
}
if (Indx=='ILM')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" آبدانان","2-آبدانان");
options[2]=new Option(" ايلام","3-ايلام");
options[3]=new Option(" ايوان","4-ايوان");
options[4]=new Option(" دره شهر","6-دره شهر");
options[5]=new Option(" دهلران","5-دهلران");
options[6]=new Option(" سرابله","7-سرابله");
options[7]=new Option(" شيروان چرداول","8-شيروان چرداول");
options[8]=new Option(" مهران","1-مهران");
}
if (Indx=='BSR')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" اهرم","3-اهرم");
options[2]=new Option(" بوشهر","4-بوشهر");
options[3]=new Option(" تنگستان","6-تنگستان");
options[4]=new Option(" خارك","8-خارك");
options[5]=new Option(" خورموج","7-خورموج");
options[6]=new Option(" دير","10-دير");
options[7]=new Option(" دشتستان","12-دشتستان");
options[8]=new Option(" دشتي","11-دشتي");
options[9]=new Option(" ديلم","9-ديلم");
options[10]=new Option(" ريشهر","13-ريشهر");
options[11]=new Option(" كنگان","1-كنگان");
options[12]=new Option(" گناوه","2-گناوه");
}
if (Indx=='ALB')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" كرج","1-كرج");
options[2]=new Option(" آسارا","2-آسارا");
options[3]=new Option(" اشتهارد","3-اشتهارد");
options[4]=new Option(" هشتگرد","4-هشتگرد");
options[5]=new Option(" كوهسار","5-كوهسار");
options[6]=new Option(" چهارباغ","6-چهارباغ");
options[7]=new Option(" طالقان","7-طالقان");
options[8]=new Option(" جوستان","8-جوستان");
options[9]=new Option(" نظرآباد","9-نظرآباد");
options[10]=new Option(" تنكمان","10-تنكمان");
}
if (Indx=='TRN')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" اسلامشهر","17-اسلامشهر");
options[2]=new Option(" بومهن","19-بومهن");
options[3]=new Option(" تجريش","21-تجريش");
options[4]=new Option(" تهران","1-تهران");
options[5]=new Option(" پاكدشت","15-پاكدشت");
options[6]=new Option(" دماوند","22-دماوند");
options[7]=new Option(" رباط كريم","25-رباط كريم");
options[8]=new Option(" ري","24-ري");
options[9]=new Option(" رودهن","23-رودهن");
options[10]=new Option(" شريف آباد","27-شريف آباد");
options[11]=new Option(" شهريار","26-شهريار");
options[12]=new Option(" فشم","2-فشم");
options[13]=new Option(" فيروزكوه","20-فيروزكوه");
options[14]=new Option(" قدس","3-قدس");
options[15]=new Option(" قرچك","4-قرچك");
options[16]=new Option(" كن","5-كن");
options[17]=new Option(" كهريزك","6-كهريزك");
options[18]=new Option(" گلستان","14-گلستان");
options[19]=new Option(" لواسان","8-لواسان");
options[20]=new Option(" ملارد","9-ملارد");
options[21]=new Option(" ورامين","13-ورامين");
}
if (Indx=='CVB')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" اردل","4-اردل");
options[2]=new Option(" بروجن","5-بروجن");
options[3]=new Option(" چلگرد","3-چلگرد");
options[4]=new Option(" سامان","6-سامان");
options[5]=new Option(" شهركرد","7-شهركرد");
options[6]=new Option(" فارسان","1-فارسان");
options[7]=new Option(" لردگان","2-لردگان");
}
if (Indx=='KHJ')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" بيرجند","2-بيرجند");
options[2]=new Option(" بشرویه","6-بشرویه");
options[3]=new Option(" خضری","8-خضری");
options[4]=new Option(" سربيشه","3-سربيشه");
options[5]=new Option(" قائن","4-قائن");
options[6]=new Option(" نهبندان","1-نهبندان");
options[7]=new Option(" فردوس","7-فردوس");
}
if (Indx=='KHR')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" بردسكن","12-بردسكن");
options[2]=new Option(" تايباد","13-تايباد");
options[3]=new Option(" تربت جام","14-تربت جام");
options[4]=new Option(" تربت حيدريه","15-تربت حيدريه");
options[5]=new Option(" چناران","9-چناران");
options[6]=new Option(" خواف","16-خواف");
options[7]=new Option(" درگز","17-درگز");
options[8]=new Option(" سبزوار","18-سبزوار");
options[9]=new Option(" سرخس","19-سرخس");
options[10]=new Option(" طبس","20-طبس");
options[11]=new Option(" طرقبه","21-طرقبه");
options[12]=new Option(" فريمان","1-فريمان");
options[13]=new Option(" قوچان","3-قوچان");
options[14]=new Option(" كاخك","11-گناباد ، كاخك");
options[15]=new Option(" كلات","6-كلات");
options[16]=new Option(" كاشمر","7-كاشمر");
options[17]=new Option(" گناباد","11-گناباد");
options[18]=new Option(" مشهد","8-مشهد");
options[19]=new Option(" نيشابور","10-نيشابور");
options[20]=new Option(" رشتخوار","22-رشتخوار");
}
if (Indx=='KHS')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" آشخانه","1-آشخانه");
options[2]=new Option(" اسفراين","2-اسفراين");
options[3]=new Option(" بجنورد","3-بجنورد");
options[4]=new Option(" جاجرم","4-جاجرم");
options[5]=new Option(" شيروان","5-شيروان");
}
if (Indx=='KZT')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" آبادان","5-آبادان");
options[2]=new Option(" اميديه","6-اميديه");
options[3]=new Option(" انديمشك","7-انديمشك");
options[4]=new Option(" اهواز","8-اهواز");
options[5]=new Option(" ايرانشهر","10-ايرانشهر");
options[6]=new Option(" ايذه","9-ايذه");
options[7]=new Option(" باغ ملك","14-باغ ملك");
options[8]=new Option(" بندرامام خميني ","12-بندرامام خميني");
options[9]=new Option(" بندر ماهشهر","11-بندر ماهشهر");
options[10]=new Option(" بهبهان","13-بهبهان");
options[11]=new Option(" دزفول","16-دزفول");
options[12]=new Option(" خرمشهر","15-خرمشهر");
options[13]=new Option(" رامهرمز","17-رامهرمز");
options[14]=new Option(" سوسنگرد","18-سوسنگرد");
options[15]=new Option(" شادگان","21-شادگان");
options[16]=new Option(" شوش","19-شوش");
options[17]=new Option(" شوشتر","20-شوشتر");
options[18]=new Option(" لالي","1-لالي");
options[19]=new Option(" مسجد سليمان","2-مسجد سليمان");
options[20]=new Option(" هنديجان","3-هنديجان");
options[21]=new Option(" هويزه","4-هويزه");
}
if (Indx=='ZNJ')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" ابهر","6-ابهر");
options[2]=new Option(" ايجرود","5-ايجرود");
options[3]=new Option(" خرمدره","8-خرمدره");
options[4]=new Option(" زنجان","9-زنجان");
options[5]=new Option(" قيدار","1-قيدار");
options[6]=new Option(" طارم","2-طارم");
options[7]=new Option(" ماهنشان","3-ماهنشان");
}
if (Indx=='SMN')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" ايوانكي","2-ايوانكي");
options[2]=new Option(" بسطام","3-بسطام");
options[3]=new Option(" سمنان","5-سمنان");
options[4]=new Option(" شاهرود","6-شاهرود");
options[5]=new Option(" دامغان","4-دامغان");
options[6]=new Option(" گرمسار","1-گرمسار");
}
if (Indx=='SVB')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" ايرانشهر","4-ايرانشهر");
options[2]=new Option(" چابهار","3-چابهار");
options[3]=new Option(" خاش","5-خاش");
options[4]=new Option(" راسك","6-راسك");
options[5]=new Option(" زابل","8-زابل");
options[6]=new Option(" زاهدان","7-زاهدان");
options[7]=new Option(" سراوان","9-سراوان");
options[8]=new Option(" سرباز","10-سرباز");
options[9]=new Option(" ميرجاوه","1-ميرجاوه");
options[10]=new Option(" نيكشهر","2-نيكشهر");
}
if (Indx=='FAS')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" آباده","13-آباده");
options[2]=new Option(" اردكان","15-اردكان");
options[3]=new Option(" ارسنجان","16-ارسنجان");
options[4]=new Option(" استهبان","17-استهبان");
options[5]=new Option(" اقليد","14-اقليد");
options[6]=new Option(" جهرم","18-جهرم");
options[7]=new Option(" حاجي آباد","19-حاجي آباد");
options[8]=new Option(" خرم بيد","20-خرم بيد");
options[9]=new Option(" داراب","21-داراب");
options[10]=new Option(" سپيدان","23-سپيدان");
options[11]=new Option(" سوريان","22-سوريان");
options[12]=new Option(" شيراز","24-شيراز");
options[13]=new Option(" صفاشهر","25-صفاشهر");
options[14]=new Option(" فراشبند","2-فراشبند");
options[15]=new Option(" فسا","3-فسا");
options[16]=new Option(" فيروز آباد","1-فيروز آباد");
options[17]=new Option(" قيروكارزين","4-قيروكارزين");
options[18]=new Option(" كازرون","5-كازرون");
options[19]=new Option(" لار","7-لار");
options[20]=new Option(" لامرد","6-لامرد");
options[21]=new Option(" مرودشت","10-مرودشت");
options[22]=new Option(" ممسني","8-ممسني");
options[23]=new Option(" مهر","9-مهر");
options[24]=new Option(" ني ريز","12-ني ريز");
options[25]=new Option(" نورآباد","11-نورآباد");
}
if (Indx=='QZV')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" آبيك","2-آبيك");
options[2]=new Option(" بوئين زهرا","3-بوئين زهرا");
options[3]=new Option(" تاكستان","4-تاكستان");
options[4]=new Option(" قزوين","1-قزوين");
options[5]=new Option(" الوند","5-الوند");
}
if (Indx=='QOM')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" قم","1-قم");
}
if (Indx=='KDT')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" بانه","5-بانه");
options[2]=new Option(" بيجار","4-بيجار");
options[3]=new Option(" ديواندره","6-ديواندره");
options[4]=new Option(" سقز","7-سقز");
options[5]=new Option(" سنندج","8-سنندج");
options[6]=new Option(" قروه","1-قروه");
options[7]=new Option(" كامياران","2-كامياران");
options[8]=new Option(" مريوان","3-مريوان");
}
if (Indx=='KRM')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" بابك","5-بابك");
options[2]=new Option(" بافت","4-بافت");
options[3]=new Option(" بردسير","6-بردسير");
options[4]=new Option(" بم","3-بم");
options[5]=new Option(" جيرفت","7-جيرفت");
options[6]=new Option(" راور","9-راور");
options[7]=new Option(" رفسنجان","8-رفسنجان");
options[8]=new Option(" زرند","10-زرند");
options[9]=new Option(" سيرجان","11-سيرجان");
options[10]=new Option(" كهنوج","1-كهنوج");
options[11]=new Option(" كرمان","2-كرمان");
}
if (Indx=='KMS')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" اسلام آباد غرب","7-اسلام آباد غرب");
options[2]=new Option(" پاوه","6-پاوه");
options[3]=new Option(" جوانرود","8-جوانرود");
options[4]=new Option(" سر پل ذهاب","10-سر پل ذهاب");
options[5]=new Option(" سنقر","9-سنقر");
options[6]=new Option(" صحنه","11-صحنه");
options[7]=new Option(" قصر شيرين","1-قصر شيرين");
options[8]=new Option(" كرمانشاه","3-كرمانشاه");
options[9]=new Option(" كنگاور","2-كنگاور");
options[10]=new Option(" گيلان غرب","5-گيلان غرب");
options[11]=new Option(" هرسين","4-هرسين");
}
if (Indx=='KVB')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" دنا","3-دنا");
options[2]=new Option(" دوگنبدان","5-دوگنبدان");
options[3]=new Option(" دهدشت","4-دهدشت");
options[4]=new Option(" سي سخت","6-سي سخت");
options[5]=new Option(" گچساران","2-گچساران");
options[6]=new Option(" ياسوج","1-ياسوج");
}
if (Indx=='GLT')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" آزاد شهر","7-آزاد شهر");
options[2]=new Option(" آق قلا","6-آق قلا");
options[3]=new Option(" بندر گز","8-بندر گز");
options[4]=new Option(" تركمن","9-تركمن");
options[5]=new Option(" راميان","10-راميان");
options[6]=new Option(" علي آباد كتول","11-علي آباد كتول");
options[7]=new Option(" كلاله","1-كلاله");
options[8]=new Option(" كردكوي","2-كردكوي");
options[9]=new Option(" گنبد كاووس","4-گنبد كاووس");
options[10]=new Option(" گرگان","5-گرگان");
options[11]=new Option(" مينو دشت","3-مينو دشت");
}
if (Indx=='GLN')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" آستانه اشرفيه","8-آستانه اشرفيه");
options[2]=new Option(" آستارا","9-آستارا");
options[3]=new Option(" املش","10-املش");
options[4]=new Option(" بندرانزلي","11-بندرانزلي");
options[5]=new Option(" تالش","12-تالش");
options[6]=new Option(" رشت","15-رشت");
options[7]=new Option(" رضوان شهر","16-رضوان شهر");
options[8]=new Option(" رودبار","14-رودبار");
options[9]=new Option(" رستم آباد","21-رستم آباد");
options[10]=new Option(" رود سر","13-رود سر");
options[11]=new Option(" سياهكل","17-سياهكل");
options[12]=new Option(" شفت","18-شفت");
options[13]=new Option(" صومعه سرا","19-صومعه سرا");
options[14]=new Option(" فومن","1-فومن");
options[15]=new Option(" كلاچاي","2-كلاچاي");
options[16]=new Option(" لاهيجان","20-لاهيجان");
options[17]=new Option(" لنگرود","3-لنگرود");
options[18]=new Option(" ماسال","6-ماسال");
options[19]=new Option(" ماسوله","5-ماسوله");
options[20]=new Option(" منجيل","4-منجيل");
options[21]=new Option(" هشتپر","7-هشتپر");
}
if (Indx=='LRT')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" ازنا","7-ازنا");
options[2]=new Option(" الشتر","6-الشتر");
options[3]=new Option(" اليگودرز","5-اليگودرز");
options[4]=new Option(" بروجرد","8-بروجرد");
options[5]=new Option(" پلدختر","4-پلدختر");
options[6]=new Option(" خرم آباد","9-خرم آباد");
options[7]=new Option(" دزفول","11-دزفول");
options[8]=new Option(" دورود","10-دورود");
options[9]=new Option(" كوهدشت","1-كوهدشت");
options[10]=new Option(" ماهشهر","2-ماهشهر");
options[11]=new Option(" نور آباد","3-نور آباد");
options[12]=new Option(" شول آباد","12-شول آباد");
}
if (Indx=='MZD')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" آمل","9-آمل");
options[2]=new Option(" بابل","12-بابل");
options[3]=new Option(" بابلسر","13-بابلسر");
options[4]=new Option(" بلده","10-بلده");
options[5]=new Option(" بهشهر","11-بهشهر");
options[6]=new Option(" پل سفيد","8-پل سفيد");
options[7]=new Option(" تنكابن","14-تنكابن");
options[8]=new Option(" ساري","18-ساري");
options[9]=new Option(" سواد كوه","17-سواد كوه");
options[10]=new Option(" جويبار","15-جويبار");
options[11]=new Option(" چالوس","7-چالوس");
options[12]=new Option(" رامسر","16-رامسر");
options[13]=new Option(" فريدون كنار","1-فريدون كنار");
options[14]=new Option(" قائم شهر","2-قائم شهر");
options[15]=new Option(" محمود آباد","3-محمود آباد");
options[16]=new Option(" نكا","4-نكا");
options[17]=new Option(" نور","5-نور");
options[18]=new Option(" نوشهر","6-نوشهر");
}
if (Indx=='MKZ')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" آشتيان","2-آشتيان");
options[2]=new Option(" اراك","3-اراك");
options[3]=new Option(" تفرش","4-تفرش");
options[4]=new Option(" خمين","5-خمين");
options[5]=new Option(" دليجان","6-دليجان");
options[6]=new Option(" ساوه","7-ساوه");
options[7]=new Option(" سربند","8-سربند");
options[8]=new Option(" سربند","9-سربند");
options[9]=new Option(" شازند","10-شازند");
options[10]=new Option(" محلات","1-محلات");
}
if (Indx=='HMG')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" ابوموسي","5-ابوموسي");
options[2]=new Option(" انگهران","4-انگهران");
options[3]=new Option(" بستك","9-بستك");
options[4]=new Option(" بندر جاسك","7-بندر جاسك");
options[5]=new Option(" بندرعباس","8-بندرعباس");
options[6]=new Option(" بندر لنگه","6-بندر لنگه");
options[7]=new Option(" تنب بزرگ","10-تنب بزرگ");
options[8]=new Option(" حاجي آباد","11-حاجي آباد");
options[9]=new Option(" دهبارز","12-دهبارز");
options[10]=new Option(" قشم","1-قشم");
options[11]=new Option(" كيش","2-كيش");
options[12]=new Option(" ميناب","3-ميناب");
options[13]=new Option(" بندر خمير","13-بندر خمير");
}
if (Indx=='HMD')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" اسدآباد","5-اسدآباد");
options[2]=new Option(" بهار","6-بهار");
options[3]=new Option(" تويسركان","7-تويسركان");
options[4]=new Option(" رزن","8-رزن");
options[5]=new Option(" كبودر آهنگ","1-كبودر آهنگ");
options[6]=new Option(" ملاير","2-ملاير");
options[7]=new Option(" نهاوند","3-نهاوند");
options[8]=new Option(" همدان","4-همدان");
}
if (Indx=='YZD')
{
options[0]=new Option("لطفا شهر خود را انتخاب کنيد","");
options[1]=new Option(" ابركوه","5-ابركوه");
options[2]=new Option(" اردكان","6-اردكان");
options[3]=new Option(" اشكذر","7-اشكذر");
options[4]=new Option(" بافق","8-بافق");
options[5]=new Option(" تفت","9-تفت");
options[6]=new Option(" طبس","10-طبس");
options[7]=new Option(" مهريز","1-مهريز");
options[8]=new Option(" ميبد","2-ميبد");
options[9]=new Option(" هرات","3-هرات");
options[10]=new Option(" يزد","4-يزد");
}
document.getElementById("city").options[0].selected=true;
}

}
                });
                </script>











	<form action="{$link->getPageLink('authentication.php', true)}" method="post" id="login_form" class="std">
		<fieldset>
			<h3>{l s='Already registered ?'}</h3>
			<p class="text">
				<label for="email">{l s='E-mail address'}</label>
				<span><input type="text" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'htmlall':'UTF-8'|stripslashes}{/if}" class="account_input" /></span>
			</p>
			<p class="text">
				<label for="passwd">{l s='Password'}</label>
				<span><input type="password" id="passwd" name="passwd" value="{if isset($smarty.post.passwd)}{$smarty.post.passwd|escape:'htmlall':'UTF-8'|stripslashes}{/if}" class="account_input" /></span>
			</p>
			<p class="submit">
				{if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}" />{/if}
				<input type="submit" id="SubmitLogin" name="SubmitLogin" class="button" value="{l s='Log in'}" />
			</p>
			<p class="lost_password"><a href="{$link->getPageLink('password.php')}">{l s='Forgot your password?'}</a></p>
		</fieldset>
	</form>
	{if isset($inOrderProcess) && $inOrderProcess && $PS_GUEST_CHECKOUT_ENABLED}
		<form action="{$link->getPageLink('authentication.php', true)}?back={$back}" method="post" id="new_account_form" class="std">
			<fieldset>
				<h3>{l s='Instant Checkout'}</h3>
				<div id="opc_account_form" style="display: block; ">
					<!-- Account -->
					<p class="required text">
						<label for="guest_email">{l s='E-mail address'}</label>
						<input type="text" class="text" id="guest_email" name="guest_email" value="{if isset($smarty.post.guest_email)}{$smarty.post.guest_email}{/if}">
						<sup>*</sup>
					</p>
					<p class="radio required">
						<span>{l s='Title'}</span>
						<input type="radio" name="id_gender" id="id_gender1" value="1" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == '1'}checked="checked"{/if}>
						<label for="id_gender1" class="top">{l s='Mr.'}</label>
						<input type="radio" name="id_gender" id="id_gender2" value="2" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == '2'}checked="checked"{/if}>
						<label for="id_gender2" class="top">{l s='Ms.'}</label>
					</p>
										<p class="required text">
						<label for="firstname">{l s='First name'}</label>
						<input type="text" class="text" id="firstname" name="firstname" onblur="$('#customer_firstname').val($(this).val());" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{/if}">
						<input type="hidden" class="text" id="customer_firstname" name="customer_firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{/if}">
						<sup>*</sup>
					</p>
					<p class="required text">
						<label for="lastname">{l s='Last name'}</label>
						<input type="text" class="text" id="lastname" name="lastname" onblur="$('#customer_lastname').val($(this).val());" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{/if}">
						<input type="hidden" class="text" id="customer_lastname" name="customer_lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{/if}">
						<sup>*</sup>
					</p>
					<p class="select">
						<span>{l s='Date of Birth'}</span>
						<select id="days" name="days">
							<option value="">-</option>
							{foreach from=$days item=day}
								<option value="{$day|escape:'htmlall':'UTF-8'}" {if ($sl_day == $day)} selected="selected"{/if}>{$day|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
							{/foreach}
						</select>
						{*
							{l s='January'}
							{l s='February'}
							{l s='March'}
							{l s='April'}
							{l s='May'}
							{l s='June'}
							{l s='July'}
							{l s='August'}
							{l s='September'}
							{l s='October'}
							{l s='November'}
							{l s='December'}
						*}
						<select id="months" name="months">
							<option value="">-</option>
							{foreach from=$months key=k item=month}
								<option value="{$k|escape:'htmlall':'UTF-8'}" {if ($sl_month == $k)} selected="selected"{/if}>{l s="$month"}&nbsp;</option>
							{/foreach}
						</select>
						<select id="years" name="years">
							<option value="">-</option>
							{foreach from=$years item=year}
								<option value="{$year|escape:'htmlall':'UTF-8'}" {if ($sl_year == $year)} selected="selected"{/if}>{$year|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
							{/foreach}
						</select>
					</p>
					{if isset($newsletter) && $newsletter}
						<p class="checkbox">
							<input type="checkbox" name="newsletter" id="newsletter" value="1" {if isset($smarty.post.newsletter) && $smarty.post.newsletter == '1'}checked="checked"{/if}>
							<label for="newsletter">{l s='Sign up for our newsletter'}</label>
						</p>
						<p class="checkbox">
							<input type="checkbox" name="optin" id="optin" value="1" {if isset($smarty.post.optin) && $smarty.post.optin == '1'}checked="checked"{/if}>
							<label for="optin">{l s='Receive special offers from our partners'}</label>
						</p>
					{/if}
					<h3>{l s='Delivery address'}</h3>
					{foreach from=$dlv_all_fields item=field_name}
						{if $field_name eq "company"}
						<p class="text">
							<label for="company">{l s='Company'}</label>
							<input type="text" class="text" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company}{/if}" />
						</p>
						{elseif $field_name eq "vat_number"}
						<div id="vat_number" style="display:none;">
							<p class="text">
								<label for="vat_number">{l s='VAT number'}</label>
								<input type="text" class="text" name="vat_number" value="{if isset($smarty.post.vat_number)}{$smarty.post.vat_number}{/if}" />
							</p>
						</div>
						{elseif $field_name eq "address1"}
						<p class="required text">
							<label for="address1">{l s='Address'}</label>
							<input type="text" class="text" name="address1" id="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}">
							<sup>*</sup>
						</p>
						{elseif $field_name eq "postcode"}
						<p class="required postcode text">
							<label for="postcode">{l s='Zip / Postal Code'}</label>
							<input type="text" class="text" name="postcode" id="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{/if}" onblur="$('#postcode').val($('#postcode').val().toUpperCase());">
							<sup>*</sup>
						</p>
		{elseif $field_name eq "Country:name" || $field_name eq "country"}
				<p class="required select">
					<label for="id_country">{l s='Country'}</label>
					<select name="id_country" id="id_country">
						<option value="">-</option>
						{foreach from=$countries item=v}
						<option value="{$v.id_country}" {if ($sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
					<sup>*</sup>
				</p>
			{elseif $field_name eq "State:name" || $field_name eq 'state'}
				{assign var='stateExist' value=true}
				<p class="required id_state select">
					<label for="id_state">{l s='State'}</label>
					<select name="id_state" id="id_state">
						<option value="">-</option>
					</select>
					<sup>*</sup>
				<br/><br/><br/>
			     <label for="city">{l s='City'}</label>
			         <select name="city" id="city" style="width: 17em;">
                        <option value="">-</option>
                      </select>
			     <sup>*</sup>
		        </p>
			{/if}
		{/foreach}
		{if $stateExist eq false}
			<p class="required id_state select">
				<label for="id_state">{l s='State'}</label>
				<select name="id_state" id="id_state">
					<option value="">-</option>
				</select>
				<sup>*</sup>
			</p>




					{/if}

	<p class="text">
			<label for="phone_mobile">{l s='Mobile phone'}</label>
			<input type="text" class="text" name="phone_mobile" id="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{/if}" />
		</p>
					<input type="hidden" name="alias" id="alias" value="{l s='My address'}">
					<input type="hidden" name="is_new_customer" id="is_new_customer" value="0">
					<!-- END Account -->
				</div>
			</fieldset>
			<fieldset class="account_creation dni">
				<h3>{l s='Tax identification'}</h3>

				<p class="required text">
					<label for="dni">{l s='Identification number'}</label>
					<input type="text" class="text" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{/if}" />
					<span class="form_info">{l s='DNI / NIF / NIE'}</span>
					<sup>*</sup>
				</p>
			</fieldset>
			<p class="cart_navigation required submit">
				<span><sup>*</sup>{l s='Required field'}</span>
				<input type="submit" class="button" name="submitGuestAccount" id="submitGuestAccount" style="float:right" value="{l s='Continue'}">
			</p>
		</form>
	{/if}
{else}
<form action="{$link->getPageLink('authentication.php', true)}" method="post" id="account-creation_form" class="std">
	{$HOOK_CREATE_ACCOUNT_TOP}
	<fieldset class="account_creation">
		<h3>{l s='Your personal information'}</h3>
		<p class="radio required">
			<span>{l s='Title'}</span>
			<input type="radio" name="id_gender" id="id_gender1" value="1" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == 1}checked="checked"{/if} />
			<label for="id_gender1" class="top">{l s='Mr.'}</label>
			<input type="radio" name="id_gender" id="id_gender2" value="2" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == 2}checked="checked"{/if} />
			<label for="id_gender2" class="top">{l s='Ms.'}</label>
		</p>
		<p class="required text">
			<label for="customer_firstname">{l s='First name'}</label>
			<input onkeyup="$('#firstname').val(this.value);" type="text" class="text" id="customer_firstname" name="customer_firstname" value="{if isset($smarty.post.customer_firstname)}{$smarty.post.customer_firstname}{/if}" />
			<sup>*</sup>
		</p>
		<p class="required text">
			<label for="customer_lastname">{l s='Last name'}</label>
			<input onkeyup="$('#lastname').val(this.value);" type="text" class="text" id="customer_lastname" name="customer_lastname" value="{if isset($smarty.post.customer_lastname)}{$smarty.post.customer_lastname}{/if}" />
			<sup>*</sup>
		</p>
		<p class="required text">
			<label for="email">{l s='E-mail'}</label>
			<input type="text" class="text" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email}{/if}" />
			<sup>*</sup>
		</p>
		<p class="required password">
			<label for="passwd">{l s='Password'}</label>
			<input type="password" class="text" name="passwd" id="passwd" />
			<sup>*</sup>
			<span class="form_info">{l s='(5 characters min.)'}</span>
		</p>
		<p class="select">
			<span>{l s='Date of Birth'}</span>
			<select id="days" name="days">
				<option value="">-</option>
				{foreach from=$days item=day}
					<option value="{$day|escape:'htmlall':'UTF-8'}" {if ($sl_day == $day)} selected="selected"{/if}>{$day|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
				{/foreach}
			</select>
			{*
				{l s='January'}
				{l s='February'}
				{l s='March'}
				{l s='April'}
				{l s='May'}
				{l s='June'}
				{l s='July'}
				{l s='August'}
				{l s='September'}
				{l s='October'}
				{l s='November'}
				{l s='December'}
			*}
			<select id="months" name="months">
				<option value="">-</option>
				{foreach from=$months key=k item=month}
					<option value="{$k|escape:'htmlall':'UTF-8'}" {if ($sl_month == $k)} selected="selected"{/if}>{l s="$month"}&nbsp;</option>
				{/foreach}
			</select>
			<select id="years" name="years">
				<option value="">-</option>
				{foreach from=$years item=year}
					<option value="{$year|escape:'htmlall':'UTF-8'}" {if ($sl_year == $year)} selected="selected"{/if}>{$year|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
				{/foreach}
			</select>
		</p>
		{if isset($newsletter) && $newsletter}
		<p class="checkbox" >
			<input type="checkbox" name="newsletter" id="newsletter" value="1" {if isset($smarty.post.newsletter) AND $smarty.post.newsletter == 1} checked="checked"{/if} />
			<label for="newsletter">{l s='Sign up for our newsletter'}</label>
		</p>
		<p class="checkbox" >
			<input type="checkbox"name="optin" id="optin" value="1" {if isset($smarty.post.optin) AND $smarty.post.optin == 1} checked="checked"{/if} />
			<label for="optin">{l s='Receive special offers from our partners'}</label>
		</p>
		{/if}
	</fieldset>
	<fieldset class="account_creation">
		<h3>{l s='Your address'}</h3>
		{foreach from=$dlv_all_fields item=field_name}
			{if $field_name eq "company"}
				<p class="text">
					<label for="company">{l s='Company'}</label>
					<input type="text" class="text" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company}{/if}" />
				</p>
			{elseif $field_name eq "vat_number"}
				<div id="vat_number" style="display:none;">
					<p class="text">
						<label for="vat_number">{l s='VAT number'}</label>
						<input type="text" class="text" name="vat_number" value="{if isset($smarty.post.vat_number)}{$smarty.post.vat_number}{/if}" />
					</p>
				</div>
			{elseif $field_name eq "firstname"}
				<p class="required text">
					<label for="firstname">{l s='First name'}</label>
					<input type="text" class="text" id="firstname" name="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{/if}" />
					<sup>*</sup>
				</p>
			{elseif $field_name eq "lastname"}
				<p class="required text">
					<label for="lastname">{l s='Last name'}</label>
					<input type="text" class="text" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{/if}" />
					<sup>*</sup>
				</p>
			{elseif $field_name eq "address1"}
				<p class="required text">
					<label for="address1">{l s='Address'}</label>
					<input type="text" class="text" name="address1" id="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}" />
					<sup>*</sup>
					<span class="inline-infos">{l s='Street address, P.O. box, company name, c/o'}</span>
				</p>
			{elseif $field_name eq "address2"}
				<p class="text">
					<label for="address2">{l s='Address (Line 2)'}</label>
					<input type="text" class="text" name="address2" id="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2}{/if}" />
					<span class="inline-infos">{l s='Apartment, suite, unit, building, floor, etc.'}</span>
				</p>
			{elseif $field_name eq "postcode"}
				<p class="required postcode text">
					<label for="postcode">{l s='Zip / Postal Code'}</label>
					<input type="text" class="text" name="postcode" id="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
					<sup>*</sup>
				</p>

				<!--
					if customer hasn't update his layout address, country has to be verified
					but it's deprecated
				-->
			{elseif $field_name eq "Country:name" || $field_name eq "country"}
				<p class="required select">
					<label for="id_country">{l s='Country'}</label>
					<select name="id_country" id="id_country">
						<option value="">-</option>
						{foreach from=$countries item=v}
						<option value="{$v.id_country}" {if ($sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
					<sup>*</sup>
				</p>
			{elseif $field_name eq "State:name" || $field_name eq 'state'}
				{assign var='stateExist' value=true}
				<p class="required id_state select">
					<label for="id_state">{l s='State'}</label>
					<select name="id_state" id="id_state">
						<option value="">-</option>
					</select>
					<sup>*</sup>
				<br/><br/><br/>
			     <label for="city">{l s='City'}</label>
			         <select name="city" id="city" style="width: 17em;">
                        <option value="">-</option>
                      </select>
			     <sup>*</sup>
		        </p>
			{/if}
		{/foreach}
		{if $stateExist eq false}
			<p class="required id_state select">
				<label for="id_state">{l s='State'}</label>
				<select name="id_state" id="id_state">
					<option value="">-</option>
				</select>
				<sup>*</sup>
			</p>
		{/if}
		<p class="textarea">
			<label for="other">{l s='Additional information'}</label>
			<textarea name="other" id="other" cols="26" rows="3">{if isset($smarty.post.other)}{$smarty.post.other}{/if}</textarea>
		</p>
		<p style="margin-left:50px;">{l s='You must register at least one phone number'} <sup style="color:red;">*</sup></p>
		<p class="text">
			<label for="phone">{l s='Home phone'}</label>
			<input type="text" class="text" name="phone" id="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{/if}" />
		</p>
		<p class="text">
			<label for="phone_mobile">{l s='Mobile phone'}</label>
			<input type="text" class="text" name="phone_mobile" id="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{/if}" />
		</p>
		<p class="required text" id="address_alias">
			<label for="alias">{l s='Assign an address title for future reference'} </label>
			<input type="text" class="text" name="alias" id="alias" value="{if isset($smarty.post.alias)}{$smarty.post.alias}{else}{l s='My address'}{/if}" />
			<sup>*</sup>
		</p>
	</fieldset>
	<fieldset class="account_creation dni">
		<h3>{l s='Tax identification'}</h3>

		<p class="required text">
			<label for="dni">{l s='Identification number'}</label>
			<input type="text" class="text" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{/if}" />
			<span class="form_info">{l s='DNI / NIF / NIE'}</span>
			<sup>*</sup>
		</p>
	</fieldset>
	{$HOOK_CREATE_ACCOUNT_FORM}
	<p class="cart_navigation required submit">
		<input type="hidden" name="email_create" value="1" />
		<input type="hidden" name="is_new_customer" value="1" />
		{if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}" />{/if}
		<input type="submit" name="submitAccount" id="submitAccount" value="{l s='Register'}" class="exclusive" />
		<span><sup>*</sup>{l s='Required field'}</span>
	</p>

</form>
{/if}

