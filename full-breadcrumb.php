<?php
/*
Plugin Name: Full Breadcrumb Wordpress
Plugin URI: https://github.com/pedroelsner/full-breadcrumb
Description: Show breadcrumb for pages, posts and custom posts. Support hierarquical taxonomys.
Usage: 
Version: 1.0
Author: Pedro Elsner
Author URI: http://pedroelsner.com/
*/


/**
 * Full Breadcrumb
 */
class FullBreadcrumb {

    /**
     * Default options
     * 
     * @var array
     * @access protected
     * @since 1.0
     */
    protected $_options = array(
        'label' => array(
            'home'   => 'Home',
            'page'   => 'Page',
            'search' => 'Searching for',
            'author' => 'Published by',
            '404'    => 'Error 404: Page not found'
        ),
        'separator' => array(
            'element' => 'span',
            'class'   => 'separator',
            'content' => '›'
        ),
        'homePage' => array(
            'showLink' => false,
            'element'  => 'span',
            'class'    => 'homePage'
        ),
        'thisPage' => array(
            'element' => 'span',
            'class'   => 'thisPage'
        )
    );

    /**
     * Store elements HTML
     * 
     * @var array
     * @access protected
     * @since 1.0
     */
    protected $_elements = array();

    /**
     * Save breadcrumb created
     * 
     * @var string
     * @access private
     * @since 1.0
     */
    protected $_breadcrumb;

    /**
     * Construct
     * 
     * @param array $options Custom options
     * @access public
     * @since 1.0
     */
    public function __construct($options = array()) {
        $this->_options = array_merge($this->_options, $options);

        $this->_elements['separator'] = sprintf(' <%s class="%s">%s</%s> ',
                                                $this->_options['separator']['element'],
                                                $this->_options['separator']['class'],
                                                $this->_options['separator']['content'],
                                                $this->_options['separator']['element']);

        $this->_elements['homePage_before'] = sprintf('<%s class="%s">',
                                                $this->_options['homePage']['element'],
                                                $this->_options['homePage']['class']);

        $this->_elements['homePage_after'] = sprintf('</%s>', $this->_options['homePage']['element']);

        $this->_elements['thisPage_before'] = sprintf('<%s class="%s">',
                                                        $this->_options['thisPage']['element'],
                                                        $this->_options['thisPage']['class']);

        $this->_elements['thisPage_after'] = sprintf('</%s>', $this->_options['thisPage']['element']);
    }

    /**
     * Make breadcrumb
     * @return string
     * @access public
     * @since 1.0
     */
    public function getBreadcrumb() {
        global $post;

        if (!is_home() && !is_front_page() || is_paged()) {

            $this->_breadcrumb = '<div id="breadcrumb">'
                               . $this->_options['label']['home']
                               . $this->_elements['separator'];

            if (is_category()) {
                $this->_category();
            } elseif (is_day()) {
                $this->_day();
            } elseif (is_year()) {
                $this->_year();
            } elseif (is_single() && !is_attachment()) {
                
            }

            $this->_breadcrumb .= '</div>';
        }

    }

    
    protected function _category() {
        global $wp_query;

        $obj            = $wp_query->get_queried_object();        
        $category       = get_category($obj->term_id);
        $parentCategory = get_category($category->parent);
            
        if ($category->parent != 0) {
            $this->_breadcrumb = get_category_parents($parentCategory, true, $this->_elements['separator']);
        }

        $this->_breadcrumb = $this->_elements['thisPage_before']
                           . single_cat_title('', false)
                           . $this->_elements['thisPage_after'];
    }

    protected function _day() {
        $this->_breadcrumb = get_the_time('Y')
                           . $this->_elements['separator']
                           . get_the_time('F')
                           . $this->_elements['separator']
                           . $this->_elements['thisPage_before']
                           . get_the_time('d')
                           . $this->_elements['thisPage_after'];
    }

    protected function _month() {
        $this->_breadcrumb = get_the_time('Y')
                           . $this->_elements['separator']
                           . $this->_elements['thisPage_before']
                           . get_the_time('F')
                           . $this->_elements['thisPage_after'];
    }

    protected function _year() {
        $this->_breadcrumb = $this->_elements['thisPage_before']
                           . get_the_time('Y')
                           . $this->_elements['thisPage_after'];   
    }

}






/**
 * Display breadcrumb
 *
 * @return string
 */
