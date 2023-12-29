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

class SidebarWidget extends Widget
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

        $user = User::query()->addSelect(['*', '+'])->findOrFail($this->userDn);

        $html = Html::openTag('ul', ['class' => 'list-group']);

        if ($this->location !== SidebarLocation::Edit) {
            $html .= Html::tag('li',
                Html::a('Edit User', $this->urlGenerator->generate('user-edit', [], ['dn' => $this->userDn])),
                ['class' => 'list-group-item']
            );
        }
        if ($this->location !== SidebarLocation::Members) {
            $memberCountBadge = '<span class="badge rounded-pill bg-primary float-end" style="text-decoration:none;margin-top:3px">' .
                count($user->getGroups()) .
                '</span>';

            $html .= Html::tag('li',
                Html::a('Groups' .
                    $memberCountBadge,
                    $this->urlGenerator->generate('user-groups', [], ['dn' => $this->userDn])
                )->encode(false),
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
