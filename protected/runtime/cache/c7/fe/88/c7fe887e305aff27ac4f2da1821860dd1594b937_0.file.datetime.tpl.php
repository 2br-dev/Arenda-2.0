<?php
/* Smarty version 3.1.32, created on 2019-01-23 20:46:40
  from 'E:\Torrents\OSPanel\domains\arenda\protected\app\core\admin-template\fields\datetime.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32',
  'unifunc' => 'content_5c48a8807e6675_11898158',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c7fe887e305aff27ac4f2da1821860dd1594b937' => 
    array (
      0 => 'E:\\Torrents\\OSPanel\\domains\\arenda\\protected\\app\\core\\admin-template\\fields\\datetime.tpl',
      1 => 1548264571,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c48a8807e6675_11898158 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="calendar calendar_datetime"><input name="<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['value']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="calendar__field js-datetimepicker" data-date-format="dd.mm.yyyy hh:ii" tabindex="<?php echo $_smarty_tpl->tpl_vars['index']->value;?>
" readonly><span class="calendar__icon add-on"><i class="zmdi zmdi-calendar"></i></span></div>
<?php }
}
