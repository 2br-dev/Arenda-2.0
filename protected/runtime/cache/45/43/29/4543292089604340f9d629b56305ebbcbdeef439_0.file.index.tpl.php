<?php
/* Smarty version 3.1.32, created on 2019-01-17 15:21:25
  from 'C:\OpenServer\domains\arenda.local\protected\app\core\admin-template\view\blocks\index\index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32',
  'unifunc' => 'content_5c407345194c33_49806180',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4543292089604340f9d629b56305ebbcbdeef439' => 
    array (
      0 => 'C:\\OpenServer\\domains\\arenda.local\\protected\\app\\core\\admin-template\\view\\blocks\\index\\index.tpl',
      1 => 1512138868,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c407345194c33_49806180 (Smarty_Internal_Template $_smarty_tpl) {
?><table class="table"><col><col width="200"><col width="150"><col width="150"><col width="120"><col width="65"><thead><tr><th colspan="6">Список зон блоков</th></tr></thead><tbody><tr><td class="h"><?php echo t('titles.name');?>
</td><td class="h">Код вывода</td><td class="h">Системное имя <span class="ness_color">*</span></td><td class="h">Шаблон страницы</td><td class="h">Блок активен</td><td class="h"></td></tr><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['list_form']->value, 'item', false, NULL, 'i', array (
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
?><tr><td><a href="<?php echo $_smarty_tpl->tpl_vars['base_path']->value;?>
/index/edit/<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
/" title="Редактировать" class="module-item-link"><i class="zmdi zmdi-edit"></i> <?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
</a></td><td><span class="inner-copy j-clipboard" data-clipboard="zone('<?php echo $_smarty_tpl->tpl_vars['item']->value['sys_name'];?>
')">php</span><span class="inner-copy j-clipboard" data-clipboard="{zone item='<?php echo $_smarty_tpl->tpl_vars['item']->value['sys_name'];?>
'}">smarty</span><span class="inner-copy j-clipboard" data-clipboard="{{ zone('<?php echo $_smarty_tpl->tpl_vars['item']->value['sys_name'];?>
') }}">twig</span></td><td><?php echo $_smarty_tpl->tpl_vars['item']->value['sys_name'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['item']->value['tid'];?>
</td><td><?php if ($_smarty_tpl->tpl_vars['item']->value['visible']) {?>Да<?php } else { ?>Нет<?php }?></td><td class="tac"><a href="<?php echo $_smarty_tpl->tpl_vars['base_path']->value;?>
/index/edit/<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
/" class="zmdi zmdi-edit" title="Редактировать"></a><a href="<?php echo $_smarty_tpl->tpl_vars['base_path']->value;?>
/index/del/<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
/" class="zmdi zmdi-delete remove-trigger" title="Удалить" onclick="return cp.dialog('Вы действительно хотите удалить зону?');" data-no-instant></a></td></tr><?php
}
} else {
?><tr><td colspan="5" class="center-middle">Зон нет</td></tr><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></tbody></table><a href="<?php echo $_smarty_tpl->tpl_vars['base_path']->value;?>
/index/add/" class="button"><i class="zmdi zmdi-plus-circle"></i>Добавить зону</a><?php }
}
