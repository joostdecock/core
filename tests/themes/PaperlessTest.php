<?php

namespace Freesewing\Tests;

use \Freesewing\Output;
require_once __DIR__.'/../src/assets/testFunctions.php';

class PaperlessTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
       Output::reset();
    }

    public function tearDown()
    {
       Output::reset();
    }

    /**
     * Tests the isPaperless method
     *
     */
    public function testIsPaperless()
    {
        $theme = new \Freesewing\Themes\Core\Paperless();
        $this->assertTrue($theme->isPaperless());
    }

    private function loadFixture($fixture)
    {
        $dir = \Freesewing\Utils::getApiDir().'/tests/themes/fixtures';
        $file = "$dir/Paperless.$fixture.data";
        return file_get_contents($file);
    }

    private function saveFixture($fixture, $data)
    {
        return true;
        $dir = \Freesewing\Utils::getApiDir().'/tests/themes/fixtures';
        $file = "$dir/Paperless.$fixture.data";
        $f = fopen($file,'w');
        fwrite($f,$data);
        fclose($f);
    }
}
