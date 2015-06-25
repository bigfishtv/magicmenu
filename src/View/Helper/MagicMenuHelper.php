<?php
namespace MagicMenu\View\Helper;

use MagicMenu\Utility\ArrayUtils;

use Cake\View\Helper;

class MagicMenuHelper extends Helper
{
    protected $_instances = array();

    protected $_defaultConfig = [
        'menuClass' => 'MagicMenu\Menu'
    ];

    public function create($data = null, $options = array()) {
        $class = ArrayUtils::pluck('menuClass', $options) ?: $this->config('menuClass');
        $menu = new $class($data, $options);

        if (!empty($options['id'])) {
            $this->_instances[$options['id']] = $menu;
        }

        return $menu;
    }

    public function get($id) {
        if (!empty($this->_instances[$id])) {
            return $this->_instances[$id];
        }
        return false;
    }
    
}
