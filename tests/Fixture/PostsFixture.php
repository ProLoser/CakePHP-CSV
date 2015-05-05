<?php
namespace CakePHPCSV\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PostsFixture
 *
 */
class PostsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => [
            'type' => 'integer',
            'length' => 10,
            'unsigned' => true,
            'null' => false,
            'default' => null,
            'comment' => '',
            'autoIncrement' => true,
            'precision' => null],
        'title' => [
            'type' => 'string',
            'length' => 255,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'body' => [
            'type' => 'string',
            'length' => 255,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'created' => [
            'type' => 'datetime',
            'length' => null,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null
        ],
        'modified' => [
            'type' => 'datetime',
            'length' => null,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null
        ],
        'user_id' => [
            'type' => 'integer',
            'length' => 10,
            'unsigned' => true,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'autoIncrement' => null
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []]
        ],
        '_options' => [
            'engine' => 'InnoDB', 'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records  = [
        [
            'id' => 5,
            'title' => 'First post',
            'body' => 'first post body',
            'user_id' => 1,
            'created' => '2015-03-10 10:35:46',
            'modified' => '2015-03-10 10:37:12'
        ],
        [
            'id' => 6,
            'title' => 'Second post',
            'body' => 'second post body',
            'user_id' => 1,
            'created' => '2015-03-10 10:36:46',
            'modified' => '2015-03-10 10:36:46'
        ],
        [
            'id' => 7,
            'title' => 'Third post',
            'body' => 'third post body',
            'user_id' => 1,
            'created' => '2015-03-10 10:37:53',
            'modified' => '2015-03-10 10:37:53'
        ],
        [
            'id' => 8,
            'title' => 'Fourth post',
            'body' => 'fourth post body',
            'user_id' => 1,
            'created' => '2015-03-11 11:33:20',
            'modified' => '2015-03-11 11:33:20'
        ]
    ];
}
