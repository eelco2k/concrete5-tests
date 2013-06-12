<?php
class PageTest extends PHPUnit_Framework_TestCase {
   //protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
	//could probably add like, three pages here under home and then just test on
	//the individual pages in the test functions below
	//then delete them on clean up.
   //protected function setUp() {
			//doesn't make sense for a model.
			//suppose we could set up a small tree in here to work on .
		  //$this->object = Loader::model('page');
    //}

	//doesn't really make any sense in this context because pages don't do much unless they're gotten.
	//public function testObjectCreated() {
		//$this->assertTrue($this->object instanceof Page);
		//$this->assertTrue($this->object instanceof Concrete5_Model_Page);
	//}

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

		$data = array(
			'uID'=>1,
			'cName'=>$pageName,
			'cHandle'=>$pageHandle
		);
			
		$newPage = $home->add($ct,$data);

		$parentID = $newPage->getCollectionParentID();

		$this->assertInstanceOf('Page',$newPage);
		$this->assertEquals($parentID, HOME_CID);

		$this->assertSame($pageName,$newPage->getCollectionName());
		$this->assertSame($pageHandle, $newPage->getCollectionHandle());
		$this->assertSame('/'.$pageHandle, $newPage->getCollectionPath());
	}

	public function testMovePage() {
		$page = Page::getByPath('/page');
		$this->assertInstanceOf('Page',$page);

		$newParent = Page::getByPath('/about');
		$this->assertNotEquals(COLLECTION_NOT_FOUND,$newParent->error);

		$parentCID = $newParent->getCollectionID();


		$page->move($newParent);

		$path = $page->getCollectionPath();
		$this->assertSame('/about/page', $path);

		$this->assertSame($parentCID, $page->getCollectionParentID());

	}

	public function testTrashPage() {
		$page = Page::getByPath('/page');
		$this->assertInstanceOf('Page',$page);
		$page->moveToTrash();

		$this->assertTrue($page->isInTrash());
	}

	//this can probably be more thorough
	public function testDeletePage() {
		$page = Page::getByPath(TRASH_PAGE_PATH.'/page');
		$cID = $page->getCollectionID();

		$page->delete();
		$noPage = Page::getByID($cID);

		$this->assertEquals(COLLECTION_NOT_FOUND,$noPage->error); //maybe there is a more certain way to determine this.
	}
	
}
