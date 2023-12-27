<?php

namespace Balemy\LdapCommander\Modules\EntityBrowser\Widgets;

use Yiisoft\Html\Html;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Widget\Widget;

enum EntitySidebarLocation
{
    case ListChildren;
    case Edit;
    case Add;
}

class EntitySidebar extends Widget
{
    public string $dn;
    public EntitySidebarLocation $location = EntitySidebarLocation::ListChildren;

    public function __construct(
        public CurrentRoute          $currentRoute,
        public UrlGeneratorInterface $urlGenerator,
        public WebView               $view,
    )
    {
        $this->dn = '';
    }

    public function render(): string
    {
        $html = Html::openTag('ul', ['class' => 'list-group']);
        if ($this->location !== EntitySidebarLocation::Add) {
            $html .= Html::tag('li',
                Html::a('Add Children', $this->urlGenerator->generate('entity-edit', [], ['dn' => $this->dn, 'new' => 1])),
                ['class' => 'list-group-item']
            );
        }
        if ($this->location !== EntitySidebarLocation::ListChildren) {
            $html .= Html::tag('li',
                Html::a('List Children', $this->urlGenerator->generate('entity-list', [], ['dn' => $this->dn])),
                ['class' => 'list-group-item']
            );
        }

        if ($this->location !== EntitySidebarLocation::Edit) {
            $html .= Html::tag('li',
                Html::a('Edit Entity', $this->urlGenerator->generate('entity-edit', [], ['dn' => $this->dn])),
                ['class' => 'list-group-item']
            );
        }

        $html .= Html::tag('li',
            Html::a('Rename Entity', $this->urlGenerator->generate('entity-rename', [], ['dn' => $this->dn])),
            ['class' => 'list-group-item']
        );

        $html .= Html::tag('li',
            Html::a('Move Entity', $this->urlGenerator->generate('entity-move', [], ['dn' => $this->dn])),
            ['class' => 'list-group-item']
        );

        $html .= Html::tag('li',
            Html::a('Duplicate Entity', $this->urlGenerator->generate('entity-edit', [], ['dn' => $this->dn, 'duplicate' => 1, 'new' => 1])),
            ['class' => 'list-group-item']
        );

        $html .= Html::tag('li',
            Html::a('Delete Entity', $this->urlGenerator->generate('entity-delete', [], ['dn' => $this->dn]), [
                'onClick' => 'return confirm("Are you sure?")'
            ]),
            ['class' => 'list-group-item']

        );

        $html .= Html::closeTag('ul');

        return $html;
    }


}
