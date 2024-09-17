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
  //load_theme_textdomain( 'buddyboss-theme', get_stylesheet_directory() . '/languages' );

  // Translate text from the CHILD theme only.
  // Change 'buddyboss-theme' instances in all child theme files to 'buddyboss-theme-child'.
  load_theme_textdomain( 'buddyboss-theme-child', get_stylesheet_directory() . '/languages' );

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


add_action('wpforms_process', 'custom_wpforms_validation', 10, 3);

function custom_wpforms_validation($fields, $entry, $form_data) {

    $form_id = 139; // Reemplaza con tu ID de formulario

    if ($form_data['id'] != $form_id) {
        return;
    }
    //error_log("Contenido de \$fields: " . print_r($fields, true));

    $errors = [];

    // Itera a través de todos los campos del formulario
    foreach ($fields as $field) {
        $field_id = $field['id'];
        $field_value = $field['value'];

        // Ejemplo de validación: campo requerido
        //if ($form_data['fields'][$field_id]['required'] == '1' && empty($field_value)) {
        if (empty($field_value)) {
            $errors[$field_id] = 'Debe realizar la captura de la información.';
        }
        // Ejemplo de validación: formato de email
        //if ($form_data['fields'][$field_id]['type'] == 'email' && !is_email($field_value)) {
            //$errors[$field_id] = 'Por favor, introduce un email válido.';
        //}
        // Aquí puedes añadir más validaciones personalizadas según tus necesidades
    }

    // Si hay errores, detenemos el proceso y mostramos los mensajes
    if (!empty($errors)) {
        wpforms()->process->errors[$form_data['id']] = $errors;
    }
}



add_action('wpforms_process_complete', 'custom_wpforms_process_complete', 10, 4);

