# Misc / Service / TreeProcessorService

Process a set of data into a Tree Structure.   
Performance is [Linear BIG-O(n)](https://en.wikipedia.org/wiki/Big_O_notation) 

see: [\BR\Toolkit\Misc\DTO\TreeProcessor](/Docs/Structure/treeProcessor.md) for object handling and subclassing options

##### Methods

* [processTreeResult](#processtreeresult)

#### processTreeResult
create a basic preset [`CurlRequestInterface`](/Docs/Structure/treeProcessor.md) object

```php
public function processTreeResult(TreeProcessorDataInterface $data): TreeProcessorResultInterface
```

##### Arguments
 * [`TreeProcessorDataInterface $data` web url](/Docs/Structure/treeProcessor.md)

##### Return
 * [`\BR\Toolkit\Misc\DTO\TreeProcessor\TreeProcessorResultInterface`](/Docs/Structure/treeProcessor.md)

##### example
```php
$processor = new \BR\Toolkit\Misc\Service\TreeProcessorService();
$data = new \BR\Toolkit\Misc\DTO\TreeProcessor\TreeProcessorArrayData(
    'uid', 
    'pid', 
    [
        ['uid' => 1, 'pid' => 0],
        ['uid' => 2, 'pid' => 1],
        ['uid' => 3, 'pid' => 2, 'data' => 'value'],
        ['uid' => 4, 'pid' => 1]
    ]
);

$result = $processor->processTreeResult($data);
$result->count();                   // 4
$result->getItem(4)->getParent();   // TreeProcessorResultItem{uid:1}
$result->getItem(2)->getChildren(); // [TreeProcessorResultItem{uid:3}]
$result->getItem(3)->getData();     // ['uid' => 3, 'pid' => 2, 'data' => 'value']
```