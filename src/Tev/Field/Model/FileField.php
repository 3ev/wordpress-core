<?php
namespace Tev\Field\Model;

use Exception;

/**
 * File field.
 *
 * Will normalize the data that comes from the field to attempt to return
 * all attributes.
 */
class FileField extends AbstractField
{
    /**
     * Attachment ID.
     *
     * @var string
     */
    protected $atId;

    /**
     * Attachemnt URL.
     *
     * @var string
     */
    protected $atUrl;

    /**
     * Attachment title.
     *
     * @var string
     */
    protected $atTitle;

    /**
     * Attachment caption.
     *
     * @var string
     */
    protected $atCaption;

    /**
     * Attachment description.
     *
     * @var string
     */
    protected $atDescription;

    /**
     * {@inheritDoc}
     */
    public function __construct($data)
    {
        parent::__construct($data);

        $this->normalize();
    }

    /**
     * Get the attachment URL.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->url();
    }

    /**
     * Get the attachment ID.
     *
     * May be empty, depending on field config.
     *
     * @return string
     */
    public function id()
    {
        return $this->atId;
    }

    /**
     * Get the attachment URL.
     *
     * May be empty, depending on field config.
     *
     * @return string
     */
    public function url()
    {
        return $this->atUrl;
    }

    /**
     * Get the attachment title.
     *
     * May be empty, depending on field config.
     *
     * @return string
     */
    public function title()
    {
        return $this->atTitle;
    }

    /**
     * Get the attachment caption.
     *
     * May be empty, depending on field config.
     *
     * @return string
     */
    public function caption()
    {
        return $this->atCaption;
    }

    /**
     * Get the attachment description.
     *
     * May be empty, depending on field config.
     *
     * @return string
     */
    public function description()
    {
        return $this->atDescription;
    }

    /**
     * Normalize attachment data, depending on how the underlying field is
     * configured.
     *
     * @return void
     *
     * @throws \Exception If invalid return_format is found
     */
    protected function normalize()
    {
        $val = $this->base['value'];

        if ($val) {
            switch ($this->base['return_format']) {
                case 'array':
                    $this->atId          = $val['ID'];
                    $this->atUrl         = $val['url'];
                    $this->atTitle       = $val['title'];
                    $this->atCaption     = $val['caption'];
                    $this->atDescription = $val['description'];
                    break;

                case 'id':
                    $atch = get_post($val);

                    $this->atId          = $val;
                    $this->atUrl         = wp_get_attachment_url($val);
                    $this->atTitle       = $atch->post_title;
                    $this->atCaption     = $atch->post_excerpt;
                    $this->atDescription = $atch->description;
                    break;

                case 'url':
                    $this->atId          = '';
                    $this->atUrl         = $this->base['value'];
                    $this->atTitle       = '';
                    $this->atCaption     = '';
                    $this->atDescription = '';
                    break;

                default:
                    throw new Exception("Field format {$this->base['return_format']} not valid");
            }
        } else {
            $this->atId          = '';
            $this->atUrl         = '';
            $this->atTitle       = '';
            $this->atCaption     = '';
            $this->atDescription = '';
        }
    }
}