function custom_wpforms_process_complete($fields, $entry, $form_data, $entry_id){ 
    //error_log(basename(__FILE__));    
    // Verificar el ID del formulario para asegurarse de que solo se procese el formulario específico
    $form_id = 139; // Reemplaza con tu ID de formulario
    $file_upload_field_id = 10;
    $count_files=0;
    $field_id_ = 4; //Nombre de la empresa

    if ($form_data['id'] != $form_id) {
        return;
    }

    $_files_ = $_FILES['wpforms']['name']['fields'][$file_upload_field_id];
    if (is_array($_files_)) {
        foreach ($_files_ as $_key_ => $_filename_) {
            if(strlen($_filename_)>0){
                $count_files ++;
            }
        }
    }
    
    $field_value = xprofile_get_field_data( $field_id_, get_current_user_id() );

    $content = '';
    $content .= sprintf('<strong>%s:</strong> <br>', sanitize_text_field($field_value));
    
    foreach ($fields as $field_id => $field) {
        $content .= sprintf('<strong>%s:</strong> %s<br>', sanitize_text_field($field['name']), sanitize_text_field($field['value']));
    }

    $places = array();
    if(isset($entry['fields']['9'])){
	    $places=$entry['fields']['9'];
    }else{
        array_push($places,0);
    }
    $thumbnails_data='';
    if (isset($entry['fields']['thumbnails']) && !empty($entry['fields']['thumbnails'])) {
        $thumbnails_data = json_decode($entry['fields']['thumbnails'], true);
    }

    $activity_ids=add_activities($places,$content);
    
    if(isset($activity_ids)){
        if(is_array($activity_ids) AND count($activity_ids)>0){
            $_ALL_FILES = array();
                        
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
                        $video_ids = array();
                        $media_done=0;
                        foreach($attachment_ids as $attachment){
                            $multimedia_id=add_activities_attachment_meta($activity_id,$attachment['attachment_id'],'public',$media_done,$attachment['file'],$thumbnails_data,$count_files);
                            if (str_starts_with($attachment['file']['type'], 'image')) {
                                array_push($media_ids,$multimedia_id);
                            }
                            if (str_starts_with($attachment['file']['type'], 'video')) {
                                array_push($video_ids,$multimedia_id);
                            }

                            $media_done ++;
                        }
                        if(count($media_ids)>0){
                            bp_activity_update_meta( $activity_id, 'bp_media_ids', implode( ',', $media_ids ) );
                        }
                        if(count($video_ids)>0){
                            bp_activity_update_meta( $activity_id, 'bp_video_ids', implode( ',', $video_ids ) );
                        }

                    }

                }
            }


        }
    }    
    
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
    //error_log(basename(__FILE__).'::add_activities::');
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
          
            //=============DUPLICA REGISTROS=============
            //bp_activity_add_meta( $activity_id, '_link_embed', '0' );
            //bp_activity_add_meta( $activity_id, '_link_preview_data', '' );

            array_push($_activity_ids,$activity_id);
        } else {
            // Log para depuración
            //error_log('Error al crear la actividad desde WPForms');
        }
        
    }
    error_log(basename(__FILE__).'::add_activities::_activity_ids'.json_encode($_activity_ids));
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
// path de carga imagenes wp-content/uploads/bb_medias/2024/
// path de carga videos   wp-content/uploads/bb_videos/
function add_attachment($_files_,$_activity_id){
    error_log(basename(__FILE__).'::add_attachment::');
    $_attachment_ids = array();
    $file_upload_field_id = 10; // ID del campo de carga de archivos
    if (isset($_files_['wpforms']['name']['fields'][$file_upload_field_id])) {
        $files = $_files_['wpforms']['name']['fields'][$file_upload_field_id];
        if (is_array($files)) {
            error_log(basename(__FILE__).'::add_attachment::files'.json_encode($files));
            foreach ($files as $key => $filename) {
                
                if(!empty($filename)){
                    $file_tmp = $_files_['wpforms']['tmp_name']['fields'][$file_upload_field_id][$key];                
                    $file_type = $_files_['wpforms']['type']['fields'][$file_upload_field_id][$key];
                    $file_size = $_files_['wpforms']['size']['fields'][$file_upload_field_id][$key];

                    $uploaded_file = array(
                        //'name' => $file_name,
                        'name' => $filename,
                        'type' => $file_type,
                        'tmp_name' => $file_tmp,
                        'size' => $file_size,
                        'error' => 0
                    );

                    $attachment_id = '';

                    error_log(basename(__FILE__).'::add_attachment::$uploaded_file::'.json_encode($uploaded_file));
                    if (str_starts_with($file_type, 'video')) {
                        //error_log(basename(__FILE__).'::add_attachment::$uploaded_file::VIDEO');
                        $attachment_id = bp_video_handle_sideload($uploaded_file);
                    }
                    if (str_starts_with($file_type, 'image')) {
                        //error_log(basename(__FILE__).'::add_attachment::$uploaded_file::IMAGE');
                        $attachment_id = bp_media_handle_sideload($uploaded_file);
                    }

                    if ( is_wp_error( $attachment_id ) ) {
                        $error_code = $attachment_id->get_error_code();
                        $error_message = $attachment_id->get_error_message();
                        $error_data = $attachment_id->get_error_data();

                        //error_log('functions.php::add_attachment::$Error message:::'.$error_message);
                        if ( ! empty( $error_data ) ) {
                            //echo 'Error data: ' . print_r( $error_data, true ) . '<br>';
                            error_log('functions.php::add_attachment::$Error data:::'.$error_data);
                        }
                    }
                    
                    error_log(basename(__FILE__).'::add_attachment::$attachment_id::'.$attachment_id);
                    // POR VERIFICAR PORQUE SE ALMACENA DOS VECES
                    /*
                    if(!is_wp_error($attachment_id) ){
                        if (str_starts_with($file_type, 'image') && !empty($attachment_id)){
                            update_post_meta( $attachment_id, 'bp_media_upload', 1 );
                            update_post_meta( $attachment_id, 'bp_media_saved', 1 );
                            update_post_meta( $attachment_id, 'bp_media_parent_activity_id', $_activity_id );
                        }
                    }
                    */
                    array_push($_attachment_ids, array('attachment_id'=>$attachment_id,'file'=>$uploaded_file));
                }
            }
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
    //error_log('functions.php::generate_new_tmp_file::$upload_dir::'.$upload_dir);
    //$new_tmp_file = $upload_dir['basedir'] . '/tmp/' . $new_filename;
    $new_tmp_file = '/tmp/' . $new_filename;
    
    if (copy($original_tmp_file, $new_tmp_file)) {
        return $new_tmp_file;
    }
    
    return false;
}


//privacy=[onlyme,loggedin,public]

function add_activities_attachment_meta($_activity_id,$_attachment_id, $_privacy, $_order,$_file,$_thumbnails_data,$_count_files){
    //error_log(basename(__FILE__).'::add_activities_attachment_meta::');
    
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

    if($_count_files>1){
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
    }

    $activity = new BP_Activity_Activity( $_activity_id );

    if($_count_files>1){
        $sub_activity_args['recorded_time'] = $activity->date_recorded;
        $sub_activity_args['secondarty_item_id']  = $activity->id;
        $sub_activity_args['primary_link']   = $activity->primary_link;
        $sub_activity_args['action']   = $activity->action;
        $sub_activity_args['component']   = $activity->component;
        $sub_activity_args['item_id']   = $activity->item_id;

        //error_log(basename(__FILE__).'::add_activities_attachment_meta::$sub_activity_args::'.json_encode($sub_activity_args));
    
        $sub_activity_id = bp_activity_add( $sub_activity_args );
    }
    

    if ( $sub_activity_id ) {
        //error_log(basename(__FILE__).'::_FILES::$sub_activity_id::'.$sub_activity_id);
        // update activity meta
        bp_activity_update_meta( $sub_activity_id, 'bp_media_activity', '1' );
        $media_args['activity_id'] = $sub_activity_id;
    }
    

    if($_count_files==1){
        //bp_activity_update_meta( $_activity_id, 'bp_media_activity', '1' );
        $media_args['activity_id'] = $_activity_id;
    }


    if ( 'groups' == $activity->component ) {
        $media_args['group_id'] = $activity->item_id;        
    }


    $media_id='';

    if (str_starts_with($_file['type'], 'video')) {
        $media_id = bp_video_add( $media_args );
    }


    if (str_starts_with($_file['type'], 'image')) {
        $media_id = bp_media_add( $media_args );
    }
    
    if ( ! empty( $media_id )  ) {

        if (str_starts_with($_file['type'], 'image')) {
            update_post_meta( $_attachment_id, 'bp_media_id', $media_id );
            update_post_meta( $_attachment_id, 'bp_media_parent_activity_id', $media_args['activity_id'] );
            if($_count_files>1){
                bp_activity_update_meta( $sub_activity_id, 'bp_media_ids', $media_id );
            }else{
                bp_activity_update_meta( $_activity_id, 'bp_media_ids', $media_id );
            }
            
        }

        if (str_starts_with($_file['type'], 'video')) {            
            update_post_meta( $_attachment_id, 'bp_video_parent_activity_id', $media_args['activity_id'] );
            
            update_post_meta( $_attachment_id, 'bb_media_draft', 1 );
            update_post_meta( $_attachment_id, 'bp_video_upload', 1 );

            if($_count_files>1){
                bp_activity_add_meta( $sub_activity_id, 'bp_video_ids', $media_id );
            }else{
                bp_activity_add_meta( $_activity_id, 'bp_video_ids', $media_id );
            }            

            $attachment_url    = bb_video_get_symlink( $media_id );
            

            $info = pathinfo($_file['name']);
            $thumbnail=search_by_filename($_thumbnails_data,$_file['name']);

            $_media=array(
                'id'          => $_attachment_id,
                //'id'          => $media_id,
                'thumb'       => '',
                'url'         => esc_url( untrailingslashit( $attachment_url ) ),
                'name'        => esc_attr( $info['filename'] ),
                'ext'         => esc_attr( $info['extension'] ),
                //'vid_msg_url' => esc_url( untrailingslashit( $video_message_url ) ),
                'vid_msg_url' => "",
                "saved"       => false,
                "menu_order"  => $_order,
                "js_preview"  => $thumbnail['thumbnail'],
            );

            $preview_image=bp_video_preview_image_by_js($_media );
            //$preview_image=bp_video_preview_image_by_js($media_id);
            $generate_thumb=bp_video_add_generate_thumb_background_process( $media_id );
        }

    }
    return $media_id;
}

function search_by_filename($data_array, $search_filename) {
    foreach ($data_array as $item) {
        if ($item['filename'] === $search_filename) {
            return $item; // Retorna el objeto encontrado
        }
    }
    return null; // Retorna null si no se encuentra
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
if(is_user_logged_in()){
    add_action('bp_setup_nav', 'custom_add_offer_tab', 100);
}


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
		  $output .= '<label class="wpforms-field-label-inline" for="wpforms-'.$form_id.'-field_'.$field_id.'_'.$idx.'">Muro público</label>';
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
    if(!is_user_logged_in() OR ( !current_user_can('administrator') AND !is_admin()) ){
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

function add_private_message_button() {
    if ( ! function_exists( 'bp_is_active' ) || ! bp_is_active( 'messages' ) ) {
        return;
    }

    $current_user_id = get_current_user_id();
    $author_id = bp_get_activity_user_id();

    if ( $current_user_id == $author_id ) {
        return;
    }

    $message_link = '';
    if ( function_exists( 'bp_get_messages_compose_url' ) ) {
        $message_link = bp_get_messages_compose_url();
    } elseif ( function_exists( 'bp_core_get_user_domain' ) && function_exists( 'bp_get_messages_slug' ) ) {
        //$author_profile_url = bp_core_get_user_domain( $author_id );
        //$message_link = $author_profile_url . bp_get_messages_slug() . '/compose/';
        $current_profile_url = bp_core_get_user_domain( $current_user_id );
        $message_link = $current_profile_url . bp_get_messages_slug() . '/compose/';

    }

    if ( ! empty( $message_link ) ) {
        $message_link = add_query_arg( 'r', bp_core_get_username( $author_id ), $message_link );
        echo '<div class="generic-button main-button">';
        echo '<a href="' . esc_url( $message_link ) . '" class="button bp-primary-action private-message-button" title="' . esc_attr__( 'Send Private Message', 'buddyboss-theme-child' ) . '">';
        echo '<span class="bp-screen-reader-text">' . esc_html__( 'Send Private Message', 'buddyboss-theme-child' ) . '</span>';
        echo '<span class="bb-icon bb-icon-envelope main-envelope" >' . esc_html__( 'Send Private Message', 'buddyboss-theme-child' ) . '</span>';
        echo '</a>';
        echo '</div>';
    }
}


function add_private_message_button_to_activity() {
    add_action( 'bp_activity_entry_meta', 'add_private_message_button', 20 );
}
if(is_user_logged_in()){
    add_action( 'bp_init', 'add_private_message_button_to_activity' );
}

function custom_styles_and_scripts() {
    //echo '<!-- Custom styles loaded -->'; // Esto debe aparecer en el código fuente HTML
    wp_enqueue_style( 'custom-style', get_stylesheet_directory_uri() . '/assets/css/custom-style.css', array(), '1.0.0', 'all' );
    //error_log( '96 ' . is_page( 96 ) . ' title ' .  get_the_title(get_the_ID()). ' -->'. is_page(get_the_title(get_the_ID())));
    if ( check_if_url_ends_with( 'offer') ) {
        //echo '<!-- Custom styles loaded -->'; // Esto debe aparecer en el código fuente HTML
        wp_enqueue_style( 'dropzone-5-style', 'https://unpkg.com/dropzone@5/dist/min/dropzone.min.css', array(), null, 'all' );
        wp_enqueue_script( 'dropzone-5-min','https://unpkg.com/dropzone@5/dist/min/dropzone.min.js', array(), null, true );
        wp_enqueue_script( 'custom-dropzone-script', get_stylesheet_directory_uri() . '/assets/js/custom-dropzone.js', array(), '1.0.0', 'all' );
    }
    if ( check_if_url_ends_with( 'register') ) {
        wp_enqueue_script( 'custom-register-script', get_stylesheet_directory_uri() . '/assets/js/custom-register.js', array(), '1.0.0', 'all' );
        wp_localize_script('custom-register-script', 'registerAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('generate_alias_nonce')
        ));
    }
}
add_action( 'wp_enqueue_scripts', 'custom_styles_and_scripts' );

function check_if_url_ends_with($_segment) {
    // Obtener la URL actual
    $current_url = home_url( add_query_arg( null, null ) );

    // Analizar la URL para obtener los segmentos de la ruta
    $parsed_url = wp_parse_url( $current_url );
    $path = $parsed_url['path'];

    // Dividir la ruta en segmentos
    $segments = explode('/', trim($path, '/'));

    // Verificar si el último segmento es 'offer'
    if ( end($segments) === $_segment ) {
        return true;
    }
    return false;
}

function custom_content_before_news_feed_title() {
    // Contenido personalizado que se insertará antes del título "News Feed"
    echo '<header class="entry-header"><h1 class="entry-title">Plataforma de empresas circulares</h1></header>';
    echo '<p>En esta Plataforma de Economía Circular podrás ofrecer y adquirir excedentes de materiales y productos para evitar su desperdicio y maximizar su aprovechamiento. Podrás vincularte con otras empresas que, como la tuya, están interesadas en aprovechar oportunidades económicas al mismo tiempo que contribuyen a cuidar el medio ambiente.</p>';
    echo '<p>Si aún no estás registrado, ¡Súmate, conoce y aprovecha los beneficios que esta plataforma puede traer para tu empresa y el medio ambiente!</p>';
    echo '<p>';
    echo 'Beneficios para tu empresa y el medio ambiente';
    echo '<ul>';
    echo '<li> Aprovechamiento de excedentes o remanentes de productos y materiales </li>';
    echo '<li> Ahorros o nuevas materias primas </li>';
    echo '<li> Apertura de nuevos mercados </li>';
    echo '<li> Diversificación de proveedores </li>';
    echo '<li> Crecimiento comercial sustentable </li>';
    echo '<li> Innovación técnica y operativa </li>';
    echo '<li> Mayor competitividad comercial </li>';
    echo '<li> Fortalecimiento y creación  de redes de apoyo </li>';
    echo '<li> Cuidado del medio ambiente </li>';
    echo '<li> Ahorros en costos por disposición final de residuos (productos o materiales excedentes o remanentes) </li>';
    echo '</ul>';
    echo '</p>';
    ?>
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var entryTitle = document.querySelector('.entry-title');
        //console.log(logoContainer);
        if(entryTitle){
            if(entryTitle.innerText==='News Feed')entryTitle.innerText='';
        }
    });
    </script>
    <?php   
}
if(!is_user_logged_in()){
    add_action( 'bp_before_directory_activity_content', 'custom_content_before_news_feed_title' );
}

function add_home_button_script() {
    //$home_url = home_url('/');
    $home_url = bp_get_root_domain();
    ?>
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var logoContainer = document.querySelector('.site-title a');
        if (logoContainer) {
            var homeButton = document.createElement('a');
            homeButton.href = '<?php echo esc_js($home_url); ?>';
            homeButton.className = 'header-home-button';
            homeButton.textContent = 'Inicio';
            logoContainer.parentNode.insertBefore(homeButton, logoContainer.nextSibling);
        }
    });
    </script>
    <?php
}
add_action( 'buddyboss_theme_header', 'add_home_button_script', 20 );

function add_alias_field() {
    ?>
    <div class="editfield">
        <input type="text" name="alias_is_unique" id="alias_is_unique" value="0" style="display:none;">
    </div>
    <?php
}
add_action('bp_signup_profile_fields', 'add_alias_field');

function check_alias_uniqueness() {
    check_ajax_referer('generate_alias_nonce', 'nonce');

    $base_alias = sanitize_user($_POST['alias']);
    $alias = $base_alias;
    $counter = 1;
  
    while (username_exists($alias)) {
        $alias = $base_alias . $counter;
        $counter++;
    }
    
    wp_send_json_success(array('alias' => $alias));
}
add_action('wp_ajax_check_alias_uniqueness', 'check_alias_uniqueness');
add_action('wp_ajax_nopriv_check_alias_uniqueness', 'check_alias_uniqueness');
?>