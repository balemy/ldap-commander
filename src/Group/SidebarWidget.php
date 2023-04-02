<?php

namespace Balemy\LdapCommander\Group;

use Yiisoft\Html\Html;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Widget\Widget;

enum GroupSidebarLocation
{
    case Edit;
    case Members;
}

class SidebarWidget extends Widget
{
    public string $dn = '';

    public GroupSidebarLocation $location = GroupSidebarLocation::Edit;

    public function __construct(
        public CurrentRoute          $currentRoute,
        public UrlGeneratorInterface $urlGenerator,
        public WebView               $view,
    )
    {
    }

    public function render(): string
    {
        $html = Html::openTag('ul', ['class' => 'list-group']);
        if ($this->location !== GroupSidebarLocation::Edit) {
            $html .= Html::tag('li',
                Html::a('Edit Group', $this->urlGenerator->generate('group-edit', ['dn' => $this->dn])),
                ['class' => 'list-group-item']
            );
        }
        if ($this->location !== GroupSidebarLocation::Members) {
            $html .= Html::tag('li',
                Html::a('Manage Members', $this->urlGenerator->generate('group-members', ['dn' => $this->dn])),
                ['class' => 'list-group-item']
            );
        }

        $html .= Html::tag('li',
            Html::a('Edit Raw Entity', $this->urlGenerator->generate('entity-edit', ['dn' => $this->dn])),
            ['class' => 'list-group-item']
        );

        $html .= Html::tag('li',
            Html::a('Delete Group', $this->urlGenerator->generate('group-delete', ['dn' => $this->dn])),
            [
                'onClick' => 'return confirm("Are you sure?")',
                'class' => 'list-group-item'
            ]);

        $html .= Html::closeTag('ul');

        return $html;
    }


}
