<?php
/* Smarty version 3.1.32, created on 2019-01-23 20:46:40
  from 'E:\Torrents\OSPanel\domains\arenda\protected\app\core\admin-template\system\buttons.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32',
  'unifunc' => 'content_5c48a8808c8fb9_81153537',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '74f57f1f6147d1fa61a5f35761bd4f68df8ac6d5' => 
    array (
      0 => 'E:\\Torrents\\OSPanel\\domains\\arenda\\protected\\app\\core\\admin-template\\system\\buttons.tpl',
      1 => 1548264572,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c48a8808c8fb9_81153537 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="button-container"><button class="button is-save" name="save" type="submit" onclick="return CheckAndSubmit('form_mdd')"><i class="zmdi zmdi-save"></i><?php echo t('buttons.save.and.close');?>
</button><button class="button is-apply" name="apply" type="submit" onclick="return CheckAndSubmit('form_mdd')"><i class="zmdi zmdi-check-square"></i><?php echo t('buttons.save');?>
</button><span class="button-container__title">или</span><a class="button-link" href="<?php echo $_smarty_tpl->tpl_vars['base_path']->value;?>
/"><i class="zmdi zmdi-arrow-left"></i><?php echo t('buttons.cancel');?>
</a></div><?php }
}
