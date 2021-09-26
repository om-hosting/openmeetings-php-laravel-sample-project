<?php

namespace App\Http\Controllers;

use Swagger\Client\Api\UserServiceApi;
use Swagger\Client\Configuration;
use Swagger\Client\Model\ExternalUserDTO;
use Swagger\Client\Model\RoomOptionsDTO;

class HelloController extends Controller
{
    private $BASE_URL = "http://localhost:5080/openmeetings";
    //
    public function index()
    {

        //1. Login to service
        $config = new Configuration();
        $config->setHost($this->BASE_URL . '/services');
        $userApiInstance = new UserServiceApi(null, $config);
        $serviceResultLoginWrapper = $userApiInstance->login("soapuser", "!HansHans1");
        if ($serviceResultLoginWrapper->getServiceResult()->getType() != "SUCCESS") {
            $text = "Login Failed " . $serviceResultLoginWrapper->getServiceResult()->getMessage();
            return view('hello_index', ['text' => $text]);
        }
        $sid = $serviceResultLoginWrapper->getServiceResult()->getMessage();

        // 2. Generate Hash for entering a conference room
        $serviceResultHashWrapper = $userApiInstance->getRoomHash($sid,
            new ExternalUserDTO(
                array(
                    "firstname" => "John",
                    "lastname" => "Doe",
                    "external_id" => "uniqueId1",
                    "external_type" => "myCMS",
                    "login" => "john.doe",
                    "email" => "john.doe@gmail.com"
                )
            ),
            new RoomOptionsDTO(
                array(
                    "room_id" => 2,
                    "moderator" => true
                )
            )
        );

        if ($serviceResultHashWrapper->getServiceResult()->getType() != "SUCCESS") {
            $text = "Create Hash Failed " . $serviceResultHashWrapper->getServiceResult()->getMessage();
            return view('hello_index', ['text' => $text]);
        }

        // 3. Construct Login URL
        $hash = $serviceResultHashWrapper->getServiceResult()->getMessage();
        $url = $this->BASE_URL . "/hash?secure=".$hash;
        return view('hello_index', ['text' => $url]);
    }
}
