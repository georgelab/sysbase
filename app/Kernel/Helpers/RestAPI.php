<?php
/**
 * @author George Azevedo <george@fenix.rio.br>
 * @copyright Copyright (c) 2023 Fênix Comunicação (https://www.fenix.rio.br)
 */
declare(strict_types=1);
namespace App\Kernel\Helpers;

//use App\Kernel\Helpers\{Mailer};

/**
 * Class RestAPI
 */
class RestAPI
{
    public $appSetup;

    /**
     * RestAPI constructor
     */
    public function __construct()
    {
        $this->appSetup = json_decode(CoreSetup);
    }

    /**
     * listen
     */
    public function listen()
    {
        $valid_verbs = ['GET', 'POST', 'PUT', 'DELETE'];
        $request_method = mb_strtoupper($_SERVER['REQUEST_METHOD']);
        if (in_array($request_method, $valid_verbs)) {
            if ($this->isAuthenticated()) {
                switch ($request_method) {
                    case 'GET':
                        $this->getRequest();
                        break;
                    case 'POST':
                        $this->postRequest();
                        break;
                    case 'PUT':
                        $this->putRequest();
                        break;
                    case 'DELETE':
                        $this->deleteRequest();
                        break;
                    default:
                        $this->badRequest();
                        break;
                }
            } else {
                $this->unauthorizedRequest();
            }

        } else {
            $this->badRequest();
        }
    }

    /**
     * badRequest
     */
    private function badRequest()
    {
        $status_info = [
            'code' => 400,
            'status_message' => 'The request could not be understood by the server due to incorrect syntax. The client SHOULD NOT repeat the request without modifications.',
            'data' => NULL
        ];

        $this->response($status_info);
    }

    /**
     * unauthorizedRequest
     */
    private function unauthorizedRequest()
    {
        $status_info = [
            'code' => 401,
            'status_message' => 'Unauthorized request.',
            'data' => NULL
        ];

        $this->response($status_info);
    }

    /**
     * notFound
     */
    private function notFound()
    {
        $status_info = [
            'code' => 404,
            'status_message' => 'The server can not find the requested resource.',
            'data' => NULL
        ];

        $this->response($status_info);
    }

    /**
     * isAuthenticated
     */
    private function isAuthenticated()
    {
        $authkeys = (array)$this->appSetup->authorized_requesters;
        $auth = false;
        if (
            isset($_SERVER["PHP_AUTH_USER"])
            && in_array(trim($_SERVER["PHP_AUTH_USER"]), $authkeys)
        ) {
            $auth = true;
        }
        return $auth;
    }

    /**
     * response
     */
    private function response($status_info)
    {
        header("HTTP/1.1 " . $status_info['code']);
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($status_info);
    }

    /**
     * getRequest
     */
    private function getRequest()
    {
        $url_params = $this->UriGetParser();
        var_dump($url_params);
        exit;
        # TODO

        //$this->badRequest();

    }

    /**
     * postRequest
     */
    private function postRequest()
    {
        //get sent info
        $sent_data = json_decode(file_get_contents("php://input"));
        var_dump($sent_data);
        exit;

        # TODO

        if (
            isset($sent_data)
        ) {
            $this->product->setSku($sent_data->sku);
            $this->product->setName($sent_data->name);
            $this->product->setPrice($sent_data->price);
            $this->product->setType($sent_data->productType);
            $this->product->setTypeArgs(
                json_decode($sent_data->productTypeArgs)
            );
            $this->product->add();

            $status_info = [
                'code' => 200,
                'status_message' => 'OK',
                'data' => json_encode([])
            ];

            $this->response($status_info);

        } else {
            $this->badRequest();
        }
    }

    /**
     * putRequest
     */
    private function putRequest()
    {
        //get sent info
        $sent_data = json_decode(file_get_contents("php://input"));

        var_dump($sent_data);
        exit;

        # TODO

        if (
            isset($sent_data)
        ) {
            $this->product->setSku($sent_data->sku);
            $this->product->setName($sent_data->name);
            $this->product->setPrice($sent_data->price);
            $this->product->setType($sent_data->productType);
            $this->product->setTypeArgs(
                json_decode($sent_data->productTypeArgs)
            );
            $this->product->add();

            $status_info = [
                'code' => 200,
                'status_message' => 'OK',
                'data' => json_encode([])
            ];

            $this->response($status_info);

        } else {
            $this->badRequest();
        }
    }

    /**
     * deleteRequest
     */
    private function deleteRequest()
    {
        $to_delete = json_decode(file_get_contents("php://input"));

        var_dump($to_delete);
        exit;

        # TODO

        if (
            isset($to_delete->ids)
            && is_array($to_delete->ids)
            && count($to_delete->ids) > 0
        ) {
            $this->product->delete([
                'attribute' => 'id',
                'values' => $to_delete->ids
            ]);
        }

        $status_info = [
            'code' => 204,
            'status_message' => 'No Content',
            'data' => json_encode([])
        ];

        $this->response($status_info);
    }


    /**
     * UriGetParser
     */
    private function UriGetParser()
    {
        $uri_params = [];
        $uri_parts = explode('/', mb_strtolower($_SERVER['REQUEST_URI']));

        foreach ($uri_parts as $pos => $value)
            if (trim($value) == "" || $value == 'api')
                unset($uri_parts[$pos]);

        $uri_parts = array_values($uri_parts);

        if (count($uri_parts) >= 1) {
            if (count($uri_parts) == 3) {
                list($model, $model_param, $model_value) = $uri_parts;
            } elseif (count($uri_parts) == 2) {
                $uri_parts[] = 'id'; //default
                list($model, $model_value, $model_param) = $uri_parts;
            } elseif (count($uri_parts) == 1) {
                $uri_parts[] = 'list';
                $uri_parts[] = 'all';
                list($model, $model_param, $model_value) = $uri_parts;
            }

            $uri_params["endpoint"] = $model;
            $uri_params["param"] = $model_param;
            $uri_params["value"] = $model_value;
        }

        return $uri_params;
    }
}