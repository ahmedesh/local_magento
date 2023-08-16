<?php declare(strict_types=1);

namespace Macademy\Blog\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;

class Index implements HttpGetActionInterface
{

//    public function execute()
//    {
//        die('Blog index');
//    }

    public $redirectFactory;

    public function __construct(
        RedirectFactory $redirectFactory
    ) {
        $this->redirectFactory = $redirectFactory;
    }

    public function execute():Redirect
    {
        $redirect = $this->redirectFactory->create();
        return $redirect->setPath('blog/post/listaction'); // return to another url (special url)
    }
}
