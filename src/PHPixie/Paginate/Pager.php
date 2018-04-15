<?php

namespace PHPixie\Paginate;

class Pager
{
    protected $loader;
    protected $pageSize;

    protected $currentPage = 1;
    protected $itemCount;
    protected $pageCount;


    public function __construct($loader, $pageSize)
    {
        $this->loader = $loader;
        $this->pageSize   = $pageSize;
    }

    public function pageSize()
    {
        return $this->pageSize;
    }

    public function currentPage()
    {
        return $this->currentPage;
    }

    public function setCurrentPage($page)
    {
        if(!$this->pageExists($page)) {
            throw new \PHPixie\Paginate\Exception("Page $page does not exist");
        }

        $this->currentPage = $page;
    }

    public function pageExists($page)
    {
        $this->requireCount();

        return $page <= $this->pageCount && $page > 0;
    }

    public function pageOffsetExists($offset)
    {
        return $this->pageExists($this->currentPage + $offset);
    }

    public function getPageByOffset($offset)
    {
        $page = $this->currentPage + $offset;
        if($this->pageExists($page)) {
            return $page;
        }
        return null;
    }

    public function previousPage()
    {
        return $this->getPageByOffset(-1);
    }

    public function nextPage()
    {
        return $this->getPageByOffset(1);
    }

    public function getAdjacentPages($limit)
    {
        $this->requireCount();
        $pageCount = $this->pageCount;

        if($limit >= $pageCount) {
            return range(1 , $pageCount);
        }

        $start = $this->currentPage - (int) ($limit/2);

        if($start < 1) {
            return range(1, $limit);
        }

        if($start + $limit - 1 > $pageCount) {
            return range($pageCount - $limit + 1, $pageCount);
        }

        $end = $start + $limit - 1;

        if($start > $end) {
            return array();
        }

        return range($start, $end);
    }

    public function itemCount()
    {
        $this->requireCount();
        return $this->itemCount;
    }

    public function pageCount()
    {
        $this->requireCount();
        return $this->pageCount;
    }

    protected function requireCount()
    {
        if($this->itemCount === null) {
            $this->itemCount = $this->loader->getCount();
            $this->pageCount = (int) ceil($this->itemCount/$this->pageSize);
            if($this->pageCount === 0) {
                $this->pageCount = 1;
            }
        }
    }

    public function getCurrentItems()
    {
        $this->requireCount();

        $offset = $this->pageSize * ($this->currentPage - 1);

        if($this->currentPage === $this->pageCount) {
            $limit = $this->itemCount - $offset;
        }else{
            $limit = $this->pageSize;
        }

        return $this->loader->getItems(
            $offset,
            $limit
        );
    }

