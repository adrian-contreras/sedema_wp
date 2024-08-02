<?php
/**
 * @package BuddyBoss Child
 * The parent theme functions are located at /buddyboss-theme/inc/theme/functions.php
 * Add your own functions at the bottom of this file.
 */


/****************************** THEME SETUP ******************************/

/**
 * Sets up theme for translation
 *
 * @since BuddyBoss Child 1.0.0
 */
function buddyboss_theme_child_languages()
{
  /**
   * Makes child theme available for translation.
   * Translations can be added into the /languages/ directory.
   */

  // Translate text from the PARENT theme.
  load_theme_textdomain( 'buddyboss-theme', get_stylesheet_directory() . '/languages' );

  // Translate text from the CHILD theme only.
  // Change 'buddyboss-theme' instances in all child theme files to 'buddyboss-theme-child'.
  // load_theme_textdomain( 'buddyboss-theme-child', get_stylesheet_directory() . '/languages' );

}
add_action( 'after_setup_theme', 'buddyboss_theme_child_languages' );

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Boss Child Theme  1.0.0
 */
function buddyboss_theme_child_scripts_styles()
{
  /**
   * Scripts and Styles loaded by the parent theme can be unloaded if needed
   * using wp_deregister_script or wp_deregister_style.
   *
   * See the WordPress Codex for more information about those functions:
   * http://codex.wordpress.org/Function_Reference/wp_deregister_script
   * http://codex.wordpress.org/Function_Reference/wp_deregister_style
   **/

  // Styles
  wp_enqueue_style( 'buddyboss-child-css', get_stylesheet_directory_uri().'/assets/css/custom.css' );

  // Javascript
  wp_enqueue_script( 'buddyboss-child-js', get_stylesheet_directory_uri().'/assets/js/custom.js' );
}
add_action( 'wp_enqueue_scripts', 'buddyboss_theme_child_scripts_styles', 9999 );


/****************************** CUSTOM FUNCTIONS ******************************/

// Add your own custom functions here

add_filter( 'buddyboss_theme_redux_is_theme', '__return_true', 999 );

/**
 * Get path from template directory to current file.
 * 
 * 
 * @param string $file_path Current file path
 * 
 * @uses get_template() Get active template directory name.
 * 
 * @return string
 */
function buddyboss_theme_dir_to_current_file_path( $file_path ) {
        // Format current file path with only right slash.
        $file_path = trailingslashit( $file_path );
        $file_path = str_replace( '\\', '/', $file_path );
        $file_path = str_replace( '//', '/', $file_path );
        $chunks    = explode( '/', $file_path );
        if ( ! is_array( $chunks ) ) {
                $chunks = array();
        }
        // Reverse array for child to parent or current file to template directory.
        $chunks   = array_reverse( $chunks );
        $template = get_template();
        $tmp_file = array();
        foreach ( $chunks as $path ) {
                if ( empty( $path ) ) {
                        continue;
                }
                if ( $path == $template ) {
                        break;
                }
                // Set all directory name from current file to template directory.
                $tmp_file[] = $path;              
        }
        // Reverse array for parent to child or template directory to file directory.
        $tmp_file = array_reverse( $tmp_file );
        $tmp_file = implode( '/', $tmp_file );
        return $tmp_file;
}

/**
 * Filter Redux URL
 * 
 * @param string $url Redux url.
 * 
 * @uses buddyboss_theme_dir_to_current_file_path() Get relative path.
 * 
 * @return string
 */
