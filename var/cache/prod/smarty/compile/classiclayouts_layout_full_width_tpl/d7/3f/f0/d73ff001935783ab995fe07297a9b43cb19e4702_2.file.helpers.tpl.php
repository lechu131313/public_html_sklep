<?php
/* Smarty version 3.1.43, created on 2022-10-17 09:02:01
  from '/home/zycie/domains/gryfsklep.pl/public_html/themes/classic/templates/_partials/helpers.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.43',
  'unifunc' => 'content_634d1a09e0b343_79229241',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd73ff001935783ab995fe07297a9b43cb19e4702' => 
    array (
      0 => '/home/zycie/domains/gryfsklep.pl/public_html/themes/classic/templates/_partials/helpers.tpl',
      1 => 1658334665,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_634d1a09e0b343_79229241 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->smarty->ext->_tplFunction->registerTplFunctions($_smarty_tpl, array (
  'renderLogo' => 
  array (
    'compiled_filepath' => '/home/zycie/domains/gryfsklep.pl/public_html/var/cache/prod/smarty/compile/classiclayouts_layout_full_width_tpl/d7/3f/f0/d73ff001935783ab995fe07297a9b43cb19e4702_2.file.helpers.tpl.php',
    'uid' => 'd73ff001935783ab995fe07297a9b43cb19e4702',
    'call_name' => 'smarty_template_function_renderLogo_59609160634d1a09e04440_43015271',
  ),
));
?> 

<?php }
/* smarty_template_function_renderLogo_59609160634d1a09e04440_43015271 */
if (!function_exists('smarty_template_function_renderLogo_59609160634d1a09e04440_43015271')) {
function smarty_template_function_renderLogo_59609160634d1a09e04440_43015271(Smarty_Internal_Template $_smarty_tpl,$params) {
foreach ($params as $key => $value) {
$_smarty_tpl->tpl_vars[$key] = new Smarty_Variable($value, $_smarty_tpl->isRenderingCache);
}
?>

  <a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['urls']->value['pages']['index'], ENT_QUOTES, 'UTF-8');?>
">
    <img
      class="logo img-fluid"
      src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shop']->value['logo_details']['src'], ENT_QUOTES, 'UTF-8');?>
"
      alt="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shop']->value['name'], ENT_QUOTES, 'UTF-8');?>
"
      width="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shop']->value['logo_details']['width'], ENT_QUOTES, 'UTF-8');?>
"
      height="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shop']->value['logo_details']['height'], ENT_QUOTES, 'UTF-8');?>
">
  </a>
<?php
}}
/*/ smarty_template_function_renderLogo_59609160634d1a09e04440_43015271 */
}
