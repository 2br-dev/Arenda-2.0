<?php
/* Smarty version 3.1.32, created on 2019-01-23 21:08:39
  from 'E:\Torrents\OSPanel\domains\arenda\protected\themes\base\smarty\base.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32',
  'unifunc' => 'content_5c48ada7b5ee71_00806565',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '001be9cc4c6fbd1b623d6c66191ec9ce25c5ab26' => 
    array (
      0 => 'E:\\Torrents\\OSPanel\\domains\\arenda\\protected\\themes\\base\\smarty\\base.tpl',
      1 => 1548264597,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:./components/meta.tpl' => 1,
    'file:./components/scripts.tpl' => 1,
  ),
),false)) {
function content_5c48ada7b5ee71_00806565 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:./components/meta.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo $_smarty_tpl->tpl_vars['_page']->value['content'];?>
<div id='root'></div><?php $_smarty_tpl->_subTemplateRender("file:./components/scripts.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
