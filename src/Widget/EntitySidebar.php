<?php

namespace App\Widget;

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
    public string $dn = '';

    public EntitySidebarLocation $location = EntitySidebarLocation::ListChildren;

    public function __construct(
        public CurrentRoute          $currentRoute,
        public UrlGeneratorInterface $urlGenerator,
        public WebView               $view,
    )
    {
    }

    protected function run(): string
    {
        $html = Html::openTag('ul');
        if ($this->location !== EntitySidebarLocation::Add) {
            $html .= Html::tag('li', Html::a('Add Children', $this->urlGenerator->generate('entity-edit', ['dn' => $this->dn, 'new' => 1])));
        }
        if ($this->location !== EntitySidebarLocation::ListChildren) {
            $html .= Html::tag('li', Html::a('List Children', $this->urlGenerator->generate('entity-list', ['dn' => $this->dn])));
        }

        if ($this->location !== EntitySidebarLocation::Edit) {
            $html .= Html::tag('li', Html::a('Edit Entity', $this->urlGenerator->generate('entity-edit', ['dn' => $this->dn])));
        }

        $html .= Html::tag('li',
            Html::a('Delete Entity', $this->urlGenerator->generate('entity-delete', ['dn' => $this->dn]), [
                'onClick' => 'return confirm("Are you sure?")'
            ])
        );

        $html .= Html::closeTag('ul');

        return $html;
    }


}
