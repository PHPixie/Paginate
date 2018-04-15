# Paginate
PHPixie Pagination library

[![Build Status](https://travis-ci.org/PHPixie/Paginate.svg?branch=master)](https://travis-ci.org/PHPixie/Paginate)
[![Test Coverage](https://codeclimate.com/github/PHPixie/Paginate/badges/coverage.svg)](https://codeclimate.com/github/PHPixie/Paginate)
[![Code Climate](https://codeclimate.com/github/PHPixie/Paginate/badges/gpa.svg)](https://codeclimate.com/github/PHPixie/Paginate)
[![HHVM Status](https://img.shields.io/hhvm/phpixie/paginate.svg?style=flat-square)](http://hhvm.h4cc.de/package/phpixie/paginate)

[![Author](http://img.shields.io/badge/author-@dracony-blue.svg?style=flat-square)](https://twitter.com/dracony)
[![Source Code](http://img.shields.io/badge/source-phpixie/paginate-blue.svg?style=flat-square)](https://github.com/phpixie/paginate)
[![Software License](https://img.shields.io/badge/license-BSD-brightgreen.svg?style=flat-square)](https://github.com/phpixie/paginate/blob/master/LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/phpixie/paginate.svg?style=flat-square)](https://packagist.org/packages/phpixie/paginate)


This is the base package for PHPixie Pagination that is split into several components.

```php
// Initializing
$paginate = new \PHPixie\Paginate();
```

While using from framework, you shouldn't create paginator manually, but request it from framework. For example, from default Controller (created by create-project command) method ('action') you may use:
```php
// Controller file: /bundles/app/src/HTTP/News.php
$paginate = $this->builder->components()->paginate();
```

The base library can only paginate arrays, there is an ORM extension available too, which
we will consider below. First let's see a usage example:

```php
// Array with a 100 items
$data = range(1, 100);

// Initialize the pager
// with 15 items per page
$pager = $paginate->arrayPager($data, 15);

// Set current page
$pager->setCurrentPage(3);

// A shorter way to do this:
$pager = $paginate->arrayPager($data, 15)
    ->setCurrentPage(3);

// Get some data
$pager->currentPage(); // 3
$pager->pageSize();    // 15
$pager->itemCount();   // 100
$pager->pageCount();   // 7

// Get current items
$pager->getCurrentItems();

// Check if page exists:
$pager->pageExists(1); // true

// Check if a page exists
// relative to the current one
$this->pageOffsetExists(-1); // true

// Get page number by relative offset
// Will return null if the page is missing
// In this case 4, since 3 is the current one
$this->getPageByOffset(1);

// Some shorthands,
// also return null if page missing
$this->nextPage(); // 4
$this->previousPage(); // 2
```

A more interesting feature is getting pages adjacent to the current one, which is useful
for custom rendering the pager.

```php
// 15 items, split into 6 pages
$data = range(1, 18);
$pager = $paginate->arrayPager($data, 3);

$pager->setCurrentPage(3);

$pager->getAdjacentPages(3); // array(2, 3, 4);

$pager->setCurrentPage(1);
$pager->getAdjacentPages(2); // array(1, 2);

$pager->setCurrentPage(5);
$pager->getAdjacentPages(4); // array(3, 4, 5, 6);
```

## ORM

First we need to build the ORM pagination library:

```php
$paginate = new \PHPixie\Paginate();
$paginateOrm = new \PHPixie\PaginateORM($paginate);
```

When using framework, this simplifies to request paginator instance from framework. For example, from default Controller (created by create-project command) method ('action') you may use:

```php
// Controller file: /bundles/app/src/HTTP/News.php
$paginator = $this->builder->components()->paginateOrm();
```

ORM pagination supports relationship preloading:

```php
$pager = $paginateOrm->queryPager($query, 15);

// Or with the relationships specified
$pager = $paginateOrm->queryPager($query, 15, array('items'));
```

It will also consider the limit and offset you specified for the query. This means if
you limit the items in the query before creating the pager, only those items will be paged:

```php
$query->limit(100)->offset(10);
$pager = $paginateOrm->queryPager($query, 15);

// Only those 100 items
// are in the pager
$pager->itemCount(); // 100
```
## Rendering
Simple paginator may be created automatically, by simply call render() method in template. For example:

```php
// somewhere in config file: /bundles/app/assets/config/routeResolver.php
// ...
'resolvers' => array(
    // ...
    'news'=>array(
        'path'=>'news(/<page>)',
        'defaults'=>array('processor'=>'news','action'=>'default','page'=>1),
        'attributePatterns'=>array(
            'id'=>'\d+',
        ),
    ),
),

// somewhere in controller file: /bundles/app/src/HTTP/News.php
public function defaultAction($request){
    // get page parameter from route
    $page = intval($request->attributes()->get('page',1));
    $components = $this->builder->components();

    $news = $components->orm()->query('new')
        ->orderDescendingBy('created');
    $pager = $components->paginateOrm()
        ->queryPager($news,2);

    // generate 'not Found' page... you should use slightly more complex code, returning real 404 error
    if(!$pager->pageExists($page))
        return 'Not Found!';
    $pager->setCurrentPage($page);

    // get template from repository
    $template = $components->template()
        ->get('app:news');
    $template->pager=$pager;
    return $template;
}

// somewhere in template file: /bundles/app/assets/templates/news.php
<center><?=$data->render(array('url'=>'$/news/%d')); ?></center>
```

Default paginator creates a simple sequence of numbers: 1 2 3 4 separated with non-breakable space (so, paginator cannot split between lines). By default, each number in sequence is a hyperlink, referring to the current page with parameter ?page=n, that may be referred from controller as $request->query()->get('page',1). But in given example, more user-friendly method (retrieving page from routing parameters) used. Rendering is controlled by array of parameters, which can contain options:

* 'url'=>'?page=%d' - url of specific page. Page parameter may be transferred as GET parameter, or route parameter, for example, '/news/%d'. All '%d' placeholders in this parameter will be replaced with actual page number. Remember, that text replacing is case sensitive.
* 'urlFirst'=>'' - url of first page. By default, generated automatically from url by simply replacing '?page=%d' to '?' (page without GET parameters) and '/%d' to '' (for example, '/news/%d' will be changed to '/news'). If this automatic conversion cannot be performed, for example, if your URL is '?page_id=%d' - you should set it manually. If this parameter has a placeholder '%d', it will be replaced with '1'.
* 'around'=>10 - for very large data sets, paginator may be very long (100 pages for example). To shorten paginator, this parameter may be used: only 'around' pages (around currently selected page) will be displayed. "To the first page", "To the last page" links will be displayed in any case.
* 'firstPage'=>'<a href="%url">&lt;&lt;&lt;</a>' - format of link to the first page. By default, this is a simple hyperlink with '<<<' caption (it's quite clear for humans independently from language he is speaking, so this option doesn't require i18n), but you may override this option to show this link, for example, as a button. '%url' placeholder is replaced with url ('url' or 'urlFirst' option, described above). Placeholder is case sensitive. This hints is related to most of options below, so I won't repeat them.
* 'firstPageInactive'=>'' - format of link to the first page when first page is selected. By default, this link is not displayed, but you may override it to show, for example, "disabled" button.
* 'prevPage'=>'<a href="%url">&lt;&lt;</a>' - format of link to previous page. By default, this is a simple hyperlink with '<<' caption.
* 'prevPageInactive'=>'' - format of link to previous page, when no previous page available (first page selected). By default, this link isn't displayed.
* 'nextPage'=>'<a href="%url">&gt;&gt;</a>' - format of link to next page. By default, simple hyperlink with '>>' caption.
* 'nextPageInactive'=>'' - format of link to next page, when no next page available (last page selected). By default, hidden.
* 'lastPage'=>'<a href="%url">&gt;&gt;&gt;</a>' - format of link to the last page. By default, simple hyperlink with '>>>' caption.
* 'lastPageInactive'=>'' - format of the link to last page, when last page is selected. By default, hidden.
* 'link'=>'<a href="%url">%d</a>' - format of regular link to a specified page. '%d' placeholder is replaced with page number, '%url' placeholder is replaced with 'url' or 'urlFirst' parameter.
* 'linkInactive'=>'%d' - format of regular link, when specified page is selected
* 'startUseHomeEnd'=>4 - minimum number of pages, when paginator will display "To the first page", "To the last page" links. By default, 3-page paginator won't contain such links and 4-pages would.
