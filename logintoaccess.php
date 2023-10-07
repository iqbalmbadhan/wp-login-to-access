<?php
/*
 * Plugin Name: Sign-in to Access Content
 * Description: Requires users to sign in before accessing content.
 * Plugin URI: https://iqbalmahmud.com/
 * Author: Iqbal Mahmud
 * Version: 0.1
 * License: GPLv3
 * Author URI: https://iqbalmahmud.com/
 * Text Domain: iqbal-mahmud-wp-login-to-access
*/

// Function to add a menu page
function content_access_add_menu_page() {
    add_menu_page(
        'Content Access Settings',
        'Content Access',
        'manage_options',
        'content-access-settings',
        'content_access_settings_page'
    );
}
add_action('admin_menu', 'content_access_add_menu_page');

// Function to display the settings page
function content_access_settings_page() {
    if (isset($_POST['submit'])) {
        $enabled = isset($_POST['enabled']) ? 1 : 0;
        update_option('content_access_enabled', $enabled);
        update_option('login_logo_url', sanitize_text_field($_POST['login_logo_url']));
        update_option('logo_width', absint($_POST['logo_width']));
        update_option('logo_height', absint($_POST['logo_height']));
        ?>
        <div class="notice notice-success is-dismissible">
            <p>Settings saved.</p>
        </div>
        <?php
    }

    $enabled = get_option('content_access_enabled', 1);
    $login_logo_url = get_option('login_logo_url', '');
    $logo_width = get_option('logo_width', 84); // Default width
    $logo_height = get_option('logo_height', 84); // Default height

    ?>
    <div class="wrap">
        <h2>Content Access Settings</h2>
        <form method="post" action="">
            <label for="enabled">Enable Content Access:</label>
            <input type="checkbox" name="enabled" <?php checked(1, $enabled); ?>><br><br>

            <label for="login_logo_url">Custom Login Logo URL:</label>
            <input type="text" name="login_logo_url" value="<?php echo esc_attr($login_logo_url); ?>"><br><br>

            <label for="logo_width">Logo Width (in pixels):</label>
            <input type="number" name="logo_width" value="<?php echo esc_attr($logo_width); ?>"><br><br>

            <label for="logo_height">Logo Height (in pixels):</label>
            <input type="number" name="logo_height" value="<?php echo esc_attr($logo_height); ?>"><br><br>

            <input type="submit" name="submit" class="button-primary" value="Save Settings">
        </form>
    </div>
    <?php
}

// Function to restrict content based on the option value
function content_access_restrict_content() {
    $enabled = get_option('content_access_enabled', 1);
    if ($enabled && !is_user_logged_in() && !is_admin()) {
        auth_redirect();
    }
}
add_action('template_redirect', 'content_access_restrict_content');

// Function to customize the login logo URL and size
function content_access_custom_login_logo() {
    $login_logo_url = get_option('login_logo_url', '');
    $logo_width = get_option('logo_width', 84);
    $logo_height = get_option('logo_height', 84);
    ?>
    <style type="text/css">
        .login h1 a {
            background-image: url(<?php echo esc_url($login_logo_url); ?>);
            background-size: <?php echo esc_attr($logo_width); ?>px <?php echo esc_attr($logo_height); ?>px;
            width: <?php echo esc_attr($logo_width); ?>px;
            height: <?php echo esc_attr($logo_height); ?>px;
            text-indent: -9999px;
        }
    </style>
    <a href="" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" style="width: <?php echo esc_attr($logo_width); ?>px; height: <?php echo esc_attr($logo_height); ?>px;"></a>
    <?php
}
add_action('login_head', 'content_access_custom_login_logo');

//login logo url
function my_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

?>
