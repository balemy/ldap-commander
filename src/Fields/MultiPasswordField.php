<?php

namespace Balemy\LdapCommander\Fields;

use Yiisoft\Html\Html;

final class MultiPasswordField extends MultiTextField
{
    protected function generateInputWithIndex(int $i, ?string $val): string
    {
        return (string)Html::div(
            Html::textInput(
                name: $this->getInputName() . '[' . $i . ']',
                value: $val,
                attributes: $this->getInputAttributes()
            )->render() .
            Html::button('Set new',
                [
                    'class' => 'btn btn-outline-secondary',
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#staticSetPassword'
                ]),
            ['class' => 'input-group mb-3'])->encode(false);
    }
}

?>
