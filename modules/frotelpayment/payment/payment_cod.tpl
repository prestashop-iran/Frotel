   <div id="frotel-show" style="padding: 0;box-shadow: 0 -65px 70px #F8F8F8 inset; width: 100%; padding-bottom: 20px; border-radius:5px 5px 5px 5px">
	
	<p>
	{l s=" شما روش" mod='frotelpayment'}<span style="color:#66CC55; font-weight:bold">{l s="پرداخت وجه هنگام تحویل کالا" mod='frotelpayment'}</span> {l s="را برای پرداخت هزینه ی سفارش انتخاب کرده اید." mod='frotelpayment'}
	</p>
	<p>
	{l s="کل وجه پرداختی از طرف شما شامل هزینه کالا بعلاوه هزینه ی ارسال و هزینه ی خدمات شرکت فروتل " mod='frotelpayment'}<span style="color:#CC6600; font-weight:bold">{$amount}</span> {l s="می باشد." mod='frotelpayment'}
	</p>
	<p>
	{l s="برای تایید سفارش خود بر روی دکمه زیر کلیک کنید" mod='frotelpayment'}
	</p>
	<table border="0" style="margin: 20px 30px 0 0">
	<form action="{$action}" method="post" name="frotelPayment" id="pForm">
	<input name="codtoken" type="hidden" value="{$token}" />
   <tr>
      <td></td>
      <td style="padding-right: 350px;"><input class="fro_textbox" name="submitPayment" type="submit" value="{l s="تایید سفارش" mod='frotelpayment'}"  style="background: #12a923; color: #eee; box-shadow: 0 -15px 15px #12bb23 inset;cursor:pointera;"/></td>
   </tr>
  </form>
  </table>

    </div>
