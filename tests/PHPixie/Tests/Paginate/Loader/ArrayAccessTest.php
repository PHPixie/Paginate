<?php

namespace PHPixie\Tests\Paginate\Loader;

/**
 * @coversDefaultClass PHPixie\Paginate\Loader\ArrayAccess
 */
class ArrayAccessTest extends \PHPixie\Test\Testcase
{
    protected $items = array();
    
    protected $Loader;
    
    public function setUp()
    {
        for($i=0; $i<20; $i++) {
            $this->items[]= 'item'.$i;
        }
        
        $this->Loader = new \PHPixie\Paginate\Loader\ArrayAccess(
            $this->items
        );
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
    
    }
    
    /**
     * @covers ::getCount
     * @covers ::<protected>
     */
    public function testGetCount()
    {
        $count = count($this->items);
        $this->assertSame($count, $this->Loader->getCount());
    }
    
    /**
     * @covers ::getItems
     * @covers ::<protected>
     */
    public function testGetItems()
    {
        $expect = array_slice($this->items, 5, 10);
        
        $items = $this->Loader->getItems(5, 10);
        $this->assertSame($expect, iterator_to_array($items));
    }
}