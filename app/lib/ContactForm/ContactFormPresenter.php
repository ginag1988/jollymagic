<?php

namespace Jollymagic\ContactForm;

use DOMElement;
use Jollymagic\Presenter;
use DOMDocument;
use DOMAttr;

class ContactFormPresenter implements Presenter
{
    public $form;
    public $formName = 'contactForm';
    private $config;
    private $opts;

    /***
     * @param $config
     * @param array|Object $opts
     */
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
        $domDocument = new DOMDocument();
        $formTag = $domDocument->createElement('form');

        $class = 'contact-form';
        $class .= isset($this->opts->success) && $this->opts->success ? ' success' : '';

        $this->addAttributes(
            $domDocument,
            $formTag,
            array(
                'class' => $class,
                'method' => $form->method
            )
        );

        if (isset($form->introParagraph) && !isset($this->opts->success)) {
            $formTag->appendChild($this->createIntroParagraph($domDocument, $form->introParagraph, 'intro'));
        } elseif (isset($form->errorParagraph) && !$this->opts->success) {
            $formTag->appendChild($this->createIntroParagraph($domDocument, $form->errorParagraph, 'error'));
        } elseif (isset($form->successParagraph) && $this->opts->success) {
            $formTag->appendChild($this->createIntroParagraph($domDocument, $form->successParagraph, 'success'));
        }

        foreach ($form->inputs as $input) {
            switch ($input->type) {
                case 'text':
                case 'tel':
                case 'email':
                    $formTag->appendChild($this->createLabel($domDocument, $input));
                    $formTag->appendChild($this->createTextInput($domDocument, $input));
                    break;
                case 'textarea':
                    $formTag->appendChild($this->createLabel($domDocument, $input));
                    $formTag->appendChild($this->createTextArea($domDocument, $input));
                    break;
                case 'submit':
                    $formTag->appendChild($this->createSubmitButton($domDocument, $input));
                    break;
                case 'default':
                    break;
            }
        }

        $domDocument->appendChild($formTag);

        return trim($domDocument->saveHTML());
    }

    /***
     * @param DOMDocument $domDocument
     * @param $string
     * @param $class
     * @return DOMElement
     */
    private function createIntroParagraph($domDocument, $string, $class)
    {
        $paragraph = $domDocument->createElement('p', $string);
        $paragraph->appendChild($this->createAttribute($domDocument, 'class', $class));
        return $paragraph;
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

        $class = isset($input->required) && $input->required ? 'required' : '';
        $class .= isset($this->opts->errors) && in_array($input->name, $this->opts->errors) ? ' failedValidation' : '';

        $label->appendChild($this->createAttribute($domDocument, 'class', $class));

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

        $class = isset($input->required) && $input->required ? 'required' : '';
        $class .= isset($this->opts->errors) && in_array($input->name, $this->opts->errors) ? ' failedValidation' : '';

        return $this->addAttributes(
            $domDocument,
            $textInput,
            array(
                'type' => $input->type,
                'name' => $input->name,
                'id' => $input->name,
                'class' => $class,
                'placeholder' => isset($input->placeholder) ? $input->placeholder : '',
                'value' => isset($this->opts->{$input->name}) ? $this->opts->{$input->name} : ''
            )
        );
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

        $class = isset($input->required) && $input->required ? 'required' : '';
        $class .= isset($this->opts->errors) && in_array($input->name, $this->opts->errors) ? ' failedValidation' : '';

        return $this->addAttributes(
            $domDocument,
            $textArea,
            array(
                'id' => $input->name,
                'name' => $input->name,
                'class' => $class,
                'placeholder' => isset($input->placeholder) ? $input->placeholder : ''
            )
        );
    }

    /***
     * @param DOMDocument $domDocument
     * @param $input
     * @return DOMDocument
     */
    private function createSubmitButton($domDocument, $input)
    {
        $submitButton = $domDocument->createElement('button', $input->title);

        return $this->addAttributes(
            $domDocument,
            $submitButton,
            array(
                'type' => $input->type,
                'name' => $this->formName,
                'id' => $this->formName
            )
        );
    }

    /***
     * @param DOMDocument $domDocument
     * @param DOMElement $input
     * @param array $kvs
     * @return DOMElement
     */
    private function addAttributes($domDocument, $input, $kvs)
    {
        foreach ($kvs as $key => $value) {
            $input->appendChild($this->createAttribute($domDocument, $key, $value));
        }
        return $input;
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
