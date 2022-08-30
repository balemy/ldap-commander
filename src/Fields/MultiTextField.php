<?php

namespace App\Fields;

use App\Ldap\EntityForm;
use Yiisoft\Form\Field\Base\InputField;
use Yiisoft\Html\Html;

class MultiTextField extends InputField
{
    protected function generateInput(): string
    {
        $html = '';

        $values = $this->getFormAttributeValue();
        if (!is_array($values)) {
            $values = [$values];
        }

        $this->setInputId = false;

        $i = 0;
        foreach ($values as $val) {
            $html .= Html::div(
                $this->generateInputWithIndex($i, $val),
                ['class' => 'inputRow']
            )->encode(false);
            $i++;
        }

        if ($this->getFormModel() instanceof EntityForm && $this->getFormModel()->isMultiValueAttribute($this->getFormAttributeName())) {
            $html .= Html::a('Add more')->addClass('btnx btn-lightx add-input')->addAttributes(['style' => 'font-size:10px'])
                ->addAttributes([
//                    'data-input-id' => $this->getInputId() . '[replace-with-id]',
                    'data-input-name' => $this->getInputName() . '[replace-with-id]',
                ]);
        }


        return $html;
    }

    protected function generateInputWithIndex($i, $val)
    {
        return Html::textInput(
            $this->getInputName() . '[' . $i . ']',
            $val,
            $this->getInputAttributes()
        )->render();
    }

}
