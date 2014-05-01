<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace GbiliLangModule\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Stdlib\ArrayUtils;

/**
 * @changes Allow specifying which attributes should be translated
 * by adding a 'translate' option with an array of attribute to boolan (true: translate)
 */
class FormSelect extends \Zend\Form\View\Helper\FormSelect
{
    protected $translatableAttributes = array(
        'label' => true,
    );

    /**
     * @see parent
     */
    public function render(ElementInterface $element)
    {
        $default = $this->translatableAttributes['label'];

        $elementOptions = $element->getOptions();
        if (isset($elementOptions['translate']['label'])) {
           $this->translatableAttributes['label'] = (boolean) $elementOptions['translate']['label'];
        }        

        $parentReturn = parent::render($element);

        $this->translatableAttributes['label'] = $default;

        return $parentReturn;
    }

    /**
     * Render an array of options
     *
     * Individual options should be of the form:
     *
     * <code>
     * array(
     *     'value'    => 'value',
     *     'label'    => 'label',
     *     'disabled' => $booleanFlag,
     *     'selected' => $booleanFlag,
     * )
     * </code>
     *
     * @param  array $options
     * @param  array $selectedOptions Option values that should be marked as selected
     * @return string
     */
    public function renderOptions(array $options, array $selectedOptions = array())
    {
        $template      = '<option %s>%s</option>';
        $optionStrings = array();
        $escapeHtml    = $this->getEscapeHtmlHelper();

        foreach ($options as $key => $optionSpec) {
            $value    = '';
            $label    = '';
            $selected = false;
            $disabled = false;

            if (is_scalar($optionSpec)) {
                $optionSpec = array(
                    'label' => $optionSpec,
                    'value' => $key
                );
            }

            if (isset($optionSpec['options']) && is_array($optionSpec['options'])) {
                $optionStrings[] = $this->renderOptgroup($optionSpec, $selectedOptions);
                continue;
            }

            if (isset($optionSpec['value'])) {
                $value = $optionSpec['value'];
            }
            if (isset($optionSpec['label'])) {
                $label = $optionSpec['label'];
            }
            if (isset($optionSpec['selected'])) {
                $selected = $optionSpec['selected'];
            }
            if (isset($optionSpec['disabled'])) {
                $disabled = $optionSpec['disabled'];
            }

            if (ArrayUtils::inArray($value, $selectedOptions)) {
                $selected = true;
            }

            if ($this->translatableAttributes['label'] && null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                    $label, $this->getTranslatorTextDomain()
                );
            }

            $attributes = compact('value', 'selected', 'disabled');

            if (isset($optionSpec['attributes']) && is_array($optionSpec['attributes'])) {
                $attributes = array_merge($attributes, $optionSpec['attributes']);
            }

            $this->validTagAttributes = $this->validOptionAttributes;
            $optionStrings[] = sprintf(
                $template,
                $this->createAttributesString($attributes),
                $escapeHtml($label)
            );
        }

        return implode("\n", $optionStrings);
    }

    /**
     * Render an optgroup
     *
     * See {@link renderOptions()} for the options specification. Basically,
     * an optgroup is simply an option that has an additional "options" key
     * with an array following the specification for renderOptions().
     *
     * @param  array $optgroup
     * @param  array $selectedOptions
     * @return string
     */
    public function renderOptgroup(array $optgroup, array $selectedOptions = array())
    {
        $template = '<optgroup%s>%s</optgroup>';

        $options = array();
        if (isset($optgroup['options']) && is_array($optgroup['options'])) {
            $options = $optgroup['options'];
            unset($optgroup['options']);
        }

        $this->validTagAttributes = $this->validOptgroupAttributes;
        $attributes = $this->createAttributesString($optgroup);
        if (!empty($attributes)) {
            $attributes = ' ' . $attributes;
        }

        return sprintf(
            $template,
            $attributes,
            $this->renderOptions($options, $selectedOptions)
        );
    }

    /**
     * Ensure that the value is set appropriately
     *
     * If the element's value attribute is an array, but there is no multiple
     * attribute, or that attribute does not evaluate to true, then we have
     * a domain issue -- you cannot have multiple options selected unless the
     * multiple attribute is present and enabled.
     *
     * @param  mixed $value
     * @param  array $attributes
     * @return array
     * @throws Exception\DomainException
     */
    protected function validateMultiValue($value, array $attributes)
    {
        if (null === $value) {
            return array();
        }

        if (!is_array($value)) {
            return (array) $value;
        }

        if (!isset($attributes['multiple']) || !$attributes['multiple']) {
            throw new Exception\DomainException(sprintf(
                '%s does not allow specifying multiple selected values when the element does not have a multiple attribute set to a boolean true',
                __CLASS__
            ));
        }

        return $value;
    }
}
