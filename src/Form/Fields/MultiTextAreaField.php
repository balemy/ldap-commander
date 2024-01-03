<?php

namespace Balemy\LdapCommander\Form\Fields;

use Yiisoft\Form\Field\Base\InputField;
use Yiisoft\Html\Html;

class MultiTextAreaField extends MultiTextField
{
    protected function generateInputWithIndex(int $i, ?string $val): string
    {
        $input = Html::textarea(
            $this->getInputName() . '[' . $i . ']',
            $val,
            $this->getInputAttributes()
        )->rows(4);

        return $input->render();
    }
}
