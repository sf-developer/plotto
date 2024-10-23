<?php

namespace PLotto\Inc\Shortcodes;

defined('ABSPATH') || exit; // Prevent direct access

if (!class_exists('PLottoShortcodes')) {
    class PLottoShortcodes
    {
        static $instance = null;

        public static function instance()
        {
            if( is_null( self::$instance ) )
            {
                self::$instance = new self();
            }
        }

        public function __construct()
        {
            add_shortcode( 'plotto-grid', [ $this, 'shortcode_grid' ] );
            add_shortcode( 'plotto-carousel', [ $this, 'shortcode_carousel' ] );
            add_shortcode( 'previous-lotteries', [ $this, 'shortcode_previous_lotteries' ] );
        }

        public function shortcode_grid( $atts )
        {
            wp_enqueue_style( 'plotto-grid' );
            wp_enqueue_script( 'plotto-grid' );

            $atts = wp_parse_args( $atts, array(
                'cols' => 4
            ) );
            $atts = shortcode_atts( array(
                'cols' => 4
            ), $atts, 'plotto-grid' );

            ob_start();
            ?>
            <div id="plotto-grid-<?php echo wp_generate_uuid4(); ?>" class="plotto-grid" data-columns="<?php echo absint( $atts['cols'] ); ?>"></div>
            <?php
            $output_string = ob_get_contents();
            ob_end_clean();
            return $output_string;
        }

        public function shortcode_carousel( $atts )
        {
            wp_enqueue_style( 'plotto-carousel' );
            wp_enqueue_script( 'plotto-carousel' );

            $atts = wp_parse_args( $atts, array(
                'perpage' => 4
            ) );
            $atts = shortcode_atts( array(
                'perpage' => 4
            ), $atts, 'plotto-carousel' );

            ob_start();
            ?>
            <div id="plotto-carousel-<?php echo wp_generate_uuid4(); ?>" class="plotto-carousel" data-per-page="<?php echo absint( $atts['perpage'] ); ?>"></div>
            <?php
            $output_string = ob_get_contents();
            ob_end_clean();
            return $output_string;
        }

        public function shortcode_previous_lotteries( $atts )
        {
            wp_enqueue_style( 'flipdown' );
            wp_enqueue_style( 'plotto' );
            wp_enqueue_script( 'flipdown' );
            wp_enqueue_script( 'plotto' );

            $atts = shortcode_atts(
                array(
                    'id' => 0
            ), $atts, 'previous-lotteries' );

            ob_start();
            include_once PLotto_PATH . 'views/public/previous-lotteries.php';
            $output_string = ob_get_contents();
            ob_end_clean();
            return $output_string;
        }
    }
}
