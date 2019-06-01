<?php
namespace MagicMenu\View\Helper;

use Cake\View\Helper;

use MagicMenu\CakePathStrategy;
use MagicMenu\CakeUrlBuilder;
use MagicMenu\Utility\ArrayUtils;

class MagicMenuHelper extends Helper
{
    protected $_instances = [];

    protected $_defaultConfig = [
        'Menu' => 'MagicMenu\Menu',
    ];

    public function create(array $items = [], array $options = [], $instanceName = null)
    {
        $Menu = $this->getConfig('Menu');
        $menu = new $Menu($items, $options);

        // maintain compatibility with CakePHP < 3.7
        if (method_exists($this->getView(), 'getRequest')) {
            $url = $this->getView()->getRequest()->getAttribute('here');
        } else {
            $url = $this->getView()->request->here;
        }

        $menu->setUrlBuilder(new CakeUrlBuilder());
        $menu->setPathStrategy(new CakePathStrategy($url));

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
