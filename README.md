# Drupal6-to-WordPress-DB-Migrator

<p>This class can migrate posts, terms, image and users from Drupal 6 DB schema into WordPress 3.x and 4.x . I created this class for a project that I was working on</p>

<h3>The following is an example for using the class:</h3>

<code>



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


$mig_obj->clean_tables();
$mig_obj->migrate_posts();
$mig_obj->migrate_images();

</code>
