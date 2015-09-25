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

The base library cn only paginate arrays, there is an ORM extension available too, which
we will look at further on. First let's see a usage example:

```php
// Array with a 100 items
$data = range(1, 100);

// Initialize the pager
// with 15 items per page
$pager = $paginate->arrayPager($data, 15);

// Set current page
$pager->setCurrentPager(3);

// A shorter way to do this:
$pager = $paginate->arrayPager($data, 15)
    ->setCurrentPage(3);

// Get some data
$pager->currentPage(); // 3
$pager->pageSize();    // 15
$pager->itemCount();   // 100
$pager->pageCount();   // 7 

// Get current items
$pager->currentItems();

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
$this->next(); // 4
$this->previous(); // 2
```

A more interesting feature is getting pages adjacent to the current one, which is useful
for rendering the pager.

```php
// 15 items, split into 6 pages
$data = range(1, 18);
$pager = $paginate->arrayPager($data, 3);

$pager->setCurrentPage(3);

$pager->getAdjacent(3); // array(2, 3, 4);

$pager->setCurrentPage(1);
$pager->getAdjacent(2); // array(1, 2);

$pager->setCurrentPage(5);
$pager->getAdjacent(4); // array(3, 4, 5, 6);
```

## ORM

First we need to build the ORM pagination library:

```php
$paginate = new \PHPixie\Paginate();
$paginateOrm = new \PHPixie\PaginateORM($paginate);
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