function buddyboss_theme_redux_url( $url ) {
        /**
         * When some parts of current file path and template directory path are match from the beginning.
         * 
         * Example
         * current_path = /bitnami/wordpress/wp-content/
         * tmpdir_path  = /bitnami/wordpress/wp-content/themes/buddyboss-theme/inc/admin/framework/ReduxCore/
         */
        if ( strpos( Redux_Helpers::cleanFilePath( __FILE__ ), Redux_Helpers::cleanFilePath( get_template_directory() ) ) !== false ) {
                return $url;
        } else if ( strpos( Redux_Helpers::cleanFilePath( __FILE__ ), Redux_Helpers::cleanFilePath( get_stylesheet_directory() ) ) !== false ) {
                return $url;
        } 
        /**
         * When some parts of current file path and template directory path are not match from the beginning.
         * 
         * Example
         * current_path = /opt/bitnami/wordpress/wp-content/
         * tmpdir_path  = /bitnami/wordpress/wp-content/themes/buddyboss-theme/inc/admin/framework/ReduxCore/
         */
        // Get template url.
        $tem_dir  = trailingslashit( get_template_directory_uri() );
        // Get template to current file directory path.
        $file_dir = buddyboss_theme_dir_to_current_file_path( $url );
        // Set url for ReduxCore directory
        $redux_url = trailingslashit( $tem_dir . $file_dir );
        // Check valid url
        if ( filter_var ( $redux_url, FILTER_VALIDATE_URL ) ) {
                return $redux_url;
        } 
        return $url;
}
add_filter( 'redux/_url', 'buddyboss_theme_redux_url' );

add_filter( 'style_loader_src', 'bb_fix_theme_option_for_custom_wp_installation' );
add_filter( 'script_loader_src', 'bb_fix_theme_option_for_custom_wp_installation' );
function bb_fix_theme_option_for_custom_wp_installation( $url ) {
  if ( is_admin() ) {
    $url = str_replace( 'plugins/bitnami/wordpress/wp-content/themes/buddyboss-theme/', 'themes/buddyboss-theme/', $url );
  }
  return $url;
}
/*********************************/
add_action('wpforms_process_complete', 'custom_wpforms_process_complete', 10, 4);

function custom_wpforms_process_complete($fields, $entry, $form_data, $entry_id){ 
   // Verificar el ID del formulario para asegurarse de que solo se procese el formulario específico
   $form_id = 139; // Reemplaza con tu ID de formulario
   

   //error_log("Contenido de \$_FILES: " . print_r($_FILES, true));
   //error_log("Contenido de \$_POST: " . print_r($_POST, true));
    //return;
    if ($form_data['id'] != $form_id) {
        return;
    }



    // Obtener los campos del formulario
    //$post_title = 'Nueva oferta' . date('Y-m-d H:i:s');
    $content = '';
	error_log('functions.php::form_data::'.json_encode($entry));
	//error_log('functions.php::form_data::'.json_encode($entry['fields']['9']));
	//error_log('functions.php::form_data::'.json_encode($form_data));
	//error_log('functions.php::fields::'.json_encode($fields));
	 
    
    //$content .= '<table class="buddypress-wrap bp-tables-report"><tbody>';
    foreach ($fields as $field_id => $field) {
    	/*
        if ($field['name'] == 'Title') { // Reemplaza 'Title' con el nombre del campo
            $post_title = sanitize_text_field($field['value']);
        }
        if ($field['name'] == 'Content') { // Reemplaza 'Content' con el nombre del campo
            $post_content = wp_kses_post($field['value']);
        }
		*/        
		//error_log('functions.php::fields::'.$field['name'].'::'.$field['value']);
		//$content.=$field['name'].': '.$field['value']. "\n";
        //$content .= '<tr>';
        $content .= sprintf('<strong>%s:</strong> %s<br>', sanitize_text_field($field['name']), sanitize_text_field($field['value']));
        //$content .= '</tr>';
    }
    //$content .= '</tbody></table>';

    // Crear la actividad en BuddyBoss
    //$user_id = get_current_user_id(); // Obtener el ID del usuario actual
    //$primary_link     = ;
    
	 //bp_loggedin_user_id()
    //error_log('functions.php::fields::'.get_current_user_id().' '.bp_loggedin_user_id());
    //error_log('functions.php::fields::'.bp_core_get_userlink( get_current_user_id(), false, true ));
    $places = array();
    if(isset($entry['fields']['9'])){
        //error_log('functions.php::places::initializing');
	    $places=$entry['fields']['9'];
    }else{
        array_push($places,0);
    }

    //$activity_ids = array();
    //error_log('functions.php::places::'.json_encode($places));
    //return;
    $activity_ids=add_activities($places,$content);
    if(isset($activity_ids)){
        if(is_array($activity_ids) AND count($activity_ids)>0){
            $_ALL_FILES = array();
            error_log('functions.php::count(activity_ids)::'.count($activity_ids));
            
            if(count($activity_ids)>1){
                for ($x = 0; $x <count($activity_ids)-1; $x++) {
                    array_push($_ALL_FILES, clone_files_array($_FILES));
                }
                array_push($_ALL_FILES, $_FILES);
                  
            }else{
                array_push($_ALL_FILES, $_FILES);
            }

            foreach($activity_ids as $activity_id){
                //$attachment_ids=add_attachment($_FILES,$activity_id);
                $attachment_ids=add_attachment(array_pop($_ALL_FILES),$activity_id);
                if(isset($attachment_ids)){
                    if(is_array($attachment_ids) AND count($attachment_ids)>0){
                        $media_ids = array();
                        $media_done=0;
                        foreach($attachment_ids as $attachment_id){
                            $media_id=add_activities_attachment_meta($activity_id,$attachment_id,'public',$media_done);
                            array_push($media_ids,$media_id);
                            $media_done ++;
                        }
                        bp_activity_update_meta( $activity_id, 'bp_media_ids', implode( ',', $media_ids ) );
                    }

                }
            }


        }
    }    
	 /*
    // Crear el post en WordPress
    $new_post = array(
        'post_title'    => $post_title,
        'post_content'  => $content,
        'post_status'   => 'publish', // O 'draft', según tus necesidades
        'post_type'     => 'post'
    );

    // Insertar el post en la base de datos
    $post_id = wp_insert_post($new_post);
    if ($post_id) {

        // Guardar los campos como meta datos del post
        foreach ($fields as $id => $field) {
            update_post_meta($post_id, 'wpforms_campo_' . $id, $field['value']);
        }

        // Opcionalmente, puedes guardar el ID del post creado en la entrada de WPForms
        //wpforms()->entry->update($entry_id, array('post_id' => $post_id), '', '', array('cap' => false));

        // Log para depuración
        error_log('Post creado con éxito. ID: ' . $post_id);
    } else {
        // Log para depuración
        error_log('Error al crear el post desde WPForms');
    }
    */
    
}

	/**
	 * Create activities.
	 *
	 * @param array $places Key value array of types of activities.
     * @param string $content HTML content.
	 *
	 * @return array
	 * @since 0.1.0
	 */

