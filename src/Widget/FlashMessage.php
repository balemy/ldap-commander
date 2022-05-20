<?php

declare(strict_types=1);

namespace App\Widget;

use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Yii\Bootstrap5\Alert;
use Yiisoft\Widget\Widget;

final class FlashMessage extends Widget
{
    private FlashInterface $flash;

    public function __construct(FlashInterface $flash)
    {
        $this->flash = $flash;
    }

    protected function run(): string
    {
        $flashes = $this->flash->getAll();

        $html = [];
        /** @var array $data */
        foreach ($flashes as $type => $data) {
            /** @var array|string $message */
            foreach ($data as $message) {
                $body = (is_array($message)) ? (string)$message['body'] : $message;

                $html[] = Alert::widget()
                    ->options(['class' => "alert-{$type} shadow"])
                    ->body($body);
            }
        }

        return implode($html);
    }
}
