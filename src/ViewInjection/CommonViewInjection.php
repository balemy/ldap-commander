<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\ViewInjection;

use Balemy\LdapCommander\ApplicationParameters;
use Yiisoft\Yii\View\CommonParametersInjectionInterface;

final class CommonViewInjection implements CommonParametersInjectionInterface
{
    private ApplicationParameters $applicationParameters;

    public function __construct(
        ApplicationParameters $applicationParameters
    )
    {
        $this->applicationParameters = $applicationParameters;
    }

    public function getCommonParameters(): array
    {
        return [
            'applicationParameters' => $this->applicationParameters,
        ];
    }
}
