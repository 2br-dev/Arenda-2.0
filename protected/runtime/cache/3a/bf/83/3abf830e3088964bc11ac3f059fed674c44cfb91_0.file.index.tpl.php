<?php
/* Smarty version 3.1.32, created on 2019-01-17 15:21:13
  from 'C:\OpenServer\domains\arenda.local\protected\app\core\admin-template\view\shopping\orders\index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32',
  'unifunc' => 'content_5c407339c0c054_64721007',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3abf830e3088964bc11ac3f059fed674c44cfb91' => 
    array (
      0 => 'C:\\OpenServer\\domains\\arenda.local\\protected\\app\\core\\admin-template\\view\\shopping\\orders\\index.tpl',
      1 => 1515667178,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:./template.tpl' => 1,
    'file:./statistic.tpl' => 1,
    'file:system/controll.tpl' => 2,
    'file:system/pager.inc.tpl' => 1,
  ),
),false)) {
function content_5c407339c0c054_64721007 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\OpenServer\\domains\\arenda.local\\protected\\app\\libs\\smarty.plugins\\modifier.to_money.php','function'=>'smarty_modifier_to_money',),));
$_smarty_tpl->_subTemplateRender("file:./template.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
$_smarty_tpl->_subTemplateRender("file:./statistic.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?><div class="widget-table"><div class="widget-table__title"><span class="icon icon-shopping-cart"></span>&nbsp;&nbsp;Заказы магазина</div><div class="widget-table__count">Всего заказов: <strong><?php echo $_smarty_tpl->tpl_vars['count']->value;?>
</strong> шт.</div></div><div class="managing"><div class="managing__start"><div class="managing__item"><a href="<?php echo $_smarty_tpl->tpl_vars['base_path']->value;?>
/orders/add" class="button button-green"><i class="icon icon-plus-square"></i>Добавить заказ</a></div><?php if (!empty($_GET)) {?><div class="managing__item"><a href="<?php echo $_smarty_tpl->tpl_vars['base_path']->value;?>
/orders" class="button"><i class="icon icon-format-clear-all"></i>Сбросить фильтры</a></div><?php }?><div class="managing__item catalog-disable" id="remove-button"><button type="button" class="button button-red" onclick="orders.deleteAll(event)"><i class="icon icon-delete"></i>Удалить отмеченные заказы</button></div></div><div class="managing__end"><div class="managing__limit"><span class="managing__limit__label"><?php echo t('on.the.page');?>
:</span><div class="managing__limit__select"><select name="limit" onchange="shopping.setLimit('orders', this)"><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['page_count']->value, 'page');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['page']->value) {
?><option value="<?php echo $_smarty_tpl->tpl_vars['page']->value;?>
"<?php if ($_smarty_tpl->tpl_vars['page']->value == $_smarty_tpl->tpl_vars['limit']->value) {?> selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['page']->value;?>
</option><?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></select></div></div></div></div><div class="orders"><table class="module-table" id="orders-table"><colgroup><col width="30">             <col width="110">             <col width="130">             <col>             <col width="170">             <col width="150">             <col width="150">             <col width="110">                                <col width="90">             <col width="70">         </colgroup><thead><tr><th class="module-table__header module-table__center"><?php $_smarty_tpl->_subTemplateRender("file:system/controll.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"checkbox",'addclass'=>"controll_single",'name'=>"order",'onchange'=>"shopping.checkAll(this)"), 0, false);
?></th><th class="module-table__header">Номер заказа</th><th class="module-table__header">Дата добавления</th><th class="module-table__header">Ф.И.О. покупателя</th><th class="module-table__header">Номер телефона</th><th class="module-table__header">Доставка</th><th class="module-table__header">Оплата</th><th class="module-table__header">Стоимость</th>                <th class="module-table__header">Состав</th><th class="module-table__header"></th></tr></thead><tbody class="order-tbody"><?php if (isset($_smarty_tpl->tpl_vars['orders']->value)) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orders']->value, 'order');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['order']->value) {
?><tr data-id="<?php echo $_smarty_tpl->tpl_vars['order']->value['id'];?>
" id="catalog-row-<?php echo $_smarty_tpl->tpl_vars['order']->value['id'];?>
" class="module-table__row product-row"><td class="module-table__column module-table__vhcenter"><?php $_smarty_tpl->_subTemplateRender("file:system/controll.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('type'=>"checkbox",'addclass'=>"controll_single",'ctrlclass'=>"check-all-spy",'name'=>(("order[").($_smarty_tpl->tpl_vars['order']->value['id'])).("]"),'value'=>$_smarty_tpl->tpl_vars['order']->value['id'],'onchange'=>"shopping.checkItem(this)"), 0, true);
?></td><td class="module-table__column"><a href="/cp/shopping/orders/item/<?php echo $_smarty_tpl->tpl_vars['order']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['order']->value['number'];?>
</a></td><td class="module-table__column"><?php echo $_smarty_tpl->tpl_vars['order']->value['date'];?>
</td><td class="module-table__column"><?php echo $_smarty_tpl->tpl_vars['order']->value['user']['name'];?>
</td><td class="module-table__column"><?php if ($_smarty_tpl->tpl_vars['order']->value['user']['tel'] && $_smarty_tpl->tpl_vars['order']->value['user']['phone']) {?><a href="tel:<?php echo $_smarty_tpl->tpl_vars['order']->value['user']['tel'];?>
" class="module-table__link"><span class="icon icon-phone"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['order']->value['user']['phone'];?>
</a><?php }?></td><td class="module-table__column"><?php echo $_smarty_tpl->tpl_vars['order']->value['delivery'];?>
</td><td class="module-table__column"><?php echo $_smarty_tpl->tpl_vars['order']->value['payment'];?>
</td><td class="module-table__column"><?php echo smarty_modifier_to_money($_smarty_tpl->tpl_vars['order']->value['cost']);?>
 руб.</td><td class="module-table__column"><a href="/cp/shopping/orders/item/<?php echo $_smarty_tpl->tpl_vars['order']->value['id'];?>
">Просмотр</a></td><td class="module-table__column module-table__vhcenter"><a href="<?php echo $_smarty_tpl->tpl_vars['base_path']->value;?>
/orders/print/<?php echo $_smarty_tpl->tpl_vars['order']->value['id'];?>
?backuri=<?php echo $_smarty_tpl->tpl_vars['_backuri']->value;?>
" onclick="order.print(event, <?php echo $_smarty_tpl->tpl_vars['order']->value['id'];?>
)" class="catalog-print" title="Удалить" data-no-instant><i class="icon icon-print"></i></a><a href="<?php echo $_smarty_tpl->tpl_vars['base_path']->value;?>
/orders/del/<?php echo $_smarty_tpl->tpl_vars['order']->value['id'];?>
?backuri=<?php echo $_smarty_tpl->tpl_vars['_backuri']->value;?>
" onclick="order.delete(event, <?php echo $_smarty_tpl->tpl_vars['order']->value['id'];?>
)" class="catalog-remove" title="Удалить" data-no-instant><i class="icon icon-delete"></i></a></td></tr><?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}?></tbody></table></div><?php $_smarty_tpl->_subTemplateRender("file:system/pager.inc.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
