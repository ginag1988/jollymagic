<?php

namespace Jollymagic\Content;

class ContentPresenterTest extends \PHPUnit_Framework_TestCase
{

    private $mockContentApi;

    public function setUp()
    {
        parent::setUp();

        $this->mockContentApi = new MockContentApi();
        $this->mockContentApi->content = (object) array(
            "home" => (object) array(
                "url" => "/",
                "navTitle" => "Mr Jolly",
                "backgroundImage" => "alan.jpeg",
                "title" => "Hi, I'm Al Jolly",
                "bodyText" => array(
                    "Paragraph __one__ £&£",
                    "Paragraph two"
                )
            ),
            "second" => (object) array(
                "url" => "/second",
                "navTitle" => "Title",
                "backgroundImage" => "image.jpeg",
                "title" => "Hi, I'm Al Jolly",
                "bodyText" => array(
                    "Paragraph one £&£",
                    "Paragraph two"
                )
            ),
        );
    }

    public function testThatAPagesDataIsReturnedWhenRequested()
    {
        $expectedBodyText = "<p>Paragraph <strong>one</strong> £&amp;£</p><p>Paragraph two</p>";

        $contentPresenter = new ContentPresenter('home');
        $contentPresenter->api = $this->mockContentApi;
        $page = $contentPresenter->present();

        $this->assertEquals($this->mockContentApi->content->home->title, $page->content->title);
        $this->assertEquals($expectedBodyText, $page->content->body);
        $this->assertEquals($this->mockContentApi->content->home->backgroundImage, $page->content->backgroundImage);
    }

    public function testThatTheNavIsReturnedAsPartOfAPage()
    {
        $contentPresenter = new ContentPresenter('home');
        $contentPresenter->api = $this->mockContentApi;
        $page = $contentPresenter->present();

        foreach ($page->nav as $key => $navItem) {
            $this->assertEquals($this->mockContentApi->content->{$key}->navTitle, $navItem->title);
            $this->assertEquals($this->mockContentApi->content->{$key}->url, $navItem->url);
        }
    }

    public function testThat404IsThrownIfPageNotFound()
    {
        $page = "doesNotExist";
        $this->setExpectedException(get_class(new NoContentException($page)), "Page not found: $page", 404);

        $contentPresenter = new ContentPresenter($page);
        $contentPresenter->api = $this->mockContentApi;
        $contentPresenter->present();
    }
}
