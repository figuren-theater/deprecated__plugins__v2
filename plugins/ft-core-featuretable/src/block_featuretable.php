<?php

namespace Figuren_Theater\Coresites\Blocks;

use Figuren_Theater\Coresites\Shortcodes as Shortcodes;

/**
 *
 */
class block_featuretable extends Shortcodes\Ft_coresites_Shortcode__featurelist
{
    /**
     * overwrite constructor,
     * to not add another same shortcode via add_shortcode()
     */
    public function __construct()
    {
        $this->name        = 'ft-block-featuretable';
        $this->plugin_name = 'ft-block-featuretable';


        $this->output = '';
        $this->prefix = '_' . $this->plugin_name . '_';
        $this->required_css_and_js = $this->set_required_css_and_js();
    }


    protected function set_required_css_and_js()
    {
        return array();
    }



    public function handler($atts)
    {
        //
        #		$this->atts = \shortcode_atts( $this->atts(), $atts, $this->name );
        $this->atts = $atts;
        //
        $this->prepare_output();

        if (false === $this->output) {
            return;
        }

        $this->load_css_and_js();

        // because this is a shortcode, only return results
        return $this->render($this->atts);
    }

    protected function render($attributes)
    {
        $shortcode_return = parent::render($attributes);

        $wrapper_markup = '<div %1$s>%2$s</div>';

        // https://github.com/WordPress/gutenberg/blob/master/packages/block-library/src/categories/index.php

        //		$class = "wp-block-ft-block-core-featuretable";
        $class = '';
        /*
                // https://github.com/WordPress/gutenberg/blob/master/packages/block-library/src/latest-posts/index.php
                if ( isset( $attributes['blockLayout'] ) && $attributes['blockLayout'] ) {
                    $class .= ' block-layout-' . $attributes['blockLayout'];
                }*/

        if (isset($attributes['backgroundColor']) && $attributes['backgroundColor']) {
            $class .= ' has-' . $attributes['backgroundColor'] . '-background-color';
        }

        if (isset($attributes['textColor']) && $attributes['textColor']) {
            $class .= ' has-' . $attributes['textColor'] . '-color';
        }

        if (isset($attributes['gradient']) && $attributes['gradient']) {
            $class .= ' has-' . $attributes['gradient'] . '-gradient-background';
        }

        if (isset($attributes['align']) && $attributes['align']) {
            $class .= ' align' . $attributes['align'];
        } else {
            $class .= ' alignnone';
        }



        /**/
        if (!is_admin()) {
            # code...
            // $wrapper_markup .= '<pre>'.var_export($attributes, true).'</pre>';
        }

        $classnames = \esc_attr($class);
        $block_wrapper_attributes = [
            'class' => $classnames
        ];
        $wrapper_attributes = \get_block_wrapper_attributes($block_wrapper_attributes);

        return sprintf(
            $wrapper_markup,
            $wrapper_attributes,
            $shortcode_return
        );
    }
}
