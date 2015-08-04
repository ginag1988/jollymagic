<?php

namespace Jollymagic\ContactForm;

class ContactFormPresenterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider inputProvider
     */
    public function testThatItGeneratesAllTheRequiredFormInputsFromConfig($input, $expected)
    {
        $contactForm = new ContactFormPresenter(null);
        $contactForm->form = $input;
        $contactForm->formName = 'formyForm';
        $renderedHtml = $contactForm->present();
        $this->assertEquals($expected, $renderedHtml);
    }

    /**
     * @dataProvider responseInputProvider
     */
    public function testThatInputsShowCompletedValuesFromResponse($input, $response, $expected)
    {
        $contactForm = new ContactFormPresenter(null, $response);
        $contactForm->form = $input;
        $contactForm->formName = 'formyForm';
        $renderedHtml = $contactForm->present();
        $this->assertEquals($expected, $renderedHtml);
    }

    /**
     * @dataProvider failureInputProvider
     */
    public function testThatInputsThatFailRequirementAddClassToInput($input, $response, $expected)
    {
        $contactForm = new ContactFormPresenter(null, $response);
        $contactForm->form = $input;
        $contactForm->formName = 'formyForm';
        $renderedHtml = $contactForm->present();
        $this->assertEquals($expected, $renderedHtml);
    }

    public function testThatIfFormIsSuccessFormHasSuccessClass()
    {
        $contactForm = new ContactFormPresenter(
            null,
            (object) array(
                'success' => true,
                'errors' => []
            )
        );
        $contactForm->form = (object) array(
            'method' => 'post',
            'inputs' => array()
        );
        $contactForm->formName = 'formyForm';
        $this->assertEquals('<form class="contact-form success" method="post"></form>', $contactForm->present());
    }

    public function testThatIfTheFormHasNotBeenFilledInIntroParagraphIsShown()
    {
        $contactForm = new ContactFormPresenter(null, array());
        $contactForm->form = (object) array(
            'method' => 'post',
            'introParagraph' => 'intro',
            'successParagraph' => 'success',
            'errorParagraph' => 'error',
            'inputs' => array()
        );
        $contactForm->formName = 'formyForm';
        $this->assertEquals(
            '<form class="contact-form" method="post"><p class="intro">intro</p></form>',
            $contactForm->present()
        );
    }

    public function testThatIfTheFormHasBeenFilledInIncorrectlyErrorParagraphIsShown()
    {
        $contactForm = new ContactFormPresenter(
            null,
            (object) array(
                'success' => false,
                'errors' => []
            )
        );
        $contactForm->form = (object) array(
            'method' => 'post',
            'introParagraph' => 'intro',
            'successParagraph' => 'success',
            'errorParagraph' => 'error',
            'inputs' => array()
        );
        $contactForm->formName = 'formyForm';
        $this->assertEquals(
            '<form class="contact-form" method="post"><p class="error">error</p></form>',
            $contactForm->present()
        );
    }

    public function testThatIfTheFormHasBeenFilledInCorrectlySuccessParagraphIsShown()
    {
        $contactForm = new ContactFormPresenter(
            null,
            (object) array(
                'success' => true,
                'errors' => []
            )
        );
        $contactForm->form = (object) array(
            'method' => 'post',
            'introParagraph' => 'intro',
            'successParagraph' => 'success',
            'errorParagraph' => 'error',
            'inputs' => array()
        );
        $contactForm->formName = 'formyForm';
        $this->assertEquals(
            '<form class="contact-form success" method="post"><p class="success">success</p></form>',
            $contactForm->present()
        );
    }

    public function inputProvider()
    {
        return array(
            array(
                (object) array(
                    'method' => 'post',
                    'inputs' => array(
                        (object) array(
                            "type" => "text",
                            "name" => "test",
                            "title" => "Test Item",
                            "placeholder" => "default"
                        )
                    )
                ),
                '<form class="contact-form" method="post">' .
                '<div class="field">' .
                '<label for="test" class="">Test Item</label>' .
                '<input type="text" name="test" id="test" class="" placeholder="default" value="">' .
                '</div>' .
                '</form>'
            ),
            array(
                (object) array(
                    'method' => 'post',
                    'inputs' => array(
                        (object) array(
                            "type" => "tel",
                            "name" => "test",
                            "title" => "Test Item",
                            "placeholder" => "default"
                        )
                    )
                ),
                '<form class="contact-form" method="post">' .
                '<div class="field">' .
                '<label for="test" class="">Test Item</label>' .
                '<input type="tel" name="test" id="test" class="" placeholder="default" value="">' .
                '</div>' .
                '</form>'
            ),
            array(
                (object) array(
                    'method' => 'post',
                    'inputs' => array(
                        (object) array(
                            "type" => "email",
                            "name" => "test",
                            "title" => "Test Item",
                            "placeholder" => "default"
                        )
                    )
                ),
                '<form class="contact-form" method="post">' .
                '<div class="field">' .
                '<label for="test" class="">Test Item</label>' .
                '<input type="email" name="test" id="test" class="" placeholder="default" value="">' .
                '</div>' .
                '</form>'
            ),
            array(
                (object) array(
                    'method' => 'post',
                    'inputs' => array(
                        (object) array(
                            "type" => "text",
                            "name" => "test",
                            "title" => "Test Item",
                        )
                    )
                ),
                '<form class="contact-form" method="post">' .
                '<div class="field">' .
                '<label for="test" class="">Test Item</label>' .
                '<input type="text" name="test" id="test" class="" placeholder="" value="">' .
                '</div>' .
                '</form>'
            ),
            array(
                (object) array(
                    'method' => 'post',
                    'inputs' => array(
                        (object) array(
                            "type" => "text",
                            "name" => "test",
                            "title" => "Test Item",
                            "required" => true
                        )
                    )
                ),
                '<form class="contact-form" method="post">' .
                '<div class="field">' .
                '<label for="test" class="required">Test Item</label>' .
                '<input type="text" name="test" id="test" class="required" placeholder="" value="">' .
                '</div>' .
                '</form>'
            ),
            array(
                (object) array(
                    'method' => 'get',
                    'inputs' => array(
                        (object) array(
                            'type' => 'textarea',
                            'name' => 'test',
                            'title' => 'test text area',
                            'placeholder' => 'default text'
                        )
                    )
                ),
                '<form class="contact-form" method="get">' .
                '<div class="field">' .
                '<label for="test" class="">test text area</label>' .
                '<textarea id="test" name="test" class="" placeholder="default text"></textarea>' .
                '</div>' .
                '</form>'
            ),
            array(
                (object) array(
                    'method' => 'get',
                    'inputs' => array(
                        (object) array(
                            'type' => 'textarea',
                            'name' => 'test',
                            'title' => 'test text area',
                            'placeholder' => 'default text',
                            'required' => true
                        )
                    )
                ),
                '<form class="contact-form" method="get">' .
                '<div class="field">' .
                '<label for="test" class="required">test text area</label>' .
                '<textarea id="test" name="test" class="required" placeholder="default text"></textarea>' .
                '</div>' .
                '</form>'
            ),
            array(
                (object) array(
                    'method' => 'get',
                    'inputs' => array(
                        (object) array(
                            'type' => 'textarea',
                            'name' => 'test',
                            'title' => 'test text area',
                        )
                    )
                ),
                '<form class="contact-form" method="get">' .
                '<div class="field">' .
                '<label for="test" class="">test text area</label>' .
                '<textarea id="test" name="test" class="" placeholder=""></textarea>' .
                '</div>' .
                '</form>'
            ),
            array(
                (object) array(
                    'method' => 'post',
                    'inputs' => array(
                        (object) array(
                            'type' => 'submit',
                            'title' => 'title'
                        )
                    )
                ),
                '<form class="contact-form" method="post">' .
                '<div class="field">' .
                '<button type="submit" name="formyForm" id="formyForm">title</button>' .
                '</div>' .
                '</form>'
            ),
            array(
                (object) array(
                    'method' => 'post',
                    'inputs' => array(
                        (object) array(
                            "type" => "text",
                            "name" => "test1",
                            "title" => "Test Item1",
                            "placeholder" => "default1",
                            "required" => true
                        ),
                        (object) array(
                            "type" => "text",
                            "name" => "test2",
                            "title" => "Test Item2",
                            "placeholder" => "default2"
                        ),
                        (object) array(
                            'type' => 'textarea',
                            'name' => 'test',
                            'title' => 'test text area',
                            'placeholder' => 'default text'
                        ),
                        (object) array(
                            'type' => 'submit',
                            'title' => 'title'
                        )
                    )
                ),
                '<form class="contact-form" method="post">' .
                '<div class="field">' .
                '<label for="test1" class="required">Test Item1</label>' .
                '<input type="text" name="test1" id="test1" class="required" placeholder="default1" value="">' .
                '</div>' .
                '<div class="field">' .
                '<label for="test2" class="">Test Item2</label>' .
                '<input type="text" name="test2" id="test2" class="" placeholder="default2" value="">' .
                '</div>' .
                '<div class="field">' .
                '<label for="test" class="">test text area</label>' .
                '<textarea id="test" name="test" class="" placeholder="default text"></textarea>' .
                '</div>' .
                '<div class="field">' .
                '<button type="submit" name="formyForm" id="formyForm">title</button>' .
                '</div>' .
                '</form>'
            )
        );
    }

    public function responseInputProvider()
    {
        return array(
            array(
                // input
                (object) array(
                    'method' => 'post',
                    'inputs' => array(
                        (object) array(
                            "type" => "text",
                            "name" => "test",
                            "title" => "Test Item",
                        )
                    )
                ),
                // response
                (object) array(
                    'success' => false,
                    'errors' => [],
                    'test' => 'result'
                ),

                // expected
                '<form class="contact-form" method="post">' .
                '<div class="field">' .
                '<label for="test" class="">Test Item</label>' .
                '<input type="text" name="test" id="test" class="" placeholder="" value="result">' .
                '</div>' .
                '</form>'
            ),
            array(
                // input
                (object) array(
                    'method' => 'get',
                    'inputs' => array(
                        (object) array(
                            'type' => 'textarea',
                            'name' => 'test',
                            'title' => 'test text area',
                            'placeholder' => 'default text'
                        )
                    )
                ),
                // response
                (object) array(
                    'success' => false,
                    'errors' => [],
                    'test' => 'result'
                ),

                // expected
                '<form class="contact-form" method="get">' .
                '<div class="field">' .
                '<label for="test" class="">test text area</label>' .
                '<textarea id="test" name="test" class="" placeholder="default text">result</textarea>' .
                '</div>' .
                '</form>'
            )
        );
    }

    public function failureInputProvider()
    {
        return array(
            array(
                // input
                (object) array(
                    'method' => 'post',
                    'inputs' => array(
                        (object) array(
                            "type" => "text",
                            "name" => "test",
                            "title" => "Test Item",
                            "required" => true
                        )
                    )
                ),
                // response
                (object) array(
                    'success' => false,
                    'errors' => [
                        'test'
                    ],
                    'test' => ''
                ),

                // expected
                '<form class="contact-form" method="post">' .
                '<div class="field">' .
                '<label for="test" class="required failedValidation">Test Item</label>' .
                '<input type="text" name="test" id="test" class="required failedValidation" placeholder="" value="">' .
                '</div>' .
                '</form>'
            ),
            array(
                // input
                (object) array(
                    'method' => 'get',
                    'inputs' => array(
                        (object) array(
                            'type' => 'textarea',
                            'name' => 'test',
                            'title' => 'test text area',
                            'placeholder' => 'default text',
                            'required' => true
                        )
                    )
                ),
                // response
                (object) array(
                    'success' => false,
                    'errors' => [
                        'test'
                    ],
                    'test' => ''
                ),

                // expected
                '<form class="contact-form" method="get">' .
                '<div class="field">' .
                '<label for="test" class="required failedValidation">test text area</label>' .
                '<textarea id="test" name="test" class="required failedValidation" placeholder="default text">' .
                '</textarea>' .
                '</div>' .
                '</form>'
            )
        );
    }
}
