<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 * @subpackage test
 */
class importExportTestCase extends UnitTestCase {
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoTestRunner::initTest();
	}
	
	public function testImportOwi() {
		$importService = taoItems_models_classes_XHTML_ImportService::singleton();
		$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		
		$owiFolder = dirname(__FILE__).DIRECTORY_SEPARATOR.'samples'.DIRECTORY_SEPARATOR.'owi'.DIRECTORY_SEPARATOR;
		
		try {
			//validate malformed html
			$owiItem = $importService->importXhtmlFile($owiFolder.'badItem.zip', $itemClass, true);
			$this->fail('No exception on malformed html');
		} catch (taoItems_models_classes_Import_ImportException $e) {
			$this->assertIsA($e, 'taoItems_models_classes_Import_ParsingException');
		}
		
		$owiItem = $importService->importXhtmlFile($owiFolder.'badItem.zip', $itemClass, false);
		$this->assertIsA($owiItem, 'core_kernel_classes_Resource');
		
		$itemService = taoItems_models_classes_ItemsService::singleton();
		$content = $itemService->getItemContent($owiItem);
		$this->assertFalse(empty($content));
		
		$folder = $itemService->getItemFolder($owiItem);
		$this->assertTrue(file_exists($folder.'index.html'));
		$this->assertTrue(file_exists($folder.'media'.DIRECTORY_SEPARATOR.'simple.png'));
		
		$this->assertTrue($itemService->deleteItem($owiItem));
	}
	
}
?>