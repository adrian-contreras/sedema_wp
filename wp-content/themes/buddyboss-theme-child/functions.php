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
        if($field['name']!='record'){
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
    }

    // Si hay errores, detenemos el proceso y mostramos los mensajes
    if (!empty($errors)) {
        wpforms()->process->errors[$form_data['id']] = $errors;
    }
}

function delete_record($record_){
    $record=load_record_detail($record_);
    if ( isset($record) && ! empty( $record ) ) {
        //error_log(basename(__FILE__).'::custom_wpforms_process_complete::'.print_r($record,true));
        //tomado de wp-content/plugins/buddyboss-platform/bp-templates/bp-nouveau/includes/activity/ajax.php :: function bp_nouveau_ajax_delete_activity()
        $deleted=0;
        $record_id=$record[0]->id;
        $activities_id=json_decode($record[0]->activities_id);
        foreach ($activities_id as $id) {
            $activity = new BP_Activity_Activity( (int) $id );
            //error_log(basename(__FILE__).'::custom_wpforms_process_complete::id:: '.$id.' :: can_delete:: '.bp_activity_user_can_delete( $activity ).':: delete_comment :: '.bp_activity_delete_comment( $activity->item_id, $activity->id ));

            // Check access.
            if ( ! bp_activity_user_can_delete( $activity ) ) {
                //wpforms()->process->errors[$form_id]['header'] = 'Su solicitud no puede ser procesada. Por favor comuniquese con el administrador de la plataforma';
                return array(
                    'success'   => false,
                    'message' => 'Su solicitud no puede ser procesada. Por favor comuniquese con el administrador de la plataforma',
                );
            }
        }
        foreach ($activities_id as $id) {
            $activity = new BP_Activity_Activity( (int) $id );


            // This action is documented in bp-activity/bp-activity-actions.php 
            do_action( 'bp_activity_before_action_delete_activity', $activity->id, $activity->user_id );
            // Deleting an activity.
            if ( ! bp_activity_delete(
                array(
                    'id'      => $activity->id,
                    'user_id' => $activity->user_id,
                )
            ) ) {
                //wpforms()->process->errors[$form_id]['header'] = 'Hubo un problema al procesar la petición. Por favor, revise su información e intente de nuevo.';
                return array(
                    'success'   => false,
                    'message' => 'Hubo un problema al procesar la petición. Por favor, revise su información e intente de nuevo.',
                );
            }
            // This action is documented in bp-activity/bp-activity-actions.php 
            do_action( 'bp_activity_action_delete_activity', $activity->id, $activity->user_id );

            //$activity_html      = '';
            $parent_activity_id = 0;
            if ( isset( $activity->secondary_item_id ) && ! empty( $activity->secondary_item_id ) ) {
                $parent_activity_id = $activity->secondary_item_id;
                ob_start();
                if ( bp_has_activities(
                    array(
                        'include' => $parent_activity_id,
                    )
                ) ) {
                    while ( bp_activities() ) {
                        bp_the_activity();
                        bp_get_template_part( 'activity/entry' );
                    }
                }
                $activity_html = ob_get_contents();
                ob_end_clean();
                //$response['activity']           = $activity_html;
                //$response['parent_activity_id'] = $parent_activity_id;
            }
            $deleted++;
        }

        $date = date("Y-m-d H:i:s");
        $status = 'deleted';
        if($deleted>0){
            $data=array(
                "data" => array(
                    "id"=>$record_id,
                    'date_modified' => $date,
                    'status' => $status
                ),
                "types" => array('%s', '%s')
            );
            update_activities($data);
        }
    
    }else{
        //wpforms()->process->errors[$form_id]['header'] = 'Hubo un problema al procesar la petición. Por favor, revise su información e intente de nuevo.';
        return array(
            'success'   => false,
            'message' => 'Hubo un problema al procesar la petición. Por favor, revise su información e intente de nuevo.',
        );
    }
    
    return array(
        'success'   => true,
        'message' => '',
    );

}

add_action('wpforms_process_complete', 'custom_wpforms_process_complete', 10, 4);

function custom_wpforms_process_complete($fields, $entry, $form_data, $entry_id){ 
    // Verificar el ID del formulario para asegurarse de que solo se procese el formulario específico
    $form_id = 139; // Reemplaza con tu ID de formulario
    $file_upload_field_id = 10;
    $count_files=0;
    //$field_id_ = 4; //Nombre de la empresa



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
    
    //$field_value = xprofile_get_field_data( $field_id_, get_current_user_id() );
    
    $content = '';
    //$content .= sprintf('<strong>%s</strong> <br>', sanitize_text_field($field_value));
    $content_data = array();
        foreach ($fields as $field_id => $field) {
        if($field['name']!='record'){
            $content .= sprintf('<strong>%s:</strong> %s<br>', sanitize_text_field($field['name']), sanitize_text_field($field['value']));
            //metada JSON wp_ec_activities
            array_push($content_data,array('name'=>sanitize_text_field($field['name']),'value'=>sanitize_text_field($field['value'])));
        }else{
            if(!empty($field['value'])){
                //error_log(basename(__FILE__).'::custom_wpforms_process_complete::'.print_r($record,true));
                $result=delete_record($field['value']);
                if($result['success']==false){
                    wpforms()->process->errors[$form_id]['header'] = $result['message'];
                    return;
                }

            }
        }
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
    
    //error_log(basename(__FILE__).'::custom_wpforms_process_complete:: activity_ids ::'.json_encode($activity_ids));

    $activity_attachment = array();
    
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

                $attachment_ids=add_attachment(array_pop($_ALL_FILES),$activity_id);
                //error_log(basename(__FILE__).'::custom_wpforms_process_complete:: attachment_ids ::'.json_encode($attachment_ids));

                $multimedia_data = array();

                

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
                            //metada JSON wp_ec_activities
                            array_push($multimedia_data,array('attachment_id'=>$attachment['attachment_id'],'multimedia_id'=>$multimedia_id,'info'=>($attachment['file'])));
                        }

                        if(count($media_ids)>0){
                            bp_activity_update_meta( $activity_id, 'bp_media_ids', implode( ',', $media_ids ) );
                        }
                        if(count($video_ids)>0){
                            bp_activity_update_meta( $activity_id, 'bp_video_ids', implode( ',', $video_ids ) );
                        }

                    }

                }
                array_push($activity_attachment,array('activity_id'=>$activity_id,'attachment_ids'=>($attachment_ids),'multimedia'=>($multimedia_data)));
                
            }

        }
    }

    save_info(get_current_user_id(),json_encode($activity_ids),json_encode($places), json_encode($content), json_encode($content_data),json_encode($activity_attachment));    
}


