<?php

/**
 * 
 * Class Name: DrupalToWordpressMigration
 * Description: This class contains methods to convert a drupal 6 database schema into a WordPress database schema.
 * @version 1.0.0
 * @author Amr Abdou <amrabdou@amrabdou.com>
 * @link http://amrabdou.com/ Author Website
 * @copyright 2015-2016 AmrAbdou.Com
 */

/*
 * Import the SimpleImage Class
 * It's used to resize the Images from the Drupal upload path
 * to the different WordPress sizes and copy them to the WordPress Upload path.
 */
use abeautifulsite\SimpleImage;
include_once('SimpleImage.php');


class DrupalToWordpressMigration
{
	
	// The pathes should end by slash "../path/to/file/"
	// Remote access like http or ftp is not supported yet. The path should be on the same server server.
	private $drupal_upload_path = '';
	private $wordpress_upload_path = '';
	
	/**
	 * Drupal DB info Variables
     * @access private
     * @var string 
     */
	private $drupal_db_host = '';
	private $drupal_db_name = '';
	private $drupal_db_user = '';
	private $drupal_db_pass = '';
	private $drupal_db_opts = '';
	
	//Drupal DB prefix
	private $drupal_db_prefix;
	
	// Drupal Connection Object
	private $drupal_db_conn = '';
	
	/**
	 * Wordpress DB info Variables
     * @access private
     * @var string 
     */
	private $wordpress_db_host = '';
	private $wordpress_db_name = '';
	private $wordpress_db_user = '';
	private $wordpress_db_pass = '';
	private $wordpress_db_opts = '';
	
	//Wordpress DB prefix
	private $wordpress_db_prefix;
	
	// Wordpress Connection Object
	private $wordpress_db_conn = '';
	
	// Wordpress wp-load.php path
	// Necessary to use some of wordpress's functions (wp_generate_meta_data) 
	private $wordpress_wp_load_path = '';
	
	/*
	 * DrupalToWordpressMigration Constructor
	 */
	public function __construct()
	{
		
	}
	
	/*
	 * 
	 * Properties' Getters
	 * 
	 */
	public function get_drupal_upload_path()
	{
		return $this->drupal_upload_path;
	}
	
	public function get_wordpress_upload_path()
	{
		return $this->wordpress_upload_path;
	}
	
	public function get_drupal_db_host()
	{
		return $this->drupal_db_host;
	}
	
	public function get_drupal_db_name()
	{
		return $this->drupal_db_name;
	}

	public function get_drupal_db_user()
	{
		return $this->drupal_db_user;
	}

	public function get_drupal_db_pass()
	{
		return $this->drupal_db_pass;
	}

	public function get_drupal_db_opts()
	{
		return $this->drupal_db_opts;
	}

	public function get_drupal_db_conn()
	{
		return $this->drupal_db_conn;
	}
	
	public function get_drupal_db_prefix()
	{
		return $this->drupal_db_prefix;
	}
	
	public function get_wordpress_db_host()
	{
		return $this->wordpress_db_host;
	}
	
	public function get_wordpress_db_name()
	{
		return $this->wordpress_db_name;
	}
	
	public function get_wordpress_db_user()
	{
		return $this->wordpress_db_user;
	}

	public function get_wordpress_db_pass()
	{
		return $this->wordpress_db_pass;
	}

	public function get_wordpress_db_opts()
	{
		return $this->wordpress_db_opts;
	}

	public function get_wordpress_db_conn()
	{
		return $this->wordpress_db_conn;
	}
	
	public function get_wordpress_db_prefix()
	{
		return $this->wordpress_db_prefix;
	}
	
	public function get_wordpress_wp_load_path()
	{
		return $this->wordpress_wp_load_path;
	}
	
	
	
	/*
	 * 
	 * Properties' Setters
	 * 
	 */
	 
	public function set_drupal_upload_path($path)
	{
		$this->drupal_upload_path = $path;
	}
	
	public function set_wordpress_upload_path($path)
	{
		$this->wordpress_upload_path = $path;
	}
	
	public function set_drupal_db_host($host)
	{
		$this->drupal_db_host = $host;
	}
	
	public function set_drupal_db_name($name)
	{
		$this->drupal_db_name = $name;
	}
	
	public function set_drupal_db_user($user)
	{
		$this->drupal_db_user = $user;
	}
	
	public function set_drupal_db_pass($pass)
	{
		$this->drupal_db_pass = $pass;
	}
	
	public function set_drupal_db_opts($opts)
	{
		$this->drupal_db_opts = $opts;
	}

