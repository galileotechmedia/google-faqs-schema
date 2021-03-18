<?php
add_action('admin_menu', 'ffws_create_menu');

function ffws_create_menu() {

    add_submenu_page('fusion-builder-options', 'Fusion Schema', 'FAQs Schema Settings',
        'manage_options','ffws_settings_page','ffws_settings_page');

    add_action( 'admin_init', 'register_ffws_settings' );
}

function register_ffws_settings() {
    register_setting( 'ffws-settings-group', 'ffws_option_id' );
    register_setting( 'ffws-settings-group', 'ffws_option_speciality' );
    register_setting( 'ffws-settings-group', 'ffws_option_assessmode' );
    register_setting( 'ffws-settings-group', 'ffws_option_additional_type' );
    register_setting( 'ffws-settings-group', 'ffws_option_url' );
    register_setting( 'ffws-settings-group', 'ffws_option_schema_name' );
    register_setting( 'ffws-settings-group', 'ffws_option_image' );
}

add_action( 'wp_ajax_ffws_get_image', 'ffws_get_image'   );
function ffws_get_image() {
    if(isset($_GET['id']) ){
        $image = wp_get_attachment_image( filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT ), 'medium', false, array( 'id' => 'ffws-image-preview-image' ) );
        $data = array(
            'image'    => $image,
        );
        wp_send_json_success( $data );
    } else {
        wp_send_json_error();
    }
}

function ffws_settings_page() {
    ?>
    <div class="wrap">
        <h1>Fusion FAQs Schema Settings</h1>

        <form method="post" action="options.php">
            <?php settings_fields( 'ffws-settings-group' ); ?>
            <?php do_settings_sections( 'ffws-settings-group' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">@id</th>
                    <td><input style="min-width: 300px" type="text" name="ffws_option_id" value="<?php echo esc_attr( get_option('ffws_option_id') ); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Specialty</th>
                    <td><input style="min-width: 300px" type="text" name="ffws_option_speciality" value="<?php echo esc_attr( get_option('ffws_option_speciality') ); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Accessmode</th>
                    <td><input style="min-width: 300px" type="text" name="ffws_option_assessmode" value="<?php echo esc_attr( get_option('ffws_option_assessmode') ); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Additional Type</th>
                    <td>
                        <textarea  style="min-width: 300px" name="ffws_option_additional_type"><?php echo esc_attr( get_option('ffws_option_additional_type') ); ?></textarea>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">URL</th>
                    <td><input style="min-width: 300px" type="text" name="ffws_option_url" value="<?php echo esc_attr( get_option('ffws_option_url') ); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Name</th>
                    <td><input style="min-width: 300px" type="text" name="ffws_option_schema_name" value="<?php echo esc_attr( get_option('ffws_option_schema_name') ); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Image</th>
                    <td>
                        <?php
                        $image_id = get_option( 'ffws_option_image' );
                        if( intval( $image_id ) > 0 ) {
                        // Change with the image size you want to use
                        $image = wp_get_attachment_image( $image_id, 'medium', false, array( 'id' => 'myprefix-preview-image' ) );
                        } else {
                        // Some default image
                        $image = '<img id="ffws-image-preview-image" src="" />';
                        }
                        ?>
                        <div>
                        <?php
                        echo $image;
                        ?>
                        </div>
                        <input type="hidden" name="ffws_option_image" id="ffws_image_id" value="<?php echo esc_attr( $image_id ); ?>" class="regular-text" />
                        <input type='button' class="button-primary" value="<?php esc_attr_e( 'Select a image', 'mytextdomain' ); ?>" id="ffws_option_media_manager"/>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>

        </form>
    </div>
<?php } ?>