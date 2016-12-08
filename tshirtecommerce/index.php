<?php
/**
 * @author tshirtecommerce - www.tshirtecommerce.com
 * @date: 2015-01-10
 * 
 * @copyright  Copyright (C) 2015 tshirtecommerce.com. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE
 *
 */
error_reporting(0);
session_start();
date_default_timezone_set('America/Los_Angeles');
define('ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);

include_once ROOT .DS. 'includes' .DS. 'functions.php';
include_once ROOT .DS. 'includes' .DS. 'addons.php';

// call language
$dg = new dg();
$lang = $dg->lang();

// call products
$products	= $dg->getProducts();
$product	= $products[0];

if (isset($_GET['product']))
{
	$product_id = $_GET['product'];
}

if (isset($_GET['cart_id']))
{
	$cache 	= $dg->cache('cart');
	$design = $cache->get($_GET['cart_id']);
	if ($design != null && isset($design['item']) && isset($design['item']['product_id']))
	{
		$product_id = $design['item']['product_id'];
	}
}

for($i=0; $i< count($products); $i++)
{
	if ($product_id == $products[$i]->id)
		$product = $products[$i];
}

// get attribute
if (isset($product->attributes->name))
{
	$product->attribute = $dg->getAttributes($product->attributes);
}
else
{
	$product->attribute = '';
}
$product->attribute .= $dg->quantity($product->min_order, lang('quantity', true), lang('min_quantity', true));

// get getSetting
$settings			= $dg->getSetting();
$settings->site_url = $dg->url();

$dg->settings		= $settings;

// fix link with www
if(preg_match('/www/', $_SERVER['HTTP_HOST']))
{
	$temp = explode('//www.', $settings->site_url);
	if (count($temp) == 1)
	{
		$settings->site_url = str_replace('http://', 'http://www.', $settings->site_url);
	}
}
else
{
	$settings->site_url = str_replace('//www.', '//', $settings->site_url);
}

// check session
if (isset($_SESSION['is_logged']) && $_SESSION['is_logged'] !== false)
{
	$is_logged = $_SESSION['is_logged'];	
	$user = md5($is_logged['id']);
}
else
{
	$user = 0;
}

// load add-on
$addons = new addons();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title><?php echo setValue($settings, 'site_name', 'T-Shirt eCommerce'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1, minimum-scale=0.5, maximum-scale=1.0"/>
	<meta content="<?php echo setValue($settings, 'meta_description', 'T-Shirt eCommerce'); ?>" name="description" />
	<meta content="<?php echo setValue($settings, 'meta_keywords', 'T-Shirt eCommerce'); ?>" name="keywords" />
	
	<link type="text/css" href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="all"/>
	<link type="text/css" href="assets/plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet" media="all" />
	<link type="text/css" href="assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" media="all" />
	<link type="text/css" href="assets/css/style.css" rel="stylesheet" media="all">
	<link type="text/css" href="assets/plugins/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">	
	
	<?php echo $addons->css(); ?>
	
	<script type="text/javascript" src="assets/js/jquery.min.js"></script>
	<script type="text/javascript" src="assets/plugins/jquery-ui/jquery-ui.min.js"></script>
	<script type="text/javascript" src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
	
	<?php echo $addons->view('lang-js'); ?>
	
	<script src="<?php echo 'assets/js/add-ons.js'; ?>"></script>
	<script src="<?php echo 'assets/js/jquery.ui.rotatable.js'; ?>"></script>	
	<script src="<?php echo 'assets/js/design.js'; ?>" type="text/javascript" charset="utf-8"></script>	
	<script src="<?php echo 'assets/js/main.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo 'assets/js/rgbcolor.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo 'assets/js/canvg.js'; ?>"></script>
	<script type="text/javascript" src="assets/plugins/perfect-scrollbar/perfect-scrollbar.js"></script>	
	<script type="text/javascript" src="assets/plugins/perfect-scrollbar/jquery.mousewheel.js"></script>
	
	<script type="text/javascript">
		<?php
		if ( isset($_GET['lang']) )
		{
			$lang_active = $_GET['lang'];
		}
		elseif(isset($_COOKIE['lang']))
		{
			$lang_active = $_COOKIE['lang'];
		}
		else
		{
			$lang_active = '';
		}
		?>
		var lang_active = '<?php echo $lang_active; ?>';
		var baseURL = '';
		var mainURL = '<?php echo $settings->site_url; ?>';
		var siteURL = '<?php echo str_replace('//tshirtecommerce', '/tshirtecommerce', $settings->site_url.'tshirtecommerce/'); ?>';
		var urlCase = '<?php echo str_replace('//tshirtecommerce', '/tshirtecommerce', $settings->site_url.'tshirtecommerce/'); ?>image-tool/thumbs.php';
		var user_id = '<?php echo $user; ?>';
		var currency_symbol = '<?php echo setValue($settings, 'currency_symbol', '$'); ?>';
		var parent_id = '<?php if (isset($_GET['parent'])) echo $_GET['parent']; else echo 0; ?>';
		var domain = '<?php echo $_SERVER['HTTP_HOST']; ?>';
		
		var urlDesign = '';
		if (typeof window.parent.urlDesign == 'undefined' || window.parent.urlDesign == '')
		{
			jQuery('#designer-alert').html(lang.text.setup).css('display', 'block');
		}
		else
		{
			urlDesign = window.parent.urlDesign;
		}		
	</script>
	
	<?php echo $dg->theme('header'); ?>
</head>
<body>
	<div class="container-fluid">
		<div id="dg-wapper" class="col-md-12">
			<div class="alert alert-danger" id="designer-alert" role="alert" style="display:none;"></div>
			<div id="dg-mask" class="loading"></div>
			
			<!-- BEGIN main of layout -->
			<div id="dg-designer">
				
				<!-- BEGIN left -->
				<?php $dg->view('tool_left'); ?>
				<!-- END left -->
				
				<!-- BEGIN left -->
				<?php $dg->view('too_center'); ?>
				<!-- END left -->
				
				<!-- BEGIN left -->
				<?php $dg->view('too_right'); ?>
				<!-- END left -->
				
			</div>
			<!-- END main of layout -->
			
		</div>
	</div>
	
	<!-- BEGIN confirm color of print -->
	<?php $dg->view('screen_colors'); ?>
	<!-- END confirm color of print -->
	
	<div id="dg-introduction">
		<?php $dg->view('modal_tshirt_introduction'); ?>
	</div>
	
	<!-- BEGIN modal -->
	<div id="dg-modal">
		
		<!-- BEGIN product info -->
		<?php $dg->view('modal_product_info'); ?>
		<!-- END product info -->
		
		<!-- BEGIN product size -->
		<?php $dg->view('modal_product_size'); ?>
		<!-- END product size -->
		
		<!-- BEGIN Login -->
		<?php $dg->view('modal_login'); ?>
		<!-- END Login -->
		
		<!-- BEGIN products -->
		<?php $dg->view('modal_products'); ?>
		<!-- END products -->
		
		<!-- BEGIN clipart -->
		<?php $dg->view('modal_clipart'); ?>
		<!-- END clipart -->
		
		<!-- BEGIN Upload -->
		<?php $dg->view('modal_upload'); ?>
		<!-- END Upload -->
		
		<!-- BEGIN Note -->
		<?php $dg->view('modal_note'); ?>
		<!-- END Note -->
		
		<!-- BEGIN Help -->
		<?php $dg->view('modal_help'); ?>
		<!-- END Help -->
		
		<!-- BEGIN My design -->
		<?php $dg->view('modal_my_design'); ?>
		<!-- END My design -->
		
		<!-- BEGIN design ideas -->
		<?php $dg->view('modal_ideas'); ?>
		<!-- END design ideas -->
		
		<!-- BEGIN team -->
		<?php $dg->view('modal_team'); ?>
		<!-- END team -->
		
		<!-- BEGIN fonts -->
		<?php $dg->view('modal_fonts'); ?>
		<!-- END fonts -->
		
		<!-- BEGIN preview -->
		<?php $dg->view('modal_preview'); ?>
		<!-- END preview -->
		
		<!-- BEGIN Share -->
		<?php $dg->view('modal_share'); ?>
		<!-- END Share -->
		
		<?php $addons->view('modal'); ?>
	</div>
	<!-- END modal -->
	
	<!-- BEGIN popover -->
	<div class="popover right" id="dg-popover">
		<div class="arrow"></div>
		<h3 class="popover-title">
			<span><?php echo $lang['designer_clipart_edit_size_position']; ?></span> 
			<a href="javascript:void(0)" class="popover-close">
				<i class="glyphicons remove_2 glyphicons-12 pull-right"></i>
			</a>
		</h3>
		
		<div class="popover-content">
		
			<!-- BEGIN clipart edit options -->
			<?php $dg->view('popover_clipart'); ?>
			<!-- END clipart edit options -->
			
			<!-- BEGIN Text edit options -->
			<?php $dg->view('popover_text'); ?>
			<!-- END Text edit options -->
			
			<!-- BEGIN team edit options -->
			<?php $dg->view('popover_team'); ?>
			<!-- END team edit options -->
			
			<!-- BEGIN qrcode -->
			<?php $dg->view('popover_qrcode'); ?>
			<!-- END qrcode -->
			
			<?php $addons->view('popover'); ?>
		</div>
	</div>
	<!-- END popover -->
	
	<!-- BEGIN colors system -->
	<div class="o-colors" style="display:none;">		
		<div class="other-colors"></div>
	</div>
	<!-- END colors system -->
	
	<div id="cacheText"></div>
	
	<div id="id_login"></div>
	<div id="save-confirm" title="<?php echo lang('designer_user_login_now_or_sign_up'); ?>" style="display:none;">
		<p><?php echo lang('designer_saved_design'); ?></p>
	</div>
	
	<?php if (isset($product->design)) {?>
	<script type="text/javascript">
		<?php 
		$min_order 			= setValue($product, 'min_order', 1);
		$max_order 			= setValue($product, 'max_oder', 9999);
		$site_upload_max 	= setValue($settings, 'site_upload_max', 10);
		$site_upload_min 	= setValue($settings, 'site_upload_min', 0.05);
		
		$min_order	= (int) $min_order;
		$max_order	= (int) $max_order;
		if ($min_order < 1)
			$min_order = 1;
		
		if ($max_order < $min_order)
			$max_order = 9999;
		
		if ($site_upload_min < 0)
			$site_upload_min = 0.05;
		
		if ($site_upload_max < 0)
			$site_upload_max = 10;
		
		?>
		var min_order = <?php echo $min_order; ?>;
		var max_order = <?php echo $max_order; ?>;
		var product_id = '<?php echo $product->id; ?>';
		var print_type = '<?php echo setValue($product, 'print_type', 'screen'); ?>';
		var uploadSize = [];
		uploadSize['max']  = <?php echo $site_upload_max; ?>;
		uploadSize['min']  = <?php echo $site_upload_min; ?>;
		var items = {};
		items['design'] = {};
		<?php 
		$js = '';
		$elment = count($product->design->color_hex);
		for($i=0; $i<$elment; $i++)
		{			
			$js .= "items['design'][$i] = {};";
			$js .= "items['design'][$i]['color'] = \"".$product->design->color_hex[$i]."\";";
			$js .= "items['design'][$i]['title'] = \"".$product->design->color_title[$i]."\";";
			$postions	= array('front', 'back', 'left', 'right');
			foreach ($postions as $v)
			{
				$view = $product->design->$v;				
				if (count($view) > 0) 
				{
					if (isset($view[$i]) == true)
					{
						$item = (string) $view[$i];						
						$js .= "items['design'][".$i."]['".$v."']=\"".$item."\";";						
					}
					else
					{
						$js .= "items['design'][$i]['$v'] = '';";
					}
				}
				else
				{
					$js .= "items['design'][$i]['$v'] = '';";
				}				
			}
		}
		echo $js;
		?>
		items['area']	= {};
		items['area']['front']	= "<?php echo $product->design->area->front; ?>";
		items['area']['back']	= "<?php echo $product->design->area->back; ?>";
		items['area']['left']	= "<?php echo $product->design->area->left; ?>";
		items['area']['right']	= "<?php echo $product->design->area->right; ?>";		
		items['params']	= [];		
		items['params']['front']	= "<?php echo $product->design->params->front; ?>";		
		items['params']['back']	= "<?php echo $product->design->params->back; ?>";		
		items['params']['left']	= "<?php echo $product->design->params->left; ?>";		
		items['params']['right']	= "<?php echo $product->design->params->right; ?>";		
	</script>
	<?php } ?>
	<script type="text/javascript" src="assets/js/design_upload.js"></script>
	<?php echo $addons->js(); ?>	
<?php 
// load design
$color = '-1';
$design_id = '';
$designer_id = '';
if (isset($_GET['color']))
{
	$color = $_GET['color'];
}

if (isset($_GET['user']))
{
	$designer_id = $_GET['user'];
}

if (isset($_GET['id']))
{
	$design_id = $_GET['id'];
}
?>
<?php
/*xulin edit start*/

	$target_json_location = $_SERVER['DOCUMENT_ROOT']."/tshirtecommerce/assets/custom_img/".$_GET['parent']."/".$_GET['parent'].".json";
/*xulin edit end*/
?>
	<script type="text/javascript">
		/*xulin edit start*/
		var global_design_status = [];
		var jsonOjb;
//		var ressource_root = "<?php //echo $_SERVER['DOCUMENT_ROOT']."/tshirtecommerce/assets/custom_img/".$_GET['parent']."/"; ?>//";
		var base_url = "<?php echo "http://".$_SERVER['SERVER_NAME']."/tshirtecommerce/assets/custom_img/".$_GET['parent']."/"; ?>";
	jQuery(document).ready(function(){
		// remove unnesscessary html block
		jQuery('#dg-sidebar, #dg-designer > div.col-left, #dg-help-functions, #product-thumbs, #view-front > div.design-area, #product-attributes > div.form-group.product-fields.product-quantity, #product-attributes > div:nth-child(2)').hide();
		jQuery('#ui-accordion-2-header-1, #ui-accordion-2-header-2').hide();

		// load json config
		var configJson = '<?php echo str_replace(array("\r", "\n"), '', file_get_contents($target_json_location));?>';
		jsonOjb = jQuery.parseJSON(configJson);

		//set standard size for main img
		jQuery("img[id*='front-img-images-']").attr("style", "width: "+getParamValue('width')+
			"; height: "+getParamValue('height')+
			"; top: "+getParamValue('top')+
			"; left: "+getParamValue('left')+
			"; z-index: 200;");

		//load button frame for the first color variant and assign Part of img to the main img
//		console.log(jsonOjb[0]);
		load_button_frame(jsonOjb[0], true);

		/*xulin edit end*/
		jQuery('[data-toggle="tooltip"]').tooltip();
		<?php if( $color  != '-1' ){ ?>
		design.imports.productColor('<?php echo $color; ?>');
		<?php } ?>
		
		<?php if( $design_id  != '' ){ ?>
		design.imports.loadDesign('<?php echo $design_id; ?>', '<?php echo $designer_id; ?>');
		<?php } ?>
		
		// load design cart
		<?php if (isset($_GET['cart_id'])) { ?>
		design.imports.cart('<?php echo $_GET['cart_id']; ?>');
		<?php } ?>
		window.parent.setHeigh(jQuery('#dg-wapper').height());
	});
	</script>

	<script type="text/javascript">
		/*xulin edit start*/

		//get Params from iframe url
		function getParamValue(paramName) {
			var url = window.location.search.substring(1); //get rid of "?" in querystring
			var qArray = url.split('&');
			for (var i = 0; i < qArray.length; i++)
			{
				var pArr = qArray[i].split('=');
				if (pArr[0] == paramName)
					return pArr[1];
			}
		}

		jQuery("#product-list-colors span").click(function(){
			//set Default Size info
			jQuery("img[id*='front-img-images-']").attr("style", "width: "+getParamValue('width')+
				"; height: "+getParamValue('height')+
				"; top: "+getParamValue('top')+
				"; left: "+getParamValue('left')+
				"; z-index: 200;");
			//callback for event click on Color option
			var target_color = jQuery(this).attr('data-original-title');
			if(global_design_status.color != target_color){
				jQuery("img[id*='"+ global_design_status.color_id +"_'], div.custom_var_field").remove();
				var target_ojb = jsonOjb.find(array_search_condition);
				load_button_frame(target_ojb);
			}
			function array_search_condition(element) {return element.color[1] == target_color;}
			// don't forget to change color and color_id in global design status json!!!
			console.log(global_design_status);
		});

		//load imgs for design color change
		function ajax_load_design_imgs(img_path, color_id, position, img_flag) {
			var position_detail = position.split(' ');
			var width = position_detail[0];
			var height = position_detail[1];
			var top = position_detail[2];
			var left = position_detail[3];
			if(img_path != 'undefined'){
				var full_img_path = base_url + color_id + '/' + img_path;
				var img_id = color_id+"_"+img_flag;
				var img_html = "<img id="+ img_id +" src="+ full_img_path +" width="+ width +" height="+ height +
					" style='z-index: 999; position: absolute; left: "+ left +";top:"+ top +"' />";
				jQuery('#view-front').append(img_html);
				return true;
			}
		}
		// load imgs when click on a variant option
		function load_change_with_var(btn_element) {
			var item_key_in_global_json = jQuery(btn_element).parent().attr('id');
			var var_type_in_global_json = global_design_status[item_key_in_global_json];
			var target_var_type = jQuery(btn_element).attr('var_typ');
			if(var_type_in_global_json != target_var_type){
				var selector = "#"+global_design_status.color_id+"_"+item_key_in_global_json;
				jQuery(selector).remove();
				ajax_load_design_imgs(jQuery(btn_element).attr('img_path'), global_design_status.color_id,
					jQuery(btn_element).attr('position'), item_key_in_global_json);
				global_design_status[item_key_in_global_json] = target_var_type;
			}
			console.log(global_design_status);
		}
		//load different button frame according to color variant
		function load_button_frame(obj,__init__ = false) {
			//wait to push into var global_design_status
			var status_item = {};
			var btn_frame_html = '';
			var label_for_btns = '';
			jQuery.each(obj, function(key_level_0, value){
				// read color sku and push into gloabl status json
				if(key_level_0 == 'color'){
					global_design_status['color_id'] = value[0];
					global_design_status['color'] = value[1];
					return;
				}
				var btn_html='';
				// read all variant from each Parition z.b for Aermel Now!
				jQuery.each(value,function (key,value) {

					if(value.hasOwnProperty('label')){
						label_for_btns = value.label;
						return;
					}
					// z.B. for Variant!!!
					var var_typ = value.variant[0].typ;
					var var_sku = value.variant[1].sku;
					var var_label = value.variant[2].label;
					var var_position = value.variant[3].position;
					var var_img_path = value.variant[4].img_path;
					var var_price = value.variant[5].price;
					var var_status =value.variant[6].active;

					//set Dafault to gloabl status json
					if(var_typ == 'default' && __init__ == true){
						global_design_status[key_level_0] = var_typ;
						ajax_load_design_imgs(var_img_path, global_design_status['color_id'], var_position, key_level_0);
					}else if(global_design_status[key_level_0] == var_typ){
						if(var_status == 'false'){
						global_design_status[key_level_0] = 'default';
						return;
						}
						ajax_load_design_imgs(var_img_path, global_design_status['color_id'], var_position, key_level_0);
					}
					btn_html = btn_html + "<button class='option_button' position='"+var_position
						+"' img_path='"+var_img_path
						+"' price='"+var_price
						+"' var_typ='"+var_typ
						+"' var_sku='"+var_sku
						+"' onclick='load_change_with_var(this)'>"+var_label+"</button>";
				});
				btn_frame_html = btn_frame_html + "<div class='custom_var_field'>" +
					"<label for='fields'>"+ label_for_btns +"</label><div id='"+ key_level_0 +"'>"+ btn_html +"</div></div>";
			});
			jQuery("#product-details").append(btn_frame_html);
			return true;
		}
		/*xulin edit end*/
	</script>
</body>
</html>

