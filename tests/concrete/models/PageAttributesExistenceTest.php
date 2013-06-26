<?
class PageAttributeExistenceTest extends PHPUnit_Framework_TestCase {

	/**
	 *  @dataProvider commonAttributes
	 */
	public function testSetCommonAttributes($handle,$first,$second,$firstStatic=null,$secondStatic=null) {
		$page = Page::getByPath('/about');

		$page->setAttribute($handle,$first);
		$page = Page::getByID($page->getCollectionID(),'ACTIVE');
		$attribute = $page->getAttribute($handle);

		if($firstStatic != null){
			$this->assertSame($attribute,$firstStatic);
		} else {
			$this->assertSame($attribute,$first);
		}

	}

	/**
	 *  @dataProvider commonAttributes
	 */
	public function testResetCommonAttributes($handle,$first,$second,$firstStatic=null,$secondStatic=null) {
		$page = Page::getByPath('/about');

		$page->setAttribute($handle,$second);
		$page->reindex();
		$attribute = $page->getAttribute($handle);
	
		if($secondStatic != null){
			$this->assertSame($attribute,$secondStatic);
		} else {
			$this->assertSame($attribute,$second);
		}
	}

	public function commonAttributes() {
		return array(
			array('exclude_nav',
				true,
				false,
				'1',
				'0'
			),
			array('exclude_page_list',
				true,
				false,
				'1',
				'0'
			),
			array('exclude_search_index',
				true,
				false,
				'1',
				'0'
			),
			array('exclude_sitemapxml',
				true,
				false,
				'1',
				'0'
			),
			array('header_extra_content',
				'<script src="//ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js"></script>',
				'<script src="fake.js"></script>'
			),
			array('meta_keywords',
				'trout, salmon, cod, sturgeon, flying fish',
				'horses, pigs, ducks'
			),
			array('meta_description',
				'A great page about fish',
				'A fun page about farms'
			),
			array('meta_title',
				'Fun Page',
				'Great Page'
			)
		);
	}
}
