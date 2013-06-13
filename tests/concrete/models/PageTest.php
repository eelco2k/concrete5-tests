<?php

class PageTest extends PHPUnit_Framework_TestCase {
	protected $ct;
	protected $home;

	function pageData(){
		Loader::model('page');
		Loader::model('collection_types');
		$data['ct'] = CollectionType::getByHandle('left_sidebar'); //everything's got a default..
		$data['home'] = Page::getByID(HOME_CID);
		return $data;
	}

	//this one actually has two tests in it:
	// - does the page fail for bad adds
	// - does it pass for good ones.
	// it should be its own test when the suite gets built, maybe.
	public function testAddPage() {
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

		$page = $home->add($ct,array(
			'uID'=>1,
			'cName'=>$pageName,
			'cHandle'=>$pageHandle
		));

		$parentID = $page->getCollectionParentID();

		$this->assertInstanceOf('Page',$page);
		$this->assertEquals($parentID, HOME_CID);

		$this->assertSame($pageName,$page->getCollectionName());
		$this->assertSame($pageHandle, $page->getCollectionHandle());
		$this->assertSame('/'.$pageHandle, $page->getCollectionPath());

		return $page;
	}

	/**
		@depends testAddPage
	 */
	public function testDeletePage($page) {
		$cID = $page->getCollectionID();

		$page->delete();

		$noPage = Page::getByID($cID);

		$this->assertEquals(COLLECTION_NOT_FOUND,$noPage->error); //maybe there is a more certain way to determine this.
	}

	/**
		@depends testAddPage
	 */
	public function testMovePage($page) {
		Loader::model('page');
		Loader::model('collection_types');
		$ct = CollectionType::getByHandle('left_sidebar'); //everything's got a default..
		$this->assertInstanceOf('CollectionType', $ct); //kind of weird to check this but hey

		$home = Page::getByID(HOME_CID);
		$pageMoveStop = $home->add($ct,array(
			'uID'=>1,
			'cName'=>"Destination",
			'cHandle'=>'destination'
		));

		$parentCID = $pageMoveStop->getCollectionID();

		$page->move($pageMoveStop);

		$parentPath = $pageMoveStop->getCollectionPath();
		$handle = $page->getCollectionHandle();
		$path = $page->getCollectionPath();

		$this->assertSame($parentPath.'/'.$handle, $path);
		$this->assertSame($parentCID, $page->getCollectionParentID());
		$page->delete();
		$pageMoveStop->delete();
	}

	/**
		@depends testAddPage
	 */
	public function testTrashPage($page) {
		$page->moveToTrash();

		$this->assertTrue($page->isInTrash());
		$page->delete();
	}

	
}
