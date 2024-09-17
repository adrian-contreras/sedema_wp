<?php
header('Content-Type: application/json');
//echo phpInfo();
//error_log('heartbeat::');

// Recibir la solicitud POST
//$cameras_connected = isset($_POST['cameras_connected']) ? $_POST['cameras_connected'] : 0;

//error_log(basename(__FILE__).'::cameras_connected::'.json_encode($_POST));


// Aquí puedes realizar cualquier lógica que necesites, por ejemplo, decidir
// si debes iniciar o detener la captura de video en función del número de cámaras conectadas
// y otras condiciones de tu sistema.
/*
$response = '';

// Simulación de una acción a enviar. Podrías decidir esta acción en base a tu lógica
if ($cameras_connected > 0) {
    $response = 'start_capture_0';  // Iniciar captura en la cámara 0
} else {
    $response = 'stop_capture';     // Detener la captura
}

// Enviar la respuesta como texto plano
echo $response;*/
//echo json_encode($_POST);
//$response = 'start_camera:0,2';
//$response = 'stop_all_cameras';
//$response = 'start_camera:0';
//$response = 'start_camera:2';
//$response = 'stop_camera:2';
//$response = 'stop_camera:0';
//$response = 'capture_image';
//$response = 'cleanup';

echo json_encode($response);
?>
