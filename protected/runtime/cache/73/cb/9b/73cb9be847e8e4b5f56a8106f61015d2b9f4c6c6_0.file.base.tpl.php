<?php
/* Smarty version 3.1.32, created on 2019-01-30 12:13:10
  from 'C:\OpenServer\domains\arenda.local\protected\themes\base\smarty\base.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32',
  'unifunc' => 'content_5c516aa6e03c56_25002257',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '73cb9be847e8e4b5f56a8106f61015d2b9f4c6c6' => 
    array (
      0 => 'C:\\OpenServer\\domains\\arenda.local\\protected\\themes\\base\\smarty\\base.tpl',
      1 => 1547727510,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:./components/meta.tpl' => 1,
    'file:./components/scripts.tpl' => 1,
  ),
),false)) {
function content_5c516aa6e03c56_25002257 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:./components/meta.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo $_smarty_tpl->tpl_vars['_page']->value['content'];?>
<div id='root'></div><?php $_smarty_tpl->_subTemplateRender("file:./components/scripts.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