function add_activities($_places,$_content){
    $_activity_ids = array();
    //$component='activity';
    $component=buddypress()->activity->id;
    $privacy='public';
    foreach($_places as $place_id){
        if($place_id!=0){
            //$component='groups';
            $component=buddypress()->groups->id;
            //$privacy='public';
        }
        $activity_args=array(
                //'id'                => $activity->id,
                //'action'            => $activity->action, 
                'content'           => $_content,
                'component'         => $component,
                'type'              => 'activity_update',
                'primary_link'      => bp_core_get_userlink( bp_loggedin_user_id(), false, true ),
                'user_id'           => bp_loggedin_user_id(),
                //'item_id'           => 0, //0 es solo el muro de la persona
                'item_id'           => $place_id,
                'secondary_item_id' => 0,
                'recorded_time'     => bp_core_current_time(),
                //'hide_sitewide'     => $activity->hide_sitewide,
                //'is_spam'           => $activity->is_spam,
                //'privacy'           => 'public',
                'privacy'           => $privacy,
                //'error_type'        => $r['error_type'],
        );


        $activity_id = bp_activity_add( $activity_args );
        
        if ($activity_id) {
            error_log('functions.php::add_activities::activity_id:: ' . $activity_id);
            bp_activity_add_meta( $activity_id, '_link_embed', '0' );
            bp_activity_add_meta( $activity_id, '_link_preview_data', '' );

            array_push($_activity_ids,$activity_id);
        } else {
            // Log para depuración
            //error_log('Error al crear la actividad desde WPForms');
        }
        
    }
    return $_activity_ids;
}


	/**
	 * Unpload files.
	 *
	 * @param array $_files_ array of _FILES.
     * @param int $_activity_id Id activity.
	 *
	 * @return array
	 * @since 0.1.0
	 */