	public function set_drupal_db_prefix($prefix)
	{
		$this->drupal_db_prefix = $prefix;
	}
	
	public function set_wordpress_db_host($host)
	{
		$this->wordpress_db_host = $host;
	}
	
	public function set_wordpress_db_name($name)
	{
		$this->wordpress_db_name = $name;
	}
	
	public function set_wordpress_db_user($user)
	{
		$this->wordpress_db_user = $user;
	}
	
	public function set_wordpress_db_pass($pass)
	{
		$this->wordpress_db_pass = $pass;
	}
	
	public function set_wordpress_db_opts($opts)
	{
		$this->wordpress_db_opts = $opts;
	}
	
	public function set_wordpress_db_prefix($prefix)
	{
		$this->wordpress_db_prefix = $prefix;
	}
	
	public function set_wordpress_wp_load_path($path)
	{
		$this->wordpress_wp_load_path = $path;
	}
	
	
	/*
	 * Connect to WordPress DB
	 */
	public function connect_to_wordpress_db()
	{
		$db_conn = new PDO("mysql:host={$this->wordpress_db_host};dbname={$this->wordpress_db_name}", $this->wordpress_db_user, $this->wordpress_db_pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
		$this->wordpress_db_conn = $db_conn;
	}
	
	/*
	 * Connect to Drupal DB
	 */
	public function connect_to_drupal_db()
	{
		// first get data from drupal
		$db_conn = new PDO("mysql:host={$this->drupal_db_host};dbname={$this->drupal_db_name}", $this->drupal_db_user, $this->drupal_db_pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
		$this->drupal_db_conn = $db_conn;
	}
	
	/*
	 * Truncate all Wordpress DB tables
	 */
	public function clean_tables()
	{
		$truncate_query = "
							TRUNCATE TABLE {$this->wordpress_db_prefix}term_relationships;
							TRUNCATE TABLE {$this->wordpress_db_prefix}term_taxonomy;
							TRUNCATE TABLE {$this->wordpress_db_prefix}terms;
						";
		/*
		 * $truncate_query = "TRUNCATE TABLE {$this->wordpress_db_prefix}comments;
							TRUNCATE TABLE {$this->wordpress_db_prefix}links;
							TRUNCATE TABLE {$this->wordpress_db_prefix}postmeta;
							TRUNCATE TABLE {$this->wordpress_db_prefix}posts;
							TRUNCATE TABLE {$this->wordpress_db_prefix}term_relationships;
							TRUNCATE TABLE {$this->wordpress_db_prefix}term_taxonomy;
							TRUNCATE TABLE {$this->wordpress_db_prefix}terms;
							DELETE FROM {$this->wordpress_db_prefix}users
							WHERE id != 1;
							DELETE FROM {$this->wordpress_db_prefix}usermeta
							WHERE user_id != 1;";
		 */
		
		$clean_exec = $this->wordpress_db_conn->exec($truncate_query);
		
		echo 'Delete Query: ' . $truncate_query . '</br>';
		echo 'Number of deleted rows= ' . $clean_exec . '</br>';
		
	}
	
	/*
	 * Migrate Posts
	 */
	public function migrate_posts()
	{
		try
		{
				// Transfer posts
				// note: There might be drupal content_types that
				// should be converted to WP custom_post_types
				
				// Get data from drupal (nodes) table
				$query = "SELECT node.nid AS nid, node.uid AS uid, node.created AS created, node.title AS title, node.status AS status , node.type AS type , node.changed AS changed , field_data_body.body_value AS body_value, field_data_body.body_summary AS body_summary, SUBSTRING_INDEX( SUBSTRING( url_alias.alias FROM 6 ) , '/', -1 ) AS alias
							FROM node
							LEFT JOIN field_data_body ON node.nid = field_data_body.entity_id
							LEFT JOIN url_alias ON url_alias.source LIKE 'node/%' AND node.nid = SUBSTRING( url_alias.source FROM 6 )";
				$migrate_query = $this->drupal_db_conn->query($query);
				$drupal_nodes_data = $migrate_query->fetchall(PDO::FETCH_ASSOC);
				
				
				// Add the nodes data to the (wp_posts) table
				foreach ($drupal_nodes_data as $post)
				{
						$post['type'] = $post['type'] == 'article'? 'post': $post['type'];
						$insert_query = "INSERT INTO {$this->wordpress_db_prefix}posts (ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, post_name, post_modified, post_modified_gmt, guid, post_type)
										VALUES ({$post['nid']}, {$post['uid']}, '" . date('Y-m-d H:i:s', $post['created']) . "',  '" . date('Y-m-d H:i:s', $post['created']) . "' , '" . addslashes($post['body_value']) . "', '" . addslashes($post['title']) . "', '" . addslashes($post['body_summary']) . "', 'publish', '" . urlencode($post['alias']) . "', '" . date('Y-m-d H:i:s', $post['changed']) . "', '" . date('Y-m-d H:i:s', $post['changed']) . "', ' ', '{$post['type']}');";
						$insert_post = $this->wordpress_db_conn->exec($insert_query);
						
						if($insert_post != 1)
						{
							echo 'Error in post ' . $post['nid'] . '</br>';
							echo 'For Query ' . $insert_query . '</br>';
						}
				}
				
		}
		catch (PDOException $e)
		{
			echo 'Database Error: ';
			echo $e->getMessage();
		}
	}
	
	
	/*
	 * Migrate Tags from db to
	 */
	public function migrate_terms()
	{
	try
		{
				// Transfer terms
				
				// Get data from drupal (taxonomy_term_data) table
				$query = "
							SELECT taxonomy_term_data.tid AS tid, taxonomy_term_data.name AS name, SUBSTRING_INDEX( url_alias.alias, '/', -1 ) AS alias
							FROM taxonomy_term_data
							LEFT JOIN url_alias ON url_alias.source LIKE 'taxonomy/term%' AND taxonomy_term_data.tid = SUBSTRING( url_alias.source FROM 15 )
							Group By name
						";
				$migrate_query = $this->drupal_db_conn->query($query);
				$drupal_terms_data = $migrate_query->fetchall(PDO::FETCH_ASSOC);
				
				// Add the nodes data to the (wp_terms) table
				foreach ($drupal_terms_data as $term)
				{
					// INSERT INTO (wp_terms) table
					$insert_query = "INSERT INTO {$this->wordpress_db_prefix}terms (term_id, name, slug)
									VALUES ({$term['tid']}, '{$term['name']}', '" . urlencode($term['alias']) . "');";
					$insert_term = $this->wordpress_db_conn->exec($insert_query);
					
					if($insert_term != 1)
					{
						echo 'Error in term Insert:  ' . $term['tid'] . '- ' . $term['name'] . '</br>';
						echo 'For Query ' . $insert_query . '</br>';
						echo '_______________________________</br>';
					}
				}
				
				// -----------------------------------------------------
				
				// Get data from drupal (taxonomy_term_data) / (taxonomy_term_vocabulary) table
				$query = "
							SELECT taxonomy_term_data.tid AS tid, taxonomy_term_data.name AS name, taxonomy_vocabulary.name AS taxonomy, taxonomy_vocabulary.machine_name AS machine_name
							FROM taxonomy_term_data
							LEFT JOIN taxonomy_vocabulary ON taxonomy_term_data.vid = taxonomy_vocabulary.vid
						";
				$migrate_query = $this->drupal_db_conn->query($query);
				$term_taxonomy_data = $migrate_query->fetchall(PDO::FETCH_ASSOC);
				
				// Add the nodes data to the (term_taxonomy) table
				foreach ($term_taxonomy_data as $term)
				{
					// Fix category / tag Taxonomy Names
					$term['machine_name'] = $term['machine_name'] == 'classification' ? 'category' :$term['machine_name'];
					$term['machine_name'] = $term['machine_name'] == 'tags' ? 'post_tag' :$term['machine_name'];
					
					// INSERT INTO (term_taxonomy) table
					$insert_query = "
									INSERT INTO {$this->wordpress_db_prefix}term_taxonomy (term_id, taxonomy)
									VALUES ({$term['tid']}, '{$term['machine_name']}');
									";
					$insert_term_taxonomy = $this->wordpress_db_conn->exec($insert_query);
						
					if($insert_term_taxonomy != 1)
					{
						echo 'Error in term_taxonomy Insert ' . $term['tid'] . '</br>';
						echo 'For Query ' . $insert_query . '</br>';
						echo '_______________________________</br>';
					}
				}
				
				// -----------------------------------------------------
				
				// Get data from drupal (taxonomy_index) table
				$query = "
							SELECT taxonomy_index.tid AS tid, taxonomy_index.nid AS nid
							FROM taxonomy_index
						";
				$migrate_query = $this->drupal_db_conn->query($query);
				$term_taxonomy_relationship_id = $migrate_query->fetchall(PDO::FETCH_ASSOC);
				
				// Add the nodes data to the (term_taxonomy_relationships) table
				foreach ($term_taxonomy_relationship_id as $term)
				{
					// Get term_taxonomy_id	
					$query = "
							SELECT term_taxonomy_id
							FROM wp_term_taxonomy
							WHERE term_id = {$term['tid']}
							LIMIT 0,1;
							";
					$migrate_query = $this->wordpress_db_conn->query($query);
					$term_taxonomy_relationships_data = $migrate_query->fetch(PDO::FETCH_ASSOC);
					
					$term_taxonomy_id = $term_taxonomy_relationships_data['term_taxonomy_id'];
					
					// INSERT INTO (term_taxonomy_relationships) table
					$insert_query = "
									INSERT INTO {$this->wordpress_db_prefix}term_relationships (object_id, term_taxonomy_id)
									VALUES ({$term['nid']}, {$term_taxonomy_id});
									";
					
					$insert_term_taxonomy_relationship = $this->wordpress_db_conn->exec($insert_query);
					
					if($insert_term_taxonomy_relationship != 1)
					{
							echo 'Error in term_taxonomy_relationship Insert: ' . $term['tid'] . '</br>';
							echo 'For Query ' . $insert_query . '</br>';
							echo '_______________________________</br>';
							var_dump($term_taxonomy_relationships_data);
					}
				 }
		}
		catch (PDOException $e)
		{
			echo 'Database Error: ';
			echo $e->getMessage();
		}
	}
	
	
	/*
	 * Migrate Images
	 */
	public function migrate_images()
	{
		/*
		 * FOR LATER DEVELOPMENT:
		 * Check Folder permissions for both drupal & WP
		 */		
				
		// Get data from drupal (file_managed) / (field_data_field_image) table
		
		$query = "
					SELECT filename, filemime, timestamp, uid, SUBSTRING( uri, 10 ) AS path, field_data_field_image.entity_id
					FROM file_managed
					LEFT JOIN field_data_field_image ON field_data_field_image.field_image_fid = file_managed.fid
					WHERE file_managed.uri LIKE '%inline%' AND file_managed.filemime LIKE 'image%'
				";
		
		$drupal_files_query = $this->drupal_db_conn->query($query);
		$drupal_files_data = $drupal_files_query->fetchall(PDO::FETCH_ASSOC);
		
		
		
		// Migrate Files
		foreach ($drupal_files_data as $current_file)
		{
			try
			{
				$meta_array = array(); // _wp_attachment_meta array (to be serialized)
				
				// Get the filename & extension for resize purpose
				$current_file_name = $current_file['filename'];
				$file_extension_pos = strrpos($current_file_name, '.');  // file '.' position
				$file_extension = substr($current_file_name, $file_extension_pos); // file extension '.something'
				$file_image_name = str_replace($file_extension, '', $current_file_name); // file name without extension
				
				// get the month and the year for the upload folder
				$current_year = date('Y');
				$current_month = date('m');
				
				// Create a SimpleImage() object
				$simple_image_obj = new SimpleImage($this->drupal_upload_path . $current_file['path']);
				
				// Get original height & width of the image
				$orig_height = $simple_image_obj->get_height();
				$orig_width = $simple_image_obj->get_width();
				
				// Get the matching heights for the resize
				$height_100 = floor((100/$orig_width) * $orig_height);
				$height_150 = floor((150/$orig_width) * $orig_height);
				$height_200 = floor((200/$orig_width) * $orig_height);
				$height_300 = floor((300/$orig_width) * $orig_height);
				$height_450 = floor((450/$orig_width) * $orig_height);
				$height_600 = floor((600/$orig_width) * $orig_height);
				$height_900 = floor((900/$orig_width) * $orig_height);
				$height_1024 = floor((1024/$orig_width) * $orig_height);
				
				
				// resize & save the images
				$simple_image_obj->save($this->wordpress_upload_path . $current_year . '/' . $current_month . '/' . $file_image_name . $file_extension);
				$simple_image_obj->resize(1024, $height_1024)->save($this->wordpress_upload_path . $current_year . '/' . $current_month . '/' . $file_image_name . '-1024x' . $height_1024 . $file_extension);
				$simple_image_obj->resize(900, $height_900)->save($this->wordpress_upload_path . $current_year . '/' . $current_month . '/' . $file_image_name . '-900x' . $height_900 . $file_extension);
				$simple_image_obj->resize(600, $height_600)->save($this->wordpress_upload_path . $current_year . '/' . $current_month . '/' . $file_image_name . '-600x' . $height_600 . $file_extension);
				$simple_image_obj->resize(450, $height_450)->save($this->wordpress_upload_path . $current_year . '/' . $current_month . '/' . $file_image_name . '-450x' . $height_450 . $file_extension);
				$simple_image_obj->resize(300, $height_300)->save($this->wordpress_upload_path . $current_year . '/' . $current_month . '/' . $file_image_name . '-300x' . $height_300 . $file_extension);
				$simple_image_obj->resize(200, $height_200)->save($this->wordpress_upload_path . $current_year . '/' . $current_month . '/' . $file_image_name . '-200x' . $height_200 . $file_extension);
				$simple_image_obj->resize(150, $height_150)->save($this->wordpress_upload_path . $current_year . '/' . $current_month . '/' . $file_image_name . '-150x' . $height_150 . $file_extension);
				$simple_image_obj->resize(100, $height_100)->save($this->wordpress_upload_path . $current_year . '/' . $current_month . '/' . $file_image_name . '-100x' . $height_100 . $file_extension);
				
				
				
				
				
				// INSERT image data INTO
				$insert_attachement_query = "
											INSERT INTO {$this->wordpress_db_prefix}posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, post_name, post_modified, post_modified_gmt, guid, post_type, post_mime_type, post_parent)
											VALUES ({$current_file['uid']}, '" . date('Y-m-d H:i:s', $current_file['timestamp']) . "',  '" . date('Y-m-d H:i:s', $current_file['timestamp']) . "' , ' ', '" . addslashes($current_file['filename']) . "', ' ', 'inherit', '" . urlencode($current_file['filename']) . "', '" . date('Y-m-d H:i:s', $current_file['timestamp']) . "', '" . date('Y-m-d H:i:s', $current_file['timestamp']) . "', ' ', 'attachment', '{$current_file['filemime']}', 0);
											";
					
				$insert_attachement = $this->wordpress_db_conn->exec($insert_attachement_query);
				
				if($insert_attachement != 1)
				{
					echo 'Error in Image Insert: ' . $current_file['filename'] . '</br>';
					echo 'For Query ' . $insert_attachement_query . '</br>';
					echo '_______________________________</br>';
				}
				else
				{
					echo $inserted_id = $this->wordpress_db_conn->lastInsertId();
				}
				
				/*
				// INSERT image data INTO 
				$insert_attachement_query = "
											INSERT INTO {$this->wordpress_db_prefix}posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, post_name, post_modified, post_modified_gmt, guid, post_type, post_mime_type, post_parent)
											VALUES ({$current_file['uid']}, '" . date('Y-m-d H:i:s', $current_file['timestamp']) . "',  '" . date('Y-m-d H:i:s', $current_file['timestamp']) . "' , ' ', '" . addslashes($current_file['filename']) . "', ' ', 'inherit', '" . urlencode($current_file['filename']) . "', '" . date('Y-m-d H:i:s', $current_file['timestamp']) . "', '" . date('Y-m-d H:i:s', $current_file['timestamp']) . "', ' ', 'attachment', '{$current_file['filemime']}', {$current_file['entity_id']});
											";
					
				$insert_attachement = $this->wordpress_db_conn->exec($insert_attachement_query);
				
				if($insert_attachement != 1)
				{
						echo 'Error in Image Insert: ' . $current_file['filename'] . '</br>';
						echo 'For Query ' . $insert_attachement_query . '</br>';
						echo '_______________________________</br>';
				}
				else 
				{
					echo $inserted_id = $this->wordpress_db_conn->lastInsertId();
				}
				*/
				// INSERT image data INTO
				$insert_attachement_meta_query = "
											INSERT INTO {$this->wordpress_db_prefix}postmeta (post_id, meta_key, meta_value)
											VALUES ('{$inserted_id}', '_wp_attached_file', '{$current_year}/{$current_month}/{$file_image_name}{$file_extension}'), 
											('{$current_file['entity_id']}', '_thumbnail_id', '{$inserted_id}');
											";
					
				$insert_attachement_meta = $this->wordpress_db_conn->exec($insert_attachement_meta_query);
				
				if($insert_attachement_meta <= 0)
				{
						echo 'Error in Image Meta Insert: ' . $current_file['filename'] . '</br>';
						echo 'For Query ' . $insert_attachement_meta_query . '</br>';
						echo '_______________________________</br>';
				}
				
				//-----------------------------
				// Create the meta_array
				//-----------------------------
				
				$meta_array['width'] = $orig_width;
				$meta_array['height'] = $orig_height;
				$meta_array['file'] = $current_month . '/' . $current_year . '/' . $current_file_name;
				
				// Assign $meta_array width
				$meta_array['sizes']['thumbnail']['width'] = 150;
				$meta_array['sizes']['medium']['width'] = 300;
				$meta_array['sizes']['large']['width'] = 1024;
				$meta_array['sizes']['responsive-100']['width'] = 100;
				$meta_array['sizes']['responsive-150']['width'] = 150;
				$meta_array['sizes']['responsive-200']['width'] = 200;
				$meta_array['sizes']['responsive-300']['width'] = 300;
				$meta_array['sizes']['responsive-450']['width'] = 450;
				$meta_array['sizes']['responsive-600']['width'] = 600;
				$meta_array['sizes']['responsive-900']['width'] = 900;
				
				// Assign $meta_array height
				$meta_array['sizes']['thumbnail']['height'] = $height_150;
				$meta_array['sizes']['medium']['height'] = $height_300;
				$meta_array['sizes']['large']['height'] = $height_1024;
				$meta_array['sizes']['responsive-100']['height'] = $height_100;
				$meta_array['sizes']['responsive-150']['height'] = $height_150;
				$meta_array['sizes']['responsive-200']['height'] = $height_200;
				$meta_array['sizes']['responsive-300']['height'] = $height_300;
				$meta_array['sizes']['responsive-450']['height'] = $height_450;
				$meta_array['sizes']['responsive-600']['height'] = $height_600;
				$meta_array['sizes']['responsive-900']['height'] = $height_900;
				
				// Assign $meta_array mime
				$meta_array['sizes']['thumbnail']['mime-type'] = $current_file['filemime'];
				$meta_array['sizes']['medium']['mime-type'] = $current_file['filemime'];
				$meta_array['sizes']['large']['mime-type'] = $current_file['filemime'];
				$meta_array['sizes']['responsive-100']['mime-type'] = $current_file['filemime'];
				$meta_array['sizes']['responsive-150']['mime-type'] = $current_file['filemime'];
				$meta_array['sizes']['responsive-200']['mime-type'] = $current_file['filemime'];
				$meta_array['sizes']['responsive-300']['mime-type'] = $current_file['filemime'];
				$meta_array['sizes']['responsive-450']['mime-type'] = $current_file['filemime'];
				$meta_array['sizes']['responsive-600']['mime-type'] = $current_file['filemime'];
				$meta_array['sizes']['responsive-900']['mime-type'] = $current_file['filemime'];
				
				// Assign $meta_array mime
				$meta_array['sizes']['thumbnail']['file'] = $file_image_name . '-150x' . $height_150 . $file_extension;
				$meta_array['sizes']['medium']['file'] = $file_image_name . '-300x' . $height_300 . $file_extension;
				$meta_array['sizes']['large']['file'] = $file_image_name . '-1024x' . $height_1024 . $file_extension;
				$meta_array['sizes']['responsive-100']['file'] = $file_image_name . '-100x' . $height_100 . $file_extension;
				$meta_array['sizes']['responsive-150']['file'] = $file_image_name . '-150x' . $height_150 . $file_extension;
				$meta_array['sizes']['responsive-200']['file'] = $file_image_name . '-200x' . $height_200 . $file_extension;
				$meta_array['sizes']['responsive-300']['file'] = $file_image_name . '-300x' . $height_300 . $file_extension;
				$meta_array['sizes']['responsive-450']['file'] = $file_image_name . '-450x' . $height_450 . $file_extension;
				$meta_array['sizes']['responsive-600']['file'] = $file_image_name . '-600x' . $height_600 . $file_extension;
				$meta_array['sizes']['responsive-900']['file'] = $file_image_name . '-900x' . $height_900 . $file_extension;
				
				$meta_array['image_meta'] = array(
						'aperture' => 0,
						'credit' => "",
						'camera'=> "",
						'caption'=> "",
						'created_timestamp'=> 0,
						'copyright'=> "",
						'focal_length'=> 0,
						'iso'=> 0,
						'shutter_speed'=> 0,
						'title'=> "",
						'orientation'=> 0
				);
				
				$serialzed_meta_array = serialize($meta_array);
			
				// INSERT image data INTO
				$insert_attachement_meta_data = "
				INSERT INTO {$this->wordpress_db_prefix}postmeta (post_id, meta_key, meta_value)
				VALUES ('{$inserted_id}', '_wp_attachment_metadata', '{$serialzed_meta_array}')
				";
					
				$insert_attachement_meta_data = $this->wordpress_db_conn->exec($insert_attachement_meta_data);
				
				if($insert_attachement_meta_data <= 0)
				{
				echo 'Error in Image Meta Serialized Data: ' . $current_file['filename'] . '</br>';
				echo 'For Query ' . $insert_attachement_meta_query . '</br>';
				echo '_______________________________</br>';
				}
				
				
				//----------------------
				//| Meta array example |
				//----------------------
				/*
				array(
				
						'width'=> 1920,
						'height'=> 1200,
						'file'=> "2014/12/distant-lights-linux-wallpaper.jpg",
						'sizes'=> array(
								'thumbnail'=> array(
										'file' => "distant-lights-linux-wallpaper-150x150.jpg",
										'width' => 150,
										'height' => 150,
										'mime-type' => "image/jpeg"
								),
				
								'medium' => array(
										'file' => "distant-lights-linux-wallpaper-300x187.jpg",
										'width' => 300,
										'height' => 187,
										'mime-type' => "image/jpeg"
								),
								'large' => array(
										'file' => "distant-lights-linux-wallpaper-1024x640.jpg",
										'width' => 1024,
										'height' => 640,
										'mime-type' => "image/jpeg"
								),
								'responsive-100' => array(
										'file' => "distant-lights-linux-wallpaper-100x62.jpg",
										'width' => 100,
										'height' => 62,
										'mime-type' => "image/jpeg"
								),
								'responsive-150'=> array(
										'file' => "distant-lights-linux-wallpaper-150x93.jpg",
										'width' => 150,
										'height' => 93,
										'mime-type' => "image/jpeg"
								),
								'responsive-200'=> array(
										'file' => "distant-lights-linux-wallpaper-200x125.jpg",
										'width' => 200,
										'height' => 125,
										'mime-type' => "image/jpeg"
								),
								'responsive-300' => array(
										'file' => "distant-lights-linux-wallpaper-300x187.jpg",
										'width' => 300,
										'height' => 187,
										'mime-type' => "image/jpeg"
								),
								'responsive-450' => array(
										'file' => "distant-lights-linux-wallpaper-450x281.jpg",
										["width"]=> int(450),
										["height"]=> int(281),
										["mime-type"]=> "image/jpeg"
								),
								'responsive-600' => array(
										'file' => "distant-lights-linux-wallpaper-600x375.jpg",
										'width' => 600,
										'height' => 375,
										'mime-type' => "image/jpeg"
								),
								'responsive-900' => array(
										'file' => "distant-lights-linux-wallpaper-900x562.jpg",
										'width' => 900,
										'height'=> 562,
										'mime-type'=> "image/jpeg"
								),
						),
						'image_meta' => array(
								'aperture' => 0,
								'credit' => "",
								'camera'=> "",
								'caption'=> "",
								'created_timestamp'=> 0,
								'copyright'=> "",
								'focal_length'=> 0,
								'iso'=> 0,
								'shutter_speed'=> 0,
								'title'=> "",
								'orientation'=> 0
						)
				
				);
				*/
				
				
			}
			catch (Exception $e)
			{
				echo 'file error: ' . $current_file['filename'];
			}	
			//resize the copied image 6 times
			// add the data with the original name to the db
		}
		
		
		
		// Migrate DB rows
		
		/*
		$drupal_images_query = $drupal_connection->prepare("SELECT* FROM {$wp_prefix}files");
		$drupal_nodes_data = $drupal_images_query->fetch(FETCH_ASSOC);
		
		
		// insert to posts meta as attached file & as thumbnail_id
		$drupal_attachment_query = $drupal_connection->prepare("INSERT INTO {$wp_prefix}wp_postmeta, VALUES(meta_key = _wp_attached_file");
		$drupal_nodes_data = $drupal_attachment_query->fetch(FETCH_ASSOC);
		*/
		
		
		// LOOP for images & push to array
		
		// add to wordpress
		
		// LOOP START
		
		// resize image
		// 
		
		// Tables
		// posts: articles
		// files: images
		// wp_postmeta: meta_key(_wp_attached_file) / post_id / meta_value
		
		// LOOP END
	}
	
	
	/*
	 * Migrate Users from db to
	 */
	public function migrate_users()
	{
		try
		{
			// Transfer posts
			// note: There might be drupal content_types that
			// should be converted to WP custom_post_types
	
			// Get data from drupal (users) / (users_roles) / (role) table
			$query = "
					SELECT users.uid AS id, users.name AS name, users.pass AS pass, users.mail AS mail, users.created AS created, roles_table.rid AS rid, role.name AS role_name
					FROM users
					LEFT JOIN (
								SELECT uid, MIN( rid ) AS rid
								FROM users_roles
								GROUP BY uid
								) AS roles_table ON users.uid = roles_table.uid
								LEFT JOIN role ON role.rid = roles_table.rid
					";
			$migrate_query = $this->drupal_db_conn->query($query);
			$drupal_users_data = $migrate_query->fetchall(PDO::FETCH_ASSOC);
			
	
			// Add the nodes data to the (wp_users) / (wp_usersmeta) tables
			foreach ($drupal_users_data as $user)
			{
				
				// Insert the users data into the users table
				$insert_user_query = "
										INSERT INTO {$this->wordpress_db_prefix}users (ID, user_login, user_pass, user_nicename, user_email, user_url, user_registered, display_name )
											VALUES
										({$user['id']}, '" . addslashes($user['name']) . "', '{$user['pass']}', '" . addslashes($user['name']) . "', '{$user['mail']}', ' ', '" . date('Y-m-d H:i:s', $user['created']) . "', '{$user['name']}');
									";
				
				$insert_user = $this->wordpress_db_conn->exec($insert_user_query);
				
				// If the current user is not the 1st user, add the meta
				if($user['id'] != 1)
				{
					
					$usermeta_array = array();
					// Assign usersmeta values
					$usermeta_array['nickname'] = $user['name'];
					$usermeta_array['first_name'] = ' ';
					$usermeta_array['last_name'] = ' ';
					$usermeta_array['description'] = ' ';
					$usermeta_array['rich_editing'] = 'true';
					$usermeta_array['comment_shortcuts'] = 'false';
					$usermeta_array['admin_color'] = 'fresh';
					$usermeta_array['use_ssl'] = '0';
					$usermeta_array['show_admin_bar_front'] = 'true';
					$usermeta_array['dismissed_wp_pointers'] = 'wp350_media,wp360_revisions,wp360_locks,wp390_widgets';
					
					// Convert user roles to wordpress format
					if($user['rid'] == '2')
					{
						$usermeta_array['wp_capabilities'] = 'a:1:{s:10:"subscriber";b:1;}';
						$usermeta_array['wp_user_level'] = '0';
					}
					elseif($user['rid'] == '3')
					{
						$usermeta_array['wp_capabilities'] = 'a:1:{s:13:"administrator";b:1;}';
						$usermeta_array['wp_user_level'] = '10';
					}
					elseif($user['rid'] == '4')
					{
						$usermeta_array['wp_capabilities'] = 'a:1:{s:11:"contributor";b:1;}';
						$usermeta_array['wp_user_level'] = '1';
					}
					elseif($user['rid'] == '5')
					{
						$usermeta_array['wp_capabilities'] = 'a:1:{s:6:"editor";b:1;}';
						$usermeta_array['wp_user_level'] = '7';
					}
					elseif($user['rid'] == '6')
					{
						$usermeta_array['wp_capabilities'] = 'a:1:{s:13:"administrator";b:1;}';
						$usermeta_array['wp_user_level'] = '10';
					}
					
					// Generate meta values string for the query
					$meta_values_query_string = '';
					
					foreach($usermeta_array as $key => $meta_value)
					{
						$meta_values_query_string .= "({$user['id']}, '{$key}', '{$meta_value}')";
							
						if($key != 'wp_user_level')
						{
							$meta_values_query_string .= ', ';
						}
					}
					
					// Insert the users data into the usermeta table
					$insert_meta_query = "
					INSERT INTO {$this->wordpress_db_prefix}usermeta (user_id, meta_key, meta_value)
					VALUES {$meta_values_query_string}";
					
					$insert_meta = $this->wordpress_db_conn->exec($insert_meta_query);
					
					if($insert_user == 0)
					{
					echo 'Error in user ' . $user['id'] . '</br>';
					echo 'For Query ' . $insert_user_query . '</br>';
					echo '________________________________ </br>';
					
					if($insert_meta == 0)
					{
						echo 'Error in usermeta ' . $user['id'] . '</br>';
						echo 'For Query ' . $insert_meta_query . '</br>';
						echo '________________________________ </br>';
					}
					}
				}
					
			}
	
		}
		catch (PDOException $e)
		{
		echo 'Database Error: ';
		echo $e->getMessage();
		}
	}
	
	
	
	/*
	 *
	 */
	public function close_connections()
	{
		$this->drupal_db_conn = null;
		$this->wordpress_db_conn = null;
	}
	
}
