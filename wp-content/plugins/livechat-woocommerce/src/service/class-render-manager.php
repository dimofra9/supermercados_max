<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Renderer_Manager class for rendering templates with given parameters.
 */
class Render_Manager
{
    /**
     * Render template function.
     *
     * @param string $template_name
     * @param array $parameters
     * @param boolean $render_output
     * @return mixed string|null
     */
    public function render( $template_name, array $parameters = array(), $render_output = true ) {
        foreach ( $parameters as $name => $value ) {
            ${$name} = $value;
        }

        ob_start();
        include __DIR__ . '/../view/' . $template_name;
        $output = ob_get_contents();
        ob_end_clean();

        if ( $render_output ) {
            echo $output;
        } else {
            return $output;
        }
    }
}
