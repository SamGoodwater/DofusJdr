<?php 
namespace PHPUnit\Framework;

use PHPUnit\Framework\TestCase;

final class PageTest extends TestCase
{
    private $_pages = [];
    /**
     * @test
     */
    public function testExistsPageNeeded(): void{
        print "zdq";
        // $this->expectException(InvalidArgumentException::class);
        $manager = new \PageManager();
        foreach (\Page::UNIQID_NO_EDIT as $uniqid) {
            $this->assertTrue($manager->existsUniqid($uniqid));
            $this->_pages[$uniqid] = $manager->getFromUniqid($uniqid);
            $this->assertInstanceOf(\Page::class, $this->_pages[$uniqid]);
        }
    }
    /**
     * @test
     */
    public function testManagerGetAllPages(): void{
        // $this->expectException(InvalidArgumentException::class);
        $manager = new \PageManager();
        $pages = $manager->getAll();
        $this->assertIsArray($pages);
        $this->assertNotEmpty($pages);
    }
    /**
     * @test
     */
    public function testManagerExistAndGetFrom(): void{
        // $this->expectException(InvalidArgumentException::class);
        $manager = new \PageManager();
        $page = $this->_pages[0];
        // ID
        $this->assertTrue($manager->existsId($page->getId()));
        $var_temp = $manager->getFromId($page->getId());
        $this->assertInstanceOf(\Page::class, $var_temp);
        // UNIQID 
        $this->assertTrue($manager->existsUniqid($page->getUniqid()));
        $var_temp = $manager->getFromUniqid($page->getUniqid());
        $this->assertInstanceOf(\Page::class, $var_temp);
        // URL_NAME
        $this->assertTrue($manager->existsUrl_name($page->getUrl_name()));
        $var_temp = $manager->getFromUrl_name($page->getUrl_name());
        $this->assertInstanceOf(\Page::class, $var_temp);
    }
    /**
     * @test
     */
    public function testManagerSearch(): void{
        // $this->expectException(InvalidArgumentException::class);
        $manager = new \PageManager();
        $pages = $manager->search("home");
        $this->assertIsArray($pages);
        $this->assertNotEmpty($pages);
    }


}