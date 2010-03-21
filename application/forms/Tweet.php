<?php

class Application_Form_Tweet extends Zend_Form
{

    public function init()
    {
        $this->setName('send-tweet');
        
        $e = new Zend_Form_Element_Text('latitude');
        $e->setLabel('Latitude');
        $e->addFilter('StringTrim');
        $e->setAttrib('onblur', 'setMarkerFromForm()');
        $e->addValidator(new Zend_Validate_Regex('/[-+0-9.]/'));
        $e->setTranslator(new Zend_Translate_Adapter_Array(array(
            'regexNotMatch'=>'Latitude is not valid',
        ), 'en'));        
        $this->addElement($e);

        $e = new Zend_Form_Element_Text('longitude');
        $e->setLabel('Longitude');
        $e->addFilter('StringTrim');
        $e->setAttrib('onblur', 'setMarkerFromForm()');
        $e->addValidator(new Zend_Validate_Regex('/[-+0-9.]/'));
        $e->setTranslator(new Zend_Translate_Adapter_Array(array(
            'regexNotMatch'=>'Longitude is not valid',
        ), 'en'));        
        $this->addElement($e);

        $e = new Zend_Form_Element_Textarea('tweet');
        $e->setLabel('Tweet');
        $e->setRequired(true);
        $e->addFilter('StripTags');
        $e->addFilter('StringTrim');
        $e->addValidator(new Zend_Validate_StringLength(array('max'=>140)));
        $e->setTranslator(new Zend_Translate_Adapter_Array(array(
            'isEmpty'=>'Please supply the message to be tweeted',
            'stringLengthTooLong'=>'No more than 140 characters please!',
        ), 'en'));
        $this->addElement($e);

        $e = new Zend_Form_Element_Submit('send');
        $e->setIgnore(true);
        $e->setLabel('Tweet!');
        $this->addElement($e);
    }

}

