<?php
namespace MagicMenu\View\Helper;

use MagicMenu\Utility\ArrayUtils;

use Cake\View\Helper;

class MagicMenuHelper extends Helper
{
    protected $_instances = [];

    protected $_defaultConfig = [
        'menuClass' => 'MagicMenu\Menu'
    ];

    public function create($data = null, $options = [])
    {
        $class = ArrayUtils::pluck('menuClass', $options) ?: $this->config('menuClass');
        $id = ArrayUtils::pluck('id', $options);
        
        $menu = new $class($data, $options);

        if (!is_null($id)) {
            $this->_instances[$id] = $menu;
        }

        return $menu;
    }

    public function get($id)
    {
        if (!empty($this->_instances[$id])) {
            return $this->_instances[$id];
        }
        return false;
    }
    
}
