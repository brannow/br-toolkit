<?php

namespace BR\Toolkit\Tests\Misc\Service;

use BR\Toolkit\Exceptions\InvalidArgumentException;
use BR\Toolkit\Misc\DTO\TreeProcessor\TreeProcessorArrayData;
use BR\Toolkit\Misc\Service\TreeProcessorService;
use PHPUnit\Framework\TestCase;

class TreeProcessorServiceTest extends TestCase
{
    /**
     * @var TreeProcessorService
     */
    private $service;

    protected function setUp()
    {
        $this->service = new TreeProcessorService();
    }

    public function validTreeDataProvider(): array
    {
        return [
            [
                'id',
                'pid',
                [
                    ['id' => 1, 'pid' => 0],
                    ['id' => 2, 'pid' => 1],
                    ['id' => 3, 'pid' => 2],
                    ['id' => 4, 'pid' => 2],
                    ['id' => 5, 'pid' => 0],
                    ['id' => 6, 'pid' => 5],
                    ['id' => 7, 'pid' => 5],
                    ['id' => 8, 'pid' => 6],
                    ['id' => 9, 'pid' => 6],
                    ['id' => 10, 'pid' => 9],
                    ['id' => 11, 'pid' => 10],
                    ['id' => 12, 'pid' => 11]
                ]
            ],
            [
                0,
                1,
                [
                    [1, 0],
                    [2, 1],
                    [3, 2],
                    [4, 2],
                    [5, 0],
                    [6, 5],
                    [7, 5],
                    [8, 6],
                    [9, 6],
                    [10, 9],
                    [11, 10],
                    [12, 11]
                ]
            ]
        ];
    }

    /**
     * @dataProvider validTreeDataProvider

    /**
     * @param $id
     * @param $pid
     * @param $data
     * @throws InvalidArgumentException
     */
    public function testTreeSuccess($id, $pid, $data)
    {
        $treeData = new TreeProcessorArrayData($id, $pid, $data);
        $treeResult = $this->service->processTreeResult($treeData);

        $this->assertSame($data[0], $treeResult->getItem(1)->getData());
        $this->assertSame($data[11], $treeResult->getItem(12)->getData());
        $this->assertSame($treeResult->getItem(10)->getChildren()[0], $treeResult->getItem(11));
        $this->assertTrue(in_array($treeResult->getItem(1),$treeResult->getRootItems(), true));
        $this->assertTrue(in_array($treeResult->getItem(5),$treeResult->getRootItems(), true));
        $this->assertFalse(in_array($treeResult->getItem(12),$treeResult->getRootItems(), true));
        $this->assertNull($treeResult->getItem(9999));
        $this->assertNull($treeResult->getItem(-9999));
        $this->assertNull($treeResult->getItem(0));
    }

    public function validTreeDataWithDataProvider(): array
    {
        return [
            [
                'id',
                'pid',
                [
                    ['id' => 1, 'pid' => 0, 0 => 'valueA', 'b' => 'valueB'],
                    ['id' => 2, 'pid' => 1, 0 => 'valueA', 'b' => 'valueB'],
                    ['id' => 3, 'pid' => 2, 0 => 'valueA', 'b' => 'valueB'],
                    ['id' => 4, 'pid' => 2, 0 => 'valueA', 'b' => 'valueB'],
                    ['id' => 5, 'pid' => 0, 0 => 'valueA', 'b' => 'valueB']
                ]
            ]
        ];
    }

    /**
     * @dataProvider validTreeDataWithDataProvider
     * @param $id
     * @param $pid
     * @param $data
     * @throws \BR\Toolkit\Exceptions\InvalidArgumentException
     */
    public function testTreeWithDataSuccess($id, $pid, $data)
    {
        $treeData = new TreeProcessorArrayData($id, $pid, $data);
        $treeResult = $this->service->processTreeResult($treeData);

        $this->assertSame($data[0], $treeResult->getItem(1)->getData());
        $this->assertSame($data[2], $treeResult->getItem(3)->getData());

        $this->assertTrue(in_array($treeResult->getItem(3), $treeResult->getItem(2)->getChildren(), true));
        $this->assertTrue(in_array($treeResult->getItem(4), $treeResult->getItem(2)->getChildren(), true));

        $this->assertTrue(in_array($treeResult->getItem(1), $treeResult->getRootItems(), true));
        $this->assertTrue(in_array($treeResult->getItem(5), $treeResult->getRootItems(), true));
        $this->assertFalse(in_array($treeResult->getItem(2), $treeResult->getRootItems(), true));
        $this->assertNull($treeResult->getItem(9999));
        $this->assertNull($treeResult->getItem(-9999));
        $this->assertNull($treeResult->getItem(0));
    }

    public function invalidTreeDataWithDataProvider(): array
    {
        return [
            [
                new \DateTime(),
                'pid',
                [
                    ['id' => 0, 'pid' => 0, 0 => 'valueA', 'b' => 'valueB'],
                    ['id' => 0, 'pid' => 0, 0 => 'valueA', 'b' => 'valueB'],
                ]
            ],
            [
                '',
                'pid',
                [
                    ['id' => 0, 'pid' => 0, 0 => 'valueA', 'b' => 'valueB'],
                    ['id' => 0, 'pid' => 0, 0 => 'valueA', 'b' => 'valueB'],
                ]
            ],
            [
                'ASD',
                'pid',
                [
                    ['id' => 0, 'pid' => 0, 0 => 'valueA', 'b' => 'valueB'],
                    ['id' => 0, 'pid' => 0, 0 => 'valueA', 'b' => 'valueB'],
                ]
            ]
        ];
    }
    /**
     * [
    'id',
    'pid',
    [
    ['id' => 0, 'pid' => 0, 0 => 'valueA', 'b' => 'valueB'],
    ['id' => 0, 'pid' => 0, 0 => 'valueA', 'b' => 'valueB'],
    ]
    ],
     */

    /**
     * @dataProvider invalidTreeDataWithDataProvider
     * @param $id
     * @param $pid
     * @param $data
     * @throws \BR\Toolkit\Exceptions\InvalidArgumentException
     */
    public function testTreeWithDataInvalidKeys($id, $pid, $data)
    {
        $this->expectException(InvalidArgumentException::class);
        $treeData = new TreeProcessorArrayData($id, $pid, $data);
        $treeResult = $this->service->processTreeResult($treeData);
    }

    public function invalidIdTreeDataWithDataProvider(): array
    {
        return [
            [
                'id',
                'pid',
                [
                    ['id' => 0, 'pid' => 0, 0 => 'valueA', 'b' => 'valueB'],
                    ['id' => 0, 'pid' => 0, 0 => 'valueA', 'b' => 'valueB'],
                ]
            ]
        ];
    }

    /**
     * @dataProvider invalidIdTreeDataWithDataProvider
     * @param $id
     * @param $pid
     * @param $data
     * @throws \BR\Toolkit\Exceptions\InvalidArgumentException
     */
    public function testTreeWithDataInvalidId($id, $pid, $data)
    {
        $treeData = new TreeProcessorArrayData($id, $pid, $data);
        $treeResult = $this->service->processTreeResult($treeData);

        $this->assertNull($treeResult->getItem(0));
        $this->assertSame(0, $treeResult->count());
    }
}