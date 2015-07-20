<?php
namespace MagicMenu\Contracts;

interface PathStrategy
{
	
	/**
	 * Return the path
	 * 
	 * @param  array  $items  An array of items
	 * @return array
	 */
	public function getActivePath(array $items);

}