<?php

namespace Balemy\LdapCommander\Form\Fields;

use Yiisoft\Form\Field\Base\InputField;
use Yiisoft\Html\Html;

class MultiTextField extends InputField
{

    protected function generateInput(): string
    {
        $html = '';

        /** @var array|string $values */
        $values = $this->getInputData()->getValue();
        if (!is_array($values)) {
            $values = [$values];
        }

        // Make sure at least one input is rendered
        if (count($values) === 0) {
            $values = [''];
        }

        #$this->setInputId = false;

        $i = 0;
        /** @var string $val */
        foreach ($values as $val) {
            $html .= Html::div(
                $this->generateInputWithIndex($i, $val),
                ['class' => 'inputRow']
            )->encode(false);
            $i++;
        }

        $html .= Html::a('Add more')->addClass('btnx btn-lightx add-input')->addAttributes(['style' => 'font-size:10px'])
            ->addAttributes(['data-input-name' => $this->getInputName() . '[replace-with-id]',]);

        return $html;
    }


    protected function generateInputWithIndex(int $i, ?string $val): string
    {
        $input = Html::textInput(
            $this->getInputName() . '[' . $i . ']',
            $val,
            $this->getInputAttributes()
        );

        return $input->render();
    }

    protected function getInputName(): string
    {
        return $this->getInputData()->getName() ?? '';
    }

}
