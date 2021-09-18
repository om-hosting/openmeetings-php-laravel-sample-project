<?php

namespace App\Http\Controllers;

use Swagger\Client\Api\UserServiceApi;
use Swagger\Client\Configuration;
use Swagger\Client\Model\ExternalUserDTO;
use Swagger\Client\Model\RoomOptionsDTO;

class HelloController extends Controller
{
    //
    public function index()
    {
        $config = new Configuration();
        $config->setHost('http://localhost:5080/openmeetings/services');
        $userApiInstance = new UserServiceApi(null, $config);
        $serviceResultLogin = $userApiInstance->login("soapuser", "!HansHans1");
        if ($serviceResultLogin->getType() != "SUCCESS") {
            $text = "Login Failed " . $serviceResultLogin->getMessage();
            return view('hello_index', ['text' => $text]);
        }
        $sid = $serviceResultLogin->getMessage();

        $serviceResultHash = $userApiInstance->getRoomHash($sid,
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
                    "room_id" => 1,
                    "moderator" => true
                )
            )
        );

        if ($serviceResultHash->getType() != "SUCCESS") {
            $text = "Create Hash Failed " . $serviceResultHash->getMessage();
            return view('hello_index', ['text' => $text]);
        }

        $text = "Hash: " . $serviceResultHash->getMessage();
        return view('hello_index', ['text' => $text]);
    }
}
