<?php
/* Smarty version 3.1.32, created on 2019-01-23 21:08:39
  from 'E:\Torrents\OSPanel\domains\arenda\protected\themes\base\smarty\components\scripts.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32',
  'unifunc' => 'content_5c48ada7e35827_91119974',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '101508eaa4f5c3ee450c8354b2bdbb97488a7757' => 
    array (
      0 => 'E:\\Torrents\\OSPanel\\domains\\arenda\\protected\\themes\\base\\smarty\\components\\scripts.tpl',
      1 => 1548264597,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c48ada7e35827_91119974 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'E:\\Torrents\\OSPanel\\domains\\arenda\\protected\\app\\libs\\smarty.plugins\\function.compress.php','function'=>'smarty_function_compress',),));
echo smarty_function_compress(array('attr'=>'data-no-instant','mode'=>'js','source'=>array(array('file'=>'/js/vendor.min.js'),array('file'=>'/js/app.min.js'))),$_smarty_tpl);
echo '<script'; ?>
 src='/frontend/build/static/js/1.94630282.chunk.js'><?php echo '</script'; ?>
><?php echo '<script'; ?>
 src='/frontend/build/static/js/main.c95841d9.chunk.js'><?php echo '</script'; ?>
><?php echo '<script'; ?>
 src='/frontend/build/static/js/runtime~main.229c360f.js'><?php echo '</script'; ?>
><?php echo '<script'; ?>
 src='/js/jquery.min.js'><?php echo '</script'; ?>
><?php echo '<script'; ?>
 src='/js/string_sum.js'><?php echo '</script'; ?>
></body></html><?php }
}
