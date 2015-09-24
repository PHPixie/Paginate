<?php

namespace PHPixie\Tests;

/**
 * @coversDefaultClass PHPixie\Paginate
 */
class PaginateTest extends \PHPixie\Test\Testcase
{
    protected $paginate;
    protected $builder;
    
   public function setUp()
    {
        $this->paginate = $this->getMockBuilder('\PHPixie\Paginate')
            ->setMethods(array('buildBuilder'))
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->builder = $this->quickMock('\PHPixie\Paginate\Builder');
        $this->method($this->paginate, 'buildBuilder', $this->builder, array(), 0);
        
        $this->paginate->__construct();
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        
    }
    
    /**
     * @covers ::pager
     * @covers ::<protected>
     */
    public function testPager()
    {
        $loader = $this->quickMock('\PHPixie\Paginate\Loader');
        $pager = $this->quickMock('\PHPixie\Paginate\Pager');
        $this->method($this->builder, 'pager', $pager, array($loader, 15), 0);
        
        $this->assertSame($pager, $this->paginate->pager($loader, 15));
    }
    
    /**
     * @covers ::arrayLoader
     * @covers ::<protected>
     */
    public function testArrayLoader()
    {
        $items = array('test');
        $loader = $this->quickMock('\PHPixie\Paginate\Loader\ArrayAccess');
        $this->method($this->builder, 'arrayLoader', $loader, array($items), 0);
        
        $this->assertSame($loader, $this->paginate->arrayLoader($items));
    }
    
    /**
     * @covers ::arrayPager
     * @covers ::<protected>
     */
    public function testArrayPager()
    {
        $items = array('test');
        $loader = $this->quickMock('\PHPixie\Paginate\Loader\ArrayAccess');
        $this->method($this->builder, 'arrayLoader', $loader, array($items), 0);
        
        $pager = $this->quickMock('\PHPixie\Paginate\Pager');
        $this->method($this->builder, 'pager', $pager, array($loader, 15), 1);
        
        $this->assertSame($pager, $this->paginate->arrayPager($items, 15));
    }
    
    /**
     * @covers ::builder
     * @covers ::<protected>
     */
    public function testBuilder()
    {
        $this->assertSame($this->builder, $this->paginate->builder());
    }
    
    /**
     * @covers ::buildBuilder
     * @covers ::<protected>
     */
    public function testBuildBuilder()
    {
        $this->paginate = new \PHPixie\Paginate();
        $builder = $this->paginate->builder();
        
        $this->assertInstance($builder, '\PHPixie\Paginate\Builder');
    }
}