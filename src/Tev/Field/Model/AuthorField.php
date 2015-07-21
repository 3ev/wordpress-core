<?php
namespace Tev\Field\Model;

use WP_User;
use Tev\Author\Factory as AuthorFactory;

/**
 * Author field.
 *
 * Returns a single Author object or array of Author objects.
 */
class AuthorField extends AbstractField
{
    /**
     * Author factory.
     *
     * @var \Tev\Author\Factory
     */
    private $authorFactory;

    /**
     * Constructor.
     *
     * @param  array               $base          Base field data
     * @param  \Tev\Author\Factory $authorFactory Author factory
     * @return void
     */
    public function __construct(array $base, AuthorFactory $authorFactory)
    {
        parent::__construct($base);

        $this->authorFactory = $authorFactory;
    }

    /**
     * Get a single author object or array of author objects.
     *
     * If no authors are configured, returned will result will be an empty array
     * if this is a mutli-select, or null if not.
     *
     * @return \Tev\Author\Model\Author|\Tev\Author\Model\Author[]|null
     */
    public function getValue()
    {
        $val = $this->base['value'];

        if (is_array($val)) {
            if (isset($val['ID'])) {
                return $this->authorFactory->create($val['ID']);
            } else {
                $authors = array();

                foreach ($val as $a) {
                    $authors[] = $this->authorFactory->create($a['ID']);
                }

                return $authors;
            }
        } else {
            if (isset($this->base['multiple']) && $this->base['multiple']) {
                return array();
            } else {
                return null;
            }
        }
    }

    /**
     * Get a string representation of this field.
     *
     * Author display name or names, comma-space separated.
     *
     * @return string
     */
    public function __toString()
    {
        $author = $this->getValue();

        if (is_array($author)) {
            return array_reduce($author, function ($string, $a) {
                return $string . (strlen($string) ? ', ' : '') . $a->getDisplayName();
            }, '');
        } elseif ($author !== null) {
            return $author->getDisplayName();
        } else {
            return '';
        }
    }
}
