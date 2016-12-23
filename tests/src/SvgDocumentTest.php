<?php

namespace Freesewing\Tests;

class SvgDocumentTest extends \PHPUnit\Framework\TestCase
{

    public $emptySvgStart = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>


<!--
  -->

<svg

>

<style type="text/css">
    <![CDATA[

    ]]>
</style>

<defs id="defs">

</defs>
';
    public $emptySvgEnd = '

</svg>


<!--
  -->
';

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
            ['defs'],
            ['footerComments'],
        ];
    }

    public function testToStringAndConstructor()
    {
        $headerComments = new \Freesewing\SvgComments();
        $svgAttributes = new \Freesewing\SvgAttributes();
        $css = new \Freesewing\SvgCss();
        $defs = new \Freesewing\SvgDefs();
        $footerComments = new \Freesewing\SvgComments();

        $svgDocument = new \Freesewing\SvgDocument(
            $headerComments,
            $svgAttributes,
            $css,
            $defs,
            $footerComments
        );

        $expect = $this->emptySvgStart.$this->emptySvgEnd;
        $this->assertEquals($expect, "$svgDocument");
    }

    public function testSetSvgBody()
    {
        $headerComments = new \Freesewing\SvgComments();
        $svgAttributes = new \Freesewing\SvgAttributes();
        $css = new \Freesewing\SvgCss();
        $defs = new \Freesewing\SvgDefs();
        $footerComments = new \Freesewing\SvgComments();

        $svgDocument = new \Freesewing\SvgDocument(
            $headerComments,
            $svgAttributes,
            $css,
            $defs,
            $footerComments
        );
        $svgDocument->setSvgBody('sorcha');
        $expect = $this->emptySvgStart.'sorcha'.$this->emptySvgEnd;
        $this->assertEquals($expect, "$svgDocument");
    }
}
