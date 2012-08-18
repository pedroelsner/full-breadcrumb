<?php
/*
Plugin Name: Full Breadcrumb
Plugin URI: https://github.com/pedroelsner/full-breadcrumb
Description: *** Support hierarquical taxonomys *** Show breadcrumb for pages, posts, custom posts, categories, taxonomies, tags, authors, attachments and archives.
Usage: 
Version: 1.0
Author: Pedro Elsner
Author URI: http://pedroelsner.com/
*/


/**
 * Full Breadcrumb
 * 
 * @since 1.0
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
        'labels' => array(
            'home'   => 'Home',
            'page'   => 'Page',
            'tag'    => 'Tag',
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
        $this->_breadcrumb = '';

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

            $this->setBreadcrumb(
                array(
                    '<div id="breadcrumb">',
                    $this->_options['labels']['home'],
                    $this->_elements['separator'],
                )
            );

            if (is_category()) {
                $this->_category();
            } elseif (is_day()) {
                $this->_day();
            } elseif (is_year()) {
                $this->_year();
            } elseif (is_single() && !is_attachment()) {
                $this->_post();
            } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
                if (is_tax()) {
                    $this->_archiveCustomPostType();
                } else {
                    $this->_archive();
                }
            } elseif (is_attachment()) {
                $this->_attachment();
            } elseif (is_page()) {
                $this->_page();
            } elseif (is_search()) {
                $this->_search();
            } elseif (is_tag()) {
                $this->_tag();
            } elseif (is_author()) {
                $this->_author();
            } elseif (is_404()) {
                $this->_404();
            }


            if ( get_query_var('paged') ) {
                $this->setBreadcrumb(
                    array(
                        $this->_elements['separator'],
                        $this->_options['labels']['page'],
                        ' ' . get_query_var('paged'),
                    )
                );
            }

            $this->setBreadcrumb('</div>');
        }

    }
    
    /**
     * Define breadcrump
     * 
     * @param boolean|string $local
     * @access public
     * @since 1.0
     */
    public function setBreadcrumb($local) {
        if (is_array($local)) {
            foreach ($local as $value) {
                $this->_breadcrumb .= $value;
            }
        } else {
            $this->_breadcrumb .= $local;
        }
    }

    /**
     * Category
     * 
     * @access protected
     * @since 1.0
     */
    protected function _category() {
        global $wp_query;

        $obj            = $wp_query->get_queried_object();        
        $category       = get_category($obj->term_id);
        $parentCategory = get_category($category->parent);
            
        if ($category->parent != 0) {
            $this->setBreadcrumb(get_category_parents($parentCategory, true, $this->_elements['separator']));
        }

        $this->setBreadcrumb(
            array(
                $this->_elements['thisPage_before'],
                single_cat_title('', false),
                $this->_elements['thisPage_after'],
            )
        );
    }

    /**
     * Day
     * 
     * @access protected
     * @since 1.0
     */
    protected function _day() {
        $this->setBreadcrumb(
            array(
                get_the_time('Y'),
                $this->_elements['separator'],
                get_the_time('F'),
                $this->_elements['separator'],
                $this->_elements['thisPage_before'],
                get_the_time('d'),
                $this->_elements['thisPage_after'],
            )
        );
    }

    /**
     * Month
     * 
     * @access protected
     * @since 1.0
     */
    protected function _month() {
        $this->setBreadcrumb(
            array(
                get_the_time('Y'),
                $this->_elements['separator'],
                $this->_elements['thisPage_before'],
                get_the_time('F'),
                $this->_elements['thisPage_after'],
            )
        );
    }

    /**
     * Year
     * 
     * @access protected
     * @since 1.0
     */
    protected function _year() {
        $this->setBreadcrumb(
            array(
                $this->_elements['thisPage_before'],
                get_the_time('Y'),
                $this->_elements['thisPage_after'],
            )
        );
    }

    /**
     * Post
     * 
     * @access protected
     * @since 1.0
     */
    protected function _post() {
        global $post;
        $taxonomies = get_post_taxonomies($post->ID);
        foreach($taxonomies as $taxonomy) {
            $objTaxonomy = get_taxonomy($taxonomy);
            if(is_taxonomy_hierarchical($objTaxonomy)) {
                foreach (wp_get_object_terms($post->ID, $taxonomy) as $term) {
                    $this->setBreadcrumb('<a href="' . get_term_link($term->slug, $taxonomy) . '" title="' . $term->name . '">' . $term->name . '</a> ');
                }
            }
        }

        $this->setBreadcrumb(
            array(
                $this->_elements['separator'],
                $this->_elements['thisPage_before'],
                get_the_title(),
                $this->_elements['thisPage_after'],
            )
        );
    }

    /**
     * Archive for Custom Post Type
     * 
     * @access protected
     * @since 1.0
     */
    protected function _archiveCustomPostType() {
        $post_type = get_post_type_object(get_post_type());
        $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
        $taxonomy = get_taxonomy($term->taxonomy);
        if(get_post_type_archive_link($post_type->name)) {
            $this->setBreadcrumb('<a href="' . get_post_type_archive_link($post_type->name) . '" title="' . $post_type->labels->menu_name . '">' . $post_type->labels->menu_name . '</a>');
        } else {
            $this->setBreadcrumb($post_type->labels->menu_name);
        }

        $this->setBreadcrumb(
            array(
                $this->_elements['separator'],
                $taxonomy->label,
                $this->_elements['separator'],
                $this->_elements['thisPage_before'],
                $term->name,
                $this->_elements['thisPage_after'],
            )
        );
    }

    /**
     * Archive
     * 
     * @access protected
     * @since 1.0
     */
    protected function _archive() {
        $post_type = get_post_type_object(get_post_type());
        $this->setBreadcrumb(
            array(
                $this->_elements['thisPage_before'],
                $post_type->labels->menu_name,
                $this->_elements['thisPage_after'],
            )
        );
    }

    /**
     * Attachment
     * 
     * @access protected
     * @since 1.0
     */
    protected function _attachment() {
        global $post;

        $parent = get_post($post->post_parent);
        $categories = get_the_category($parent->ID);
        foreach ($categories as $category) {
            $this->setBreadcrumb(get_category_parents($category, TRUE, $this->_elements['separator']));
        }
        
        $this->setBreadcrumb(
            array(
                $parent->post_title,
                $this->_elements['separator'],
                $this->_elements['thisPage_before'],
                get_the_title(),
                $this->_elements['thisPage_after'],
            )
        );
    }

    /**
     * Page
     * 
     * @access protected
     * @since 1.0
     */
    protected function _page() {
        global $post;

        if (!$post->post_parent) {
            $this->setBreadcrumb(
                array(
                    $this->_elements['thisPage_before'],
                    get_the_title(),
                    $this->_elements['thisPage_after'],
                )
            );
            return;
        }

        $parent_id = $post->post_parent;
        $pages = array();
        while ($parent_id) {
            $page = get_page($parent_id);
            $pages[] = '' . get_the_title($page->ID) . '';
            $parent_id = $page->post_parent;
        }
        $pages = array_reverse($pages);
        foreach ($pages as $page) {
            $this->setBreadcrumb(
                array(
                    $page,
                    $this->_elements['separator'],
                )
            );
        }

        $this->setBreadcrumb(
            array(
                $this->_elements['thisPage_before'],
                get_the_title(),
                $this->_elements['thisPage_after'],
            )
        );
    }

    /**
     * Search
     * 
     * @access protected
     * @since 1.0
     */
    protected function _search() {
        $this->setBreadcrumb(
            array(
                $this->_elements['thisPage_before'],
                $this->_options['labels']['search'],
                ' `' . get_search_query() . '´',
                $this->_elements['thisPage_after'],
            )
        );
    }

    /**
     * Tag
     * 
     * @access protected
     * @since 1.0
     */
    protected function _tag() {
        $this->setBreadcrumb(
            array(
                $this->_elements['thisPage_before'],
                $this->_options['labels']['tag'],
                ' `' . single_tag_title('', false) . '´',
                $this->_elements['thisPage_after'],
            )
        );
    }

    /**
     * Author
     * 
     * @access protected
     * @since 1.0
     */
    protected function _author() {
        global $author;

        $userdata = get_userdata($author);
        $this->setBreadcrumb(
            array(
                $this->_elements['thisPage_before'],
                $this->_options['labels']['author'],
                ' ' . $userdata->display_name,
                $this->_elements['thisPage_after'],
            )
        );
    }

    /**
     * 404
     * 
     * @access protected
     * @since 1.0
     */
    protected function _404() {
        $this->setBreadcrumb(
            array(
                $this->_elements['thisPage_before'],
                $this->_options['labels']['404'],
                $this->_elements['thisPage_after'],
            )
        );
    }

}

/**
 * Show Breadcrumb
 * 
 * @param array $settings
 * @access public
 * @since 1.0
 */
function showFullBreadcrumb($settings = array()) {
    $breadcrumb = new FullBreadcrumb($settings);
    echo $breadcrumb->getBreadcrumb();
}

/**
 * Return Breadcrumb
 * 
 * @param array $settings
 * @return string
 * @access public
 * @since 1.0
 */
function getFullBreadcrumb($settings = array()) {
    $breadcrumb = new FullBreadcrumb($settings);
    return $breadcrumb->getBreadcrumb();
}

 
?>