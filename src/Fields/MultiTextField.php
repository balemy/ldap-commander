<?php

namespace Balemy\LdapCommander\Fields;

use Balemy\LdapCommander\Ldap\EntityForm;
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

        $entityForm = $this->getEntityForm();
        if ($entityForm !== null) {
            if ($entityForm->isMultiValueAttribute($this->getFormAttributeName())) {
                $html .= Html::a('Add more')->addClass('btnx btn-lightx add-input')->addAttributes(['style' => 'font-size:10px'])
                    ->addAttributes(['data-input-name' => $this->getInputName() . '[replace-with-id]',]);
            }
        }

        return $html;
    }

    protected function getEntityForm(): ?EntityForm
    {
        $model = $this->getFormModel();

        if ($model instanceof EntityForm) {
            return $model;
        }

        return null;
    }


    protected function generateInputWithIndex(int $i, ?string $val): string
    {
        $input = Html::textInput(
            $this->getInputName() . '[' . $i . ']',
            $val,
            $this->getInputAttributes()
        );

        $entityForm = $this->getEntityForm();
        if ($entityForm !== null &&
            !$entityForm->isNewRecord &&
            $entityForm->getRdnAttributeId() === $this->getFormAttributeName() &&
            $entityForm->getRdnAttributeValue() === $val) {

            $input = $input->disabled();
        }

        return $input->render();
    }

}