function clone_activity_media($old_activity_id, $new_activity_id) {
    // Obtener los IDs de archivos multimedia de la actividad original (ejemplo con una clave específica)
    global $wpdb;
    $medias = bp_activity_get_meta($old_activity_id, 'bp_media_ids');
    error_log(basename(__FILE__).'::clone_activity_media:: medias ::'.json_encode($medias));

    if (!empty($medias)) {
        $media_ids=explode( ',', $medias );
        foreach ($media_ids as $media_id) {
            // Obtener el attachment_id de wp_bp_media
            $attachment_id = $wpdb->get_var($wpdb->prepare(
                "SELECT attachment_id FROM {$wpdb->prefix}bp_media WHERE id = %d",
                $media_id
            ));

            if ($attachment_id) {
                // Clonar el archivo multimedia usando el attachment_id obtenido
                $new_media_id = clone_media($attachment_id);
                error_log(basename(__FILE__).'::clone_activity_media:: media_ids:: new_media_id::'.$new_media_id);
                // Asignar el nuevo archivo multimedia a la nueva actividad
                bp_activity_update_meta($new_activity_id, 'bp_media_ids', $new_media_id);
            }
        }
    }
    $videos = bp_activity_get_meta($old_activity_id, 'bp_video_ids');
    error_log(basename(__FILE__).'::clone_activity_media:: video_ids ::'.json_encode($videos));
    if (!empty($videos)) {
        $video_ids=explode( ',', $videos );
        foreach ($video_ids as $video_id) {
            // Obtener el attachment_id de wp_bp_media
            $attachment_id = $wpdb->get_var($wpdb->prepare(
                "SELECT attachment_id FROM {$wpdb->prefix}bp_media WHERE id = %d",
                $video_id
            ));

            if ($attachment_id) {
                // Clonar el archivo multimedia usando el attachment_id obtenido
                $new_media_id = clone_media($attachment_id);
                error_log(basename(__FILE__).'::clone_activity_media:: video_ids:: new_media_id::'.$new_media_id);

                // Asignar el nuevo archivo multimedia a la nueva actividad
                bp_activity_update_meta($new_activity_id, 'bp_video_ids', $new_media_id);
            }
        }
    }

}

function clone_media($media_id) {
    // Obtener el archivo multimedia original
    $media = get_post($media_id);

    if (!$media) {
        return false; // El archivo multimedia no existe
    }

    // Clonar el archivo de la biblioteca de medios
    $new_media_id = wp_insert_attachment(array(
        'post_mime_type' => $media->post_mime_type,
        'post_title'     => $media->post_title,
        'post_content'   => $media->post_content,
        'post_status'    => 'inherit'
    ), get_attached_file($media_id));

    // Copiar los metadatos
    $media_meta = wp_get_attachment_metadata($media_id);
    wp_update_attachment_metadata($new_media_id, $media_meta);
    
    error_log(basename(__FILE__).'::clone_media:: media_meta::'.json_encode($media_meta).'new_media_id::'.json_encode($new_media_id));
    
    return $new_media_id;
}



