<?php

namespace Balemy\LdapCommander\Modules\SlapdConfig\Widgets;

use Yiisoft\Html\Html;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Widget\Widget;

class BindUserSidebarWidget extends Widget
{
    public string $dn = '';

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

        $html .= Html::tag('li',
            Html::a('Edit Raw Entity', $this->urlGenerator->generate('entity-edit', [], ['dn' => $this->dn])),
            ['class' => 'list-group-item']
        );

        $html .= Html::tag('li',
            Html::a('Delete Bind User', $this->urlGenerator->generate('bind-user-delete', [], ['dn' => $this->dn])),
            [
                'onClick' => 'return confirm("Are you sure?")',
                'class' => 'list-group-item'
            ]);

        $html .= Html::closeTag('ul');

        return $html;
    }


}
