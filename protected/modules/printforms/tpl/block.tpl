<link type="text/css" rel="stylesheet" href="/css/printform.css" />


{if $document == 'sch'}
	<div class="wrapper-schet">
		<div class="arendodatel-name">
			<p>Индивидуальный предприниматель Кононович Галина Павловна</p>
		</div>
		<div class="arendodatel-address">
			<p>Адрес: 666784, Иркутская обл, Усть-Кут г., Кирова ул, 12, кв. 14</p>
		</div>
		<div class="arendodatel-bank">
			<p>Образец заполнения платежного поручения</p>
			<table border="1" cellspacing="0">
				<tr>
					<td>ИНН 381800677995</td>
					<td>КПП</td>
					<td rowspan="2">Сч. №</td>
					<td rowspan="2">40802810130000045576</td>
				</tr>
				<tr>
					<td colspan="2">Получатель<br>Индивидуальный предприниматель Кононович Галина Павловна</td>

				</tr>
				<tr>
					<td rowspan="2" colspan="2">Банк Получателя<br>КРАСНОДАРСКОЕ ОТДЕЛЕНИЕ N8619 ПАО СБЕРБАНК</td>
					<td>БИК</td>
					<td colspan="2">040349602</td>
				</tr>
				<tr>
					<td>Сч. №</td>
					<td colspan="2">30101810100000000602</td>
				</tr>
			</table>
		</div>
		<div class="schet-number">
		 	{if $peni == 0}
			<p>Счет № A-{$print.document_number} от {$invoice_day} {$month_string} {$date.0} года</p>
			{else}
			<p>Счет № П-{$print.document_number} от {$invoice_day} {$month_string} {$date.0} года</p>
			{/if}
		</div> 
		<div class="arendator-name">
			<p>Плательщик: {$print.renter_name}</p>
			<p>Грузополучатель: {$print.renter_name}</p>
		</div>
		{$count=1}
		<div class="schet">
			<table border="1" cellspacing="0">
				<tr>
					<td>№</td>
					<td>Наименование товара (услуги)</td>
					<td>Единица измерения</td>
					<td>Коли<br>чество</td>
					<td style="width: 90px;">Цена</td>
					<td style="width: 90px;">Сумма</td>
				</tr>
				{if $print.ground}

					{if $peni == 1}
						<tr>
							<td>{$count}</td>
							<td>{$item.desc} за {$print.period_month} {$print.period_year} года.</td>
							<td><input type="text" value="{$allpeni.ed}"></td>
							<td><input type="text" value="{$allpeni.amount}"></td>
							<td><input type="text" value="{$allpeni.price}"></td>
							<td><input type="text" value="{$allpeni.peni}"></td>
						</tr>
						{$count = $count + 1}
					{else}
						{foreach from=$print.ground item=a}
						<tr>
							<td>{$count}</td>
							<td>{$a.desc} за {$print.period_month} {$print.period_year} года.</td>
							<td><input type="text" value="{$a.ed}"></td>
							<td><input type="text" value="{$print.invoice_amount}"></td>
							<td><input type="text" value="{$a.price}"></td>
							<td><input type="text" value="{$a.summ}"></td>
						</tr>
						{$count = $count + 1}
						{/foreach}
					{/if}						
				</table>
				<table style="widht: 200px; float: right;">
					<tr>
						<td style="text-align: right; font-weight: bold; border: none;">Итого:</td>
						<td style="width: 90px; border: 1px solid #000000; border-top: none;"><input type="text" value="{$print.allprice}"></td>
					</tr>
					<tr>
						<td style="text-align: right; font-weight: bold; border: none;">Без налога (НДС).</td>
						<td style="border: 1px solid #000000;">-</td>
					</tr>
					<tr>
						<td style="text-align: right; font-weight: bold; border: none;">Всего к оплате:</td>
						<td style="border: 1px solid #000000;"><input type="text" value="{$print.allprice}"></td>
					</tr>
				</table>
				<div style="clear: both;"></div>
				<div class="podval">
					<p>Всего наименований {$count} на сумму <input type="text" value="{$print.allprice}"></p>
					<p><strong>(<input type="text" value="{$print.allprice_string}">)</strong></p>
				</div>
				{else}
				<tr>
					<td>1</td>
					{if $peni == 0}
						<td>{$print.ground_desc} за {$print.period_month} {$print.period_year} года. Основание: Договор аренды нежилого помещения № {$print.contract_number} от {$print.contract_date} г.</td>
						<!--<td>{$print.ground_desc}. Основание: Государственный контракт на аренду нежилого помещения № {$print.contract_number} от {$print.contract_date} г.</td>-->
						<td><input type="text" value="{$print.ground_ed}"></td>
						<td><input type="text" value="{$print.invoice_amount}"></td>
						{if $print.modified == '1'}
							<td><input type="text" value="{$print.invoice_summa}"></td>
						{else}
							<td><input type="text" value="{$print.contract_summa}"></td>
						{/if}
						<td><input type="text" value="{$print.invoice_summa}"></td>
					{else}			
						<td>{$allpeni.desc} за {$print.period_month} {$print.period_year} года.</td>
						<td><input type="text" value="{$allpeni.ed}"></td>
						<td><input type="text" value="{$allpeni.delay}"></td>
						<td><input type="text" value="{math equation="x / y" x=$allpeni.peni y=$allpeni.delay format="%.2f"}"></td>
						<td><input type="text" value="{$allpeni.peni}"></td>
					{/if}
				</tr>				
				</table>
				<table style="widht: 200px; float: right;">
					<tr>
						<td style="text-align: right; font-weight: bold; border: none;">Итого:</td>
						{if $peni == 1}
							<td style="width: 90px; border: 1px solid #000000; border-top: none;"><input type="text" value="{$allpeni.peni}"></td>
							{else}
							<td style="width: 90px; border: 1px solid #000000; border-top: none;"><input type="text" value="{$print.invoice_summa}"></td>
						{/if}						
					</tr>
					{if $disc == 1}
						<tr>
							<td style="text-align: right; font-weight: bold; border: none;">Скидка:</td>
							<td style="width: 90px; border: 1px solid #000000; border-top: none;"><input type="text" value="{$discount_summ}"></td>
						</tr>
					{/if}
					<tr>
						<td style="text-align: right; font-weight: bold; border: none;">Без налога (НДС).</td>
						<td style="border: 1px solid #000000;">-</td>
					</tr>
					{if $disc == 1}
						<tr>
							<td style="text-align: right; font-weight: bold; border: none;">Всего к оплате:</td>
							<td style="border: 1px solid #000000;"><input type="text" value="{$print.discoint}"></td>
						</tr>
					{else}
						{if $peni == 1}
							<td style="text-align: right; font-weight: bold; border: none;">Всего к оплате:</td>
							<td style="border: 1px solid #000000;"><input type="text" value="{$allpeni.peni}"></td>	
							{else}
							<tr>
								<td style="text-align: right; font-weight: bold; border: none;">Всего к оплате:</td>
								<td style="border: 1px solid #000000;"><input type="text" value="{$print.invoice_summa}"></td>
							</tr>
						{/if}
					{/if}
				</table>
				<div style="clear: both;"></div>
				<div class="podval">
					{if $peni == 1}
						<p>Всего наименований 1 на сумму <input type="text" value="{$allpeni.peni}"></p>
						{elseif $disc == 1}
							<p>Всего наименований 1 на сумму <input type="text" id='numeric-sum' value="{$print.discoint}"></p>
						{else}
							<p>Всего наименований 1 на сумму <input type="text" id='numeric-sum' value="{$print.invoice_summa}"></p>
					{/if}

					{if $peni == 1}
						<p><input style="width: 97%;" type="text" value="( {$allpeni.string} )"></p>
						{else}
						<p><input style="width: 100% !important; max-width: unset;" type="text" class='numeric-sum' value="обновите страницу"></p>
					{/if}					
					
				</div>
				{/if}			
		</div>
		<div class="sign">
			<p>Руководитель ______________________ (Гавриленко Н.В.)</p>
			<!--<p>Руководитель ______________________ (Глазков С.А.)</p>-->
			<p style="font-style: italic; font-size: 12px;">Действующая за Кононович Галину Павловну по Доверенности от 09.09.2016 г.<br>Бланк доверенности: 23АА6203810</p>
			<!--<p style="font-style: italic; font-size: 12px;">Действующий за Кононович Галину Павловну по Доверенности от 07.02.2014 г.<br>Бланк доверенности: 23АА3453154</p>-->
			{if $pr == 1}
			<div class="akt-sign-img">
				<img src="/images/sign.png" width="100">
				<!--<img src="/images/sign-glazkov.png" width="100" style="padding-top: 20px;">-->
			</div>
			<div class="akt-print">
				<img src="/images/print.png" width="146">
			</div>
			{/if}
			{if $disc == 1}
				<p style="margin-top: 50px; width: 100%; text-align: center; font-size: 18px;">Счет действителен до "05" {$month_string} {$date.0} года</p>
			{/if}
		</div>		
	</div>
{elseif $document == 'akt'}
	<div class="wrapper-akt">
		<div class="arendodatel-name">
			<p>Индивидуальный предприниматель Кононович Галина Павловна</p>
		</div>
		<div class="arendodatel-address">
			<p>Адрес: 666784, Иркутская обл, Усть-Кут г., Кирова ул, 12, кв. 14</p>
		</div>
		<div class="schet-number">
			{if $peni == 1}
				<p>Акт № П-{$print.document_number} от {$date.2} {$month_string} {$print.period_year} года</p>
			{else}
				<p>Акт № A-{$print.document_number} от {$date.2} {$month_string} {$print.period_year} года</p>
			{/if}
		</div>
		<div class="arendator-name">
			<p>Заказчик: {$print.renter_name}</p>
		</div>
		{$count=1}
		<div class="schet">
			<table border="1" cellspacing="0">
				<tr>
					<td>№</td>
					<td>Наименование товара (услуги)</td>
					<td>Единица измерения</td>
					<td>Коли<br>чество</td>
					<td style="width: 90px;">Цена</td>
					<td style="width: 90px;">Сумма</td>
				</tr>
				{if $print.ground}
						{foreach from=$print.ground item=a}
						<tr>
							<td>{$allpeni.ed}</td>
							<td>{$a.desc} за {$print.period_month} {$print.period_year} года</td>
							<!--<td>{$a.desc}</td>-->
							<td>{$a.ed}</td>
							<td><input type="text" value="{$print.invoice_amount}"></td>
							<td><input type="text" value="{$a.price}"></td>
							<td><input type="text" value="{$a.summ}"></td>
						</tr>
						{$count = $count + 1}
						{/foreach}									
				</table>
				<table style="widht: 200px; float: right;">
					<tr>
						<td style="text-align: right; font-weight: bold; border: none;">Итого:</td>
						<td style="width: 90px; border: 1px solid #000000; border-top: none;"><input type="text" value="{$print.allprice}"></td>
					</tr>
					<tr>
						<td style="text-align: right; font-weight: bold; border: none;">Без налога (НДС).</td>
						<td style="border: 1px solid #000000;">-</td>
					</tr>
					<tr>
						<td style="text-align: right; font-weight: bold; border: none;">Всего к оплате:</td>
						<td style="border: 1px solid #000000;"><input type="text" value="{$print.allprice}"></td>
					</tr>
				</table>
				<div style="clear: both;"></div>
				<div class="podval">
					<p style="font-style: italic; font-size: 12px;">Всего оказано услуг на сумму:  <strong> <input type="text" value="{$print.allprice_string}"> </strong> в т.ч.: НДС - Ноль рублей 00 копеек</p>
					<p style="font-style: italic; font-size: 12px;">Вышеперечисленные услуги выполненые полностью и в срок. Заказчик претензий по объему, качеству и срокам оказания услуг не имеет</p>
				</div>
				{else}
					{if $peni == 1}
						<tr>
							<td>1</td>
							<td>{$allpeni.desc} за {$print.period_month} {$print.period_year} года. Основание: Договор аренды нежилого помещения № {$print.contract_number} от {$print.contract_date} г.</td>
							<!--<td>{$print.ground_desc}. Основание: Государственный контракт на аренду нежилого помещения № {$print.contract_number} от {$print.contract_date} г.</td>-->
							<td><input type="text" value="{$allpeni.ed}"></td>
							<td><input type="text" value="{$allpeni.delay}"></td>
							<td><input type="text" value="{math equation="x / y" x=$allpeni.peni y=$allpeni.delay format="%.2f"}"></td>
							<td><input type="text" value="{$allpeni.peni}"></td>
						</tr>						
					{else}
						<tr>
							<td>1</td>
							<td>{$print.ground_desc} за {$print.period_month} {$print.period_year} года. Основание: Договор аренды нежилого помещения № {$print.contract_number} от {$print.contract_date} г.</td>
							<!--<td>{$print.ground_desc}. Основание: Государственный контракт на аренду нежилого помещения № {$print.contract_number} от {$print.contract_date} г.</td>-->
							<td>{$print.ground_ed}</td>
							<td><input type="text" value="{$print.invoice_amount}"></td>
							{if $print.modified == '1'}
							<td><input type="text" value="{$print.invoice_summa}"></td>
							{else}
							<td><input type="text" value="{$print.contract_summa}"></td>
							{/if}
							<td><input type="text" value="{$print.invoice_summa}"></td>
						</tr>	
					{/if}			
				</table>
				<table style="widht: 200px; float: right;">
					<tr>
						<td style="text-align: right; font-weight: bold; border: none;">Итого:</td>
						{if $peni == 1} 
							<td style="width: 90px; border: 1px solid #000000; border-top: none;"><input type="text" value="{$allpeni.peni}"></td>
						{else}
							<td style="width: 90px; border: 1px solid #000000; border-top: none;"><input type="text" value="{$print.invoice_summa}"></td>
						{/if}				
					</tr>
					{if $disc == 1}
						<tr>
							<td style="text-align: right; font-weight: bold; border: none;">Скидка:</td>
							<td style="width: 90px; border: 1px solid #000000; border-top: none;"><input type="text" value="{$discount_summ}"></td>
						</tr>
					{/if}
					<tr>
						<td style="text-align: right; font-weight: bold; border: none;">Без налога (НДС).</td>
						<td style="border: 1px solid #000000;">-</td>
					</tr>
					{if $peni == 1}
						<tr>
							<td style="text-align: right; font-weight: bold; border: none;">Всего:</td>
							<td style="border: 1px solid #000000;"><input type="text" value="{$allpeni.peni}"></td>
						</tr>
					{else}
						{if $disc == 1}
							<tr>
								<td style="text-align: right; font-weight: bold; border: none;">Всего:</td>
								<td style="border: 1px solid #000000;"><input type="text" id='numeric-sum' value="{$print.discoint}"></td>
							</tr>
						{else}
							<tr>
								<td style="text-align: right; font-weight: bold; border: none;">Всего:</td>
								<td style="border: 1px solid #000000;"><input type="text" id='numeric-sum' value="{$print.invoice_summa}"></td>
							</tr>
						{/if}
					{/if}					

				</table>
				<div style="clear: both;"></div>
				<div class="podval">
				{if $peni == 1}
					<p style="font-style: italic; font-size: 12px;">Всего оказано услуг на сумму: <strong> <input style="width: 100% !important; max-width: unset;" type="text" class='numeric-sum' value="{$allpeni.string}"></strong> в т.ч.: НДС - Ноль рублей 00 копеек</p>
				{else}
					{if $disc == 1}
						<p style="font-style: italic; font-size: 12px;">Всего оказано услуг на сумму: <strong> <input style="width: 100% !important; max-width: unset;" type="text" class='numeric-sum' value=""></strong> в т.ч.: НДС - Ноль рублей 00 копеек</p>
					{else}
						<p style="font-style: italic; font-size: 12px;">Всего оказано услуг на сумму: <strong> <input style="width: 100% !important; max-width: unset;" type="text" class='numeric-sum' value=""></strong> в т.ч.: НДС - Ноль рублей 00 копеек</p>
					{/if}
				{/if}	
					<p style="font-style: italic; font-size: 12px;">Вышеперечисленные услуги выполненые полностью и в срок. Заказчик претензий по объему, качеству и срокам оказания услуг не имеет</p>
				</div>
				{/if}			
		</div>
		<div class="sign">
			<p style="display: inline-block; margin-right: 120px;">Исполнитель: _____________(Гавриленко Н.В.)</p>
			<!--<p style="display: inline-block; margin-right: 120px;">Исполнитель: _____________(Глазков С.А.)</p>-->
			<p  style="display: inline-block;">Заказчик: ________________________</p>
			<p style="font-style: italic; font-size: 12px;">за Кононович Галину Павловну по доверенности от 09.09.2016 г.<br>Бланк доверенности: 23АА6203810</p>
			<!--<p style="font-style: italic; font-size: 12px;">за Кононович Галину Павловну по доверенности от 07.02.2014 г.<br>Бланк доверенности: 23АА 3453154</p>-->
			{if $pr == 1}
			<div class="akt-print">
				<img src="/images/print.png" width="146">				
			</div>
			<div class="akt-sign-img">
				<img src="/images/sign.png" width="100">
				<!--<img src="/images/sign-glazkov.png" width="100" style="padding-top: 20px;">-->
			</div>
			{/if}
		</div>
	</div>
{elseif $document == 'sf'}
	<div class="wrapper-sf">
		<div class="forma">
			<p>Приложение № 1<br>к постановлению Правительства Российской Федерации<br>от 26 декабря 2011 г.  № 1137</p>
		</div>
		<div style="clear: both;"></div>
		
		<div class="sf-number">
			{if $peni == 1}
				<p>Счет-фактура № П-{$print.document_number} от {$date.2} {$month_string} {$print.period_year} года</p>
			{else}
				<p>Счет-фактура № A-{$print.document_number} от {$date.2} {$month_string} {$print.period_year} года</p>
			{/if}
			<p style="font-size: 16px; font-weight: normal;">Исправление № ---- от ----</p>
		</div>
		
		<div class="sf-data">
			<p>Продавец: Индивидуальный предприниматель Кононович Галина Павловна</p>
			<p>Адрес: 666784, Иркутская обл, Усть-Кут г., Кирова ул, 12, кв. 14</p>
			<p>ИНН/КПП продавца  381800677995</p>
			<p>Грузоотправитель и его адрес: ----</p>
			<p>Грузополучатель и его адрес: ----</p>
			<p>К платежно-расчетному документу №    от</p>
			<p>Покупатель: {$print.renter_name}</p>
			<p>Адрес: {$print.renter_address}</p>
			<p>ИНН/КПП покупателя: {$print.inn} {if $print.kpp != ""}/{/if} {$print.kpp}</p>
			<p>Валюта: наименование, код  Российский рубль, 643</p>
			<p>Идентификатор государственного контракта, договора (соглашения) (при наличии)________________________</p>
		</div>

		<div class="sf-table">
			<table cellspacing="0">
				<tr>
					<td rowspan="2">Наименование товара (описание<br>выполненных работ, оказанных<br>услуг), имущественного права</td>
					<td rowspan="2">Код<br>вида<br>товара</td>
					<td colspan="2">Единица<br>измерения</td>
					<td rowspan="2">Коли-<br>чество<br>(объем)</td>
					<td rowspan="2">Цена (тариф)<br>за единицу<br>измерения</td>
					<td rowspan="2">Стоимость<br>товаров<br>(работ, услуг),<br>имуществен-<br>ных прав<br>без налога-<br>всего</td>
					<td rowspan="2">В том числе<br>сумма<br>акциза</td>
					<td rowspan="2">Налоговая<br>ставка</td>
					<td rowspan="2">Сумма<br>налога,<br>предъявляемая<br>покупателю</td>
					<td rowspan="2">Стоимость<br>товаров<br>(работ, услуг),<br>имуществен-<br>ных прав с<br>налогом-всего</td>
					<td colspan="2">Страна<br>происхождения товара</td>
					<td rowspan="2">Регистр. номер<br>таможенной<br>декларации</td>
				</tr>
				<tr>
					
					<td>Код</td>
					<td>условное<br>обозначение<br>(национальное)</td>
									
					<td>цифровой<br>код</td>
					<td>краткое<br>наименование</td>
					
				</tr>
				<tr>
					<td>1</td>
					<td>1a</td>
					<td>2</td>
					<td>2а</td>
					<td>3</td>
					<td>4</td>
					<td>5</td>
					<td>6</td>
					<td>7</td>
					<td>8</td>
					<td>9</td>
					<td>10</td>
					<td>10а</td>
					<td>11</td>
				</tr>
				{if $print.ground}
					{foreach from=$print.ground item=a}
					<tr>
						<td>{$a.desc} за {$print.period_month} {$print.period_year} года</td>
						<td> - </td>
						<td>{$a.code_ed}</td>
						<td><input type="text" value="{$a.ed}"></td>
						<td><input type="text" value="{$print.invoice_amount}"></td>
						<td><input type="text" value="{$a.price}"></td>
						<td><input type="text" value="{$a.summ}"></td>
						<td>без акциза</td>
						<td>без НДС</td>
						<td>без НДС</td>
						<td><input type="text" value="{$a.summ}"></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					{/foreach}
					<tr>
						<td colspan="6"><strong>Всего к оплате:</strong></td>
						<td><input type="text" value="{$print.allprice}"></td>
						<td colspan="2">X</td>
						<td>без НДС</td>
						<td><input type="text" value="{$print.allprice}"></td>
						<td colspan="3" style="border:none;"></td>
					</tr>
				{else}
					<tr>
					{if $peni == 1}
						<td>{$allpeni.desc} за {$print.period_month} {$print.period_year} года. Основание: Договор аренды нежилого помещения № {$print.contract_number} от {$print.contract_date} г.</td>
						<td> - </td>
						<td>{$allpeni.code_ed}</td>
						<td><input type="text" value="{$allpeni.ed}"></td>
						<td><input type="text" value="{$allpeni.delay}"></td>
						<td><input type="text" value="{math equation="x / y" x=$allpeni.peni y=$allpeni.delay format="%.2f"}"></td>
						<td><input type="text" value="{$allpeni.peni}"></td>
					{else}
						<td>{$print.ground_desc} за {$print.period_month} {$print.period_year} года. Основание: Договор аренды нежилого помещения № {$print.contract_number} от {$print.contract_date} г.</td>
						<td> - </td>
						<td>{$print.ground_code}</td>
						<td><input type="text" value="{$print.ground_ed}"></td>
						<td><input type="text" value="{$print.invoice_amount}"></td>
						{if $disc == 1}
							<td><input type="text" value="{$print.discoint}"></td>
						{else}
							{if $print.modified == '1'}
							<td><input type="text" value="{$print.invoice_summa}"></td>
							{else}
							<td><input type="text" value="{$print.contract_summa}"></td>
							{/if}
						{/if}
						
						{if $disc == 1}
							<td><input type="text" value="{$print.discoint}"></td>
						{else}
							<td><input type="text" value="{$print.invoice_summa}"></td>
						{/if}
					{/if}						
						<td>без акциза</td>
						<td>без НДС</td>
						<td>без НДС</td>
					{if $peni == 1}
						<td><input type="text" value="{$allpeni.peni}"></td>
					{else}
						{if $disc == 1}
							<td><input type="text" value="{$print.discoint}"></td>
						{else}
							<td><input type="text" value="{$print.invoice_summa}"></td>
						{/if}
					{/if}			
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="6" style="text-transform: uppercase;"><strong>Всего к оплате:</strong></td>
					{if $peni == 1}
						<td><input type="text" value="{$allpeni.peni}"></td>
					{else}
						{if $disc == 1}
							<td><input type="text" value="{$print.discoint}"></td>
						{else}
							<td><input type="text" value="{$print.invoice_summa}"></td>
						{/if}
					{/if}	
						<td colspan="2">X</td>
						<td>без НДС</td>
					{if $peni == 1}
						<td><input type="text" value="{$allpeni.peni}"></td>
					{else}
						{if $disc == 1}
							<td><input type="text" value="{$print.discoint}"></td>
						{else}
							<td><input type="text" value="{$print.invoice_summa}"></td>
						{/if}
					{/if}	
						<td colspan="3" style="border:none;"></td>
					</tr>
				{/if}
			</table>
		</div>
		<div class="sign">
			<p style="display: inline-block; margin-right: 100px; ">Индивидуальный предприниматель ________________ (Гавриленко Н.В.)</p>
			<!--<p style="display: inline-block; margin-right: 100px; ">Индивидуальный предприниматель ________________ (Глазков С.А.)</p>-->
			<p style="display: inline-block;"><span style="text-decoration: underline; padding: 0 100px;">серия 38 № 002688578</span><br><span style="font-style: italic; font-size: 10px;">(реквизиты свидетельства о государственной регистрации индивидуального предпринимателя)</span></p>
			<p style="font-style: italic; font-size: 12px;">За Кононович Г.П. по доверенности от 09.09.2016 г.</p>
			<!--<p style="font-style: italic; font-size: 12px;">За Кононович Г.П. по доверенности от 07.02.2014 г.</p>-->
			{if $pr == '1'}
			<div class="sf-sign-img">
				<img src="/images/sign.png" width="100">
				<!--<img src="/images/sign-glazkov.png" width="100" style="padding-top: 20px;">-->
			</div>
			{/if}
		</div>
	</div>
{/if}

