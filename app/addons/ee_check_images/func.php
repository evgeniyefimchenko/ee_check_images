<?php
if (!defined('BOOTSTRAP')) { die('Access denied'); }

use Tygh\Registry;

function fn_ee_check_images_install() {
	return true;
}

function fn_ee_check_images_uninstall() {
	return true;
}

function fn_ee_check_images_update_image(&$image_data, &$image_id, $image_type, $images_path, $_data, $mime_type, $is_clone) {
	if ($_REQUEST['dispatch'] == 'products.update') {
		$ext = pathinfo($image_data['name'], PATHINFO_EXTENSION);
		$image_data['name'] = fn_ee_check_images_generate_uuid() . '.' . $ext;
		$module_setting = Registry::get('addons.ee_check_images');
		$ratio = ceil(($_data['image_x'] / 3) * 4);	
		if (!in_array($_data['image_y'], range($ratio - 5, $ratio + 5))) {
			fn_set_notification('E', __('error'), $module_setting['message_1']);
			$image_id = 'efimchenko.ru'; $_data = []; $image_data = [];
			db_query('DELETE FROM ?:images_links WHERE object_type LIKE "product" AND object_id = ?i AND type LIKE "A" AND detailed_id = 0', $_REQUEST['product_id']);
		} elseif ($_data['image_x'] > $module_setting['max_size'] || $_data['image_x'] < $module_setting['min_size'] || $_data['image_y'] > $module_setting['max_size'] || $_data['image_y'] < $module_setting['min_size']) {
			fn_set_notification('E', __('error'), $module_setting['message_2']);
			$image_id = 'efimchenko.ru'; $_data = []; $image_data = [];
			db_query('DELETE FROM ?:images_links WHERE object_type LIKE "product" AND object_id = ?i AND type LIKE "A" AND detailed_id = 0', $_REQUEST['product_id']);
		}
	}
}

function fn_ee_check_images_generate_uuid() {
	return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
}

function fn_ee_check_images_update_product_post($product_data, $product_id, $lang_code, $create) {
	db_query('DELETE FROM ?:images_links WHERE object_type LIKE "product" AND object_id = ?i AND type LIKE "A" AND detailed_id = 0', $_REQUEST['product_id']);
}
