<?php
namespace CakePHPCSV\Test\TestCase\Model\Behavior;

use CakePHPCSV\Model\Behavior\CsvBehavior;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;

/**
 * CakePHP-CSV\Model\Behavior\CsvBehavior Test Case
 */
class CsvBehaviorTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'Posts' => 'plugin.CakePHPCSV.posts'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $Table = TableRegistry::get('posts');
        $this->Csv = new CsvBehavior(
            $Table,
            [
                'text' => true
            ]
        );
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Csv);

        parent::tearDown();
    }

    /**
     * Test importCsv method
     *
     * @return void
     */
    public function testImportCsv()
    {
        $csv = 'posts.title, body, created, user_id, comments.0.body, comments.1.body' . PHP_EOL;
        $csv .= 'A title once again,And the article body follows,2014-05-04 10:30:32, 3,First comment,Second comment' . PHP_EOL;
        $csv .= 'The title,This is the article body.,2014-05-04 10:30:33, 5,Third comment,Another comment' . PHP_EOL;
        $csv .= 'Title strikes back,This is really exciting! Not.,2014-05-05 10:30:39, 3,Awesome comment,Last comment' . PHP_EOL;

        $expected = [
            0 => [
                'title' => 'A title once again',
                'body' => 'And the article body follows',
                'created' => '2014-05-04 10:30:32',
                'user_id' => 3,
                'comments' => [
                    ['body' => 'First comment'],
                    ['body' => 'Second comment'],
                ]
            ],
            1 => [
                'title' => 'The title',
                'body' => 'This is the article body.',
                'created' => '2014-05-04 10:30:33',
                'user_id' => 5,
                'comments' => [
                    ['body' => 'Third comment'],
                    ['body' => 'Another comment'],
                ]
            ],
            2 => [
                'title' => 'Title strikes back',
                'body' => 'This is really exciting! Not.',
                'created' => '2014-05-05 10:30:39',
                'user_id' => 3,
                'comments' => [
                    ['body' => 'Awesome comment'],
                    ['body' => 'Last comment'],
                ]
            ]
        ];
        $actual = $this->Csv->importCsv($csv);
        $this->assertEquals($expected, $actual);

        $expected = [
            0 => [
                'title' => 'A title once again',
                'body' => 'And the article body follows'
            ],
            1 => [
                'title' => 'The title',
                'body' => 'This is the article body.',
            ],
            2 => [
                'title' => 'Title strikes back',
                'body' => 'This is really exciting! Not.',
            ]
        ];
        $actual = $this->Csv->importCsv(
            $csv,
            ['title','body']
        );
        $this->assertEquals($expected, $actual);
    }
}
