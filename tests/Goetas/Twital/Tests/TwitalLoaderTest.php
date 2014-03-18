<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\TwitalLoader;
use Goetas\Twital\Twital;
use Goetas\Twital\SourceAdapter\XMLAdapter;

class TwitalLoaderTest extends \PHPUnit_Framework_TestCase
{

    protected $twital;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->twital = new Twital();
    }

    protected function getRequiredAdapters()
    {
        return array(
            'Goetas\Twital\SourceAdapter\HTML5Adapter',
            'Goetas\Twital\SourceAdapter\XMLAdapter',
            'Goetas\Twital\SourceAdapter\XHTMLAdapter'
        );
    }

    public function testInternalLoader()
    {
        $loader = new \Twig_Loader_String();

        $twitalLoader = new TwitalLoader($this->twital, true, $loader);

        $this->assertSame($loader, $twitalLoader->getLoader());

        $newLoader = new \Twig_Loader_String();
        $twitalLoader->setLoader($newLoader);
        $this->assertSame($newLoader, $twitalLoader->getLoader());
        $this->assertNotSame($loader, $twitalLoader->getLoader());
    }

    public function testDefaultAdapters()
    {
        $twitalLoader = new TwitalLoader($this->twital, true, new \Twig_Loader_String());

        $adapters = $twitalLoader->getSourceAdapters();

        $this->assertContainsOnlyInstancesOf('Goetas\Twital\SourceAdapter', $adapters);

        foreach ($this->getRequiredAdapters() as $class) {
            $filteredAdapters = array_filter($adapters, function ($adapter) use($class)
            {
                return is_a($adapter, $class);
            });
            $this->assertGreaterThanOrEqual(1, count($filteredAdapters), "Cant find any $class adapter");
        }
    }

    public function testTwitalFile()
    {
        $twital = $this->getMock('Goetas\Twital\Twital');
        $twitalLoader = new TwitalLoader($twital, false, new \Twig_Loader_String());
        $twitalLoader->addSourceAdapter('/.*\.xml$/', new XMLAdapter());

        $twital->expects($this->once())->method('compile');
        $twitalLoader->getSource('aaa.xml');
    }

    public function testNonTwitalFile()
    {
        $twital = $this->getMock('Goetas\Twital\Twital');
        $twitalLoader = new TwitalLoader($twital, false, new \Twig_Loader_String());
        $twitalLoader->addSourceAdapter('/.*\.xml$/', new XMLAdapter());

        $twital->expects($this->never())->method('compile');
        $twitalLoader->getSource('aaa.txt');
    }

    public function __test__VisitNode($source, $expected)
    {
        $twitalLoader = new TwitalLoader($this->twital, false, new \Twig_Loader_String());
        $twitalLoader->addSourceAdapter("/.*/", new XMLAdapter());

        $this->twig = new \Twig_Environment($twitalLoader);
    }
}