function save_info($user_id, $activities_id, $group_id, $content,$content_data, $multimedia) {
    global $wpdb;
    $date = date("Y-m-d H:i:s");
    $status = 'active';
    $wpdb->insert(
        $wpdb->prefix . 'ec_activities',
        //'wp_ec_activities',
        array(
            'user_id' => $user_id,
            'activities_id' => $activities_id,
            'group_id' => $group_id,
            'content' => $content,
            'content_data' => $content_data,
            'multimedia' => $multimedia,
            'date_created' => $date,
            'status' => $status
        ),
        array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );
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
    //error_log(basename(__FILE__).'::add_activities::_activity_ids'.json_encode($_activity_ids));
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
    //error_log(basename(__FILE__).'::add_attachment::_files::'.print_r($_files_,true));
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
                        'error' => 0,
                        'thumbnail' =>'',
                    );

                    $attachment_id = '';

                    //error_log(basename(__FILE__).'::add_attachment::uploaded_file::'.json_encode($uploaded_file));
                    if (str_starts_with($file_type, 'video')) {
                        //error_log(basename(__FILE__).'::add_attachment::$uploaded_file::VIDEO');
                        $thumbnail_generated=generate_thumbnail($file_tmp);
                        $uploaded_file['thumbnail']=$thumbnail_generated;
                        //error_log(basename(__FILE__).'::add_attachment::$uploaded_file::VIDEO::thumbnail_generated::'.print_r($thumbnail_generated,true));

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
                    
                    //error_log(basename(__FILE__).'::add_attachment::$attachment_id::'.$attachment_id);
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
                $hash = substr(md5(uniqid($filename, true)), 0, 6);
                //$new_filename = $info['filename'] . '_' . $hash . '.' . $info['extension'];
                //$new_filename = sanitize_text_field($info['filename'] . ' ' . $key . '.' . $info['extension']);
                //$new_filename = sanitize_text_field($info['filename'] . '.' . $info['extension']);
                //$new_filename = $info['basename'];
                //$new_filename = ($info['filename'] . '.' . $info['extension']);
                $new_filename = $info['basename'];
                $cloned_files['wpforms']['name']['fields'][$file_upload_field_id][$key] = $new_filename;
                
                // Clonar el archivo temporal
                $orig_tmp_name = $_files['wpforms']['tmp_name']['fields'][$file_upload_field_id][$key];
                //$new_tmp_name = '/www-data-tmp/php' . $hash;
                $new_tmp_name = '/tmp/php' . $hash;
                //copy($orig_tmp_name, $new_tmp_name);
                @copy($orig_tmp_name, $new_tmp_name);
                //stream_copy($orig_tmp_name, $new_tmp_name);
                
                $info_new = pathinfo($new_tmp_name);
                //error_log(basename(__FILE__).'::clone_files_array:: pathinfo ::'.$info_new['dirname'] . ' '.$info_new['basename']);
                

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


function stream_copy($src, $dest)

{
    $fsrc = fopen($src,'r');
    $fdest = fopen($dest,'w+');
    $len = stream_copy_to_stream($fsrc,$fdest);
    fclose($fsrc);
    fclose($fdest);
    return $len;
}


function copyemz($file1,$file2){
    $contentx =@file_get_contents($file1);
    $openedfile = fopen($file2, "w");
    fwrite($openedfile, $contentx);
    fclose($openedfile);
    if ($contentx === FALSE) {
        $status=false;
    }else $status=true;
         
    return $status;

}


//privacy=[onlyme,loggedin,public]

function add_activities_attachment_meta($_activity_id,$_attachment_id, $_privacy, $_order,$_file,$_thumbnails_data,$_count_files){   
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
            'privacy'       => (str_starts_with($_file['type'], 'video')?'video':'media'),
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
        //$sub_activity_args['secondarty_item_id']  = $activity->id;
        $sub_activity_args['primary_link']   = $activity->primary_link;
        $sub_activity_args['action']   = $activity->action;
        $sub_activity_args['component']   = $activity->component;
        $sub_activity_args['item_id']   = $activity->item_id;

        //error_log(basename(__FILE__).'::add_activities_attachment_meta::$sub_activity_args::'.json_encode($sub_activity_args));
    
        $sub_activity_id = bp_activity_add( $sub_activity_args );
        
    }
    
    if($_count_files>1){
        if ( $sub_activity_id ) {
            //error_log(basename(__FILE__).'::_FILES::$sub_activity_id::'.$sub_activity_id);
            // update activity meta
            bp_activity_update_meta( $sub_activity_id, 'bp_media_activity', '1' );
            $media_args['activity_id'] = $sub_activity_id;
        }
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
            update_post_meta( $_attachment_id, 'bp_media_activity_id', $media_args['activity_id'] );
            update_post_meta( $_attachment_id, 'bp_media_parent_activity_id', $_activity_id );
            if($_count_files>1){
                bp_activity_update_meta( $sub_activity_id, 'bp_media_id', $media_id );
            }else{
                bp_activity_update_meta( $_activity_id, 'bp_media_id', $media_id );
            }
            update_post_meta( $_attachment_id, 'bb_media_draft', 1 );
        }

        if (str_starts_with($_file['type'], 'video')) {            
            update_post_meta( $_attachment_id, 'bp_video_activity_id', $media_args['activity_id'] );
            update_post_meta( $_attachment_id, 'bp_video_parent_activity_id', $_activity_id );
            
            
            update_post_meta( $_attachment_id, 'bp_video_upload', 1 );
            update_post_meta( $_attachment_id, 'bp_video_saved', '0' );

            if($_count_files>1){
                bp_activity_add_meta( $sub_activity_id, 'bp_video_id', $media_id );
            }else{
                bp_activity_add_meta( $_activity_id, 'bp_video_id', $media_id );
            }            

            $attachment_url    = bb_video_get_symlink( $media_id );
            

            $info = pathinfo($_file['name']);
            
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
                "js_preview"  => $_file['thumbnail'],
                //"js_preview"  => $thumbnail['thumbnail'],
                
            );

            $preview_image=bp_video_preview_image_by_js($_media );
            //$preview_image=bp_video_preview_image_by_js($media_id);
            //error_log(basename(__FILE__).'::add_activities_attachment_meta::preview_image:: '.$preview_image);
            $generate_thumb=bp_video_add_generate_thumb_background_process( $media_id );
            //error_log(basename(__FILE__).'::add_activities_attachment_meta::generate_thumb:: '.$generate_thumb);
        }

    }

    $sub_activity_args['id']  = $sub_activity_id;
    $sub_activity_args['secondarty_item_id']  = $_activity_id;
    
    bp_activity_add( $sub_activity_args );

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


function generate_thumbnail($video_file) {
    // Ruta al ejecutable de FFmpeg
    //$ffmpeg_path = '/usr/bin/ffmpeg';
    $ffmpeg_path = FFMPEG_PATH;

    // Obtener la duración del video
    //$duration = get_video_duration($video_file);

    // Generar la miniatura en el segundo 5 (o en el segundo medio del video)
    //$thumbnail_time = min(5, $duration / 2);
    $thumbnail_time = 1;

    // Comando de FFmpeg para generar la miniatura como flujo de datos
    //$command = $ffmpeg_path." -i '".$video_file."' -ss ".$thumbnail_time." -vframes 1 -f image2pipe -vcodec mjpeg -";
    $command = $ffmpeg_path." -i '".$video_file."' -ss ".$thumbnail_time." -vframes 1 -f image2pipe -vcodec png -";
    //error_log(basename(__FILE__).'::generate_thumbnail::command:: '.$command);
    // Ejecutar el comando de FFmpeg y capturar el flujo de salida
    $proc = popen($command, 'r');
    $thumbnail_data = stream_get_contents($proc);
    pclose($proc);
    //error_log(basename(__FILE__).'::generate_thumbnail::thumbnail_data:: '.$thumbnail_data);
    // Devolver el flujo de datos de la miniatura
    $base64_data = base64_encode($thumbnail_data);
    return 'data:image/png;base64,' . $base64_data;
    
}

