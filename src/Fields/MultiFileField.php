<?php

namespace App\Fields;

use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;

final class MultiFileField extends MultiTextField
{

    public function __construct(public UrlGeneratorInterface $urlGenerator,
                                public ?string                $dn = '',
    )
    {
    }


    protected function generateInputWithIndex(int $i, ?string $val): string
    {
        $fileControls = '';

        if (!empty($val)) {
            $fileControls = Html::a(
                    'Download (' . strlen($val) . ' bytes)',
                    $this->urlGenerator->generate('entity-attribute-download', [
                        'dn' => $this->dn, 'attribute' => $this->getFormAttributeName(), 'i' => $i
                    ]),
                    ['target' => '_blank', 'class' => 'btn btn-outline-secondary download-binary-button']
                ) .
                Html::a(
                    'Delete',
                    'javascript:void();',
                    ['class' => 'btn btn-outline-secondary delete-binary-button']
                );
        }

        return (string)Html::div(
            $fileControls .
            Html::file(
                name: $this->getInputName() . '[' . $i . ']',
                value: $val,
                attributes: $this->getInputAttributes()
            )->addAttributes(['style' => (empty($val)) ? '' : 'display:none'])->disabled(true)->render(),
            ['class' => 'input-group mb-3']
        )->encode(false);
    }
}

?>
