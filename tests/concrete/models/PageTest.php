<?php

class PageTest extends PHPUnit_Framework_TestCase {

	private static function createPage($handle, $name) {
		Loader::model('page');
		Loader::model('collection_types');
		$ct = CollectionType::getByHandle('left_sidebar'); //everything's got a default..
		//$this->assertInstanceOf('CollectionType', $ct); //kind of weird to check this but hey

		$home = Page::getByID(HOME_CID);
		$page = $home->add($ct,array(
			'uID'=>1,
			'cName'=>$name,
			'cHandle'=>$handle
		));

		return $page;
	}

	public function testPageOperations() {
		Loader::model('page');
		Loader::model('collection_types');
		$ct = CollectionType::getByHandle('left_sidebar'); //everything's got a default..
		$this->assertInstanceOf('CollectionType', $ct); //kind of weird to check this but hey

		$home = Page::getByID(HOME_CID);
		$pageName = "My Cool Page";
		$pageHandle = 'page'; //this tests that page handles will be set as the page handle.
			//The actual add function does some transforms on the handles if they are not
			//set.
		
		$badPage = Page::getByID(42069);
		try {
			$page = $badPage->add($ct,array(
				'uID'=>1,
				'cName'=>$pageName,
				'cHandle'=>$pageHandle
			));
		} catch(Exception $e) {
			$caught = true;
		}

		if(!$caught) {
			$this->fail('Added a page to a non-page');
		}

		$page = self::createPage($pageHandle,$pageName);

		$parentID = $page->getCollectionParentID();

		$this->assertInstanceOf('Page',$page);
		$this->assertEquals($parentID, HOME_CID);

		$this->assertSame($pageName,$page->getCollectionName());
		$this->assertSame($pageHandle, $page->getCollectionHandle());
		$this->assertSame('/'.$pageHandle, $page->getCollectionPath());
		//now we know adding pages works.

		$destination = self::createPage('destination',"Destination");

		$parentCID = $destination->getCollectionID();

		$page->move($destination);
		$parentPath = $destination->getCollectionPath();
		$handle = $page->getCollectionHandle();
		$path = $page->getCollectionPath();

		$this->assertSame($parentPath.'/'.$handle, $path);
		$this->assertSame($parentCID, $page->getCollectionParentID());
		//now we know that moving pages works

		$page->moveToTrash();
		$this->assertTrue($page->isInTrash());
		//stuff is going to the trash

		$cID = $page->getCollectionID();
		$page->delete();
		$noPage = Page::getByID($cID);
		$this->assertEquals(COLLECTION_NOT_FOUND,$noPage->error); //maybe there is a more certain way to determine this.
		//now we know deleting pages works

		$destination->delete();
		//clean up the destination page
	}
}