function ocultar_barra_navegacion_buddyboss() {
    ?>
    <style type="text/css">       
        .actvity-head-bar, .main-navs, .bp-navs.bp-subnavs {
            display: none !important;
        }
    </style>
    <?php
}
add_action('wp_head', 'ocultar_barra_navegacion_buddyboss');

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

    //add_action( 'wp_head', 'custom_news_feed_title' );
}

function custom_styles_and_scripts() {
    //echo '<!-- Custom styles loaded -->'; // Esto debe aparecer en el código fuente HTML
    wp_enqueue_style( 'custom-style', get_stylesheet_directory_uri() . '/assets/css/custom-style.css', array(), '1.0.0', 'all' );
    //error_log( '96 ' . is_page( 96 ) . ' title ' .  get_the_title(get_the_ID()). ' -->'. is_page(get_the_title(get_the_ID())));
    if ( check_if_url_ends_with( 'offer') ) {
        //echo '<!-- Custom styles loaded -->'; // Esto debe aparecer en el código fuente HTML
        wp_enqueue_style( 'dropzone-5-style', 'https://unpkg.com/dropzone@5/dist/min/dropzone.min.css', array(), null, 'all' );
        wp_enqueue_script( 'dropzone-5-min','https://unpkg.com/dropzone@5/dist/min/dropzone.min.js', array(), null, true );
        wp_enqueue_script( 'custom-dropzone-script', get_stylesheet_directory_uri() . '/assets/js/custom-dropzone.js', array('jquery'), '1.0.0', 'all' );

        wp_enqueue_script( 'custom-offer-script', get_stylesheet_directory_uri() . '/assets/js/custom-offer.js', array('jquery'), '1.0.0', 'all' );
        wp_localize_script('custom-offer-script', 'ajax_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('generate_alias_nonce')
        ));        
    }
    if ( check_if_url_ends_with( 'register') ) {
        wp_enqueue_script( 'custom-register-script', get_stylesheet_directory_uri() . '/assets/js/custom-register.js', array(), '1.0.0', 'all' );
        wp_localize_script('custom-register-script', 'ajax_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('generate_alias_nonce')
        ));
    }

    if ( check_if_url_ends_with( 'offers') ) {
        wp_enqueue_script( 'custom-offers-script', get_stylesheet_directory_uri() . '/assets/js/custom-offers.js', array('jquery'), '1.0.0', 'all' );
        wp_localize_script('custom-offers-script', 'ajax_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'id'  => get_current_user_id(),
            'nonce' => wp_create_nonce('generate_alias_nonce')
        ));

        wp_enqueue_style('datatables-2-style', 'https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css', array(), '2.1.8', 'all');
        wp_enqueue_script('datatables-2-script', 'https://cdn.datatables.net/2.1.8/js/dataTables.js', array('jquery'), '2.1.8', true);
        
        // DataTables Buttons extension
        wp_enqueue_style('datatables-button-2-style', 'https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.min.css', array(), '3.0.1', 'all');
        wp_enqueue_script('datatables-button-2-script', 'https://cdn.datatables.net/buttons/3.0.1/js/dataTables.buttons.min.js', array('datatables-2-script'), '3.0.1', true);

        // Si necesitas exportación, agrega estos scripts
        wp_enqueue_script('jszip', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', array(), '3.10.1', true);
        wp_enqueue_script('pdfmake', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js', array(), '0.2.7', true);
        wp_enqueue_script('pdfmake-fonts', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js', array('pdfmake'), '0.2.7', true);
        wp_enqueue_script('buttons-html5', 'https://cdn.datatables.net/buttons/3.0.1/js/buttons.html5.min.js', array('datatables-button-2-script'), '3.0.1', true);
        wp_enqueue_script('buttons-print', 'https://cdn.datatables.net/buttons/3.0.1/js/buttons.print.min.js', array('datatables-button-2-script'), '3.0.1', true);

        // Buttons para visibilidad de columnas
        wp_enqueue_script('buttons-colvis', 'https://cdn.datatables.net/buttons/3.0.1/js/buttons.colVis.min.js', array('datatables-button-2-script'), '3.0.1', true);

        wp_enqueue_script( 'bp-confirm' );

        wp_localize_script(
            'bp-confirm',
            'BP_Confirm',
            array(
                'are_you_sure' => __( 'Are you sure?', 'buddyboss' ),
            )
        );        

    }    
}
add_action( 'wp_enqueue_scripts', 'custom_styles_and_scripts' );

/*function wp_custom_offers_action(){
}*/


function check_if_url_ends_with($_segment) {
    // Obtener la URL actual
    $current_url = home_url( add_query_arg( null, null ) );
    //error_log(basename(__FILE__).':: check_if_url_ends_with :: current_url ::'. $current_url);
    // Analizar la URL para obtener los segmentos de la ruta
    $parsed_url = wp_parse_url( $current_url );
    //error_log(basename(__FILE__).':: check_if_url_ends_with :: parsed_url ::'. print_r($parsed_url, true));

    $path = $parsed_url['path'];

    // Dividir la ruta en segmentos
    $segments = explode('/', trim($path, '/'));
    //error_log(basename(__FILE__).':: check_if_url_ends_with :: segments ::'. print_r($segments, true));
    // Verificar si el último segmento es 'offer'
    if ( end($segments) === $_segment ) {
        return true;
    }
    return false;
}

function custom_content_before_news_feed_title() {
    // Contenido personalizado que se insertará antes del título "News Feed"
    echo '<header class="entry-header"><h1 class="entry-title">Plataforma de encadenamiento circular</h1></header>';
    echo '<p>En esta Plataforma de encadenamiento circular podrás ofrecer y adquirir excedentes de materiales y productos para evitar su desperdicio y maximizar su aprovechamiento. Podrás vincularte con otras empresas que, como la tuya, están interesadas en aprovechar oportunidades económicas al mismo tiempo que contribuyen a cuidar el medio ambiente.</p>';
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
            //if(entryTitle.innerText==='News Feed')entryTitle.innerText='';
            if(entryTitle.innerText==='Muro General')entryTitle.innerText='';
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


function enviar_correo_nuevo_mensaje( $message ) {
    // Obtener información del mensaje
    $sender_id = $message->sender_id;
    $sender = get_userdata( $sender_id );
    $thread_id = $message->thread_id;
    $recipients = BP_Messages_Thread::get_recipients_for_thread( $thread_id );
    
    // Excluir al remitente de la lista de destinatarios
    unset( $recipients[ $sender_id ] );
    
    foreach ( $recipients as $recipient ) {
        $to = get_userdata( $recipient->user_id )->user_email;
        $subject = 'Nuevo mensaje en ' . get_bloginfo( 'name' );
        
        // Crear el cuerpo del correo
        $body = "Hola " . bp_core_get_user_displayname( $recipient->user_id ) . ",<br>";
        //$body .= "Has recibido un nuevo mensaje de " . $sender->display_name . " en " . get_bloginfo( 'name' ) . ".\n\n";
        $body .= "Has recibido un nuevo mensaje de " . $sender->display_name . "<br>";
        $body .= "<b>Mensaje:</b> " . wp_trim_words( $message->message, 20 ) . "...<br>"; // Limita el mensaje a 20 palabras
        $body .= "Para ver el mensaje completo y responder, por favor visita:<br>";
        $body .= bp_core_get_user_domain( $recipient->user_id ) . bp_get_messages_slug() . '/view/' . $thread_id . '/';
        
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        // Enviar el correo
        wp_mail( $to, $subject, $body, $headers );
    }
}

add_action( 'messages_message_sent', 'enviar_correo_nuevo_mensaje' );


function sedema_address_shortcode() {
    $output = '';
    $output .= '<strong>SECRETARÍA</strong><br/>';
    $output .= '<strong>DEL MEDIO AMBIENTE</strong><br/><br/>';
    $output .= '<strong>Atención ciudadana</strong><br/><br/>';
    $output .= '<strong>Dirección</strong>: Plaza de la Constitución 1, 3er piso<br/>Colonia Centro, Alcaldía Cuauhtémoc C.P. 06000, Ciudad de México<br/>';
    $output .= '<strong>Teléfonos</strong>: 5553458187, 5553458188<br/>';
    $output .= '<strong>Correo electrónico</strong>: atencionciudadana@sedema.cdmx.gob.mx<br/>';
    $output .= '<strong>Horario</strong>: Miércoles 9:00 a 13:00 horas';
    return $output;
}
add_shortcode('sedema_address', 'sedema_address_shortcode');


function define_global_constant() {
    // Verificar si la constante no está definida aún
    if ( !defined('FIELD_NAME_COMPANY') ) {
        // Definir la constante global
        define( 'FIELD_NAME_COMPANY', '4' );
    }

    if ( !defined('FORM_OFFER') ) {
        // Definir la constante global
        define( 'FORM_OFFER', 139 );
    }
}
add_action( 'after_setup_theme', 'define_global_constant' );


// Shortcode para mostrar la tabla vacía, que luego se llenará con AJAX
function display_offers_shortcode() {
    // Crear la tabla vacía donde se cargarán los datos vía AJAX
    ob_start();
    ?>
    <form></form>
    <table id="user-offers-table"  class="display" style="width:100%">
        <thead>
            <tr>
                <th>Índice</th>
                <th>Contenido</th>
                <th>Publicado en</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <!-- Los datos se cargarán aquí vía AJAX -->
        </tbody>
    </table>
    <div class="bb-remove-connection bb-action-popup" style="display: none">
    <transition name="modal">
        <div class="modal-mask bb-white bbm-model-wrap">
        <div class="modal-wrapper">
            <div class="modal-container">
            <header class="bb-model-header">
                <h4><span class="target_name">Eliminar conexión</span></h4>
                <a class="bb-close-remove-connection bb-model-close-button" href="#">
                <span class="bb-icon-l bb-icon-times"></span>
                </a>
            </header>
            <div class="bb-remove-connection-content bb-action-popup-content">
                <p>
                ¿Está seguro de que desea eliminar <span class="bb-user-name"></span> de sus conexiones?            </p>
            </div>
            <footer class="bb-model-footer flex align-items-center">
                <a class="bb-close-remove-connection bb-close-action-popup" href="#">Cancelar</a>
                <a class="button push-right bb-confirm-remove-connection" href="#">Confirmar</a>
            </footer>
            </div>
        </div>
        </div>
    </transition>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode( 'user_offers', 'display_offers_shortcode' );


//DataTable.js
function load_user_offers() {
    // Verificar si el ID del usuario fue pasado en la solicitud
    if ( isset( $_POST['id'] ) ) {
        global $wpdb;
        $user_id = intval( $_POST['id'] );
        $status='active';
        //$status='deleted';
        
        // Variables de DataTables
        $start = intval($_POST['start']);
        $length = intval($_POST['length']);
        $search_value = $_POST['search']['value'];
        
        // Base query
        $base_query = "FROM {$wpdb->prefix}ec_activities WHERE user_id = %d AND status = %s";
        $base_args = array($user_id, $status);        

        // Add search condition if search value exists
        if (!empty($search_value)) {
            $base_query .= " AND (content LIKE %s)";
            $base_args[] = '%' . $wpdb->esc_like($search_value) . '%';
        }        


        // Get total records without filtering
        $total_query = "SELECT COUNT(*) " . $base_query;
        $total_records = $wpdb->get_var($wpdb->prepare($total_query, ...$base_args));

        // Get total records with filtering (same as total if no search)
        $filtered_records = $total_records;

        // Get the actual data
        $data_query = "SELECT * " . $base_query . " LIMIT %d, %d";
        $data_args = array_merge($base_args, array($start, $length));
        $activities = $wpdb->get_results($wpdb->prepare($data_query, ...$data_args));



        //error_log(basename(__FILE__).'::load_user_offers:: totalRecords :: '. print_r($totalRecords,true));
        if ( $activities ) {
            
            $results = [];

            // Construir las filas de la tabla con los datos
            $idx=$start;
            foreach ( $activities as $activity ) {

                $qry = "SELECT GROUP_CONCAT(wp_bp_groups.name SEPARATOR ', ') 
                       FROM ecsedemacdmx.wp_bp_groups 
                       WHERE wp_bp_groups.id IN (". implode(',', json_decode($activity->group_id)) .") 
                       GROUP BY wp_bp_groups.name";
                $groups = $wpdb->get_col($wpdb->prepare($qry));
                
                if (in_array("0", json_decode($activity->group_id))) {
                    array_push($groups, "Mi muro");
                }
                $idx+=1;

                $reference='<a class="updater" data-record="'.$activity->id.'" style="cursor:pointer;">';
                //$reference.='Actualizar';
                $reference.='<span data-balloon-pos="down" data-balloon="Actualizar" >';
                $reference.='<i class="bb-icon-l bb-icon-edit"></i>';
                $reference.='</span>';
                $reference.='</a><br><a class="remover confirm" data-record="'.$activity->id.'" style="cursor:pointer;">';
                //$reference.='Eliminar';
                $reference.='<span data-balloon-pos="down" data-balloon="Eliminar" >';
                $reference.='<i class="bb-icon-l bb-icon-trash"></i>';
                $reference.='</span>';                
                $reference.='</a>';
                $results[] = array(
                    "index" => $idx,
                    "content" => json_decode($activity->content),
                    "groups" => implode('<br/>', $groups),
                    "reference" => $reference,
                );                
            }

            $response = array(
                "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
                //"recordsTotal" => $totalRecords,
                "recordsTotal" => intval($total_records),
                //"recordsFiltered" => count($activities),
                "recordsFiltered" => intval($filtered_records),
                "data" => $results
            );

            //error_log(basename(__FILE__).'::load_user_offers:: totalRecords :: '. print_r($response,true));

            // Devolver los datos en formato JSON para DataTables
            wp_send_json($response);

        } else {
            wp_send_json(array(
                "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            ));
        }
    } else {
        wp_send_json(array(
            "draw" => 0,
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => [],
            "error" => 'No se recibió el ID.'
        ));
    }

    wp_die(); // Finalizar correctamente la solicitud
}


add_action( 'wp_ajax_load_user_offers', 'load_user_offers' );


//add_action( 'wp_ajax_nopriv_load_user_activities', 'load_user_offers' );

// Función para manejar la solicitud AJAX
function load_offer() {
    // Verificar si el ID del usuario fue pasado en la solicitud
    if ( isset( $_POST['record'] ) ) {
        global $wpdb;
        $id = intval( $_POST['record'] );

        // Consultar las actividades del usuario
        $activities = $wpdb->get_col(
            $wpdb->prepare( "SELECT content_data FROM {$wpdb->prefix}ec_activities WHERE id = %d", $id )
        );
        //error_log(basename(__FILE__).'::load_offer:: activities :: '. print_r($activities,true));
        if ( $activities ) {
            // Enviar los datos como respuesta AJAX
            wp_send_json_success( $activities );
        } else {
            wp_send_json_error( 'No se encontraron actividades.' );
        }
    } else {
        wp_send_json_error( 'No se recibieron argumentos' );
    }

    wp_die(); // Finalizar correctamente la solicitud
}
add_action( 'wp_ajax_load_offer', 'load_offer' );


// Función para manejar la solicitud AJAX
function load_record() {
    // Verificar si el ID del usuario fue pasado en la solicitud
    if ( isset( $_POST['record'] ) ) {
        global $wpdb;
        $id = intval( $_POST['record'] );

        // Consultar las actividades del usuario
        $activities = $wpdb->get_results(
            $wpdb->prepare( "SELECT content_data content, group_id parcel, multimedia FROM {$wpdb->prefix}ec_activities WHERE id = %d", $id )
        );
        //error_log(basename(__FILE__).'::load_offer:: activities :: '. print_r($activities,true));
        if ( $activities ) {
            // Enviar los datos como respuesta AJAX
            wp_send_json_success( $activities );
        } else {
            wp_send_json_error( 'No se encontraron actividades.' );
        }
    } else {
        wp_send_json_error( 'No se recibieron argumentos' );
    }

    wp_die(); // Finalizar correctamente la solicitud
}
add_action( 'wp_ajax_load_record', 'load_record' );


function get_existing_files() {
    //check_ajax_referer('wp_rest', 'nonce');
    if ( isset( $_POST['record'] ) ) {
        global $wpdb;
        $user_id = get_current_user_id();
        $id = intval( $_POST['record'] );

        $activities = $wpdb->get_col(
            $wpdb->prepare( "SELECT multimedia FROM {$wpdb->prefix}ec_activities WHERE id = %d", $id )
        );
        //error_log(basename(__FILE__).'::get_existing_files:: activities :: '. print_r($activities,true));
        if ( $activities ) {

            $data=json_decode($activities[0],true);
            $attachments = [];
            foreach($data as $item){
                foreach($item['attachment_ids'] as $attachment){
                    $attachments[]=$attachment['attachment_id'];

                }
            }
            
            $dts_=implode(',', $attachments );

            $qry = "";
            $qry.= " SELECT p.ID, p.post_title, p.post_mime_type as type, p.guid, pm.meta_value as file_size ";
            $qry.= " FROM ".$wpdb->posts." p";
            $qry.= " JOIN ".$wpdb->postmeta." pm ON p.ID = pm.post_id";
            $qry.= " JOIN ".$wpdb->prefix."bp_media bpm ON p.ID = bpm.attachment_id";
            $qry.= " WHERE bpm.user_id = ".$user_id." AND pm.meta_key = '_wp_attachment_metadata'";
            $qry.= " AND bpm.attachment_id IN (";
            $qry.= " SELECT min(post_id) FROM ".$wpdb->postmeta." pmt ";
            $qry.= " WHERE pmt.post_id IN (".$dts_.")";
            $qry.= " AND pmt.meta_key = '_wp_attachment_metadata' ";
            $qry.= " group by pmt.meta_value";
            $qry.= ") ";
                        

            $query = $wpdb->prepare($qry);

            $results = $wpdb->get_results($query);
            $files = array();

            foreach ($results as $result) {
                $meta = unserialize($result->file_size);
                $filesize = isset($meta['filesize']) ? $meta['filesize'] : 0;
                $url=wp_get_attachment_url($result->ID);
                $files[] = array(
                    'id' => $result->ID,
                    'name' => getFullName($result->post_title,$url),
                    'size' => $filesize,
                    'type' => $result->type,
                    //'url' => $result->guid,
                    'url' => $url, // Usar función segura de WordPress
                    'thumbnail' => get_post_meta($result->ID, '_video_thumbnail', true) // Si guardas thumbnails
                );
            }

            wp_send_json_success($files);
        } else {
            wp_send_json_error( 'No se encontraron actividades.' );
        }

    } else {
        wp_send_json_error( 'No se recibieron argumentos' );
    }        

    wp_die();
}
add_action('wp_ajax_get_existing_files', 'get_existing_files');


function getFullName($name_, $url_) {
    // Verifica si el nombre del archivo está en la URL
    if (strpos($url_, $name_) !== false) {
        // Obtén la extensión del archivo de la URL
        $extension = pathinfo($url_, PATHINFO_EXTENSION);        
        // Devuelve el nombre del archivo con la extensión
        return $name_ . '.' . $extension;
    }    
    // Si no coincide, devuelve null o un mensaje de error
    return '';
}



function preload_fields( $fields, $form_data ) {

    // Verifica si es el formulario correcto utilizando el ID del formulario
    $action = isset( $_POST['action'] ) ? sanitize_text_field( $_POST['action'] ) : '';

    if ( !empty( $action ) ) {
        if($_POST['action']=='fill'){
            $Tipo_de_producto = isset( $_POST['Tipo_de_producto'] ) ? sanitize_text_field( $_POST['Tipo_de_producto'] ) : '';
            $Nombre_del_producto = isset( $_POST['Nombre_del_producto'] ) ? sanitize_text_field( $_POST['Nombre_del_producto'] ) : '';
            $Cantidad_disponible = isset( $_POST['Cantidad_disponible'] ) ? sanitize_text_field( $_POST['Cantidad_disponible'] ) : '';

            $Tipo_de_oferta = isset( $_POST['Tipo_de_oferta'] ) ? sanitize_text_field( $_POST['Tipo_de_oferta'] ) : '';
            $Caracteristicas = isset( $_POST['Características'] ) ? sanitize_text_field( $_POST['Características'] ) : '';
            $Entidad_Federativa = isset( $_POST['Entidad_Federativa'] ) ? sanitize_text_field( $_POST['Entidad_Federativa'] ) : '';
            
            $Alcaldia_Municipio = isset( $_POST['Alcaldía/Municipio'] ) ? sanitize_text_field( $_POST['Alcaldía/Municipio'] ) : '';
            $Codigo_Postal = isset( $_POST['Código_Postal'] ) ? sanitize_text_field( $_POST['Código_Postal'] ) : '';
            $record = isset( $_POST['record'] ) ? sanitize_text_field( $_POST['record'] ) : '';

            /*if ( !empty( $Tipo_de_producto ) ) {
                if($form_data['id']==1){
                    $fields['inputs']['primary']['attr']['value']=$Tipo_de_producto;
                }
            }*/
            if ( !empty( $Nombre_del_producto ) ) {
                if($form_data['id']==2){
                    $fields['inputs']['primary']['attr']['value']=$Nombre_del_producto;
                }
            }
            if ( !empty( $Cantidad_disponible ) ) {
                if($form_data['id']==3){
                    $fields['inputs']['primary']['attr']['value']=$Cantidad_disponible;
                }
            }
            /*if ( !empty( $Tipo_de_oferta ) ) {
                if($form_data['id']==4){
                    $fields['inputs']['primary']['attr']['value']=$Tipo_de_oferta;
                }
            }*/
            if ( !empty( $Caracteristicas ) ) {
                if($form_data['id']==5){
                    $fields['inputs']['primary']['attr']['value']=$Caracteristicas;
                }
            }
            /*if ( !empty( $Entidad_Federativa ) ) {
                if($form_data['id']==6){
                    $fields['inputs']['primary']['attr']['value']=$Entidad_Federativa;
                }
            }*/
            if ( !empty( $Alcaldia_Municipio ) ) {
                if($form_data['id']==7){
                    $fields['inputs']['primary']['attr']['value']=$Alcaldia_Municipio;
                }
            }
            if ( !empty( $Codigo_Postal ) ) {
                if($form_data['id']==8){
                    $fields['inputs']['primary']['attr']['value']=$Codigo_Postal;
                }
            }
            if ( !empty( $record ) ) {
                if($form_data['id']==11){
                    $fields['inputs']['primary']['attr']['value']=$record;
                }
            }            
        }
    }

    return $fields;
}
add_filter( 'wpforms_field_properties', 'preload_fields', 10, 2 );
//add_filter( 'wpforms_process_filter', 'preload_fields', 10, 2 );

function update_record() {
    // Verificar si el ID del usuario fue pasado en la solicitud
    //error_log(basename(__FILE__).'::update_record:: bp_nouveau_ajax_post_update :: '. function_exists('bp_nouveau_ajax_post_update'));
    //error_log(basename(__FILE__).'::update_record:: bp_nouveau_ajax_post_update :: '. function_exists('bp_nouveau_ajax_post_update'));
    //wp_die();
    //return;
    if ( isset( $_POST['values'] ) ) {
        //error_log(basename(__FILE__).'::update_record:: values :: '. print_r($_POST['values'],true));
        $values=$_POST['values'];
        $record=0;
        $places=[];
        $content_data = array();
        $post_content = '';
        foreach ($values as $item) {
            // Verifica que los elementos 'name' y 'value' existan
            if (isset($item['name']) && isset($item['value'])) {
                
                //error_log(basename(__FILE__).'::update_record:: name :: '. $item['name']. ' :: '.str_len(str_contains($item['name'],'record')) );

                if(strlen(str_contains($item['name'],'record'))>0){
                    $record=$item['value'];
                    continue;
                }
                if(strlen(str_contains($item['name'],'places'))>0){
                    $places=explode(',',$item['value']);
                    continue;
                }
                $post_content .='<strong>' . esc_html($item['name']) . ':</strong> ' . esc_html($item['value']) . '<br>';
                array_push($content_data,array('name'=>sanitize_text_field($item['name']),'value'=>sanitize_text_field($item['value'])));
            }
        }

        if($record>0){
            
            $activity=load_record_detail($record);
            

            $groups=json_decode($activity[0]->group_id);

            $diffsL = array_diff($groups, $places);
            $diffsR = array_diff($places, $groups);
            
            
            if(count($diffsL)==count($diffsR)){

                $activities_id=json_decode($activity[0]->activities_id);

                /*if (!function_exists('bp_nouveau_ajax_post_update')) {
                    require_once ABSPATH . 'wp-content/plugins/buddyboss-platform/bp-templates/bp-nouveau/includes/activity/ajax.php';
                }*/
                            
                foreach ($activities_id as $id) {
                    //error_log(basename(__FILE__).'::update_record:: post_content :: '. $id);
                    //bp_nouveau_ajax_post_update(array(
                    bp_activity_post_update(array(
                        'id' => $id,
                        'content' => $post_content
                    ));
                }

                $data=array(
                    "data" => array(
                        "id"=>$record,
                        "content"=>json_encode($post_content),
                        "content_data"=>json_encode($content_data)
                    ),
                    "types" => array('%s', '%s')
                );
                //error_log(basename(__FILE__).'::update_record:: resultado :: '. $json_data);
                $resultado = update_activities($data);
                //error_log(basename(__FILE__).'::update_record:: resultado :: '. $resultado);

            }

        }else{
            wp_send_json_error( 'No se puede procesar la información' );
        }

    } else {
        wp_send_json_error( 'No se recibieron argumentos' );
    }
    wp_die();
}
add_action( 'wp_ajax_update_record', 'update_record' );

function load_record_detail($id) {
    // Verificar si el ID del usuario fue pasado en la solicitud
    //error_log(basename(__FILE__).'::load_record_detail:: id :: '. $id);
    global $wpdb;

    // Consultar las actividades del usuario
    $activities = $wpdb->get_results(
        $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ec_activities WHERE id = %d", $id )
    );
    //error_log(basename(__FILE__).'::load_offer:: activities :: '. print_r($activities,true));

    //wp_die(); // Finalizar correctamente la solicitud
    //error_log(basename(__FILE__).'::update_record:: load_record_detail :: '. print_r($activities,true));
    return $activities;
}


function update_activities($data_){
    global $wpdb;

    // Nombre de la tabla
    $table_name = $wpdb->prefix . 'ec_activities';

    // Decodificar el JSON en un array asociativo
    $data = $data_['data'];
    $data_types = $data_['types'];
    //error_log(basename(__FILE__).'::update_activities:: data :: '. print_r($data,true));
    // Verificar si el JSON tiene datos válidos
    if (!is_array($data) || !isset($data['id'])) {
        return false; // Retorna falso si no es válido
    }

    // Obtener el ID de la actividad a actualizar
    $id = $data['id'];

    // Eliminar el ID del array, ya que no será actualizado
    unset($data['id']);

    // Si no hay campos para actualizar, retornar
    if (empty($data)) {
        return false;
    }

    // Actualizar la tabla utilizando el ID como condición
    $result = $wpdb->update(
        $table_name,      // Nombre de la tabla
        $data,            // Array de datos para actualizar (columnas y valores)
        array('id' => $id), // Condición (WHERE id = $id)
        $data_types, //null,             // Formato de los valores de las columnas, se infiere automáticamente
        array('%d')       // Formato del valor de la condición (ID)
    );

    return $result !== false; // Retorna true si fue exitoso, de lo contrario false
}

function remove_record() {
    if ( isset( $_POST['record'] ) ) {
        error_log(basename(__FILE__).'::remove_record:: record :: '.$_POST['record']);
        $result=delete_record($_POST['record']);
        /*wp_send_json_success(array(
            'success'   => true,
            'message' => '',
        ));*/

        wp_send_json_success($result);
    }
    
    
    wp_die(); // Finalizar correctamente la solicitud    
}
add_action( 'wp_ajax_remove_record', 'remove_record' );
?>