function add_attachment($_files_,$_activity_id){
    $_attachment_ids = array();
    $file_upload_field_id = 10; // ID del campo de carga de archivos
    if (isset($_files_['wpforms']['name']['fields'][$file_upload_field_id])) {
        //error_log('functions.php::_files_::NOT:EMPTY');
        //error_log('functions.php::_files_::'.json_encode($_files_));
        //error_log('functions.php::_files_::NOT:EMPTY::'.isset($_files_['wpforms']['name']['fields'][$file_upload_field_id]));
        $files = $_files_['wpforms']['name']['fields'][$file_upload_field_id];
        //error_log('functions.php::_files_::NOT:EMPTY::'.json_encode($files));
        //return;
        if (is_array($files)) {
            //if(count($files)>0 AND !empty($files)){
                error_log('functions.php::_files_::NOT:EMPTY::'.json_encode($files));
                foreach ($files as $key => $filename) {
                    
                    if(!empty($filename)){
                        $file_tmp = $_files_['wpforms']['tmp_name']['fields'][$file_upload_field_id][$key];                
                        $file_type = $_files_['wpforms']['type']['fields'][$file_upload_field_id][$key];
                        $file_size = $_files_['wpforms']['size']['fields'][$file_upload_field_id][$key];
                        //error_log('functions.php::_files_::NOT:EMPTY:: '.$file_tmp.' :: '.$filename.' :: '.$file_type);
                        //$path_parts = pathinfo($filename);
    
                        // Obtener el nombre del archivo sin la extensión
                        //$file_name_part = $path_parts['filename'];
                        
                        // Obtener la extensión del archivo
                        //$file_extension_part = $path_parts['extension'];
                        //$hash = substr(md5(uniqid($filename, true)), 0, 8);
                        //error_log('functions.php::add_attachment::$uploaded_file::file_name::file_extension'.$file_name.'-'.$file_extension);
                        //$file_name=$path_parts['filename'] .'_'. $hash. '.' . $path_parts['extension'];
                        //error_log('functions.php::add_attachment::$uploaded_file::file_name:: '.$file_name);
                        $uploaded_file = array(
                            //'name' => $file_name,
                            'name' => $filename,
                            'type' => $file_type,
                            'tmp_name' => $file_tmp,
                            'size' => $file_size,
                            'error' => 0
                        );
                        //$upload_overrides = array('test_form' => false);
                        error_log('functions.php::add_attachment::$uploaded_file::'.json_encode($uploaded_file));
                        $attachment_id = bp_media_handle_sideload($uploaded_file);
                        //$attachment_id = wp_handle_upload($uploaded_file, $upload_overrides);
                        //error_log('functions.php::add_attachment::$attachment_id::'.$attachment_id);
                        //return;

                        if ( is_wp_error( $attachment_id ) ) {
                            // Obtener el código de error
                            $error_code = $attachment_id->get_error_code();
                            
                            // Obtener el mensaje de error
                            $error_message = $attachment_id->get_error_message();
                            
                            // Obtener datos adicionales del error, si existen
                            $error_data = $attachment_id->get_error_data();
                            
                            // Mostrar los detalles del error
                            //echo 'Error code: ' . esc_html( $error_code ) . '<br>';
                            //error_log('functions.php::add_attachment::$Error code:::'.$error_code);
                            //echo 'Error message: ' . esc_html( $error_message ) . '<br>';
                            error_log('functions.php::add_attachment::$Error message:::'.$error_message);
                            if ( ! empty( $error_data ) ) {
                                //echo 'Error data: ' . print_r( $error_data, true ) . '<br>';
                                error_log('functions.php::add_attachment::$Error data:::'.$error_data);
                            }
                            //$path_parts = pathinfo($filename);
                            //$hash = substr(md5(uniqid($filename, true)), 0, 8);
                            
                            //$new_tmp_file = generate_new_tmp_file($file_tmp, $hash);

                            /*$uploaded_file = array(
                                'name' => $path_parts['filename'] .'_'. $hash. '.' . $path_parts['extension'],
                                'type' => $file_type,
                                'tmp_name' => $new_tmp_file,
                                'size' => $file_size,
                                'error' => 0
                            );*/
                            //$upload_overrides = array('test_form' => false);
                            //error_log('functions.php::add_attachment::$uploaded_file::'.json_encode($uploaded_file));
                            //$attachment_id = bp_media_handle_sideload($uploaded_file);

                            //return;
                        }
                        
                        error_log('functions.php::add_attachment::$attachment_id::'.$attachment_id);
                        
                        if(!is_wp_error($attachment_id)){
                            update_post_meta( $attachment_id, 'bp_media_upload', 1 );
                            update_post_meta( $attachment_id, 'bp_media_saved', 1 );
                            update_post_meta( $attachment_id, 'bp_media_parent_activity_id', $_activity_id );
                            array_push($_attachment_ids,$attachment_id);
                        }
                    }
                }
            //}
        }

    }else{
        error_log('functions.php::_FILES::EMPTY');
    }
    return $_attachment_ids;
}

