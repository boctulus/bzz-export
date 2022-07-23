<?php

use boctulus\BzzExport\libs\Files;
use boctulus\BzzExport\libs\Strings;
use boctulus\BzzExport\libs\Products;

require_once __DIR__ . '/libs/Files.php';
require_once __DIR__ . '/libs/Strings.php';
require_once __DIR__ . '/libs/Products.php';

/*
	REST

*/

/*
    El archivo CSV debe enviarse como "form-data"
*/
function ajax_bzz_export(WP_REST_Request $req)
{
    try {        
        $error = new WP_Error();
        
        $msg    = null;
        $errors = [];

        $ruta_rel = '/logs/product_export.php';
        $path = __DIR__ . $ruta_rel;

        $ok = Products::exportProducts($path);

        if ($ok){
            $url = plugin_dir_url(__FILE__) . $ruta_rel;

            $msg = [
                'path' => $path
            ];
        } else {
            $msg = 'Error';
            $errors = [
                'Error al exportar'
            ];
        }

        $res = [
            'code' => 200,
            'message' => $msg,
            'errors' => $errors
        ];

        $res = new WP_REST_Response($res);
        $res->set_status(200);

        return $res;
    } catch (\Exception $e) {
        $error = new WP_Error();
        $error->add(500, $e->getMessage());

        return $error;
    }
}

function a_dummy10(){
    sleep(2);

    $res = new WP_REST_Response('OK');
    $res->set_status(200);

    return $res;
}


add_action('rest_api_init', function () {
    #	{VERB} /wp-json/xxx/v1/zzz
    register_rest_route('bzz-export/v1', '/post', array(
        'methods' => 'POST',
        'callback' => 'ajax_bzz_export',
        'permission_callback' => '__return_true'
    ));

    register_rest_route('bzz-export/v1', '/dummy', array(
        'methods' => 'GET',
        'callback' => 'a_dummy10',
        'permission_callback' => '__return_true'
    ));
});
