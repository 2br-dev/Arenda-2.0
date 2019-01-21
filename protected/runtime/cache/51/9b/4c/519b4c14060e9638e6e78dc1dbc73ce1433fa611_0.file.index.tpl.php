<?php
/* Smarty version 3.1.32, created on 2019-01-21 12:36:43
  from 'C:\OpenServer\domains\arenda.local\protected\app\core\admin-template\view\modules\binds\index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32',
  'unifunc' => 'content_5c4592abaa09a1_28467130',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '519b4c14060e9638e6e78dc1dbc73ce1433fa611' => 
    array (
      0 => 'C:\\OpenServer\\domains\\arenda.local\\protected\\app\\core\\admin-template\\view\\modules\\binds\\index.tpl',
      1 => 1512138868,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c4592abaa09a1_28467130 (Smarty_Internal_Template $_smarty_tpl) {
?><table class="table" id="meta_data"><col><col><col width="110"><col width="60"><thead><tr class="th"><th colspan="4">Список функций</th></tr></thead><tbody><tr><td class="h">Название функции</td><td class="h">Системное имя</td><td class="h"><?php echo t('caching');?>
</td><td class="h"></td></tr><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['binds']->value, 'bind');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['bind']->value) {
?><tr class="row-<?php echo $_smarty_tpl->tpl_vars['bind']->value['id'];?>
"><td><a href="<?php echo $_smarty_tpl->tpl_vars['base_path']->value;?>
/binds/edit/<?php echo $_smarty_tpl->tpl_vars['bind']->value['id'];?>
/" title="Редактировать" class="module-item-link"><i class="zmdi zmdi-edit"></i> <?php echo $_smarty_tpl->tpl_vars['bind']->value['name'];?>
</a></td><td><?php echo $_smarty_tpl->tpl_vars['bind']->value['func_name'];?>
</td><td class="va_m"><span class="label <?php if (isset($_smarty_tpl->tpl_vars['bind']->value['cache']) && $_smarty_tpl->tpl_vars['bind']->value['cache'] == 1) {?>green<?php } else { ?>amber<?php }?>"><?php if (isset($_smarty_tpl->tpl_vars['bind']->value['cache']) && $_smarty_tpl->tpl_vars['bind']->value['cache'] == 1) {?>Включено<?php } else { ?>Выключено<?php }?></span></td><td class="va_m tac"><a href="<?php echo $_smarty_tpl->tpl_vars['base_path']->value;?>
/binds/edit/<?php echo $_smarty_tpl->tpl_vars['bind']->value['id'];?>
" class="zmdi zmdi-edit" title="Редактировать"></a><a href="<?php echo $_smarty_tpl->tpl_vars['base_path']->value;?>
/binds/del/<?php echo $_smarty_tpl->tpl_vars['bind']->value['id'];?>
" rel=".row-<?php echo $_smarty_tpl->tpl_vars['bind']->value['id'];?>
" class="zmdi zmdi-delete remove-trigger" title="Удалить" onclick="return cp.dialog('Вы действительно хотите удалить модуль и все что связанно с ним?');" data-no-instant></a></td></tr><?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></tbody></table><a href="<?php echo $_smarty_tpl->tpl_vars['base_path']->value;?>
/binds/add" class="button button-purple"><i class="zmdi zmdi-plus-circle"></i>Добавить функцию</a><?php }
}
