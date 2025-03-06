<?php

namespace Balemy\LdapCommander\Modules\UserManager;

use LdapRecord\Models\Entry;
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

final class SidebarWidget extends Widget
{
    public ?UserForm $userForm = null;
    public string $userDn = '';

    public SidebarLocation $location = SidebarLocation::Edit;

    public function __construct(
        public CurrentRoute          $currentRoute,
        public UrlGeneratorInterface $urlGenerator,
        public WebView               $view,
    )
    {
    }

    public function render(): string
    {
        if (empty($this->userDn)) {
            return '';
        }

        $html = Html::openTag('ul', ['class' => 'list-group']);

        if ($this->location !== SidebarLocation::Edit) {
            $html .= Html::tag('li',
                Html::a('Edit User', $this->urlGenerator->generate('user-edit', [], ['dn' => $this->userDn])),
                ['class' => 'list-group-item']
            );
        }

        $html .= Html::tag('li',
            Html::a('Edit Raw Entity', $this->urlGenerator->generate('entity-edit', [], ['dn' => $this->userDn])),
            ['class' => 'list-group-item']
        );

        $html .= Html::tag('li',
            Html::a('Delete User', $this->urlGenerator->generate('user-delete', [], ['dn' => $this->userDn])),
            [
                'onClick' => 'return confirm("Are you sure?")',
                'class' => 'list-group-item'
            ]);

        $html .= Html::closeTag('ul');

        return $html;
    }


}
