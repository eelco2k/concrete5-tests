<?php
class DateHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DateHelper
     */
    protected $object;
    
    private $deleteDirs;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = Loader::helper('date');
        $locales = array("de_DE","zh_CN");
        foreach ($locales as $locale) {
            // if language is not installed copy a fake mo file there
            $localedir = DIR_BASE . '/languages/' . $locale;
            if (! is_dir($localedir)) {
                $this->deleteDirs[] = $localedir;
                mkdir($localedir . "/LC_MESSAGES",0777,true);
                copy(DIR_FIXTURES . '/messages.mo' ,$localedir . "/LC_MESSAGES/messages.mo");
            }
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        // delete all directories that were created in setUp()
        if (! empty($this->deleteDirs)) {
            foreach ($this->deleteDirs as $dir) {
                self::delTree($dir); 
            }
        }
    }

    /**
     * delete a directory recursively
     * taken from http://de1.php.net/manual/de/function.rmdir.php#110489
     */
    public static function delTree($dir) {
       $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    } 
    
    public function testDate() 
    {
        $time = mktime(1,1,1,1,1,2011);
        
        Localization::changeLocale("en_US");
        $this->assertEquals("Sat", $this->object->date("D",$time));
        $this->assertEquals("Saturday", $this->object->date("l",$time));
        $this->assertEquals("January", $this->object->date("F",$time));
        $this->assertEquals("Jan", $this->object->date("M",$time));
        $this->assertEquals("am AM", $this->object->date("a A",$time));
        $this->assertEquals("test", $this->object->date('\t\e\s\t',$time));
        $this->assertEquals("2011-01-01 01:01:01", $this->object->date('Y-m-d H:i:s',$time));
        
        Localization::changeLocale("de_DE");
        $this->assertEquals("Sa.", $this->object->date("D",$time));
        $this->assertEquals("Samstag", $this->object->date("l",$time));
        $this->assertEquals("Januar", $this->object->date("F",$time));
        $this->assertEquals("Jan", $this->object->date("M",$time));
        $this->assertEquals("vorm. vorm.", $this->object->date("a A",$time));
        $this->assertEquals("test", $this->object->date('\t\e\s\t',$time));
        $this->assertEquals("2011-01-01 01:01:01", $this->object->date('Y-m-d H:i:s',$time));
        
        Localization::changeLocale("zh_CN");
        $this->assertEquals("周六", $this->object->date("D",$time));
        $this->assertEquals("星期六", $this->object->date("l",$time));
        $this->assertEquals("1月", $this->object->date("F",$time));
        $this->assertEquals("1月", $this->object->date("M",$time));
        $this->assertEquals("上午 上午", $this->object->date("a A",$time));
        $this->assertEquals("test", $this->object->date('\t\e\s\t',$time));
        $this->assertEquals("2011-01-01 01:01:01", $this->object->date('Y-m-d H:i:s',$time));
    }

    public function testTimeSince() {
        Localization::changeLocale("en_US");
        $minutes = 60;
        $hours = $minutes * 60;
        $days = $hours * 24;
        
        // time is in the future
        $future = time()+ 7;
        $this->assertEquals(date(DATE_APP_GENERIC_MDY,$future), $this->object->timeSince($future));
        
        // time is now
        $this->assertEquals("0 seconds", $this->object->timeSince(time()));
        
        // time is in the past
        $this->assertEquals("7 seconds",
                            $this->object->timeSince(time() - 7));
        $this->assertEquals("3 minutes",
                            $this->object->timeSince(time() - (3 * $minutes + 13)));
        $this->assertEquals("3 minutes, 13 seconds",
                            $this->object->timeSince(time() - (3 * $minutes + 13),1));
        $this->assertEquals("4 hours",
                            $this->object->timeSince(time() - (4 * $hours + 2 * $minutes)));
        $this->assertEquals("4 hours, 1 minute",
                            $this->object->timeSince(time() - (4 * $hours + 1 * $minutes),1));
        $this->assertEquals("1 day",
                            $this->object->timeSince(time() - (1 * $days + 1 * $minutes)));
        $this->assertEquals("2 days, 2 hours",
                            $this->object->timeSince(time() - (2 * $days + 2 * $hours),1));
        $this->assertEquals('1 year',
                            $this->object->timeSince(time() - (367 * $days)));
    }
}
