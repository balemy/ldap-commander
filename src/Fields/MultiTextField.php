<?php

namespace App\Fields;

use App\Ldap\EntityForm;
use Cycle\Schema\Definition\Entity;
use Yiisoft\Form\Field\Base\InputField;
use Yiisoft\Html\Html;

class MultiTextField extends InputField
{
    protected function generateInput(): string
    {
        $html = '';

        /** @var array|string $values */
        $values = $this->getFormAttributeValue();
        if (!is_array($values)) {
            $values = [$values];
        }

        $this->setInputId = false;

        $i = 0;
        /** @var string $val */
        foreach ($values as $val) {
            $html .= Html::div(
                $this->generateInputWithIndex($i, $val),
                ['class' => 'inputRow']
            )->encode(false);
            $i++;
        }

        $model = $this->getFormModel();

        if ($model instanceof EntityForm) {
            if ($model->isMultiValueAttribute($this->getFormAttributeName())) {
                $html .= Html::a('Add more')->addClass('btnx btn-lightx add-input')->addAttributes(['style' => 'font-size:10px'])
                    ->addAttributes(['data-input-name' => $this->getInputName() . '[replace-with-id]',]);
            }
        }

        return $html;
    }

    protected
    function generateInputWithIndex(int $i, ?string $val): string
    {
        return Html::textInput(
            $this->getInputName() . '[' . $i . ']',
            $val,
            $this->getInputAttributes()
        )->render();
    }

}
