<?php // @codingStandardsIgnoreFile
namespace Balemy\LdapCommander\Tests\Support\Helper;


use Exception;

// Select2 helpers for Codeception
// See: https://select2.org
//
// Works with Select2 version 4.0 or greater
// Which is a jQuery based replacement for select boxes.
// See: https://select2.org
//
// Author: Tortue Torche <tortuetorche@spam.me>
// Author: Florian Krämer
// Author: Tom Walsh
// License: MIT
//
// Installation:
// * Put this file in your 'tests/support/Helper' directory
// * Add it in your 'tests/acceptance.suite.yml' file, like this:
//    class_name: AcceptanceTester
//    modules:
//        enabled:
//            - WebDriver:
//              url: 'http://localhost:8000'
//              # ...
//            - \Helper\Select2
//
// * Then run: ./vendor/bin/codecept build

class Select2 extends \Codeception\Module
{
    /**
     * @param $selector
     * @param $optionText
     * @param bool $expectedReturn Default to true
     *
     * @return string JavaScript
     */
    protected function _optionIsSelectedForSelect2($selector, $optionText, $expectedReturn = true)
    {
        $returnFlag = $expectedReturn === true ? '' : '!';
        return $script = <<<EOT
return (function (\$) {
  var isSelected = false;
  var values = \$("$selector").select2("data");
  values = \$.isArray(values) ? values : [values];
  if (values && values.length > 0) {
    isSelected = values.some(function (data) {
      if (data && data.text && data.text === "$optionText") {
        return data;
      }
    });
  }
  return ${returnFlag}isSelected;
}(jQuery));
EOT;
    }

    /**
     * Wait until the select2 component is loaded
     *
     * @param $selector
     * @param int $timeout seconds. Default to 5
     */
    public function waitForSelect2($selector, $timeout = 5)
    {
        $I = $this->getAcceptanceModule();
        $I->waitForJS('return !!jQuery("' . $selector . '").data("select2");', $timeout);
    }

    /**
     * Checks that the given option is not selected.
     *
     * @param $selector
     * @param $optionText
     * @param int $timeout seconds. Default to 5
     */
    public function dontSeeOptionIsSelectedForSelect2($selector, $optionText, $timeout = 5)
    {
        $I = $this->getAcceptanceModule();
        $this->waitForSelect2($selector, $timeout);
        $script = $this->_optionIsSelectedForSelect2($selector, $optionText, false);
        $I->waitForJS($script, $timeout);
    }

    /**
     * Checks that the given option is selected.
     *
     * @param $selector
     * @param $optionText
     * @param int $timeout seconds. Default to 5
     */
    public function seeOptionIsSelectedForSelect2($selector, $optionText, $timeout = 5)
    {
        $I = $this->getAcceptanceModule();
        $this->waitForSelect2($selector, $timeout);
        $script = $this->_optionIsSelectedForSelect2($selector, $optionText);
        $I->waitForJS($script, $timeout);
    }

    /**
     * Selects an option in a select2 component.
     *
     *   $I->selectOptionForSelect2('#my_select2', 'Option value');
     *   $I->selectOptionForSelect2('#my_select2', ['Option value 1', 'Option value 2']);
     *   $I->selectOptionForSelect2('#my_select2', ['text' => 'Option text']);
     *   $I->selectOptionForSelect2('#my_select2', ['id' => 'Option value', 'text' => 'Option text']);
     *
     * @param $selector
     * @param $option
     * @param int $timeout seconds. Default to 1
     */
    public function selectOptionForSelect2($selector, $option, $timeout = 5)
    {
        $I = $this->getAcceptanceModule();
        $this->waitForSelect2($selector, $timeout);

        if (is_int($option)) {
            $option = (string)$option;
        }

        if (is_string($option) || (is_array($option) && array_values($option) === $option)) {
            $I->executeJS('jQuery("' . $selector . '").val(' . json_encode($option) . ');', [$timeout]);
            $I->executeJS('jQuery("' . $selector . '").trigger("select2:select").trigger("change");', [$timeout]);
        } else if (is_array($option)) {
            $optionId = 'null';
            if (isset($option['text']) && empty($option['id'])) {
                $optionText = $option['text'];
                $optionId = <<<EOT
function() {
  if (!\$.expr[':'].textEquals) {
    // Source: http://stackoverflow.com/a/26431267
    \$.expr[':'].textEquals = function(el, i, m) {
      var searchText = m[3];
      return $(el).text().trim() === searchText;
    }
  }
  // Find select option by text
  return \$("$selector").find("option:textEquals(\"$optionText\"):first").val();
}()
EOT;
            }
            $jsonOption = json_encode($option);
            $script = <<<EOT
(function (\$) {
  var option = $jsonOption;
  if (!option.id) {
    option.id = $optionId;
  }
  \$("$selector").val(option.id).trigger('select2:select').trigger('change');
}(jQuery));
EOT;
            $I->executeJS($script, [$timeout]);
        } else {
            $I->fail();
        }
    }

    /**
     * Unselect an option in the given select2 component.
     *
     * @param $selector
     * @param $option
     * @param int $timeout seconds. Default to 1
     */
    public function unselectOptionForSelect2($selector, $option = null, $timeout = 1)
    {
        $I = $this->getAcceptanceModule();
        $this->waitForSelect2($selector, $timeout);
        if ($option && is_string($option)) {
            $script = <<<EOT
(function (\$) {
  var values = \$("$selector").val();
  values = \$.isArray(values) ? values : [values];
  var index = values.indexOf("$option");
  if (index > -1) {
    values.splice(index, 1);
  }
  \$("$selector").val(values);
  \$("$selector").trigger("select2:select").trigger("change");
}(jQuery));
EOT;
            $I->executeJS($script, [$timeout]);
        } else {
            $I->executeJS('jQuery("' . $selector . '").val(null);', [$timeout]);
            $I->executeJS('jQuery("' . $selector . '").trigger("select2:select").trigger("change");', [$timeout]);
        }
    }

    /**
     * Open the Select2 component
     * @param string $selector
     */
    public function openSelect2($selector)
    {
        $I = $this->getAcceptanceModule();
        $this->waitForSelect2($selector);
        $I->executeJS('jQuery("' . $selector . '").select2("open");');
    }

    /**
     * Close the Select2 component
     * @param string $selector
     */
    public function closeSelect2($selector)
    {
        $I = $this->getAcceptanceModule();
        $this->waitForSelect2($selector);
        $I->executeJS('jQuery("' . $selector . '").select2("close");');
    }

    protected function getAcceptanceModule()
    {
        if (!$this->hasModule('WebDriver')) {
            throw new Exception("You must enable the WebDriver module", 1);
        }

        return $this->getModule('WebDriver');
    }
}
