<?php

namespace Jollymagic\Components;

class BookingFormPresenterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider inputProvider
     */
    public function testThatItGeneratesAllTheRequiredFormInputsFromConfig($input, $expected)
    {
        $bookingForm = new BookingFormPresenter(null);
        $bookingForm->form = $input;
        $renderedHtml = $bookingForm->present();
        $this->assertEquals($expected, $renderedHtml);
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
                            "defaultValue" => "default"
                        )
                    )
                ),
                '<form method="post">' .
                '<label for="test">Test Item</label>' .
                '<input type="text" name="test" id="test" value="default">' .
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
                            'defaultValue' => 'default text'
                        )
                    )
                ),
                '<form method="get">' .
                '<label for="test">test text area</label>' .
                '<textarea id="test" name="test">default text</textarea>' .
                '</form>'
            ),
            array(
                (object) array(
                    'method' => 'post',
                    'inputs' => array(
                        (object) array(
                            'type' => 'submit',
                            'name' => 'submit',
                            'title' => 'title'
                        )
                    )
                ),
                '<form method="post">' .
                '<input type="submit" value="title" name="submit" id="submit">' .
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
                            "defaultValue" => "default1"
                        ),
                        (object) array(
                            "type" => "text",
                            "name" => "test2",
                            "title" => "Test Item2",
                            "defaultValue" => "default2"
                        ),
                        (object) array(
                            'type' => 'textarea',
                            'name' => 'test',
                            'title' => 'test text area',
                            'defaultValue' => 'default text'
                        ),
                        (object) array(
                            'type' => 'submit',
                            'name' => 'submit',
                            'title' => 'title'
                        )
                    )
                ),
                '<form method="post">' .
                '<label for="test1">Test Item1</label>' .
                '<input type="text" name="test1" id="test1" value="default1">' .
                '<label for="test2">Test Item2</label>' .
                '<input type="text" name="test2" id="test2" value="default2">' .
                '<label for="test">test text area</label>' .
                '<textarea id="test" name="test">default text</textarea>' .
                '<input type="submit" value="title" name="submit" id="submit">' .
                '</form>'
            )
        );
    }
}
