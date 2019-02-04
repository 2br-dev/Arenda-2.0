<?php
/* Smarty version 3.1.32, created on 2019-02-04 15:19:58
  from 'C:\OpenServer\domains\arenda.local\protected\themes\base\smarty\components\scripts.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32',
  'unifunc' => 'content_5c582deec91881_05962766',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c83a68266d0b47734982c2b144500b197b1bea8c' => 
    array (
      0 => 'C:\\OpenServer\\domains\\arenda.local\\protected\\themes\\base\\smarty\\components\\scripts.tpl',
      1 => 1549282756,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c582deec91881_05962766 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\OpenServer\\domains\\arenda.local\\protected\\app\\libs\\smarty.plugins\\function.compress.php','function'=>'smarty_function_compress',),));
echo smarty_function_compress(array('attr'=>'data-no-instant','mode'=>'js','source'=>array(array('file'=>'/js/vendor.min.js'),array('file'=>'/js/app.min.js'))),$_smarty_tpl);
echo '<script'; ?>
 src='/frontend/build/static/js/1.1febb811.chunk.js'><?php echo '</script'; ?>
><?php echo '<script'; ?>
 src='/frontend/build/static/js/main.e41ac467.chunk.js'><?php echo '</script'; ?>
><?php echo '<script'; ?>
 src='/frontend/build/static/js/runtime~main.229c360f.js'><?php echo '</script'; ?>
><?php echo '<script'; ?>
 src='/js/jquery.min.js'><?php echo '</script'; ?>
><?php echo '<script'; ?>
 src='/js/string_sum.js'><?php echo '</script'; ?>
></body></html><?php }
}
