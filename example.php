
<html>
<head>
<meta charset="utf-8">
</head>
<body>
<?php 

$mig_obj = new DrupalToWordpressMigration();
$mig_obj->set_drupal_db_host('localhost');
$mig_obj->set_drupal_db_name('drupal_migration_test');
$mig_obj->set_drupal_db_user('drupal');
$mig_obj->set_drupal_db_pass('drupal');
$mig_obj->connect_to_drupal_db();

$mig_obj->set_wordpress_db_host('localhost');
$mig_obj->set_wordpress_db_name('wp_migration_test');
$mig_obj->set_wordpress_db_user('wordpress');
$mig_obj->set_wordpress_db_pass('wordpress');
$mig_obj->set_wordpress_db_prefix('wp_');
$mig_obj->connect_to_wordpress_db();

$mig_obj->set_wordpress_upload_path('../wordpress/wp-content/uploads/');
$mig_obj->set_drupal_upload_path('../drupal/sites/default/files/');

$mig_obj->set_wordpress_wp_load_path('../wordpress/wp-load.php');


//$mig_obj->clean_tables();
$mig_obj->migrate_views();
//$mig_obj->migrate_posts();
//$mig_obj->migrate_images();


// -------------------------------------------------------------------
/*
 * This is for images copying
 * 
$path = "/var/www/drupal/sites/default/files/field/image/";

$path_scan = scandir($path);

$directories_array = array();
$files_array = array();

foreach ($path_scan as $key => $listing)
{
	if($listing === '.')
	{
		unset($path_scan[$key]);
	}
	elseif($listing === '..')
	{
		unset($path_scan[$key]);
	}
	else 
	{
		if(is_dir($path . $listing))
		{
			array_push($directories_array, $path . $listing);
		}
		else 
		{
			array_push($files_array, $path . $listing);
		}
	}
}

var_dump($path_scan);
echo '</br>_____</br>';
var_dump($directories_array);
echo '</br>_____</br>';
var_dump($files_array);
echo '</br>_____</br>';


$wordpress_upload_path = "/var/www/wordpress/wp-content/uploads/";

foreach ($files_array as $current_file)
{
	$file_extension_pos = strrpos($current_file, '.');
	$file_extension = substr($current_file, $file_extension_pos);
	
	$file_seperator_pos = strrpos($current_file, '/');
	$current_file_name = substr($current_file, $file_seperator_pos);
	$current_file_name = str_replace($file_extension, '', $current_file_name);
	
	$simple_image_obj = new SimpleImage($current_file);
	copy($current_file, $wordpress_upload_path . 'drupalized_images/' . $current_file_name);
	$simple_image_obj->resize(150, 150)->save($wordpress_upload_path . 'drupalized_images' . $current_file_name . '-150x' . $file_extension);
	$simple_image_obj->resize(300, 187)->save($wordpress_upload_path . 'drupalized_images' . $current_file_name . '-300x187' . $file_extension);
	$simple_image_obj->resize(672, 372)->save($wordpress_upload_path . 'drupalized_images' . $current_file_name . '-672x372' . $file_extension);
	$simple_image_obj->resize(1024, 640)->save($wordpress_upload_path . 'drupalized_images' . $current_file_name . '-1024x640' . $file_extension);
	$simple_image_obj->resize(1038, 576)->save($wordpress_upload_path . 'drupalized_images' . $current_file_name . '-1038x576' . $file_extension);
	
	
	$wordpress_host = new PDO("mysql:host=localhost;dbname={$this->wordpress_db_name}", $this->wordpress_db_user, $this->wordpress_db_pass);
	
	$drupal_host = new PDO("mysql:host=localhost;dbname={$this->drupal_db_name}", $this->drupal_db_user, $this->drupal_db_pass);
	
	
	//resize the copied image 6 times
	// add the data with the original name to the db
}


*/


/*
 * 
 * inistantiate class object
 * class->set drupal connect info
 * class->set wordpress connect info
 * class->set file paths
 * 
 * class->connect to drupal
 * class->connect to wordpress
 * class->migrate data
 * 
 * class->log_errors
 * 
 */
 ?>
 </body>
 </html>
