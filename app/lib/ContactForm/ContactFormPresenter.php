<?php

namespace Jollymagic\ContactForm;

use Jollymagic\Presenter;
use DOMDocument;
use DOMAttr;

class ContactFormPresenter implements Presenter
{
    public $form;
    public $formName = 'contactForm';
    private $config;
    private $opts;

    public function __construct($config, $opts = array())
    {
        $this->config = $config;
        $this->opts = $opts;
    }

    /***
     * @returns String
     */
    public function present()
    {
        $form = $this->getForm();
        return $this->createForm($form);
    }

    private function createForm($form)
    {
        $dd = new DOMDocument();
        $formTag = $dd->createElement('form');
        $formTag->appendChild($this->createAttribute($dd, 'method', $form->method));

        foreach ($form->inputs as $input) {
            switch ($input->type) {
                case 'text':
                case 'tel':
                case 'email':
                    $formTag->appendChild($this->createLabel($dd, $input));
                    $formTag->appendChild($this->createTextInput($dd, $input));
                    break;
                case 'textarea':
                    $formTag->appendChild($this->createLabel($dd, $input));
                    $formTag->appendChild($this->createTextArea($dd, $input));
                    break;
                case 'submit':
                    $formTag->appendChild($this->createSubmitButton($dd, $input));
                    break;
                case 'default':
                    break;
            }
        }

        $dd->appendChild($formTag);

        return trim($dd->saveHTML());
    }

    /***
     * @param DOMDocument $domDocument
     * @param $input
     * @return DOMDocument
     */
    private function createLabel($domDocument, $input)
    {
        $label = $domDocument->createElement('label', $input->title);
        $labelFor = $domDocument->createAttribute('for');
        $labelFor->value = $input->name;
        $label->appendChild($labelFor);

        if (isset($input->required) && $input->required) {
            $class = isset($this->opts->errors) && in_array($input->name, $this->opts->errors) ?
                'required failedValidation' : 'required';
            $label->appendChild($this->createAttribute($domDocument, 'class', $class));
        }

        return $label;
    }

    /***
     * @param DOMDocument $domDocument
     * @param $input
     * @return DOMDocument
     */
    private function createTextInput($domDocument, $input)
    {
        $textInput = $domDocument->createElement('input');

        $textInput->appendChild($this->createAttribute($domDocument, 'type', $input->type));
        $textInput->appendChild($this->createAttribute($domDocument, 'name', $input->name));
        $textInput->appendChild($this->createAttribute($domDocument, 'id', $input->name));

        if (isset($input->defaultValue)) {
            $textInput->appendChild($this->createAttribute($domDocument, 'placeholder', $input->defaultValue));
        }

        if (isset($this->opts->{$input->name})) {
            $textInput->appendChild($this->createAttribute($domDocument, 'value', $this->opts->{$input->name}));
        }

        if (isset($input->required) && $input->required) {
            $class = isset($this->opts->errors) && in_array($input->name, $this->opts->errors) ?
                'required failedValidation' : 'required';
            $textInput->appendChild($this->createAttribute($domDocument, 'class', $class));
        }

        return $textInput;
    }

    /***
     * @param DOMDocument $domDocument
     * @param $input
     * @return DOMDocument
     */
    private function createTextArea($domDocument, $input)
    {
        $value = isset($this->opts->{$input->name}) ? $this->opts->{$input->name} : '';
        $textArea = $domDocument->createElement('textarea', $value);

        $textArea->appendChild($this->createAttribute($domDocument, 'id', $input->name));
        $textArea->appendChild($this->createAttribute($domDocument, 'name', $input->name));

        if (isset($input->defaultValue)) {
            $textArea->appendChild($this->createAttribute($domDocument, 'placeholder', $input->defaultValue));
        }

        if (isset($input->required) && $input->required) {
            $class = isset($this->opts->errors) && in_array($input->name, $this->opts->errors) ?
                'required failedValidation' : 'required';
            $textArea->appendChild($this->createAttribute($domDocument, 'class', $class));
        }


        return $textArea;
    }

    /***
     * @param DOMDocument $domDocument
     * @param $input
     * @return DOMDocument
     */
    private function createSubmitButton($domDocument, $input)
    {
        $submitButton = $domDocument->createElement('input');

        $submitButton->appendChild($this->createAttribute($domDocument, 'type', $input->type));
        $submitButton->appendChild($this->createAttribute($domDocument, 'value', $input->title));
        $submitButton->appendChild($this->createAttribute($domDocument, 'name', $this->formName));
        $submitButton->appendChild($this->createAttribute($domDocument, 'id', $this->formName));

        return $submitButton;
    }

    /***
     * @param DOMDocument $domDocument
     * @param $name
     * @param $value
     * @return DOMAttr
     */
    private function createAttribute($domDocument, $name, $value)
    {
        $attr = $domDocument->createAttribute($name);
        $attr->value = $value;
        return $attr;
    }

    private function getForm()
    {
        return $this->form ?: json_decode(
            file_get_contents($this->config['routeDir'].$this->config['contentDir'].'contactForm.json')
        );
    }
}
