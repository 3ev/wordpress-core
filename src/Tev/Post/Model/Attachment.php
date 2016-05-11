<?php
namespace Tev\Post\Model;

/**
 * Entity class for the 'attachment' post type.
 */
class Attachment extends AbstractPost
{
    /**
     * Get attachment URL.
     *
     * @return string
     */
    public function getAttachmentUrl()
    {
        return wp_get_attachment_url($this->getId());
    }


    /**
     * Get attachment file.
     *
     * @return string
     */
    public function getAttachmentFile()
    {
        return get_attached_file($this->getId());
    }

    /**
     * Get attachment image URL.
     *
     * @param  mixed       $size Arguments accepted by second param of wp_get_attachment_image_src()
     * @return string|null       Image URL or null if not found
     */
    public function getImageUrl($size = 'thumbnail')
    {
        $res = wp_get_attachment_image_src($this->getId(), $size);
        return $res && isset($res[0]) ? $res[0] : null;
    }

    /**
     * Get alt text for this attachment.
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->meta('_wp_attachment_image_alt');
    }

    /**
     * Get caption for this attachment.
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->getExcerpt();
    }

    /**
     * Get description for this attachment.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getContent();
    }
}
