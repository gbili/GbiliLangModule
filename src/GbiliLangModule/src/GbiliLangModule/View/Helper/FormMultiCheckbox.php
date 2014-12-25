<?php
/**
 * Look for "Added by g"
 * Added support for valueOptions specific label translation and attributes
 */

namespace GbiliLangModule\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\Element\MultiCheckbox as MultiCheckboxElement;
use Zend\Form\Exception;

class FormMultiCheckbox extends \Zend\Form\View\Helper\FormMultiCheckbox
{
    protected $translatableAttributes = array(
        'label' => true,
    );

    /**
     * Render options
     *
     * @param  MultiCheckboxElement $element
     * @param  array                $options
     * @param  array                $selectedOptions
     * @param  array                $attributes
     * @return string
     */
    protected function renderOptions(MultiCheckboxElement $element, array $options, array $selectedOptions,
        array $attributes)
    {
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $labelHelper      = $this->getLabelHelper();
        $labelClose       = $labelHelper->closeTag();
        $labelPosition    = $this->getLabelPosition();
        $globalLabelAttributes = $element->getLabelAttributes();
        $closingBracket   = $this->getInlineClosingBracket();
        //Added by g # allow specifying whether the some valueOption should be translated or not
        $elementOptions   = $element->getOptions();
        $translationFlag  = ((isset($elementOptions['translate']['label']))? $elementOptions['translate']['label'] : $this->translatableAttributes['label']);
        if (isset($elementOptions['value_option_label_attributes'])) {
            $globalLabelAttributes = $elementOptions['value_option_label_attributes'];
        }
        //ENDOF added by g

        if (empty($globalLabelAttributes)) {
            $globalLabelAttributes = $this->labelAttributes;
        }

        $combinedMarkup = array();
        $count          = 0;

        foreach ($options as $key => $optionSpec) {
            $count++;
            if ($count > 1 && array_key_exists('id', $attributes)) {
                unset($attributes['id']);
            }

            $value           = '';
            $label           = '';
            $inputAttributes = $attributes;
            $labelAttributes = $globalLabelAttributes;
            $selected        = isset($inputAttributes['selected']) && $inputAttributes['type'] != 'radio' && $inputAttributes['selected'] != false ? true : false;
            $disabled        = isset($inputAttributes['disabled']) && $inputAttributes['disabled'] != false ? true : false;

            if (is_scalar($optionSpec)) {
                $optionSpec = array(
                    'label' => $optionSpec,
                    'value' => $key
                );
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
            if (isset($optionSpec['label_attributes'])) {
                $labelAttributes = (isset($labelAttributes))
                    ? array_merge($labelAttributes, $optionSpec['label_attributes'])
                    : $optionSpec['label_attributes'];
            }
            if (isset($optionSpec['attributes'])) {
                $inputAttributes = array_merge($inputAttributes, $optionSpec['attributes']);
            }

            if (in_array($value, $selectedOptions)) {
                $selected = true;
            }

            $inputAttributes['value']    = $value;
            $inputAttributes['checked']  = $selected;
            $inputAttributes['disabled'] = $disabled;

            $input = sprintf(
                '<input %s%s',
                $this->createAttributesString($inputAttributes),
                $closingBracket
            );
            
            //Added by g
            if ($translationFlag && null !== ($translator = $this->getTranslator())) {
            //ENDOF Added by g
                $label = $translator->translate(
                    $label, $this->getTranslatorTextDomain()
                );
            }

            $label     = $escapeHtmlHelper($label);
            $labelOpen = $labelHelper->openTag($labelAttributes);
            $template  = $labelOpen . '%s%s' . $labelClose;
            switch ($labelPosition) {
                case self::LABEL_PREPEND:
                    $markup = sprintf($template, $label, $input);
                    break;
                case self::LABEL_APPEND:
                default:
                    $markup = sprintf($template, $input, $label);
                    break;
            }

            $combinedMarkup[] = $markup;
        }

        return implode($this->getSeparator(), $combinedMarkup);
    }
}
