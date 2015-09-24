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
        $repository = $this->quickMock('\PHPixie\Paginate\Repository');
        $pager = $this->quickMock('\PHPixie\Paginate\Pager');
        $this->method($this->builder, 'pager', $pager, array($repository, 15), 0);
        
        $this->assertSame($pager, $this->paginate->pager($repository, 15));
    }
    
    /**
     * @covers ::arrayRepository
     * @covers ::<protected>
     */
    public function testArrayRepository()
    {
        $items = array('test');
        $repository = $this->quickMock('\PHPixie\Paginate\Repository\ArrayAccess');
        $this->method($this->builder, 'arrayRepository', $repository, array($items), 0);
        
        $this->assertSame($repository, $this->paginate->arrayRepository($items));
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
     * @covers ::arrayPager
     * @covers ::<protected>
     */
    public function testArrayPager()
    {
        $items = array('test');
        $repository = $this->quickMock('\PHPixie\Paginate\Repository\ArrayAccess');
        $this->method($this->builder, 'arrayRepository', $repository, array($items), 0);
        
        $pager = $this->quickMock('\PHPixie\Paginate\Pager');
        $this->method($this->builder, 'pager', $pager, array($repository, 15), 1);
        
        $this->assertSame($pager, $this->paginate->arrayPager($items, 15));
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