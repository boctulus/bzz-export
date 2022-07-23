<?php

use boctulus\BzzExport\libs\Strings;
use boctulus\BzzExport\libs\Files;
use boctulus\BzzExport\libs\Debug;
use boctulus\BzzExport\libs\Request;
use boctulus\BzzExport\libs\Url;
use boctulus\BzzExport\libs\Products;
use boctulus\BzzExport\libs\System;

require_once __DIR__ . '/libs/Debug.php';
require_once __DIR__ . '/libs/Strings.php';
require_once __DIR__ . '/libs/Files.php';
require_once __DIR__ . '/libs/Request.php';
require_once __DIR__ . '/libs/Url.php';
require_once __DIR__ . '/libs/Arrays.php';
require_once __DIR__ . '/libs/Products.php';
require_once __DIR__ . '/libs/System.php';

require_once __DIR__ . '/helpers/debug.php';
require_once __DIR__ . '/helpers/system.php';

require_once __DIR__ . '/ajax.php';




/*
    Panel administraitivo
*/
if ( is_admin() ) {
    add_action( 'admin_menu', 'bzz_export', 100 );
}


function bzz_export() {
    add_submenu_page(
        'edit.php?post_type=product',
        __( 'Bzz Export' ),
        __( 'Bzz Export' ),
        'manage_woocommerce', // Required user capability
        'bzz-export',
        'bzz_export_admin_panel'
    );
}

function bzz_export_admin_panel() {
    if (!current_user_can('administrator'))  {
        wp_die( __('Su usuario no tiene permitido acceder') );
    }

    echo bzz_export_shortcode();
}


// function that runs when shortcode is called
function bzz_export_shortcode() 
{   
    ?>
        <script>

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById("do_export").addEventListener("click", loadingAjaxNotification)
			
            function setNotification(msg){
                document.getElementById("bzz-notifications").innerHTML = msg;
            }

            function loadingAjaxNotification() {
				document.getElementById("loading-text").innerHTML = "Actualizando productos, NO CIERRE ESTA PÁGINA!";
			}

            function clearAjaxNotification() {
				document.getElementById("loading-text").innerHTML = "";
			}

            function bzz_export_do_it(event){
                event.preventDefault();

                const base_url = '<?= home_url('/') ?>';
                const url = base_url + 'wp-json/bzz-export/v1/post';

                jQuery.ajax({
                    url: url, // post
                    type: "post",
                    dataType: 'json',
                    cache: false,
                    processData: false, // important
                    contentType: false, // important
                    data: '',
                    success: function(res) {
                        clearAjaxNotification();

                        if (typeof res['message'] != 'undefined'){
                            let msg = res['message'];

                            if (typeof res['errors'] != 'undefined'){
                                if (typeof msg['path'] != 'undefined'){
                                    console.log(msg['path']);
                                    setNotification(msg['path']);
                                }
                            }
                        }

                        //console.log(res);                        
                    },
                    error: function(res) {
                        clearAjaxNotification();

                        if (typeof res['message'] != 'undefined'){
                            setNotification(res['message']);
                        }

                        console.log(res);
                        console.log("An error occured, please try again.");         
                    }
                });
            }

            jQuery('#bzz_export_form').on("submit", function(event){ bzz_export_do_it(event); });

        });

        </script>
    <?php

    $config =  include(__DIR__ . '/config/config.php');

    ini_set("memory_limit", $config["memory_limit"] ?? "728M");
    ini_set("max_execution_time", $config["max_execution_time"] ?? 1800);
    ini_set("upload_max_filesize",  $config["upload_max_filesize"] ?? "50M");
    ini_set("post_max_size",  $config["post_max_size"] ?? "50M");


    $out = '';

    // ...

    $out .= '
    
    <h3>Bzz Export</h3>

    <p>
        Exportar todos los productos a archivo para su posterior descarga.
    </p>

    <form id="bzz_export_form">
        <input type="submit" id="do_export" value="Go">
    </form>
    
    <p></p>
    <div id="loading-text"></div>

    <div id="bzz-notifications">
        
    </div>
    
    ';
    
    return $out;
}


// register shortcodes
add_shortcode('bzz-export', 'bzz_export_shortcode');

