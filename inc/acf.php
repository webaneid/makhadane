<?php
add_action( 'wp_print_styles', 'my_deregister_styles', 100 );
 
function my_deregister_styles() {
  wp_deregister_style( 'acf' );
  wp_deregister_style( 'acf-field-group' );
  wp_deregister_style( 'acf-global' );
  wp_deregister_style( 'acf-input' );
  wp_deregister_style( 'acf-datepicker' );
}
/*
	1. Google
*/
function my_acf_init() {
    acf_update_setting('google_api_key', 'AIzaSyCa5iaW5vvMX2d4Ul4vC88Y6BhYFP5YCtM');
}
add_action('acf/init', 'my_acf_init');

function ane_load_gmap_script (){
    wp_enqueue_script( 'googlemap', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCa5iaW5vvMX2d4Ul4vC88Y6BhYFP5YCtM&callback=Function.prototype');
}
add_action( 'wp_enqueue_scripts', 'ane_load_gmap_script' );

//colors
function hex2rgb($colour)
{
    if ($colour[0] == '#') {
        $colour = substr($colour, 1);
    }
    if (strlen($colour) == 6) {
        list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
    } elseif (strlen($colour) == 3) {
        list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
    } else {
        return false;
    }
    $r = hexdec($r);
    $g = hexdec($g);
    $b = hexdec($b);
    return "$r, $g, $b";
}
add_action('wp_head', function () {
    $colors = ['text', 'gelap', 'utama', 'terang', 'alternatif', 'utama-2', 'putih'];
?>


    <style id="ane-colors">
        :root {
            <?php
            foreach ($colors as $name) {
                $color = get_field('ane-warna-' . $name, 'option');
                if($color){
                    echo '--ane-warna-' . $name . ': ' . $color . ';';
                    echo '--ane-warna-' . $name . '-rgb: ' . hex2rgb($color) . ';';
                }
                // var_dump($name, $color);
            }
            ?>
        }
    </style>

<?php
}, 999);