<?php
namespace MagicMenu;

use Cake\Http\ServerRequest;
use Cake\Routing\Router;

class CakePathStrategy implements \MagicMenu\Contracts\PathStrategy
{
    protected $_url;

    public function __construct($url = null)
    {
        $this->setUrl($url);
    }

    public function setUrl($url)
    {
        $this->_url = $url;

        return $this;
    }

    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Return the path
     *
     * @param  array  $items  An array of items
     * @return array
     */
    public function getActivePath(array $items)
    {
        $normalized = Router::normalize($this->getUrl());
        $request = new ServerRequest(['url' => $normalized]);
        $parsed = Router::parseRequest($request);

        $items = array_filter(array_map(function ($item) {
            if (empty($item['item']['url'])) {
                return false;
            }
            $normalized = Router::normalize($item['item']['url']);
            try {
                $request = new ServerRequest(['url' => $normalized]);
            } catch (\InvalidArgumentException $e) {
                return false;
            }
            $parsed = Router::parseRequest($request);

            return [
                'path' => $item['path'],
                'normalized' => $normalized,
                'parsed' => $parsed
            ];
        }, $items));

        $exact_matches = array_values(array_filter($items, function ($item) use ($normalized) {
            return $item['normalized'] == $normalized;
        }));

        if ($exact_matches) {
            // TODO: order by path length
            return $exact_matches[0]['path'];
        }

        $highscore = 0;
        $path = [];

        foreach ($items as $item) {
            if ($item['parsed']['controller'] != $parsed['controller'] ||
                $item['parsed']['plugin'] != $parsed['plugin']) {
                continue;
            }

            $score = 0;

            if ($item['parsed']['action'] == $parsed['action']) {
                $score += 10000;
            } elseif ($item['parsed']['action'] == 'index') {
                $score += 9000;
            } else {
                continue;
            }

            if (array_slice($parsed['pass'], 0, count($item['parsed']['pass'])) == $item['parsed']['pass']) {
                $score += 100 * count($item['parsed']['pass']);
            } else {
                continue;
            }

            // TODO: see note in test CakePathStrategyTest::testQuerystringValues
            // if (!empty($parsed['?']) && !empty($item['parsed']['?'])) {
            //  $score += count(array_intersect_assoc($parsed['?'], $item['parsed']['?']));
            // }

            if ($score > $highscore) {
                $highscore = $score;
                $path = $item['path'];
            }
        }

        return $path;
    }
}
