<?php
/**
 * Loader class for IM.CON plugin.
 * Responsible for wiring hooks, shortcodes, admin UI and AJAX actions.
 */
class IM_Con_Loader {

    /**
     * Initialize and register all components.
     */
    public function run() {
        // Admin UI.
        $admin = new IM_Con_Admin();
        $admin->register_hooks();

        // Shortcode.
        $shortcode = new IM_Con_Shortcode();
        $shortcode->register_hooks();
    }
}
?>