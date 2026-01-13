<?php

declare(strict_types=1);

namespace oat\taoItems\test\unit\models\classes\search;

use core_kernel_classes_Class;
use oat\oatbox\user\User;
use oat\taoItems\model\search\ItemClassListService;
use oat\generis\model\data\Ontology;
use oat\generis\model\data\permission\PermissionInterface;
use oat\oatbox\session\SessionService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ItemClassListServiceTest extends TestCase
{
    private ItemClassListService $sut;
    private PermissionInterface|MockObject $permissionManagerMock;
    private SessionService|MockObject $sessionServiceMock;
    private MockObject|core_kernel_classes_Class $rootClassMock;

    protected function setUp(): void
    {
        $ontologyMock = $this->createMock(Ontology::class);
        $this->permissionManagerMock = $this->createMock(PermissionInterface::class);
        $this->sessionServiceMock = $this->createMock(SessionService::class);
        $this->rootClassMock = $this->createMock(core_kernel_classes_Class::class);
        $ontologyMock->method('getClass')->willReturn($this->rootClassMock);

        $this->sut = new ItemClassListService(
            $ontologyMock,
            $this->permissionManagerMock,
            $this->sessionServiceMock
        );
    }

    public function testGetListHappyPath(): void
    {
        $query = 'test';
        $page = '1';

        $this->permissionManagerMock->method('getSupportedRights')->willReturn([]);

        $mockFoundClass = $this->createMock(core_kernel_classes_Class::class);
        $mockFoundClass->method('getUri')->willReturn('test-uri');
        $mockFoundClass->method('getLabel')->willReturn('Test Label');
        $mockFoundClass->method('getParentClassesIds')->willReturn([]);
        $this->rootClassMock->method('searchInstances')->willReturn([$mockFoundClass]);
        $this->rootClassMock->method('countInstances')->willReturn(1);

        $result = $this->sut->getList($query, $page);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('items', $result);
        $this->assertEquals(1, $result['total']);
        $this->assertCount(1, $result['items']);
        $this->assertEquals('test-uri', $result['items'][0]['id']);
        $this->assertEquals('Test Label', $result['items'][0]['text']);
    }

    public function testGetListFiltersOutInaccessibleItems()
    {
        $query = 'test';
        $page = '1';

        $this->sessionServiceMock->method('getCurrentUser')->willReturn(
            $this->createMock(User::class)
        );
        $this->permissionManagerMock->method('getSupportedRights')->willReturn([
            PermissionInterface::RIGHT_WRITE,
            PermissionInterface::RIGHT_READ,
        ]);
        $this->permissionManagerMock->method('getPermissions')->willReturn([
            'accessible-uri' => [PermissionInterface::RIGHT_WRITE],
            'inaccessible-uri' => [PermissionInterface::RIGHT_READ]
        ]);

        $accessibleClass = $this->createMock(core_kernel_classes_Class::class);
        $accessibleClass->method('getUri')->willReturn('accessible-uri');
        $accessibleClass->method('getLabel')->willReturn('Accessible Label');

        $inaccessibleClass = $this->createMock(core_kernel_classes_Class::class);
        $inaccessibleClass->method('getUri')->willReturn('inaccessible-uri');
        $inaccessibleClass->method('getLabel')->willReturn('Inaccessible Label');

        $this->rootClassMock->method('searchInstances')->willReturn([$accessibleClass, $inaccessibleClass]);
        $this->rootClassMock->method('countInstances')->willReturn(2);

        $result = $this->sut->getList($query, $page);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('items', $result);
        $this->assertEquals(2, $result['total']);
        $this->assertCount(1, $result['items']);
        $this->assertEquals('accessible-uri', $result['items'][0]['id']);
    }
}
