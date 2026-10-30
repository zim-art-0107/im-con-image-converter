<?php
/**
 * Shortcode class for IM.CON plugin.
 * Provides [im_con_image id="attachment_id"] to display converted image.
 */
class IM_Con_Shortcode {

    /**
     * Register shortcode hook.
     */
    public function register_hooks() {
        add_shortcode( 'im_con_image', [ $this, 'render_image_shortcode' ] );
    }

    /**
     * Render the image based on attachment ID.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function render_image_shortcode( $atts ) {
        $atts = shortcode_atts( [
            'id' => 0,
            'size' => 'large',
            'class' => 'im-con-output',
        ], $atts, 'im_con_image' );

        $attachment_id = intval( $atts['id'] );
        if ( ! $attachment_id ) {
            return '<!-- IM.CON: invalid attachment ID -->';
        }
        $img = wp_get_attachment_image( $attachment_id, $atts['size'], false, [ 'class' => esc_attr( $atts['class'] ) ] );
        if ( ! $img ) {
            return '<!-- IM.CON: image not found -->';
        }
        return $img;
    }
}
?>