function clone_files_array($_files) {
    $file_upload_field_id = 10; // ID del campo de carga de archivos
    $cloned_files = $_files;

    if (isset($_files['wpforms']['name']['fields'][$file_upload_field_id])) {
        foreach ($_files['wpforms']['name']['fields'][$file_upload_field_id] as $key => $filename) {
            if (!empty($filename)) {
                $info = pathinfo($filename);
                $hash = substr(md5(uniqid($filename, true)), 0, 8);
                $new_filename = $info['filename'] . '_' . $hash . '.' . $info['extension'];
                
                $cloned_files['wpforms']['name']['fields'][$file_upload_field_id][$key] = $new_filename;
                
                // Clonar el archivo temporal
                $orig_tmp_name = $_files['wpforms']['tmp_name']['fields'][$file_upload_field_id][$key];
                $new_tmp_name = '/tmp/' . $hash;
                copy($orig_tmp_name, $new_tmp_name);
                
                $cloned_files['wpforms']['tmp_name']['fields'][$file_upload_field_id][$key] = $new_tmp_name;
            }
        }
    }

    return $cloned_files;
}


function generate_new_tmp_file($original_tmp_file, $new_filename) {
    
    $upload_dir = wp_upload_dir();
    error_log('functions.php::generate_new_tmp_file::$upload_dir::'.$upload_dir);
    //$new_tmp_file = $upload_dir['basedir'] . '/tmp/' . $new_filename;
    $new_tmp_file = '/tmp/' . $new_filename;
    
    if (copy($original_tmp_file, $new_tmp_file)) {
        return $new_tmp_file;
    }
    
    return false;
}


//privacy=[onlyme,loggedin,public]

