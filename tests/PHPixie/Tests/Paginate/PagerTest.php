<?php

namespace PHPixie\Tests\Paginate;

/**
 * @coversDefaultClass PHPixie\Paginate\Pager
 */
class PagerTest extends \PHPixie\Test\Testcase
{
    protected $repository;
    protected $pageSize = 5;
    
    protected $pager;
    
    public function setUp()
    {
        $this->repository = $this->quickMock('\PHPixie\Paginate\Repository');
        
        $this->pager = $this->pager();
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
    
    }
    
    /**
     * @covers ::pageSize
     * @covers ::<protected>
     */
    public function testPageSize()
    {
        $this->assertSame($this->pageSize, $this->pager->pageSize());
    }
    
    /**
     * @covers ::itemCount
     * @covers ::pageCount
     * @covers ::<protected>
     */
    public function testCount()
    {
        $this->prepareRequireCount(17);
        for($i=0; $i<2; $i++) {
            $this->assertSame(17, $this->pager->itemCount());
            $this->assertSame(4, $this->pager->pageCount());
        }
        
        $this->pager = $this->pager();
        $this->prepareRequireCount(0);
        
        for($i=0; $i<2; $i++) {
            $this->assertSame(0, $this->pager->itemCount());
            $this->assertSame(1, $this->pager->pageCount());
        }
    }
    
    /**
     * @covers ::pageExists
     * @covers ::<protected>
     */
    public function testPageExists()
    {
        $this->prepareRequireCount(17);
        
        for($i=1; $i<=4; $i++) {
            $this->assertTrue($this->pager->pageExists($i));
        }
        
        foreach(array(-1, 0, 5) as $page) {
            $this->assertFalse($this->pager->pageExists($page));
        }
    }
    
    /**
     * @covers ::currentPage
     * @covers ::setCurrentPage
     * @covers ::<protected>
     */
    public function testCurrentPage()
    {
        $this->prepareRequireCount(17);
        
        $this->assertSame(1, $this->pager->currentPage());
        
        $this->pager->setCurrentPage(2);
        $this->assertSame(2, $this->pager->currentPage());
        
        $pager = $this->pager;
        $this->assertException(function() use($pager) {
            $pager->setCurrentPage(5);
        }, '\PHPixie\Paginate\Exception');
    }
    
    /**
     * @covers ::pageOffsetExists
     * @covers ::getPageByOffset
     * @covers ::<protected>
     */
    public function testPageOffsetExists()
    {
        $this->prepareRequireCount(17);
        $this->pager->setcurrentPage(3);
        
        foreach(array(-1, 1) as $offset) {
            $this->assertTrue($this->pager->pageOffsetExists($offset));
            $this->assertSame(3+$offset, $this->pager->getPageByOffset($offset));
        }
        
        foreach(array(-5, 5) as $offset) {
            $this->assertFalse($this->pager->pageOffsetExists($offset));
            $this->assertSame(null, $this->pager->getPageByOffset($offset));
        }
    }
    
    /**
     * @covers ::next
     * @covers ::previous
     * @covers ::<protected>
     */
    public function testPreviousNext()
    {
        $this->prepareRequireCount(3*$this->pageSize);
        
        $map = array(
            1 => array(null, 2),
            2 => array(1, 3),
            3 => array(2, null),
        );
        
        foreach($map as $page => $expect) {
            $this->pager->setCurrentPage($page);
            $this->assertSame($expect[0], $this->pager->previous());
            $this->assertSame($expect[1], $this->pager->next());
        }
    }
    
    /**
     * @covers ::getAdjacentPages
     * @covers ::<protected>
     */
    public function testGetAdjacentPages()
    {
        $this->prepareRequireCount(8*$this->pageSize);
        $this->assertAdjacentPages(3, array(
            3 => range(2, 4),
            8 => range(1, 8),
            9 => range(1, 8),
            0 => array()
        ));
        
        $this->assertAdjacentPages(2, array(
            6 => range(1, 6)
        ));
        
        $this->assertAdjacentPages(7, array(
            6 => range(3, 8)
        ));
        
        $this->pager = $this->pager();
        
        $this->prepareRequireCount(0);
        $this->assertAdjacentPages(1, array(
            6 => range(1, 1)
        ));
    }
    
    protected function assertAdjacentPages($currentPage, $map)
    {
        $this->pager->setCurrentPage($currentPage);
        foreach($map as $limit => $expect) {
            $this->assertSame($expect, $this->pager->getAdjacentPages($limit));
        }
    }
    
    /**
     * @covers ::getCurrentItems
     * @covers ::<protected>
     */
    public function testCurrentItems()
    {
        $this->prepareRequireCount(6*$this->pageSize);
        $this->pager->setCurrentPage(3);
        
        $iterator = $this->quickMock('\Iterator');
        $offset = 2*$this->pageSize;
        $this->method($this->repository, 'getItems', $iterator, array($offset), 0);
        
        $this->assertSame($iterator, $this->pager->getCurrentItems());
    }
    
    protected function prepareRequireCount($itemCount, $repositoryAt = 0)
    {
        $this->method($this->repository, 'getCount', $itemCount, array(), $repositoryAt);
    }
    
    protected function pager()
    {
        return new \PHPixie\Paginate\Pager(
            $this->repository,
            $this->pageSize
        );
    }
}