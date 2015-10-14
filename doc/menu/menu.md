##Menu
___
###About:
The menu system based on [KnpMenu](https://github.com/KnpLabs/KnpMenu). Please read their docs.
####Contents:
- [Add new menu](#markdown-header-add-new-menu)
- [Customize menu](#markdown-header-customize-menu)
___


###Add new menu:

#####To add new menu there are 3 steps:
**1.** **Create new menu class** in ``application/src/Core/Service/Menu/Builder``, for example ``SomeMenu``.

It **MUST** implements ``Core\Service\Menu\BuilderInterface`` interface.

````
//application/src/Core/Service/Menu/Builder/SomeMenu.php
namespace Core\Service\Menu\Builder;

use Core\Service\Menu\MenuBuilder;
use Knp\Menu\MenuItem;

class SomeMenu extends MenuBuilder
{
    public function build()
    {
        $menu = $this->getMenuFactory()->createItem('Customer Main Menu');

        $menu->addChild('Dashboard', array(
            'path' => 'dashboard',
            'attributes' => array('class' => 'dashboard-link')
        ));

        $menu->addChild('Video Trainings', array(
            'path' => 'videotrainings',
            'icon_class' => 'icon-play-circle'
        ));

        return $menu;
    }
}
````

**2.** **Create service**:

Goto ``application/config/dependency_injection.php`` and add new menu service.

Menu service name **MUST** starts with ``core.menu.builder.``

````
$config['core.menu.builder.some.menu'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Menu\Builder\SomeMenu',
    'arguments' => array(
        '@services.container',
    ),
);
````

**3.** **Render menu**:

Load helper "*menu*" and use function ``menu_render('menu.name')``.

**Menu name** is line after the predefined *menu service name* e.g.``core.menu.builder.``

In our case **Menu name is** - ``some.menu``

To render menu in template use:

````
<?php echo menu_render('some.menu'); ?>
````


###Customize menu:

Menu rendered via special *render object*. It **MUST** implemens ``Knp\Menu\Renderer\RendererInterface``.

KnpMenu has already got  **ListRenderer**, and you can use it or extend.

**Create service**:

````
$config['core.menu.renderer.list'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Menu\MenuListRenderer',
    'arguments' => array(
        '@knp.menu.matcher',
    ),
);
````

Now you have 2 ways to tell menu which way it should be rendered:

1. Add **getRendererService** method to your menu-builder class. The method should return *service name* by means of which the menu should be rendered.

````
    //....... some code
    
    public function getRendererService()
    {
        return 'core.menu.renderer.list';
    }    

    //....... come code    
````
2. Another way is tell 	*service renderer name* directly in a template. Pass *service renderer name* as 3rd parameter of ``menu_render()`` function.

````
<?php echo menu_render('some.menu', array(), 'core.menu.renderer.list'); ?
````