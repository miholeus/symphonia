<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of NewsServiceTest
 *
 * @author miholeus
 */
require_once dirname(__FILE__) . "/../../../../application/bootstrap.php";
require_once dirname(__FILE__) . "/../../../../application/ControllerTestCase.php";

class Soulex_Components_News_NewsServiceTest extends ControllerTestCase
{
    /**
     * @var Soulex_Components_News_NewsService
     * @access protected 
     */
    protected $object;

    protected function setUp()
    {
        parent::setUp();
        $this->object = new Soulex_Components_News_NewsService();
    }

    public function testFetchAll()
    {
        $allNews = $this->object->fetchAll();
        $this->assertEquals(2, count($allNews));
    }

    public function testFindById()
    {
        $created_time = "2010-05-20 08:00:00";
        $updated_time = "2010-05-21 13:39:12";
        $published_time = $created_time;
        $stub = new Soulex_Components_News_NewsModel();
        $stub->setId(1)
                ->setTitle('test')
                ->setShortDescription('test description')
                ->setDetailDescription('detail description')
                ->setCreatedAt($created_time)
                ->setUpdatedAt($updated_time)
                ->setPublishedAt($published_time)
                ->setPublished(1);

        $actual = $this->object->findById(1);

        $this->_assertNewsObjects($stub, $actual);
    }

    public function testSave()
    {
        $data = $this->_prepareNewsData();
        $this->object->setOptions($data);
        $news = $this->object->save();
        $createdId = $news->getId();
        $savedNews = $this->object->findById($createdId);

        $this->_assertNewsObjects($news, $savedNews);

        $this->object->delete($createdId);

    }

    public function testDelete()
    {
        $data = $this->_prepareNewsData();
        $this->object->setOptions($data);
        $news = $this->object->save();
        $createdId = $news->getId();
        $this->object->delete($createdId);
        $news = $this->object->findById($createdId);
        $this->assertNull($news);
    }

    private function _assertNewsObjects($expected, $actual)
    {
        $this->assertEquals($expected->getId(), $actual->getId());
        $this->assertEquals($expected->getTitle(), $actual->getTitle());
        $this->assertEquals($expected->getShortDescription(), $actual->getShortDescription());
        $this->assertEquals($expected->getDetailDescription(), $actual->getDetailDescription());
        $this->assertEquals($expected->getCreatedAt(), $actual->getCreatedAt());
        $this->assertEquals($expected->getUpdatedAt(), $actual->getUpdatedAt());
        $this->assertEquals($expected->getPublished(), $actual->getPublished());
        $this->assertEquals($expected->getPublished(), $actual->getPublished());
    }

    private function _prepareNewsData()
    {
        $date = date("Y-m-d H:i:s");
        $random_number = rand(0, 1000000);
        $data = array(
            'title' => 'title_' . $random_number,
            'shortDescription' => 'short_' . $random_number,
            'detailDescription' => 'detail_' . $random_number,
            'published' => 0,
            'createdAt' => $date,
            'updatedAt' => $date,
            'publishedAt' => $date
        );
        return $data;
    }
}
