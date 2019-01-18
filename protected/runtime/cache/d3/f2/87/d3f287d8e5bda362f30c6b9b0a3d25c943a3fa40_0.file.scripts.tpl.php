<?php
/* Smarty version 3.1.32, created on 2018-12-13 16:58:24
  from 'C:\OpenServer\domains\test.local\protected\themes\base\smarty\components\scripts.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32',
  'unifunc' => 'content_5c126580510b77_37699440',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd3f287d8e5bda362f30c6b9b0a3d25c943a3fa40' => 
    array (
      0 => 'C:\\OpenServer\\domains\\test.local\\protected\\themes\\base\\smarty\\components\\scripts.tpl',
      1 => 1544706075,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c126580510b77_37699440 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\OpenServer\\domains\\test.local\\protected\\app\\libs\\smarty.plugins\\function.compress.php','function'=>'smarty_function_compress',),));
echo smarty_function_compress(array('attr'=>'data-no-instant','mode'=>'js','source'=>array(array('file'=>'/js/vendor.min.js'),array('file'=>'/js/app.min.js'))),$_smarty_tpl);
echo '<script'; ?>
 type="text/javascript" src="/js/jquery.min.js"><?php echo '</script'; ?>
><?php echo '<script'; ?>
 type="text/javascript" src="/js/main.js"><?php echo '</script'; ?>
></body></html><?php }
}
