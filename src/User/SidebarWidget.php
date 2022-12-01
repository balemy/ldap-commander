<?php

namespace Balemy\LdapCommander\User;

use Yiisoft\Html\Html;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Widget\Widget;

enum SidebarLocation
{
    case Edit;
    case Members;
}

class SidebarWidget extends Widget
{
    public string $dn = '';

    public SidebarLocation $location = SidebarLocation::Edit;

    public function __construct(
        public CurrentRoute          $currentRoute,
        public UrlGeneratorInterface $urlGenerator,
        public WebView               $view,
    )
    {
    }

    protected function run(): string
    {
       if (empty($this->dn)) {
            return '';
        }

        $html = Html::openTag('ul', ['class' => 'list-group']);

        if ($this->location !== SidebarLocation::Edit) {
            $html .= Html::tag('li',
                Html::a('Edit User', $this->urlGenerator->generate('user-edit', ['dn' => $this->dn])),
                ['class' => 'list-group-item']
            );
        }
        if ($this->location !== SidebarLocation::Members) {
            $html .= Html::tag('li',
                Html::a('Groups', $this->urlGenerator->generate('user-groups', ['dn' => $this->dn])),
                ['class' => 'list-group-item']
            );
        }

        $html .= Html::tag('li',
            Html::a('Edit Raw Entity', $this->urlGenerator->generate('entity-edit', ['dn' => $this->dn])),
            ['class' => 'list-group-item']
        );

        $html .= Html::tag('li',
            Html::a('Delete User', $this->urlGenerator->generate('user-delete', ['dn' => $this->dn])),
            [
                'onClick' => 'return confirm("Are you sure?")',
                'class' => 'list-group-item'
            ]);

        $html .= Html::closeTag('ul');

        return $html;
    }


}
