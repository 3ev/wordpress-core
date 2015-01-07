# 3ev Core Wordpress library

## Installation

Add the library to your `composer.json`:

```json
{
    "require": {
        "3ev/wordpress-core": "~1.0"
    }
}
```

## Model layer

* Post
* Taxonomy
* Term
* Authors

### Loop example

```php
<?php while (have_posts()): $p = tev_post_factory(); ?>

<article>
    <header>
        <p>Published on <?php echo $p->getPublishedDate()->format('jS F Y'); ?></p>
        <h1><?php echo $p->getTitle(); ?></h1>
        <p>Written by <?php echo $p->getAuthor()->getNiceName(); ?>
    </header>

    <section>
        <?php echo $p->getContent(); ?>
    </section>

    <section>
        <?php echo $p->field('extra_section_subtitle'); ?>

        <?php foreach ($p->field('extra_list_items') as $item): ?>
            <?php echo $item->field('special_item_data'); ?>
        <?php endforeach; ?>
    </section>
</article>

<?php endwhile; ?>
```

## DI Container


## Plugins

This library provides an improved way of structuring your Wordpress plugins.
Configuration and loading of common plugin code is made far more straightforward,
by way of a highly opinionated set of conventions.

Your plugin structure will look something like this:

```
- my-plugin
    - config/
        - actions.php
        - post_types.php
        - shortcodes.php
    - src/
        MyPlugin/
            Action/
            Model/
            Repository/
            Shortcode/
        views/
    - my-plugin.php
```

### Action registration

You should register action hooks in `actions.php`. This file should return an array
of key value pairs, where the key is the action name and the value is the callback.

The callback can either be an inline closure, or a string containing the name of
an action provider class. An action provider class must extend
`Tev\Plugin\Action\AbstractProvider`, and provide a method called `action()` (
which can accept any parameters you want).

For example:

`MyProvider.php`:

```php
<?php
namespace MyPlugin\Actions;

use Tev\Plugin\Action\AbstractProvider;

/**
 * Provider for my action.
 */
class MyProvider extends AbstractProvider
{
    /**
     * Action callback.
     *
     * @param  string $param Callback parameter
     * @return void
     */
    public function action($param)
    {
        // Add your callback code here...
    }
}
```

`actions.php`:

```php
<?php

return array(

    'init'      => 'MyPlugin\Actions\MyProvider',

    'save_post' => function () {
        // Add your callback code here...
    }
);
```

### Custom post types

You should register custom post types in `post_types.php`. This is a simple
array of post type keys to attributes, as follows:

```php
<?php

return array(

    'my_post_type' => array(
        'label'        => 'ecipepublications',
        'description'  => 'Publications',
        'hierarchical' => false,
        'public'       => true,

        // Other config....
    )
);
```

### Shortcodes

You should register shortcode
```php
<?php

return array(

    'my_shortcode' => 'Ecipe\Shortcode\LatestPublicationsProvider'
);
```

## Documentation

Full Phpdoc is provided at `docs/`. You can serve it easily with the built in
PHP webserver.

## Contributing

