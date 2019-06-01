<?php
namespace MagicMenu;

use Cake\Routing\Router;

class CakeUrlBuilder implements \MagicMenu\Contracts\UrlBuilder
{

    /**
     * Return the URL component for a menu item.
     *
     * @param  array  $item
     * @return string
     */
    public function getItemUrl($item)
    {
        return Router::url($item['url']);
    }
}