{if $document == 'as'}
<div style="width: 1060px; padding: 15px; position: relative;">
	<p style="font-size: 20px; width: fit-content; margin: 0 auto;"><b>Акт сверки</b></p>
	<p style="width: fit-content; margin: 0 auto; text-align: center;">
		взаимных рассчётов за период: {$smarty.get.start} - {$smarty.get.end} <br>
		между: ИП Кононович Галина Павловна и {$client.short_name} <br>
		по договору аренды нежилого помещения № {foreach from=$history item=hist} {$hist.contract}{break} {/foreach} от {$client.datetime}
	</p>
	<div style="margin-top: 40px">
		<p>Мы, нижеподписавшиеся, ИП Кононович Галина Павловна, с одной стороны, и {$client.short_name}, с другой стороны, составили данный акт сверки в том, что, состояние взаимных рассчётов по данным учёта слудующее:</p>
	</div>
	<table style="width: 100%; cellspacing: 0; border-collapse: collapse;">
		<tr>
			<td colspan="6" style="width: 50%">По данным ИП Кононович Галина Павловна, руб</td>
			<td colspan="6" style="width: 50%">По данным {$client.short_name}, руб</td>
		</tr>
		<tr>
			<td class="ta" colspan="1"><b>Дата</b></td>
			<td class="ta" colspan="3" style="width: 20%"><b>Документ</b></td>
			<td class="ta" colspan="1"><b>Дебет</b></td>
			<td class="ta" colspan="1"><b>Кредит</b></td>
			<td class="ta" colspan="1"><b>Дата</b></td>
			<td class="ta" colspan="3" style="width: 20%"><b>Документ</b></td>
			<td class="ta" colspan="1"><b>Дебет</b></td>
			<td class="ta" colspan="1"><b>Кредит</b></td>		
		</tr>
		<tr>
			<td class="fs-11" colspan="4" style="width: 20%"><b>Сальдо начальное</b></td>
				{if $saldo < 0}
				<td class="fs-11 ta" colspan="1"></td>
				<td class="fs-11 ta period-credit" colspan="1">{math equation="x * y" x=$saldo y=-1 format="%.2f"}</td>
				{else}
				<td class="fs-11 ta period-debet" colspan="1">{$saldo}</td>
				<td class="fs-11 ta" colspan="1"> </td>
				{/if}
			<td class="fs-11" colspan="4"  style="width: 20%"><b>Сальдо начальное</b></td>
				{if $saldo < 0}
				<td class="fs-11 ta" colspan="1">{math equation="x * y" x=$saldo y=-1 format="%.2f"}</td>
				<td class="fs-11 ta" colspan="1"></td>
				{else}
				<td class="fs-11 ta" colspan="1"></td>
				<td class="fs-11 ta" colspan="1">{$saldo}</td>
				{/if}	
		</tr>
		{foreach from=$history item=action}
			{if $action.ground == 'schet' && $action.valid}
			<tr style="line-height: 25px">
				<td class="fs-11" colspan="1">{$action.date}</td>
				<td class="fs-11" colspan="3" style="width: 20%">Cчёт за аренду (№ {$action.ground_id}  от {$action.date})</td>
				<td class="fs-11 ta" colspan="1"></td>
				<td class="fs-11 ta period-credit" colspan="1">{$action.summa}</td>
				<td class="fs-11" colspan="1">{$action.date}</td>
				<td class="fs-11" colspan="3" style="width: 20%">Cчёт за аренду (№ {$action.ground_id} от {$action.date})</td>
				<td class="fs-11 ta" colspan="1">{$action.summa}</td>
				<td class="fs-11 ta" colspan="1"></td>	
			</tr>
			{elseif $action.ground == 'peni' && $action.valid}
			<tr style="line-height: 25px">
				<td class="fs-11" colspan="1">{$action.date}</td>
				<td class="fs-11" colspan="3" style="width: 20%">Пени по счёту (№ {$action.ground_id}  от {$action.date})</td>
				<td class="fs-11 ta" colspan="1"></td>
				<td class="fs-11 ta period-credit" colspan="1">{$action.summa}</td>
				<td class="fs-11" colspan="1">{$action.date}</td>
				<td class="fs-11" colspan="3" style="width: 20%">Пени по счёту (№ {$action.ground_id} от {$action.date})</td>
				<td class="fs-11 ta" colspan="1">{$action.summa}</td>
				<td class="fs-11 ta" colspan="1"></td>	
			</tr>
			{elseif $action.ground == 'payment' && $action.valid}
			<tr style="line-height: 25px">
				<td class="fs-11" colspan="1">{$action.date}</td>
				<td class="fs-11" colspan="3" style="width: 20%">Оплата счёта (№ {$action.ground_id} от {$action.date})</td>
				<td class="fs-11 ta period-debet" colspan="1">{$action.summa}</td>
				<td class="fs-11 ta" colspan="1"></td>
				<td class="fs-11" colspan="1">{$action.date}</td>
				<td class="fs-11" colspan="3" style="width: 20%">Оплата счёта (№ {$action.ground_id} от {$action.date})</td>
				<td class="fs-11 ta" colspan="1"></td>
				<td class="fs-11 ta" colspan="1">{$action.summa}</td>	
			</tr>			
			{elseif $action.ground == 'peni-payment' && $action.valid}
			<tr style="line-height: 25px">
				<td class="fs-11" colspan="1">{$action.date}</td>
				<td class="fs-11" colspan="3" style="width: 20%">Оплата пени (№ {$action.ground_id} от {$action.date})</td>
				<td class="fs-11 ta period-debet" colspan="1">{$action.summa}</td>
				<td class="fs-11 ta" colspan="1"></td>
				<td class="fs-11" colspan="1">{$action.date}</td>
				<td class="fs-11" colspan="3" style="width: 20%">Оплата пени (№ {$action.ground_id} от {$action.date})</td>
				<td class="fs-11 ta" colspan="1"></td>
				<td class="fs-11 ta" colspan="1">{$action.summa}</td>	
			</tr>			
			{/if}
		{/foreach}
		<tr>
			<td class="fs-11" colspan="4" style="width: 20%"><b>Обороты за период</b></td>
			<td class="fs-11 ta final-debet" colspan="1"><b></b></td>
			<td class="fs-11 ta final-credit" colspan="1"><b></b></td>
			<td class="fs-11" colspan="4"  style="width: 20%"><b>Обороты за период</b></td>
			<td class="fs-11 ta final-credit" colspan="1"><b></b></td>
			<td class="fs-11 ta final-debet" colspan="1"><b></b></td>	
		</tr>
		<tr>
			<td class="fs-11" colspan="4" style="width: 20%"><b>Сальдо конечное</b></td>
			<td class="fs-11 ta final-saldo" colspan="1"><b></b></td>
			<td class="fs-11 ta saldo-minus" colspan="1"><b></b></td>
			<td class="fs-11" colspan="4"  style="width: 20%"><b>Сальдо конечное</b></td>
			<td class="fs-11 ta saldo-minus" colspan="1"><b></b></td>
			<td class="fs-11 ta final-saldo" colspan="1"><b></b></td>	
		</tr>										
	</table>
	<div style="display: flex; justify-content: center;">
		<div class="fs-11" style="width: 50%;">
			<p>По данным ИП Кононович Галина Павловна</p>
			<div>
				{if $client.balance <= 0}
				<p><b>на {$smarty.get.end} Задолженность в пользу ИП Кононович Галина Павловна<br>
							<span class="final-saldo"></span> руб. (<span class="num2str"></span>)</b></p>
				<p>От {$client.short_name}</p>
				{else}
				<p><b>на {$smarty.get.end} Задолженность в пользу {$client.short_name} <br>
							<span class="saldo-minus"></span> руб. (<span class="num2str"></span>)</b></p>
				<p>От ИП Кононович Галина Павловна</p>
				{/if}
			</div>
			<p><b><i>Директор</i></b></p>
			<p>________________________<b><i>(Г.П.Кононович)</i></b></p>
			<p>М.П</p>
		</div>
		<div class="fs-11" style="width: 50%;">
			<p>По данным {$smarty.session.login}</p>
			<div>
				{if $client.balance <= 0}
				<p><b>на {$smarty.get.end} Задолженность в пользу ИП Кононович Галина Павловна<br>
							<span class="final-saldo"></span> руб. (<span class="num2str"></span>)</b></p>
				<p>От {$client.short_name}</p>
				{else}
				<p><b>на {$smarty.get.end}Задолженность в пользу {$client.short_name} <br>
							<span class="saldo-minus"></span> руб. (<span class="num2str"></span>)</b></p>
				<p>От ИП Кононович Галина Павловна</p>
				{/if}
			</div>
			<p><b><i>Директор</i></b></p>
			{if $client.chief_name}
			<p>________________________<b><i>{$client.chief_name}</i></b></p>
			{else}
			{/if}
			<pre><p>________________________<b><i>(                 )</i></b></p></pre>
			<p>М.П</p>
		</div>
	</div>
	{if $pr == '1'}
	<div class="sf-sign-img" style="top: unset; bottom: 42px; left: 59px;">
		<img src="/images/sign.png" width="100">
		<!--<img src="/images/sign-glazkov.png" width="100" style="padding-top: 20px;">-->
	</div>
	<div class="sf-sign-img" style="top: unset; left: 350px; bottom: 0;">
		<img src="/images/print.png" width="100">
	</div>
	{/if}	
</div>


<style>
	tr, td {
		font-size: 12px;
		border: 1px solid #000;
		padding-left: 5px;
	}
	.fs-11 {
		font-size: 11px !important;
	}
	.ta {
		text-align: center;
	}
</style>
{/if}
