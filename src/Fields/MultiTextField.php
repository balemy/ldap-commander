<?php

namespace Balemy\LdapCommander\Fields;

use Balemy\LdapCommander\Ldap\EntityForm;
use Yiisoft\Form\Field\Base\InputField;
use Yiisoft\Html\Html;

class MultiTextField extends InputField
{

    public ?EntityForm $entityForm = null;

    protected function generateInput(): string
    {
        $html = '';

        /** @var array|string $values */
        $values = $this->getInputData()->getValue();
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

        if ($this->entityForm !== null) {
            $inputName = $this->getInputData()->getName() ?? 'EmptyInputName!!';

            if ($this->entityForm->isMultiValueAttribute($inputName)) {
                $html .= Html::a('Add more')->addClass('btnx btn-lightx add-input')->addAttributes(['style' => 'font-size:10px'])
                    ->addAttributes(['data-input-name' => $inputName . '[replace-with-id]',]);
            }
        }

        return $html;
    }


    protected function generateInputWithIndex(int $i, ?string $val): string
    {
        $inputName = $this->getInputData()->getName() ?? 'EmptyInputName!!';

        $input = Html::textInput(
            $inputName . '[' . $i . ']',
            $val,
            $this->getInputAttributes()
        );

        if ($this->entityForm !== null &&
            !$this->entityForm->isNewRecord &&
            $this->entityForm->getRdnAttributeId() === $this->getInputData()->getName() &&
            $this->entityForm->getRdnAttributeValue() === $val) {

            $input = $input->disabled();
        }

        return $input->render();
    }

}