function add_activities_attachment_meta($_activity_id,$_attachment_id, $_privacy, $_order){
    $sub_activity_args = array(
        'user_id'       => bp_loggedin_user_id(),
        'hide_sitewide' => true,
        'privacy'       => 'media',
        'type'          => 'activity_update',
        'component'     => '',
        'recorded_time'         => '',
        'secondarty_item_id'    => '',
        'primary_link'          => '',
        'action'                => '',
    );
    $activity = new BP_Activity_Activity( $_activity_id );
    //error_log('functions.php::add_activities_attachment_meta::$activity::'.json_encode($activity));
    $media_args = array(
        'attachment_id' => $_attachment_id,
        'user_id'       => bp_loggedin_user_id(),
        'title'         => get_the_title( $_attachment_id ),
        'album_id'      => 0,
        'message_id'    => 0,
        'privacy'       => $_privacy,
        'menu_order'    => $_order,
        'group_id'      => '',
        'activity_id'   => '',
    );

    $sub_activity_args['recorded_time'] = $activity->date_recorded;
    $sub_activity_args['secondarty_item_id']  = $activity->id;
    $sub_activity_args['primary_link']   = $activity->primary_link;
    $sub_activity_args['action']   = $activity->action;
    $sub_activity_args['component']   = $activity->component;
    $sub_activity_args['item_id']   = $activity->item_id;

    if ( 'groups' == $activity->component ) {
        $media_args['group_id'] = $activity->item_id;
        //$sub_activity_args['component'] = buddypress()->groups->id;        
    }

    /*if ( ! empty( $activity_id ) ) {*/

        //$activity = new BP_Activity_Activity( $activity_id );
        //error_log('functions.php::_FILES::$activity::'.json_encode($activity));
    /*
        if ( ! empty( $activity->id ) ) {
        */          

     /*   }
    }*/
    error_log('functions.php::add_activities_attachment_meta::$sub_activity_args::'.json_encode($sub_activity_args));
    // make an activity for the media
    $sub_activity_id = bp_activity_add( $sub_activity_args );

    if ( $sub_activity_id ) {
        error_log('functions.php::_FILES::$sub_activity_id::'.$sub_activity_id);
        // update activity meta
        bp_activity_update_meta( $sub_activity_id, 'bp_media_activity', '1' );
        $media_args['activity_id'] = $sub_activity_id;
    }

    $media_id = bp_media_add( $media_args );

    if ( ! empty( $media_id )  ) {
        error_log('functions.php::_FILES::$media_id::'.$media_id);

        update_post_meta( $_attachment_id, 'bp_media_id', $media_id );
        update_post_meta( $_attachment_id, 'bp_media_activity_id', $media_args['activity_id'] );

        //bp_activity_update_meta( $sub_activity_id, 'bp_media_activity', 1 );
        bp_activity_update_meta( $sub_activity_id, 'bp_media_id', $media_id );
    }
    return $media_id;
}


function custom_add_offer_tab() {
    global $bp;

    bp_core_new_nav_item(array(
        'name'                => 'Ofertas', // Nombre del tab
        'slug'                => 'offer', // Slug del tab
        'screen_function'     => 'custom_offer_tab_screen', // Función de callback
        'position'            => 30, // Posición en el menú de navegación
        'parent_url'          => bp_loggedin_user_domain() . '/offer/', // URL del tab
        'parent_slug'         => $bp->profile->slug, // Slug del padre
        'default_subnav_slug' => 'offer', // Slug del subnav por defecto
    ));
}
add_action('bp_setup_nav', 'custom_add_offer_tab', 100);

function custom_offer_tab_screen() {
    add_action('bp_template_content', 'custom_offer_tab_content');
    bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
}

function custom_offer_tab_content() {
    // Obtener la página con slug 'offer'
    $page = get_page_by_path('offer');

    if ($page) {
        // Mostrar el contenido de la página
        echo apply_filters('the_content', $page->post_content);
    } else {
        echo '<p>Página no encontrada.</p>';
    }
}

/*

function custom_redirect_to_activity() {
    // Asegurarse de que estamos en el perfil de un usuario
    if (bp_is_user() && !bp_is_user_activity()) {
        // Obtener la URL del perfil del usuario
        $activity_link = bp_loggedin_user_domain() . BP_ACTIVITY_SLUG . '/';
		  error_log('custom_redirect_to_activity: ' . $post_id);
        // Redirigir a la página de actividades
        wp_redirect($activity_link);
        exit();
    }
}
add_action('bp_template_redirect', 'custom_redirect_to_activity');
*/

// Función para recuperar los grupos del usuario autenticado
function custom_user_groups_shortcode() {
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $groups = groups_get_user_groups($user_id);
        
        if (!empty($groups['groups'])) {
            $output = '<ul>';
            foreach ($groups['groups'] as $group_id) {
                $group = groups_get_group(array('group_id' => $group_id));
                $output .= '<li>' . esc_html($group->name) . '</li>';
            }
            $output .= '</ul>';
        } else {
            $output = '<p>No perteneces a ningún grupo.</p>';
        }
    } else {
        $output = '<p>Debes estar autenticado para ver tus grupos.</p>';
    }

    return $output;
}
add_shortcode('user_groups', 'custom_user_groups_shortcode');

