<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Widget;

use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Bootstrap5\Alert;
use Yiisoft\Widget\Widget;

final class FlashMessage extends Widget
{
    private FlashInterface $flash;

    public function __construct(FlashInterface $flash)
    {
        $this->flash = $flash;
    }

    public function render(): string
    {
        $flashes = $this->flash->getAll();

        $html = [];
        /** @var array $data */
        foreach ($flashes as $type => $data) {
            /** @var array|string $message */
            foreach ($data as $message) {
                $body = (is_array($message)) ? (string)$message['body'] : $message;

                $html[] = Alert::widget()
                    ->addClass("alert-{$type}", 'shadow')
                    ->body($body);
            }
        }

        return implode($html);
    }
}
