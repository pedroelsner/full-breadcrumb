=== Full Breadcrumb ===
Contributors: Pedro Elsner, musashinm
Requires at least: 2.8
Tested up to: 3.4.2
Stable tag: trunk
Tags: breadcrumb, breadcrumb for custom posts, breadcrumb support taxonomy, taxonomy hierarquical

Show breadcrumb for taxonomies, custom posts and all another pages.

== Description ==

* Support Hierarquical Taxonomies

Show breadcrumb in pages, posts, custom posts, categories, taxonomies, tags, authors, attachments and archives.

= Basic Usage =

Put this code `<?php if (function_exists('show_full_breadcrumb')) show_full_breadcrumb(); ?>` in your theme and enjoy!

Or, for to get the breadcrumb: `<?php if (function_exists('get_full_breadcrumb')) $var = get_full_breadcrumb(); ?>`

In `get_full_breadcrumb` you can use the additional parameter `'type' => 'array'` to return an array with the links. eg. `array( 'type' => 'array' )`

= Basic Customization =

`<?php
show_full_breadcrumb(
    array(
        'separator' => array(
            'content' => '&raquo;'
        ), // set FALSE to hide
        'home' => array(
            'showLink' => false
        )
    )
);
?>`

= Advanced Customization =

`<?php
if (function_exists('show_full_breadcrumb')) show_full_breadcrumb(
    array(
        'labels' => array(
            'local'  => __('You are here:'), // set FALSE to hide
            'home'   => __('Home'),
            'page'   => __('Page'),
            'tag'    => __('Tag'),
            'search' => __('Searching for'),
            'author' => __('Published by'),
            '404'    => __('Error 404 &rsaquo; Page not found')
        ),
        'separator' => array(
            'element' => 'span',
            'class'   => 'separator',
            'content' => '&rsaquo;'
        ), // set FALSE to hide
        'local' => array(
            'element' => 'span',
            'class'   => 'local'
        ),
        'home' => array(
            'showLink'       => false,
            'showBreadcrumb' => true
        ),
        'actual' => array(
            'element' => 'span',
            'class'   => 'actual'
        ),
        'quote' => array(
            'tag'    => true,
            'search' => true
        )
    )
);
?>`

= Settings for Portuguese-BR =

`<?php
if (function_exists('show_full_breadcrumb')) show_full_breadcrumb(
    array(
        'labels' => array(
            'local'  => __('Você está aqui:'), // set FALSE to hide
            'home'   => __('Início'),
            'page'   => __('Página'),
            'tag'    => __('Etiqueta'),
            'search' => __('Buscando'),
            'author' => __('Publicado por'),
            '404'    => __('Error 404 &rsaquo; Página não encontrada')
        ),
        'separator' => array(
            'element' => 'span',
            'class'   => 'separator',
            'content' => '&rsaquo;'
        ), // set FALSE to hide
        'home' => array(
            'showLink' => true
        )
    )
);
?>`

== Installation ==

1. Go to your admin area and select Plugins -> Add new from the menu.
2. Search for "Full Breadcrumb".
3. Click install.
4. Click activate.
5. Put this code `<?php if (function_exists('show_full_breadcrumb')) show_full_breadcrumb(); ?>` in your theme and enjoy!

See the [description tab](http://wordpress.org/extend/plugins/full-breadcrumb/screenshots/) to know how customize. the breadcrumb,

== Screenshots ==

1. The Full Breadcrumb in my (Pedro Elsner) website =)

== Changelog ==

= 1.1 =
* Bug correction. Breaks on author without published posts.
* Include all category hierarchy of the post.
* Added a option to return as array.
* Added a option to disable the quotes in search and tags.
* Added a option to hide the separator.

= 1.0 =
* First revision.

== Upgrade Notice ==
