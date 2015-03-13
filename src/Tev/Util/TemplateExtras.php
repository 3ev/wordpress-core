<?php
namespace Tev\Util;

use Closure;
use Carbon\Carbon;
use Tev\Post\Factory as PostFactory;

/**
 * This class contains some useful extra template methods.
 */
class TemplateExtras
{
    /**
     * Post object factory.
     *
     * @var \Tev\Post\Factory
     */
    private $postFactory;

    /**
     * Constructor.
     *
     * Inject dependencies.
     *
     * @param  \Tev\Post\Factory $postFactory Post object factory
     * @return void
     */
    public function __construct(PostFactory $postFactory)
    {
        $this->postFactory = $postFactory;
    }

    /**
     * Generate an array of breadcrumbs, based on the current page.
     *
     * Each item in the array is a hash containing 'title' and 'url' (which
     * might be null).
     *
     * You can use this to generate nice breadcrumb lists in your page.
     *
     * @param  \Closure|\Closure[] Optional. Set of user-defined callbacks that can
     *                             return breadcrumbs. If any of the supplied callbacks
     *                             return data, then their data will be used rather than
     *                             the defaults supplied by this method. Note that
     *                             the initial 'Home' crumb is always included
     * @return array
     */
    public function breadcrumbs($callbacks = null)
    {
        $breadcrumbs = array();

        // Load blog page - it's used later, if configured

        $blogPage = null;
        if ($bp = get_page(get_option('page_for_posts'))) {
            $blogPage = $this->postFactory->create($bp);
        }

        // Home page is always present in breadcrumb trail

        $breadcrumbs[] = array(
            'title' => 'Home',
            'url'   => !is_front_page() ? get_option('home') : null
        );

        // Check if we've got any custom user defined crumbs

        if ($callbacks !== null) {
            if (!is_array($callbacks)) {
                $callbacks = array($callbacks);
            }

            foreach ($callbacks as $cb) {
                if ($cb instanceof Closure) {
                    if ($res = $cb()) {
                        $breadcrumbs[] = $res;
                    }
                }
            }
        }

        // If we haven't got any user crumbs, proceed with default logic

        if (count($breadcrumbs) === 1) {

            // Blog home page (when different to front page)

            if ($blogPage && is_home() && !is_front_page()) {
                $breadcrumbs[] = array(
                    'title' => $blogPage->getTitle(),
                    'url'   => null
                );
            }

            // Custom post type archive page, no filtering

            if (is_archive()) {

                // Custom post type archive

                if (is_post_type_archive()) {
                    if (!$this->isFilteredArchive()) {
                        $breadcrumbs[] = array(
                            'title' => post_type_archive_title('', false),
                            'url'   => null
                        );
                    } else {
                        $breadcrumbs[] = array(
                            'title' => post_type_archive_title('', false),
                            'url'   => get_post_type_archive_link(get_post_type())
                        );

                        $breadcrumbs[] = array(
                            'title' => $this->archiveTitle(),
                            'url'   => null
                        );
                    }
                }

                // Normal post archive

                else {
                    if ($blogPage && !$this->isBlogPageHome()) {
                        $breadcrumbs[] = array(
                            'title' => $blogPage->getTitle(),
                            'url'   => $blogPage->getUrl()
                        );
                    }

                    if ($this->isFilteredArchive()) {
                        $breadcrumbs[] = array(
                            'title' => $this->archiveTitle(),
                            'url'   => null
                        );
                    }
                }
            }

            // Search page

            if (is_search()) {
                $breadcrumbs[] = array(
                    'title' => 'Search',
                    'url'   => null
                );
            }

            // 404 page

            if (is_404()) {
                $breadcrumbs[] = array(
                    'title' => '404 Not Found',
                    'url'   => null
                );
            }

            // Single post

            if (is_single()) {
                if (is_singular('post')) {
                    if ($blogPage && !$this->isBlogPageHome()) {
                        $breadcrumbs[] = array(
                            'title' => $blogPage->getTitle(),
                            'url'   => $blogPage->getUrl()
                        );
                    }
                } else {
                    $postType = get_post_type();

                    $breadcrumbs[] = array(
                        'title' => get_post_type_object($postType)->labels->name,
                        'url'   => get_post_type_archive_link($postType)
                    );
                }

                $breadcrumbs[] = array(
                    'title' => get_the_title(),
                    'url'   => null
                );
            }

            // Single Page

            if (is_page()) {
                global $post;

                $page = $post;

                $pageTree = array(
                    array(
                        'title' => $page->post_title,
                        'url'   => null
                    )
                );

                while ($parentId = $page->post_parent) {
                    $page = get_post($parentId);

                    array_unshift($pageTree, array(
                        'title' => $page->post_title,
                        'url'   => get_permalink($parentId)
                    ));
                }

                foreach ($pageTree as $t) {
                    $breadcrumbs[] = $t;
                }
            }
        }

        return $breadcrumbs;
    }

    /**
     * Render a view partial.
     *
     * @param  string $file   Partial file name
     * @param  array  $params Optional variables to make available to partial
     * @return string         Rendered partial
     */
    public function partial($file, array $variables = array())
    {
        extract($variables);

        if (substr($file, -4) !== '.php') {
            $file .= '.php';
        }

        $view = '';

        if ($tmpl = locate_template($file)) {
            ob_start();
            include($tmpl);
            $view = ob_get_contents();
            ob_end_clean();
        }

        return $view;
    }

    /**
     * Get a nice title for an archive page.
     *
     * Will return nice titles for taxonomy or date archive pages.
     *
     * Optionally config this method with formatting:
     *
     * - default ('Archive'): Title to display if archive page is not date or taxonomy
     * - prefix (''): Text to prefix your title with
     * - suffix (''): Text to suffix your title with
     * - year_format ('Y'): Date format for 'year' archive pages
     * - month_format ('F Y'): Date format for 'month' archive pages
     * - day_format ('js F Y'): Date format for 'day' archive pages
     *
     * @param  array  $config Optional formatting config, as described above
     * @return string         Archive title
     */
    public function archiveTitle(array $config = array())
    {
        $defaults = array(
            'default'      => 'Archive',
            'prefix'       => '',
            'suffix'       => '',
            'year_format'  => 'Y',
            'month_format' => 'F Y',
            'day_format'   => 'jS F Y'
        );

        $config = array_merge($defaults, $config);

        $title = $config['default'];

        if (is_category() || is_tag() || is_tax()) {
            $title = single_term_title('', false);
        } elseif (is_year()) {
            $title = Carbon::create(
                get_query_var('year')
            )->format($config['year_format']);
        } elseif (is_month()) {
            $title = Carbon::create(
                get_query_var('year') ?: null,
                get_query_var('monthnum')
            )->format($config['month_format']);
        } elseif (is_day()) {
            $title = Carbon::create(
                get_query_var('year') ?: null,
                get_query_var('monthnum') ?: null,
                get_query_var('day')
            )->format($config['day_format']);
        }

        return $config['prefix'] . $title . $config['suffix'];
    }

    /**
     * Check if the 'Blog' page is the 'Home' page.
     *
     * @return boolean
     */
    private function isBlogPageHome()
    {
        return !get_option('page_for_posts') ?: get_option('page_on_front') === get_option('page_for_posts');
    }

    /**
     * Check if we're currently on an archive page that has some filtering.
     *
     * @return boolean
     */
    private function isFilteredArchive()
    {
        return is_archive() && (is_category() || is_tag() || is_tax() || is_date() || is_author());
    }
}
