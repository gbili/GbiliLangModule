<?php
namespace GbiliLangModule\Form;

class Merge extends \Zend\Form\Form 
{
    public function __construct($name=null, array $options=array())
    {
        parent::__construct('merge', $options);

        $this->add(array(
            'name' => 'confirm',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Confirmation:'
            ),
            'attributes' => array(
                'placeholder' => "Type the magic word: 'merge'",
                'class' => 'form-control'
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Merge',
                'id' => 'submitbutton',
                'class' => 'btn btn-default', 
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'merge' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name'    => 'Regex',
                        'options' => array(
                            'pattern'      => '/\\Amerge\\z/',
                        ),
                    ),
                ),
            ),
        );
    }
}
