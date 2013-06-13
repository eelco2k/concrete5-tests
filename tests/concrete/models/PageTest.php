<?php

class PageTest extends PHPUnit_Framework_TestCase {
	protected $pageAdd;
	protected $pageMove;
	//protected $pageMoveStart;
	protected $pageMoveStop;
	protected $pageTrash;
	protected $pageDelete;
	//protected $pageData;

	function makePages(){
		Loader::model('page');
		Loader::model('collection_types');
		$ct = CollectionType::getByHandle('left_sidebar'); //everything's got a default..
		$home = Page::getByID(HOME_CID);
	
		//keeping track of this stuff should be unnecessary.
		// but leaving it here in case
		//self::pageData['pageMove'] = array(
			//'uID'=>1,
			//'cName'=>"This is moving",
			//'cHandle'=>'page_move'
		//);
		//$home->add($ct, self::pageData['pageMove']);

		$this->pageMove = $home->add($ct,array(
			'uID'=>1,
			'cName'=>"This is moving",
			'cHandle'=>'page_move'
		));

		//self::pageMoveStart = $home->add($ct,array(
			//'uID'=>1,
			//'cName'=>"Origin",
			//'cHandle'=>'origin'
		//));
		$this->pageMoveStop = $home->add($ct,array(
			'uID'=>1,
			'cName'=>"Destination",
			'cHandle'=>'destination'
		));

		$this->pageTrash = $home->add($ct,array(
			'uID'=>1,
			'cName'=>"Going to trash",
			'cHandle'=>'page_trash'
		));

		$this->pageDelete = $home->add($ct,array(
			'uID'=>1,
			'cName'=>"Deleting this",
			'cHandle'=>'page_delete'
		));
	}

	function __destruct() {
		$this->pageAdd->delete();
		$this->pageMove->delete();
		$this->pageMoveStop->delete();
      $this->pageTrash->delete();
		parent::__destruct();
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
			$this->pageAdd = $badPage->add($ct,array(
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

		$this->pageAdd = $home->add($ct,array(
			'uID'=>1,
			'cName'=>$pageName,
			'cHandle'=>$pageHandle
		));

		$parentID = $this->pageAdd->getCollectionParentID();

		$this->assertInstanceOf('Page',$this->pageAdd);
		$this->assertEquals($parentID, HOME_CID);

		$this->assertSame($pageName,$this->pageAdd->getCollectionName());
		$this->assertSame($pageHandle, $this->pageAdd->getCollectionHandle());
		$this->assertSame('/'.$pageHandle, $this->pageAdd->getCollectionPath());
	}

	/**
		@depends makePages
	 */
	public function testMovePage() {
		$parentCID = $this->pageMoveStop->getCollectionID();

		$this->pageMove->move($this->pageMoveStop);

		$parentPath = $this->pageMoveStop->getCollectionPath();
		$handle = $this->pageMove->getCollectionHandle();
		$path = $this->pageMove->getCollectionPath();

		$this->assertSame($parentPath.'/'.$handle, $path);
		$this->assertSame($parentCID, $this->pageMove->getCollectionParentID());
	}

	public function testTrashPage() {
		$this->pageTrash->moveToTrash();

		$this->assertTrue($this->pageTrash->isInTrash());
	}

	//this can probably be more thorough
	public function testDeletePage() {
		$cID = $this->pageDelete->getCollectionID();

		$this->pageDelete->delete();
		$noPage = Page::getByID($cID);

		$this->assertEquals(COLLECTION_NOT_FOUND,$noPage->error); //maybe there is a more certain way to determine this.
	}
	
}
