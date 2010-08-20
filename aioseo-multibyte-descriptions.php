<?php
/*
Plugin Name: Aioseo Multibyte Descriptions
Plugin URI: http://retujyou.com/aioseo-multibyte-descriptions/
Description: Aioseo Multibyte Descriptions is plugin for multibyte language user, work well with All in One SEO Pack autogenerating META descriptions.
Author: Rui Mashita
Version: 0.0.1
Author URI: http://retujyou.com
*/

/*
    Copyright (C) 2010 Rui Mashita

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( !class_exists( 'AioseoMultibyteDescriptions' )  ) {
    class AioseoMultibyteDescriptions {

        var $i18n_domain = 'aioseo-multibyte-descriptions';
        // wp_opions table's option_name column value
        var $option_name = "aioseo_multibyte_descriptions" ;
        var $options;
        var $default_options = array(
                'descriptions_length' => '120',
        );
        var $plugin_dir_name;
        var $plugin_dir;
        var $plugin_dir_url;
        var $blog_encoding;
        var $has_mbfunctions;
        var $aioseop_options;

        function __construct() {

            $dirs = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
            // aioseo-multibyte-descriptions
            $this->plugin_dir_name = array_pop($dirs);
            // /path/to/aioseo-multibyte-descriptions
            $this->plugin_dir = WP_PLUGIN_DIR .'/'.  $this->plugin_dir_name;
            // http://sample.com/path/to/aioseo-multibyte-descriptions
            $this->plugin_dir_url = WP_PLUGIN_URL .'/'.  $this->plugin_dir_name;

            //Localization
            $locale = get_locale();
            load_plugin_textdomain($this->i18n_domain, $this->plugin_dir. '/locales/', $this->plugin_dir_name. '/locales/' );

            $this->blog_encoding = get_option('blog_charset');
            $this->has_mbfunctions = $this->mbfunctions_exist();



            //plugin deactive
            if(!$this->has_mbfunctions ) {
                $plugin = plugin_basename(__FILE__);
                $current = get_option('active_plugins');
                $key = array_search($plugin, $current);

                if(false !== $key && null !== $key) {
                    array_splice($current, $key, 1);
                    update_option('active_plugins', $current);
                    add_action('admin_notices', 'warning');
                    return;
                }
            }

            $this->load_options();

            add_action('admin_print_styles', array(&$this,'add_admin_print_styles'));
            add_action('admin_print_scripts', array(&$this,'add_admin_print_scripts'));

            add_action('admin_menu', array(&$this, 'add_admin_option_page'));
//            add_action('admin_notices', array (&$this,"add_admin_notices"));
            add_action('in_admin_footer', array (&$this,"add_admin_notices"));


            add_filter( 'plugin_action_links_'. plugin_basename(__FILE__), array(&$this, 'add_plugin_action_links'));

            add_filter('aioseop_description', array(&$this, 'get_multibyte_post_description'));

        }



        function get_multibyte_post_description($description) {
            global $wp_query;
            $post = $wp_query->get_queried_object();

            if ( $this->is_almost_ascii($description, $this->blog_encoding) && strlen($description) > $this->options['descriptions_length'] ) {
                return $description;
            }else {
                $this->trim_excerpt_without_filters_full_length($description);
                $description = mb_substr($post->post_content, 0, $this->options['descriptions_length'], $this->blog_encoding);
                $description = preg_replace("/\s\s+/", " ", $description);
                return $description;
            }

        }


        /**
         * Retrieves the plugin's options from the database.
         *
         *
         * @return boolean
         */
        function load_options() {
            if( false === ( $options = get_option( $this->option_name) ) ) {
                $this->options = $this->default_options;
                return false;
            } else {
                $this->options = $options;
                return true;
            }

        }



        /**
         * delete the plugin's options from the database.
         *
         *
         */
        function delete_options() {
            return delete_option($this->option_name);
        }


        /**
         * save the options value to the WordPress database
         *
         *
         * @return boolean true if the save was successful.
         */
        function  save_options( ) {
            return update_option( $this->option_name, $this->options );
        }

        function  add_admin_print_styles() {
            wp_enqueue_style('dashboard');


        }

        function add_admin_print_scripts() {
            wp_enqueue_script('dashboard');
            //          wp_enqueue_script('thickbox');
        }

        // Add option page
        function add_admin_option_page() {
            add_options_page(
                    __('Aioseo Multibyte Descriptions Option', $this->i18n_domain),
                    __('Aioseo Multibyte Descriptions', $this->i18n_domain),
                    8,
                    basename(__FILE__),
                    array(&$this, 'admin_option_page')
            );

        }





        /**
         * Admin option page
         *
         * @return void
         */
        function admin_option_page() {


            ?>
<div class="wrap" id="AioseoMultibyteDescriptionsAdminOptionPage">
    <h2><?php _e('Aioseo Multibyte Descriptions Setting', $this->i18n_domain); ?></h2>
    <h3><?php _e("Aioseo Multibyte Descriptions is plugin for multibyte language user, work well with All in One SEO Pack autogenerating META descriptions.", $this->i18n_domain); ?></h3>

    <div style="width: 70%;" class="postbox-container">
        <div class="metabox-holder" >


            <div class="meta-box-sortables ui-sortable">

                <div class="postbox" id="present">
                    <div title="Click to toggle" class="handlediv"><br></div>
                    <h3 class="hndle"><span><?php _e('Present for Plugin Editor', $this->i18n_domain); ?></span></h3>
                    <div class="inside">
                        <div><label><?php _e('If you turn off monitor and close your eyes, and imagine that real plugin editor be, the tow things you can do now.', $this->i18n_domain); ?></label>
                        </div>

                        <div class="alignleft inside" style="width: 45%;">
                            <p>1. <?php _e('you can send present for me.', $this->i18n_domain); ?> </p>
                            <div class="inside">
                                <a href="http://www.amazon.co.jp/wishlist/1PKVG1PQUK0PX">
                                    <img src="<?php echo $this->plugin_dir_url. '/images/amazon.gif' ;?>" width="122" title="amazon wish list" />
                                </a>
                            </div>
                        </div>
                        <div class="alignright inside" style="width: 45%;">
                            <p>2. <?php _e('you can donate to me.' , $this->i18n_domain); ?></p>
                            <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                                <input type="hidden" name="cmd" value="_donations" />
                                <input type="hidden" name="business" value="BAT75LYEU6B3L" />
                                <input type="hidden" name="item_name" value="All in One SEO Multibyte Descriptions" />
                                <input type="hidden" name="item_number" value="1" />
                                <input type="hidden" name="item_number" value="1" />
                                <input type="hidden" name="currency_code" value="JPY" />
                                <input type="hidden" name="lc" value="JP" />
                                <input type="hidden" name="first_name" value="ありがとうございます" />
                                <input type="hidden" name="last_name" value="御寄付" />
                                <input type="hidden" name="charset" value="UTF-8" />
                                <input type="hidden" name="country" value="JP" />
                                <input
                                    type="image"
                                    src="https://www.paypal.com/ja_JP/JP/i/btn/btn_donateCC_LG.gif"
                                    border="0"
                                    name="submit"
                                    alt="PayPal- オンラインで安全・簡単に決済"
                                    />
                                <img alt="" border="0" src="https://www.paypal.com/ja_JP/i/scr/pixel.gif" width="1" height="1" />
                            </form>
                        </div>
                        <div class="clear"></div>



                    </div>
                </div>

                <div class="postbox" id="setting">
                    <div title="Click to toggle" class="handlediv"><br></div>
                    <h3 class="hndle"><span><?php _e('Descriptions Setting', $this->i18n_domain); ?></span></h3>
                    <div class="inside">
                        <form action="<?php  echo $_SERVER['REQUEST_URI']; ?>" method="post">



                            <table class="form-table">
                                <tr>
                                    <th><label for="descriptions_length"><?php _e('META Descriptions Length', $this->i18n_domain); ?> : </label> 
                                        <p class="description"><?php _e('Autogenerate META Description Length (Multibyte)', $this->i18n_domain); ?></p></th>
                                    <td>
                                        <input type="text" name="descriptions_length" id="descriptions_length" value="<?php echo htmlspecialchars($this->options['descriptions_length']); ?>" />

                                    </td>
                                </tr>
                            </table>

                            <div class="alignright">
                                <input class="button-primary" id="save_options" type="submit" value="<?php _e('Update Options &raquo;', $this->i18n_domain); ?>" />
                            </div>
                            <input type="hidden" name="option_method" value="save_options" />
                            <div class="clear"></div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="clear"></div>



    <form action="<?php  echo $_SERVER['REQUEST_URI']; ?>" method="post">
        <div class="">
            <input class="button" id="reset_options" type="submit" value="<?php _e('Reset Options &raquo;', $this->i18n_domain); ?>" />
        </div>
        <input type="hidden" name="option_method" value="reset_options" />
    </form>
</div>
            <?php

        }




        function add_admin_notices() {

            if( "save_options" == $_POST['option_method'] ) :
                $this->options['descriptions_length'] = $_POST['descriptions_length'];
                $this->save_options();
                ?>

<div id="aioseo_multibyte_descriptions_success" class="updated fade" >
    <p><?php _e('Aioseo Multibyte Descriptions Options Updated ', $this->i18n_domain); ?></p>
</div>
            <?php
            endif;

            if( "reset_options" == $_POST['option_method'] ) :
                $this->delete_options();
                $this->load_options();
            endif;

            $this->aioseop_options = get_option('aioseop_options');
            if( 'on' != $this->aioseop_options["aiosp_generate_descriptions"] ):
                ?>
<div id="aioseo_multibyte_descriptions_error" class="error" >
    <p><?php _e('Cannot work Aioseo Multibyte Descriptions plugin.', $this->i18n_domain); ?><br />
                        <?php _e('Check "Autogenerate Descriptions" in', $this->i18n_domain); ?> <a href="<?php echo get_bloginfo( 'wpurl' ); ?>/wp-admin/options-general.php?page=all-in-one-seo-pack/aioseop.class.php" >All in One SEO Option</a></p>
</div>
            <?php
            endif;

        }



        function add_plugin_action_links($actions) {
            $link = '<a href="'.get_bloginfo( 'wpurl' ).'/wp-admin/options-general.php?page='.basename(__FILE__).'" >'.__(Settings).'</a>';
            array_unshift($actions, $link);
            return $actions;
        }

        //from
        //Plugin Name: All in One SEO Pack
        //Plugin URI: http://semperfiwebdesign.com
        //Version: 1.6.12.1
        //Author: Michael Torbert
        function trim_excerpt_without_filters_full_length($text) {
            $text = str_replace(']]>', ']]&gt;', $text);
            $text = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $text );
            $text = strip_tags($text);
            return trim(stripcslashes($text));
        }



        // from
        // WP Multibyte Patch
        // Plugin URI: http://eastcoder.com/code/wp-multibyte-patch/
        // Author: tenpura
        // Version: 1.1.6
        function mbfunctions_exist() {
            return (
                    function_exists('mb_convert_encoding') &&
                            function_exists('mb_convert_kana') &&
                            function_exists('mb_detect_encoding') &&
                            function_exists('mb_strcut') &&
                            function_exists('mb_strlen') &&
                            function_exists('mb_substr')
                    ) ? true : false;
        }

        // from
        // WP Multibyte Patch
        // Plugin URI: http://eastcoder.com/code/wp-multibyte-patch/
        // Author: tenpura
        // Version: 1.1.6
        function is_almost_ascii($string, $encoding) {
            return (90 < round(@(mb_strlen($string, $encoding) / strlen($string)) * 100)) ? true : false;
        }

    }

}

if ( class_exists( 'AioseoMultibyteDescriptions' ) ) {
    $aioseo_multibyte_descriptions = new AioseoMultibyteDescriptions();
}

?>
