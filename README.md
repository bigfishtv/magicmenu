# magicmenu


### Installation
```
composer require bigfishtv/magicmenu
```

### Usage

AppView.php

```php
$this->loadHelper('MagicMenu.MagicMenu');
```

[your-view].[ctp|php]

```php
$items = [
    ['title' => 'About', 'url' => '/about'],
    ['title' => 'Work', 'url' => '/work', 'children' => [
        ['title' => 'One', 'url' => '/work/one'],
        ['title' => 'Two', 'url' => '/work/two'],
        ['title' => 'Three & Four', 'url' => '/work/three-and-four'],
    ]],
    ['title' => 'Contact', 'url' => '/contact']
];
$menu = $this->MagicMenu->create($items);
echo $menu->render();
```

### Output

```html
<ul>
    <li>
        <a href="/about">
            <span>About</span>
        </a>
    </li>
    <li>
        <a href="/work">
            <span>Work</span>
        </a>
        <ul>
            <li>
                <a href="/work/one">
                    <span>One</span>
                </a>
            </li>
            <li>
                <a href="/work/two">
                    <span>Two</span>
                </a>
            </li>
            <li>
                <a href="/work/three-and-four">
                    <span>Three &amp; Four</span>
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a href="/contact">
            <span>Contact</span>
        </a>
    </li>
</ul>
```
