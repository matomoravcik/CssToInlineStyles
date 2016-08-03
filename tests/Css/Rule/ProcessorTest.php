<?php

namespace MatoMoravcik\CssToInlineStyles\Tests\Css\Rule;

use Symfony\Component\CssSelector\Node\Specificity;
use MatoMoravcik\CssToInlineStyles\Css\Rule\Processor;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Processor
     */
    protected $processor;

    public function setUp()
    {
        $this->processor = new Processor();
    }

    public function tearDown()
    {
        $this->processor = null;
    }

    public function testMostBasicRule()
    {
        $css = <<<EOF
            a {
                padding: 5px;
                display: block;
            }
EOF;

        $rules = $this->processor->convertToObjects($css, 1);

        $this->assertCount(1, $rules);
        $this->assertInstanceOf('MatoMoravcik\CssToInlineStyles\Css\Rule\Rule', $rules[0]);
        $this->assertEquals('a', $rules[0]->getSelector());
        $this->assertCount(2, $rules[0]->getProperties());
        $this->assertEquals('padding', $rules[0]->getProperties()[0]->getName());
        $this->assertEquals('5px', $rules[0]->getProperties()[0]->getValue());
        $this->assertEquals('display', $rules[0]->getProperties()[1]->getName());
        $this->assertEquals('block', $rules[0]->getProperties()[1]->getValue());
        $this->assertEquals(1, $rules[0]->getOrder());
    }

    public function testMaintainOrderOfProperties()
    {
        $css = <<<EOF
            div {
                width: 200px;
                _width: 211px;
            }
EOF;
        $rules = $this->processor->convertToObjects($css, 1);

        $this->assertCount(1, $rules);
        $this->assertInstanceOf('MatoMoravcik\CssToInlineStyles\Css\Rule\Rule', $rules[0]);
        $this->assertEquals('div', $rules[0]->getSelector());
        $this->assertCount(2, $rules[0]->getProperties());
        $this->assertEquals('width', $rules[0]->getProperties()[0]->getName());
        $this->assertEquals('200px', $rules[0]->getProperties()[0]->getValue());
        $this->assertEquals('_width', $rules[0]->getProperties()[1]->getName());
        $this->assertEquals('211px', $rules[0]->getProperties()[1]->getValue());
        $this->assertEquals(1, $rules[0]->getOrder());
    }

    public function testSingleIdSelector()
    {
        $this->assertEquals(
            new Specificity(1, 0, 0),
            $this->processor->calculateSpecificityBasedOnASelector('#foo')
        );
    }

    public function testSingleClassSelector()
    {
        $this->assertEquals(
            new Specificity(0, 1, 0),
            $this->processor->calculateSpecificityBasedOnASelector('.foo')
        );
    }

    public function testSingleElementSelector()
    {
        $this->assertEquals(
            new Specificity(0, 0, 1),
            $this->processor->calculateSpecificityBasedOnASelector('a')
        );
    }
}
