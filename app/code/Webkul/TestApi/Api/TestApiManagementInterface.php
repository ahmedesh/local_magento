<?php

namespace Webkul\TestApi\Api;

interface TestApiManagementInterface
{
    /**
     * get test Api data.
     *
     * @api
     *
     * @param int $id
     *
     * @return \Webkul\TestApi\Api\Data\TestApiInterface
     */
    public function getApiData();  // for guest

//    public function getApiData($id); // id or token for customer or admin

}
