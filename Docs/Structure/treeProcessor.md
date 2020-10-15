# Misc / TreeProcessor

TreeService Data Abstraction (used in: `BR\Toolkit\Misc\Service\TreeProcessorService`)

## Classes

* [`BR\Toolkit\Typo3\DTO\TreeProcessor\TreeProcessorArrayData`](#treeprocessorarraydata)
* [`BR\Toolkit\Typo3\DTO\TreeProcessor\TreeProcessorResult`](#treeprocessorresult)
* [`BR\Toolkit\Typo3\DTO\TreeProcessor\TreeProcessorResultItem`](#treeprocessorresultitem)

### TreeProcessorArrayData

TreeProcessorArrayData can handle Array structures.     
Other data structures are possible, like Objects or binary data.    
see: [TreeProcessorDataInterface](#treeprocessordatainterface)

### TreeProcessorResult

Representation of the final TreeStructure     
see: [TreeProcessorResultInterface](#treeprocessorresultinterface)

### TreeProcessorResultItem

Representation a tree item which contains *the data*, *the Parent* & *Children*      
see: [TreeProcessorResultItemInterface](#treeprocessorresultiteminterface)

## Interfaces

* [`BR\Toolkit\Typo3\DTO\TreeProcessor\TreeProcessorDataInterface`](#treeprocessordatainterface)
* [`BR\Toolkit\Typo3\DTO\TreeProcessor\TreeProcessorResultInterface`](#treeprocessorresultinterface)
* [`BR\Toolkit\Typo3\DTO\TreeProcessor\TreeProcessorResultItemInterface`](#treeprocessorresultiteminterface)   

special:
* [`BR\Toolkit\Typo3\DTO\TreeProcessor\TreeProcessorResultGenerateInterface`](#treeprocessorresultgenerateinterface)

### TreeProcessorDataInterface

described how the TreeProcessor can find the needed data.    
use this interface to write your own custom TreeProcessing Data Handler

* [getPrimaryIdFromData](#getprimaryidfromdata)
* [getRelationIdFromData](#getrelationidfromdata)
* [getData](#getdata)

#### getPrimaryIdFromData
get the UNIQUE Primary Identifier for the given TreeItemData
```php
public function getPrimaryIdFromData($data): int;
```

##### Arguments
* `mixed $data` the data from `getData()`

##### Return
* `int` if primary id is not found, `0` is returned.

##### Example
```php
$dataTreeObj = new \BR\Toolkit\Misc\DTO\TreeProcessor\TreeProcessorArrayData(
    'uid', 
    'pid', 
    [['uid' => 1, 'pid' => 0], ['uid' => 2, 'pid' => 1]]
);
$uid = $dataTreeObj->getPrimaryIdFromData($dataTreeObj->getData()[0]);
// $uid = 1
```

#### getRelationIdFromData
get the Relation Identifier for the given TreeItemData
```php
public function getRelationIdFromData($data): int;
```

##### Arguments
* `mixed $data` the data from `getData()`

##### Return
* `int` if relation id is not found, `0` is returned.

##### Example
```php
$dataTreeObj = new \BR\Toolkit\Misc\DTO\TreeProcessor\TreeProcessorArrayData(
    'uid', 
    'pid', 
    [['uid' => 1, 'pid' => 0], ['uid' => 2, 'pid' => 1]]
);
$pid = $dataTreeObj->getRelationIdFromData($dataTreeObj->getData()[0]);
// $pid = 0
$pid = $dataTreeObj->getRelationIdFromData($dataTreeObj->getData()[1]);
// $pid = 1
```

#### getData
the entire dataset
```php
public function getData(): mixed;
```

##### Return
* `mixed` any given data objects or array or anything that can processed...

##### Example
```php
$dataTreeObj = new \BR\Toolkit\Misc\DTO\TreeProcessor\TreeProcessorArrayData(
    'uid', 
    'pid', 
    [['uid' => 1, 'pid' => 0], ['uid' => 2, 'pid' => 1]]
);
$item = $dataTreeObj->getData()[0];
// $item = ['uid' => 1, 'pid' => 0]
```

### TreeProcessorResultInterface

TreeProcessor Result, which contains the entire tree

* [getRootItems](#getrootitems)
* [getItem](#getitem)
* [count](#count)

#### getRootItems
get All Root Tree Items (all who has `NULL` Parents)
```php
public function getRootItems(): TreeProcessorResultItemInterface[];
```

##### Return
* `TreeProcessorResultItemInterface[]` list of all TreeItem who has `NULL` Parents (Root items)

##### Example
```php
$treeData = new \BR\Toolkit\Misc\DTO\TreeProcessor\TreeProcessorArrayData(
    'uid', 
    'pid', 
    [['uid' => 1, 'pid' => 0], ['uid' => 2, 'pid' => 1]]
);
$treeProcessorService = new \BR\Toolkit\Misc\Service\TreeProcessorService();
$treeResult = $treeProcessorService->processTreeResult($treeData);
$rootItems = $treeResult->getRootItems();
// $rootItems = [TreeProcessorResultItem{data:['uid' => 1, 'pid' => 0]...}]
```

#### getItem
get All Root Tree Items (all who has `NULL` Parents)
```php
public function getItem(int $id): ?TreeProcessorResultItemInterface;
```

##### Arguments
* `int $id` lookup UNIQUE primary ID

##### Return
* `TreeProcessorResultItemInterface|null` TreeProcessorResultItem if ID was found, otherwise `NULL`

##### Example
```php
$treeData = new \BR\Toolkit\Misc\DTO\TreeProcessor\TreeProcessorArrayData(
    'uid', 
    'pid', 
    [['uid' => 1, 'pid' => 0], ['uid' => 2, 'pid' => 1]]
);
$treeProcessorService = new \BR\Toolkit\Misc\Service\TreeProcessorService();
$treeResult = $treeProcessorService->processTreeResult($treeData);
$item = $treeResult->getItem(1);
// $item = TreeProcessorResultItem{data:['uid' => 1, 'pid' => 0]...}
```

#### count
count of all successful processed items in result
```php
public function count(): int;
```

##### Return
* `int` count of all successful processed items in result

##### Example
```php
$treeData = new \BR\Toolkit\Misc\DTO\TreeProcessor\TreeProcessorArrayData(
    'uid', 
    'pid', 
    [['uid' => 1, 'pid' => 0], ['uid' => 2, 'pid' => 1]]
);
$treeProcessorService = new \BR\Toolkit\Misc\Service\TreeProcessorService();
$treeResult = $treeProcessorService->processTreeResult($treeData);
$count = $treeResult->count();
// $count = 2
```

### TreeProcessorResultItemInterface

TreeProcessor Result Item knows his childen / parent and data

* [setData](#setdata)
* [getData](#getdata)
* [addChild](#addchild)
* [getChildren](#getchildren)
* [getParent](#getparent)
* [setParent](#setparent)

#### setData
set the data of the Result Item
```php
public function setData($data): void;
```

##### Arguments
* `mixed $data` anything

##### Example
```php
// ... Tree Processing stuff
$item = $treeResult->getItem(1);
$item->setData($updatedData);
```

#### getData
get the current item data
```php
public function getData();
```

##### Return
* `mixed` anything


##### Example
```php
// ... Tree Processing stuff
$item = $treeResult->getItem(1);
$payloadData = $item->getData();
```

#### addChild
relate children to the current item, this will also trigger setParent on the children.     
the same `TreeProcessorResultItemInterface` object cannot added twice,     
(spl_object_id(), to prevent endless loops between addChild and setParent)
```php
public function addChild(TreeProcessorResultItemInterface ...$child): void;
```

##### Arguments
* `TreeProcessorResultItemInterface[] $child` (N) children that will be added

##### Example
```php
// ... Tree Processing stuff
$item = $treeResult->getItem(1);
$item->addChild($treeResult->getItem(2), $treeResult->getItem(3));
$r = ($item->getChildren()[0]->getParent() === $item);
// $r = true
```

#### getChildren
return all direct related children  
```php
public function getChildren(): TreeProcessorResultItemInterface[];
```

##### Return
* `TreeProcessorResultItemInterface[]` all direct children of the TreeProcessorResultItemInterface

##### Example
```php
// ... Tree Processing stuff
$item = $treeResult->getItem(1);
$item->addChild($treeResult->getItem(2), $treeResult->getItem(3));
$r = ($item->getChildren()[0]->getParent() === $item);
// $r = true
```

#### getParent
get the Parent of the current `TreeProcessorResultItemInterface`, if root element `NULL` returned
```php
public function getParent(): ?TreeProcessorResultItemInterface;
```

##### Return
* `TreeProcessorResultItemInterface|NULL` Parent of the current `TreeProcessorResultItemInterface`, if root element `NULL` returned

##### Example
```php
// ... Tree Processing stuff
$item = $treeResult->getItem(1);
$item->addChild($treeResult->getItem(2), $treeResult->getItem(3));
$r = ($item->getChildren()[0]->getParent() === $item);
// $r = true
$parent = $item->getParent();
// $parent = null
```

#### setParent
set the Parent of the current `TreeProcessorResultItemInterface`. this will also trigger addChild for the given parent Item.    
(spl_object_id(), to prevent endless loops between addChild and setParent)
```php
public function setParent(?TreeProcessorResultItemInterface $parent): void;
```

##### Arguments
* `TreeProcessorResultItemInterface $parent` parent item

##### Example
```php
// ... Tree Processing stuff
$item = $treeResult->getItem(1);
$child = $treeResult->getItem(2);
$child->setParent($item);

in_array($child, $item->getChildren(), true);
// return true
```

---

### TreeProcessorResultGenerateInterface

SPECIAL Interface, this is only needed for the direct processsing of the TreeResult

* [setItemData](#setitemdata)
* [getItem](#getitem)

#### setItemData
if no item for the given id exists, create a new item and set the data, if data exists overwrite data
```php
public function setItemData(int $id, $data): TreeProcessorResultItemInterface;
```

##### Arguments
* `int $id` UNIQUE data id
* `mixed $data` anything

##### Return
* `TreeProcessorResultItemInterface` created/modified item

##### Example
```php
$tree = new TreeProcessorResult();
// id and pid in data is at this point not really important, but for the semantic :)
$item = $tree->setItemData(1, ['id' => 1, 'pid' => 2, 'data' => 'value']);
```

#### getItem
overrides the existing `getItem(int $id)` from `TreeProcessorResultInterface`, add new `$createIfNotExists` functionality
```php
public function getItem(int $id, bool $createIfNotExists = false): ?TreeProcessorResultItemInterface;
```

##### Arguments
* `int $id` UNIQUE data id
* `bool $createIfNotExists` set true, non existing item for id will be created.

##### Return
* `TreeProcessorResultItemInterface` return requestet item for ID

##### Example
```php
$tree = new TreeProcessorResult();
// creates a new item or gets a existing item
$item = $tree->getItem(1, true);
```