<?php
namespace MagicMenu\Contracts;

interface UrlBuilder
{

    /**
     * Return the URL component for a menu item.
     *
     * @param  array  $item
     * @return string
     */
    public function getItemUrl($item);
}
