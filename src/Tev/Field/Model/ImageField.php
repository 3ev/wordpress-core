<?php
namespace Tev\Field\Model;

/**
 * Image field.
 *
 * Provides access to different image sizes and base information where
 * possible.
 */
class ImageField extends FileField
{
    /**
     * Full image width.
     *
     * @var int
     */
    private $atWidth;

    /**
     * Full image height.
     *
     * @var int
     */
    private $atHeight;

    /**
     * Get full image width.
     *
     * May be 0 depending on field config.
     *
     * @return int
     */
    public function width()
    {
        return $this->atWidth;
    }

    /**
     * Get full image height.
     *
     * May be 0 depending on field config.
     *
     * @return int
     */
    public function height()
    {
        return $this->atHeight;
    }

    /**
     * Get the image thumbnail URL if possible.
     *
     * @return string URL or empty string
     */
    public function thumbnailUrl()
    {
        return $this->sizeUrl('thumbnail');
    }

    /**
     * Get the image medium URL if possible.
     *
     * @return string URL or empty string
     */
    public function mediumUrl()
    {
        return $this->sizeUrl('medium');
    }

    /**
     * Get the image large URL if possible.
     *
     * @return string URL or empty string
     */
    public function largeUrl()
    {
        return $this->sizeUrl('large');
    }

    /**
     * Get an image URL of a specic size.
     *
     * @param  string $size Image size (e.g thumbnail, large or custom size)
     * @return string       Image URL
     */
    public function sizeUrl($size)
    {
        if (($this->base['return_format'] === 'array') && isset($this->base['value']['sizes'][$size])) {
            return $this->base['value']['sizes'][$size];
        } elseif ($this->base['return_format'] === 'id') {
            if ($src = wp_get_attachment_image_src($this->id(), $size)) {
                return $src[0];
            }
        }

        return '';
    }

    /**
     * {@inheritDoc}
     */
    protected function normalize()
    {
        parent::normalize();

        $val = $this->base['value'];

        if ($val) {
            switch ($this->base['return_format']) {
                case 'array':
                    $this->atWidth  = $val['width'];
                    $this->atHeight = $val['height'];
                    break;

                case 'id':
                    $src = wp_get_attachment_image_src($this->id(), 'full');

                    $this->atWidth  = $src[1];
                    $this->atHeight = $src[2];
                    break;

                case 'url':
                    $this->atWidth  = 0;
                    $this->atHeight = 0;
                    break;

                default:
                    throw new Exception("Field format {$this->base['return_format']} not valid");
            }
        } else {
            $this->atWidth  = 0;
            $this->atHeight = 0;
        }
    }
}
