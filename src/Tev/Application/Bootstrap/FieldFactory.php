<?php
namespace Tev\Application\Bootstrap;

use Tev\Application\Application,
    Tev\Contracts\BootstrapperInterface,
    Tev\Field\Factory,
    Tev\Field\Model\PostField,
    Tev\Field\Model\TaxonomyField,
    Tev\Field\Model\RepeaterField,
    Tev\Field\Model\FlexibleContentField,
    Tev\Field\Model\AuthorField;

/**
 * Bootstrap the field factory.
 */
class FieldFactory implements BootstrapperInterface
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap(Application $app)
    {
        $app->bind('field_factory', function ($app) {

            $factory = new Factory($app);

            // Register defaults

            return $factory

                // Simple fields

                ->register('true_false',   'Tev\Field\Model\BasicField')
                ->register('page_link',    'Tev\Field\Model\BasicField')
                ->register('color_picker', 'Tev\Field\Model\BasicField')
                ->register('oembed',       'Tev\Field\Model\BasicField')
                ->register('text',         'Tev\Field\Model\BasicField')
                ->register('wysiwyg',      'Tev\Field\Model\BasicField')
                ->register('textarea',     'Tev\Field\Model\BasicField')
                ->register('url',          'Tev\Field\Model\BasicField')
                ->register('email',        'Tev\Field\Model\BasicField')
                ->register('date_picker',  'Tev\Field\Model\DateField')
                ->register('file',         'Tev\Field\Model\FileField')
                ->register('select',       'Tev\Field\Model\SelectField')
                ->register('checkbox',     'Tev\Field\Model\SelectField')
                ->register('radio',        'Tev\Field\Model\SelectField')
                ->register('google_map',   'Tev\Field\Model\GoogleMapField')
                ->register('image',        'Tev\Field\Model\ImageField')
                ->register('number',        'Tev\Field\Model\NumberField')

                // Post and other relationship fields

                ->register('post_object', function ($data, $app) {
                    return new PostField($data, $app->fetch('post_factory'));
                })
                ->register('relationship', function ($data, $app) {
                    return new PostField($data, $app->fetch('post_factory'));
                })
                ->register('taxonomy', function ($data, $app) {
                    return new TaxonomyField(
                        $data,
                        $app->fetch('taxonomy_factory'),
                        $app->fetch('term_factory')
                    );
                })
                ->register('user', function ($data, $app) {
                    return new AuthorField($data, $app->fetch('author_factory'));
                })

                // Collection fields

                ->register('repeater', function ($data, $app) {
                    return new RepeaterField(
                        $data,
                        $app->fetch('field_factory')
                    );
                })
                ->register('flexible_content', function ($data, $app) {
                    return new FlexibleContentField(
                        $data,
                        $app->fetch('field_factory')
                    );
                });
        });
    }
}
