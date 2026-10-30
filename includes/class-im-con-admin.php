<?php
/**
 * Admin class for IM.CON plugin.
 * Handles admin menu, settings registration, enqueue scripts, and AJAX conversion.
 */
class IM_Con_Admin {

    /**
     * Register WordPress hooks for admin functionality.
     */
    public function register_hooks() {
        add_action( 'admin_menu', [ $this, 'add_plugin_admin_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'wp_ajax_im_con_convert_image', [ $this, 'ajax_convert_image' ] );
    }

    /**
     * Add the plugin top‑level menu.
     */
    public function add_plugin_admin_menu() {
        add_menu_page(
            __( 'IM.CON Dashboard', 'im-con' ),
            'IM.CON',
            'upload_files', // capability that most editors have.
            'im-con',
            [ $this, 'render_dashboard' ],
            'dashicons-admin-tools',
            65
        );
        // Settings submenu.
        add_submenu_page(
            'im-con',
            __( 'IM.CON Settings', 'im-con' ),
            __( 'Settings', 'im-con' ),
            'upload_files',
            'im-con-settings',
            [ $this, 'render_settings' ]
        );
        // System Status submenu.
        add_submenu_page(
            'im-con',
            __( 'IM.CON System Status', 'im-con' ),
            __( 'System Status', 'im-con' ),
            'upload_files',
            'im-con-status',
            [ $this, 'render_status' ]
        );
    }

    /**
     * Register plugin settings (placeholder for future options).
     */
    public function register_settings() {
        // Example: register a setting to store default conversion quality.
        register_setting( 'im_con_options_group', 'im_con_quality', [
            'type'              => 'integer',
            'description'       => 'Default image quality (1‑100).',
            'default'           => 85,
            'sanitize_callback' => 'absint',
        ] );
    }

    /**
     * Enqueue admin CSS/JS.
     */
    public function enqueue_assets( $hook ) {
        // Only load on our plugin pages.
        if ( strpos( $hook, 'im-con' ) === false ) {
            return;
        }
        wp_enqueue_style( 'im-con-admin-css', IM_CON_URL . 'admin/css/im-con-admin.css', [], IM_CON_VERSION );
        wp_enqueue_script( 'im-con-admin-js', IM_CON_URL . 'admin/js/im-con-admin.js', [ 'jquery', 'wp-mediaelement' ], IM_CON_VERSION, true );
        wp_localize_script( 'im-con-admin-js', 'imCon', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'im_con_nonce' ),
        ] );
    }

    /**
     * Render the Dashboard page – drag‑&‑drop UI and preview area.
     */
    public function render_dashboard() {
        ?>
        <div class="wrap im-con-dashboard">
            <h1><?php esc_html_e( 'IM.CON Image Converter', 'im-con' ); ?></h1>
            <div id="im-con-dropzone" class="im-con-dropzone">
                <p><?php esc_html_e( 'Drag & drop images here or click to select files.', 'im-con' ); ?></p>
                <input type="file" id="im-con-file-input" multiple accept="image/*" style="display:none;" />
                <button class="button" id="im-con-select-btn"><?php esc_html_e( 'Select Images', 'im-con' ); ?></button>
            </div>
            <div id="im-con-preview" class="im-con-preview"></div>
        </div>
        <?php
    }

    /**
     * Render Settings page (currently minimal).
     */
    public function render_settings() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'IM.CON Settings', 'im-con' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'im_con_options_group' );
                do_settings_sections( 'im_con_options_group' );
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="im_con_quality"><?php esc_html_e( 'Default Quality', 'im-con' ); ?></label></th>
                        <td><input type="number" id="im_con_quality" name="im_con_quality" value="<?php echo esc_attr( get_option( 'im_con_quality', 85 ) ); ?>" min="1" max="100" /></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render System Status page – show GD/Imagick availability.
     */
    public function render_status() {
        $gd   = extension_loaded( 'gd' );
        $im   = extension_loaded( 'imagick' );
        $upload_dir = wp_upload_dir();
        $writable = is_writable( $upload_dir['basedir'] );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'IM.CON System Status', 'im-con' ); ?></h1>
            <table class="widefat striped">
                <tbody>
                    <tr><th>GD Library</th><td><?php echo $gd ? 'Enabled' : 'Missing'; ?></td></tr>
                    <tr><th>Imagick Extension</th><td><?php echo $im ? 'Enabled' : 'Missing'; ?></td></tr>
                    <tr><th>Upload Directory Writable</th><td><?php echo $writable ? 'Yes' : 'No'; ?></td></tr>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * AJAX handler – convert image and return attachment ID + preview HTML.
     */
    public function ajax_convert_image() {
        check_ajax_referer( 'im_con_nonce', 'nonce' );
        if ( empty( $_FILES['image'] ) ) {
            wp_send_json_error( [ 'message' => __( 'No file received.', 'im-con' ) ] );
        }
        $file = $_FILES['image'];
        // Simple validation – only allow images.
        $allowed = [ 'image/jpeg', 'image/png', 'image/webp', 'image/avif' ];
        if ( ! in_array( $file['type'], $allowed, true ) ) {
            wp_send_json_error( [ 'message' => __( 'Unsupported file type.', 'im-con' ) ] );
        }
        // Move to a temporary location.
        $tmp = $file['tmp_name'];
        // Here we would invoke the conversion logic (class-im-con-engine.php).
        // For brevity we just use the original file.
        // Insert into Media Library.
        $attachment_id = media_handle_sideload( [
            'name'     => $file['name'],
            'type'     => $file['type'],
            'tmp_name' => $tmp,
            'error'    => 0,
            'size'     => $file['size'],
        ], 0 );
        if ( is_wp_error( $attachment_id ) ) {
            wp_send_json_error( [ 'message' => $attachment_id->get_error_message() ] );
        }
        $preview = wp_get_attachment_image( $attachment_id, 'medium', false, [ 'class' => 'im-con-preview-img' ] );
        $shortcode = sprintf( '[im_con_image id="%d"]', $attachment_id );
        wp_send_json_success( [ 'preview' => $preview, 'shortcode' => $shortcode ] );
    }
}
?>