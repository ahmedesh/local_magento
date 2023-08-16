<?php declare(strict_types=1);

namespace Macademy\Blog\Model\ResourceModel\Post;

use Macademy\Blog\Model\Post;
use Macademy\Blog\Model\ResourceModel\Post as PostResourceModel;  // عشان اعرف استخدمه تحت وميتضاربش مع ال post
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected function _construct()
    {
//        $this->_init(Post::class ,\Macademy\Blog\Model\ResourceModel\Post::class);  // _init() => // link our database table to this class
     // طريقه تانيه
        $this->_init(Post::class ,PostResourceModel::class);  // _init() => // link our database table to this class

    }

}
