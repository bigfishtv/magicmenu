<?php
namespace MagicMenu\View\Helper;

use MagicMenu\Utility\ArrayUtils;

use Cake\View\Helper;

class MagicMenuHelper extends Helper
{
    protected $_instances = [];

    protected $_defaultConfig = [
        'Menu' => 'MagicMenu\Menu',
    ];

    public function create(array $items = [], array $options = [], $instanceName = null)
    {
        $Menu = $this->config('Menu');
        $menu = new $Menu($items, $options);

        if (!is_null($instanceName)) {
            $this->setMenu($instanceName, $menu);
        }

        return $menu;
    }

    public function setMenu($instanceName, $menu)
    {
        $this->_instances[$instanceName] = $menu;
        return $this;
    }

    public function getMenu($instanceName)
    {
        if (!empty($this->_instances[$instanceName])) {
            return $this->_instances[$instanceName];
        }
        return false;
    }
    
}