function show_full_breadcrumb() {

    $separador = '<span class="separator">›</span>';
    $inicio = __('Início');

    $antes = '<span class="here">';
    $depois = '</span>';

    if (!is_home() && !is_front_page() || is_paged()) {

        echo '<div id="breadcrumb">';

        global $post;
        $linkInicio = get_bloginfo('url');

        echo '' . $inicio . ' ' . $separador . ' ';
        
        
        
        if (is_category()) {
            global $wp_query;
            $cat_obj = $wp_query->get_queried_object();
            $thisCat = $cat_obj->term_id;
            $thisCat = get_category($thisCat);
            $categoriaMae = get_category($thisCat->parent);
            
            if ($thisCat->parent != 0) {
                echo(get_category_parents($categoriaMae, TRUE, ' ' . $separador . ' '));
            }
            echo $antes . single_cat_title('', false) . $depois;
            
        } elseif (is_day()) {
            echo '' . get_the_time('Y') . ' ' . $separador . ' ';
            echo '' . get_the_time('F') . ' ' . $separador . ' ';
            echo $antes . get_the_time('d') . $depois;
            
        } elseif (is_month()) {
            echo '' . get_the_time('Y') . ' ' . $separador . ' ';
            echo $antes . get_the_time('F') . $depois;
        
        } elseif (is_year()) {
           echo $antes . get_the_time('Y') . $depois;
            
        } elseif (is_single() && !is_attachment()) {
            
            if (get_post_type() != 'post' ){

                $post_type = get_post_type_object(get_post_type());
                if(get_post_type_archive_link($post_type->name)) {
                    echo '<a href="' . get_post_type_archive_link($post_type->name) . '" title="' . $post_type->labels->menu_name . '">' . $post_type->labels->menu_name . '</a>';
                } else {
                    echo $post_type->labels->menu_name;
                }
                echo ' ' . $separador . ' ';

                //$taxonomies = get_post_taxonomies($post->ID);
                //foreach($taxonomies as $taxonomy) {
                    //$objTax = get_taxonomy($taxonomy);
                    //if($objTax->hierarchical) {
                        //foreach (wp_get_object_terms($post->ID, $taxonomy) as $term) {
                            //echo '<a href="' . get_term_link($term->slug, $taxonomy) . '" title="' . $term->name . '">' . $term->name . '</a> ';
                        //}
                    //}
                //} 
                //echo ' ' . $separador . ' ';
                echo $antes . get_the_title() . $depois;
                
            } else {

                $taxonomies = get_post_taxonomies($post->ID);
                foreach($taxonomies as $taxonomy) {
                    $objTax = get_taxonomy($taxonomy);
                    if($objTax->hierarchical) {
                        foreach (wp_get_object_terms($post->ID, $taxonomy) as $term) {
                            echo '<a href="' . get_term_link($term->slug, $taxonomy) . '" title="' . $term->name . '">' . $term->name . '</a> ';
                        }
                    }
                } 
                echo ' ' . $separador . ' ';
                echo $antes . get_the_title() . $depois;

            }
        
        } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
            
            $post_type = get_post_type_object(get_post_type());
            if (is_tax()) {
                $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
                $tax = get_taxonomy($term->taxonomy);
                
                if(get_post_type_archive_link($post_type->name)) {
                    echo '<a href="' . get_post_type_archive_link($post_type->name) . '" title="' . $post_type->labels->menu_name . '">' . $post_type->labels->menu_name . '</a>';
                } else {
                    echo $post_type->labels->menu_name;
                }
                echo ' ' . $separador . ' ';
                
                echo $tax->label . ' ' . $separador . ' ';
                echo $antes . $term->name . $depois;
                
            } else {
                echo $antes . $post_type->labels->menu_name . $depois;
            }

        } elseif (is_attachment()) {
            $parent = get_post($post->post_parent);
            $cat = get_the_category($parent->ID); $cat = $cat[0];
            echo get_category_parents($cat, TRUE, ' ' . $separador . ' ');
            echo '' . $parent->post_title . ' ' . $separador . ' ';
            echo $antes . get_the_title() . $depois;

        } elseif (is_page() && !$post->post_parent) {
            echo $antes . get_the_title() . $depois;

        } elseif (is_page() && $post->post_parent) {
            $parent_id = $post->post_parent;
            $breadcrumbs = array();
            while ($parent_id) {
                $page = get_page($parent_id);
                $breadcrumbs[] = '' . get_the_title($page->ID) . '';
                $parent_id = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            foreach ($breadcrumbs as $crumb) {
                echo $crumb . ' ' . $separador . ' ';
            }
            echo $antes . get_the_title() . $depois;

        } elseif (is_search()) {
            echo $antes . __('Buscando') . ' `' . get_search_query() . '´' . $depois;

        } elseif (is_tag()) {
            echo $antes . __('Tag') . ' `' . single_tag_title('', false) . '´' . $depois;

        } elseif (is_author()) {
            global $author;
            $userdata = get_userdata($author);
            echo $antes . __('Posts publicados por'). ' ' . $userdata->display_name . $depois;

        } elseif (is_404()) {
            echo $antes . __('Erro 404: Página não encotrada') . $depois;
        }

        if ( get_query_var('paged') ) {
            if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
            echo __('Página') . ' ' . get_query_var('paged');
            if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
        }

        echo '</div>';
    }

}

 
?>