    /**
     * render paginator into HTML. $params may contain configuration information ('key'=>'default'):
     * 'url'=>'?page=%d' - url of specific page. Page parameter may be transferred as GET parameter, or route parameter,
     *      for example, '/news/%d'. All '%d' placeholders in this parameter will be replaced with actual page number.
     *      Remember, that text replacing is case sensitive.
     * 'urlFirst'=>'' - url of first page. By default, generated automatically from url by simply replacing '?page=%d'
     *      to '?' (page without GET parameters) and '/%d' to '' (for example, '/news/%d' will be changed to '/news').
     *      If this automatic conversion cannot be performed, for example, if your URL is '?page_id=%d' - you should set it
     *      manually. If this parameter has a placeholder '%d', it will be replaced with '1'.
     * 'around'=>10 - for very large data sets, paginator may be very long (100 pages for example). To shorten paginator,
     *      this parameter may be used: only 'around' pages (around currently selected page) will be displayed.
     *      "To the first page", "To the last page" links will be displayed in any case.
     * 'firstPage'=>'<a href="%url">&lt;&lt;&lt;</a>' - format of link to the first page. By default, this is a
     *      simple hyperlink with '<<<' caption (it's quite clear for humans independently from language he is speaking,
     *      so this option doesn't require i18n), but you may override this option to show this link, for example, as a button.
     *      '%url' placeholder is replaced with url ('url' or 'urlFirst' option, described above). Placeholder is case sensitive.
     *      This hints is related to most of options below, so I won't repeat them.
     * 'firstPageInactive'=>'' - format of link to the first page when first page is selected. By default, this link
     *      is not displayed, but you may override it to show, for example, "disabled" button.
     * 'prevPage'=>'<a href="%url">&lt;&lt;</a>' - format of link to previous page. By default, this is a simple hyperlink
     *      with '<<' caption.
     * 'prevPageInactive'=>'' - format of link to previous page, when no previous page available (first page selected).
     *      By default, this link isn't displayed.
     * 'nextPage'=>'<a href="%url">&gt;&gt;</a>' - format of link to next page. By default, simple hyperlink with '>>' caption.
     * 'nextPageInactive'=>'' - format of link to next page, when no next page available (last page selected). By default, hidden.
     * 'lastPage'=>'<a href="%url">&gt;&gt;&gt;</a>' - format of link to the last page. By default, simple hyperlink
     *      with '>>>' caption.
     * 'lastPageInactive'=>'' - format of the link to last page, when last page is selected. By default, hidden.
     * 'link'=>'<a href="%url">%d</a>' - format of regular link to a specified page. '%d' placeholder is replaced with
     *      page number, '%url' placeholder is replaced with 'url' or 'urlFirst' parameter.
     * 'linkInactive'=>'%d' - format of regular link, when specified page is selected
     * 'startUseHomeEnd'=>4 - minimum number of pages, when paginator will display "To the first page", "To the last page"
     *      links. By default, I suggest, that 3-page paginator should not contain such links and 4-pages should.
     * WARNING! I make HTML-specific options as parameters of render() method, because they are related
     * to V (view) letter, not to the C (controller) letter of MVC conception.
     * @return string
     */
    public function render($params=array())
    {
        if($this->pageCount===0)
            return '';
        $links=array();
        $params=array_merge(
            array(
                'firstPage'=>'<a href="%url">&lt;&lt;&lt;</a>',
                'firstPageInactive'=>'',
                'prevPage'=>'<a href="%url">&lt;&lt;</a>',
                'prevPageInactive'=>'',
                'nextPage'=>'<a href="%url">&gt;&gt;</a>',
                'nextPageInactive'=>'',
                'lastPage'=>'<a href="%url">&gt;&gt;&gt;</a>',
                'lastPageInactive'=>'',
                'link'=>'<a href="%url">%d</a>',
                'linkInactive'=>'%d',
                'around'=>10,
                'url'=>'?page=%d',
                'urlFirst'=>'',
                'startUseHomeEnd'=>4,
            ),$params
        );
        if(intval($params['around'])<=0)
            $params['around']=0;

        // automatic generation of URL to the first page
        if($params['urlFirst']==''){
            $params['urlFirst']=str_replace('?page=%d', '?', $params['url']);
            $params['urlFirst']=str_replace('/%d', '', $params['urlFirst']);
        }

        // shortcuts
        $first=1;
        $last=intval($this->pageCount);
        $current=intval($this->currentPage);

        // prepare array of pages, that should be visible
        if($params['around']!=0){
            //$stop=$params['around']+$first;
            //if($stop>$last)
            //    $stop=$last;
            //for($i=$first;$i<=$stop;$i++)$links[]=$i; - questionable
            $start=$current-$params['around'];
            if($start<$first)
                $start=$first;
            $stop=$current+$params['around'];
            if($stop>$last)
                $stop=$last;
            for($i=$start;$i<=$stop;$i++)$links[]=$i;
            //$start=$last-$params['around'];
            //if($start<$first)
            //    $start=$first;
            //for($i=$start;$i<=$last;$i++)$links[]=$i; - questionable
            //$links=array_unique($links);
        }else{
            $links=range($first, $last);
        }

        $result='';
        if($last>$params['startUseHomeEnd']-1){
            $item=$params[$first==$current?'firstPageInactive':'firstPage'];
            $item=str_replace('%url', $params['urlFirst'], $item);
            $item=str_replace('%d', $first, $item);
            $result.=$item;
            if($item!=''){
                $result.='&nbsp;';
            }
            // select appropriate format of link
            $item=$current-1==$first?$params['urlFirst']:$params['url'];
            $item=str_replace('%url', $item, $params[$first==$current?'prevPageInactive':'prevPage']);
            $item=str_replace('%d', $current-1, $item);
            $result.=$item;
            if($item!=''){
                $result.='&nbsp;';
            }
        }
        $pre=0;

        for($i=$first;$i<=$last;$i++){
            if(!in_array($i, $links))
                continue;
            // if some parameters was skipped, add three point at their space
            if($i!=$pre+1){
                $result.='&hellip;&nbsp;';
            }
            $pre=$i;
            $item=$params[$i==$first?'urlFirst':'url'];
            $item=str_replace('%url', $item, $params[$i==$current?'linkInactive':'link']);
            $item=str_replace('%d', $i, $item);
            $result.=$item;
            if($item!='')
                $result.='&nbsp;';
        }

        $result=substr($result,0,-6);
        if($pre!=$last)
            $result.='&hellip;';

        if($last>$params['startUseHomeEnd']-1){
            $item=$params['url'];
            $item=str_replace('%url', $item, $params[$last==$current?'nextPageInactive':'nextPage']);
            $item=str_replace('%d', $current+1, $item);
            if($item!='')
                $result.='&nbsp;'.$item;
            $item=$params['url'];
            $item=str_replace('%url', $item, $params[$last==$current?'lastPageInactive':'lastPage']);
            $item=str_replace('%d', $last, $item);
            if($item!='')
                $result.='&nbsp;'.$item;
        }

        return $result;
    }
}
