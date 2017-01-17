<?php

namespace Freesewing\Tests;

class SvgDocumentTest extends \PHPUnit\Framework\TestCase
{

    protected function setUp()
    {
        $this->svgBasic = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n\n\n<svg\n    \n>\n\n\n</svg>\n\n";
        $this->svgWithBody = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n\n\n<svg\n    \n>\nsorcha\n\n</svg>\n\n";
        $headerComments = new \Freesewing\SvgComments();
        $svgAttributes = new \Freesewing\SvgAttributes();
        $css = new \Freesewing\SvgCss();
        $script = new \Freesewing\SvgScript();
        $defs = new \Freesewing\SvgDefs();
        $footerComments = new \Freesewing\SvgComments();

        $this->object = new \Freesewing\SvgDocument(
            $headerComments,
            $svgAttributes,
            $css,
            $script,
            $defs,
            $footerComments
        );
    }


    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\SvgDocument');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['svgBody'],
            ['headerComments'],
            ['svgAttributes'],
            ['css'],
            ['script'],
            ['defs'],
            ['footerComments'],
        ];
    }

    public function testToStringAndConstructor()
    {
        $this->assertEquals($this->svgBasic, ''.$this->object); // Using to-string conversion
    }

    public function testSetSvgBody()
    {
        $this->object->setSvgBody('sorcha');
        $this->assertEquals($this->svgWithBody, ''.$this->object); // Using to-string conversion
    }
}
