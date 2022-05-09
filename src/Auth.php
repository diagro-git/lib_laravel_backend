<?php
namespace Diagro\Backend;

use Diagro\Token\ApplicationAuthenticationToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Auth
{


    public static function system(int $company_id, ?string $system_user = null)
    {
        $app_id = config('diagro.system.frontend_application');
        $system_user ??= config('diagro.system.default_user');
        $request = request();

        //has AAT in cache?
        $aat = Cache::tags(['system', 'aat'])->get($system_user);
        if(empty($aat)) {
            $password = config('diagro.system.users')[$system_user];
            $aat = self::authSystemUserByCredentials($system_user, decrypt($password), $app_id, $company_id);
        }

        try {
            ApplicationAuthenticationToken::createFromToken($aat);
        } catch(\Exception $e) {
            $at = Cache::tags(['system', 'at'])->get($system_user);
            $aat = self::authSystemUserByToken($at, $system_user, $app_id, $company_id);
        }

        //set in request if success
        $request->headers->set('x-app-id', $app_id);
        $request->headers->set('Authorization', "Bearer $aat");
    }


    private static function authSystemUserByCredentials($email, $password, $app_id, $company_id): string
    {
        $headers = [
            'X-APP-ID' => $app_id,
            'Accept' => 'application/json',
            'x-company-preffered' => $company_id,
        ];
        $url = config('diagro.service_auth_uri') . '/login';
        $response = Http::withHeaders($headers)->post($url);
        if($response->ok()) {
            $json = $response->json();
            if(isset($json['at']) && isset($json['aat'])) {
                Cache::tags(['system', 'at'])->set($email, $json['at']);
                Cache::tags(['system', 'aat'])->set($email, $json['aat']);
                return $json['aat'];
            } else {
                throw new \Exception("Obtaining AAT for system user '$email' failed!");
            }
        } else {
            throw new \Exception("Obtaining token failed for system user '$email'! Reason: " . $response->body());
        }
    }


    private static function authSystemUserByToken($token, $email, $app_id, $company_id): string
    {
        $headers = [
            'X-APP-ID' => $app_id,
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'x-company-preffered' => $company_id,
        ];
        $url = config('diagro.service_auth_uri') . '/login';
        $response = Http::withHeaders($headers)->post($url, []);
        if($response->ok()) {
            $json = $response->json();
            if(isset($json['aat'])) {
                Cache::tags(['system', 'aat'])->set($email, $json['aat']);
                return $json['aat'];
            }
        }

        //maybe AT is revoked, try again with login password
        $password = config('diagro.system.users')[$email];
        self::authSystemUserByCredentials($email, $password, $app_id, $company_id);
    }


}