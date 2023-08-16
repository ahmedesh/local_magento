<?php declare(strict_types=1);

namespace Macademy\Blog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Post extends AbstractDB
{

    const MAIN_TABLE = 'macademy_blog_post';
    const ID_FIELD_NAME = 'id';
    protected function _construct()
    {
//      $this->_init('macademy_blog_post' , 'id');  // _init() => // link our database table to this class
        $this->_init(self::MAIN_TABLE , self::ID_FIELD_NAME);  // _init() => // link our database table to this class
    }
}
