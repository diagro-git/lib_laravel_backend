<?php
namespace Diagro\Backend\Diagro;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;


/**
 * Helpers to call API backends
 *
 * @package Diagro\Web\Diagro
 */
class Api
{


    private static function makeHeaders(array $headers = []) : array
    {
        $defaultHeaders = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . request()->bearerToken(),
            'x-app-id' => config('diagro.app_id')
        ];

        return array_merge($headers, $defaultHeaders);
    }


    private static function makeHttp(array $headers)
    {
        return Http::withHeaders(self::makeHeaders($headers))->timeout(5);
    }


    private static function exception(RequestException $e)
    {
        switch($e->response->status())
        {
            case 406: //Invalid token
                abort(406, "Invalid token");
                break;
            case 400: //Invalid front app id
                abort(400, "Invalid front app ID");
                break;
            case 403: //Unauthorized
                abort(403, "Unauthorized access");
                break;
            default:
                throw $e;
        }
    }


    public static function get(string $url, array $headers = [], array $query = [])
    {
        try {
            return self::makeHttp($headers)->get($url, $query)->throw();
        } catch(RequestException $e) {
            self::exception($e);
        }
    }


    public static function post(string $url, array $data, array $headers = [], array $query = [])
    {
        try {
            return self::makeHttp($headers)->post($url, $data)->throw();
        } catch (RequestException $e) {
            self::exception($e);
        }
    }


    public static function put(string $url, array $data, array $headers = [], array $query = [])
    {
        try {
            return self::makeHttp($headers)->put($url, $data)->throw();
        } catch (RequestException $e) {
            self::exception($e);
        }
    }


    public static function delete(string $url, array $data, array $headers = [], array $query = [])
    {
        try {
            return self::makeHttp($headers)->delete($url, $data)->throw();
        } catch (RequestException $e) {
        }
    }


}