// Función para recuperar los grupos del usuario autenticado
function custom_user_groups_feeds_shortcode() {
    if (is_user_logged_in()) {
		  $form_id=139;
		  $field_id=9;
        $user_id = get_current_user_id();
        $groups = groups_get_user_groups($user_id);
        $idx=1;
        $output = '<ul id="wpforms-'.$form_id.'-field_'.$field_id.'">';
		  $output .= '<li class="choice-'.$idx.' depth-1">';
		  $output .= '<input type="checkbox" id="wpforms-'.$form_id.'-field_'.$field_id.'_'.$idx.'" name="wpforms[fields]['.$field_id.'][]" value="0" aria-errormessage="wpforms-'.$form_id.'-field_'.$field_id.'_'.$idx.'-error">';
		  $output .= '<label class="wpforms-field-label-inline" for="wpforms-'.$form_id.'-field_'.$field_id.'_'.$idx.'">Muro</label>';
		  $output .= '</li>';
		  $output .= '<div>Grupos</div>';
        if (!empty($groups['groups'])) {
           
            foreach ($groups['groups'] as $group_id) {
            	 $idx+=1;
                $group = groups_get_group(array('group_id' => $group_id));
                //$output .= '<li>' . esc_html($group->name) . '</li>';
		  			 $output .= '<li class="choice-'.$idx.' depth-1">';
		  			 $output .= '<input type="checkbox" id="wpforms-'.$form_id.'-field_'.$field_id.'_'.$idx.'" name="wpforms[fields]['.$field_id.'][]" value="'.$group_id.'" aria-errormessage="wpforms-'.$form_id.'-field_'.$field_id.'_'.$idx.'-error">';
		  			 $output .= '<label class="wpforms-field-label-inline" for="wpforms-'.$form_id.'-field_'.$field_id.'_'.$idx.'">' . esc_html($group->name) . '</label>';
		  			 $output .= '</li>';                
            }
        } /*else {
            $output = '<p>No perteneces a ningún grupo.</p>';
        }*/
        $output .= '</ul>';
        if (empty($groups['groups'])) {
        		$output = '<p>No perteneces a ningún grupo.</p>';
        }
    } else {
        $output = '<p>Debes estar autenticado para ver tus grupos.</p>';
    }

    return $output;
}
add_shortcode('user_groups_feeds', 'custom_user_groups_feeds_shortcode');

// Personalizar mensaje de error para el tamaño máximo de archivo
add_filter( 'wpforms_upload_file_error_message', function( $message, $form_data, $field_id ) {
	 error_log('function::wpforms_upload_file_error_message::');	 
    $message = 'El tamaño del archivo supera el límite permitido de ' . wpforms_get_upload_max_filesize( $form_data['fields'][ $field_id ]['max_file_size'] ) . ' MB.';
    return $message;
}, 10, 3 );

//Ocultar barra de administración
add_action('after_setup_theme', 'hide_admin_bar');

function hide_admin_bar() {
    //show_admin_bar(false);
    //add_filter( 'show_admin_bar', '__return_false' );
    //error_log("functions.php::hide_admin_bar::is_admin::".is_admin());
    //error_log("functions.php::hide_admin_bar::manage_options::".current_user_can('manage_options'));
    //error_log("functions.php::hide_admin_bar::administrator::".current_user_can('administrator'));
    //error_log("functions.php::hide_admin_bar::is_user_logged_in::".is_user_logged_in());
    if(!is_user_logged_in() OR ( !current_user_can('administrator') AND !is_admin()) ){
        error_log("functions.php::hide_admin_bar::is_user_logged_in::NOT::");
        //show_admin_bar(false);
        //add_filter( 'show_admin_bar', '__return_false' );
        add_filter('show_admin_bar', '__return_false');
        add_filter('wp_admin_bar_class', '__return_false');
        add_action('wp_head', function() {
            echo '<style type="text/css">
                #wpadminbar { display:none !important; }
                html { margin-top: 0 !important; }
            </style>';
        });        
    }
    /*
    if (!is_admin()) { 
        add_filter( 'show_admin_bar', '__return_false' );
        show_admin_bar(false);
    }*/
}

?>