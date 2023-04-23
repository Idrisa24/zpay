<?php

namespace Saidtech\Zpay;


use Mockery;
use Exception;
use Saidtech\Zpay\Http\Helpers\APIContext;
use Saidtech\Zpay\Http\Helpers\APIRequest;
use Saidtech\Zpay\Http\Helpers\APIMethodType;

class Zpay
{
    /**
     * The personal access client model class name.
     *
     * @var string
     */
    public static $zpayTransactionModel = 'Saidtech\\Zpay\\ZpayTransaction';

    const TRANSACT_TYPE = [
                'c2b' => [
                    'name' => 'Consumer 2 Business',
                    'url' => "/c2bPayment/singleStage/",
                    'encryptSessionKey' => true,
                    'rules' => []
                ],
                'b2c' => [
                    'name' => 'Business 2 Consumer',
                    'url' => "/b2cPayment/singleStage/",
                    'encryptSessionKey' => true,
                    'rules' => []
                ],
    ];

    /**
     * A callback that can get the token from the request.
     *
     * @var callable|null
     */
    public static $zpayRetrievalCallback;

    /**
     * A callback that can add to the validation of the access token.
     *
     * @var callable|null
     */
    public static $zpayTransactionSuccessCallback;

    /**
     * Indicates if Zpay's migrations will be run.
     *
     * @var bool
     */
    public static $runsMigrations = true;

    /**
     * Get the current application URL from the "APP_URL" environment variable - with port.
     *
     * @return string
     */
    public static function currentApplicationUrlWithPort()
    {
        $appUrl = config('app.zpay_url');

        return $appUrl ? ','.parse_url($appUrl, PHP_URL_HOST).(parse_url($appUrl, PHP_URL_PORT) ? ':'.parse_url($appUrl, PHP_URL_PORT) : '') : '';
    }

    /**
     * Set the current user for the application with the given abilities.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|\Laravel\Zpay\HasApiTokens  $user
     * @param  array  $abilities
     * @param  string  $guard
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public static function actingAs($user, $abilities = [], $guard = 'Zpay')
    {
        $token = Mockery::mock(self::zpayTransactionModel())->shouldIgnoreMissing(false);

        if (in_array('*', $abilities)) {
            $token->shouldReceive('can')->withAnyArgs()->andReturn(true);
        } else {
            foreach ($abilities as $ability) {
                $token->shouldReceive('can')->with($ability)->andReturn(true);
            }
        }

        $user->withAccessToken($token);

        if (isset($user->wasRecentlyCreated) && $user->wasRecentlyCreated) {
            $user->wasRecentlyCreated = false;
        }

        app('auth')->guard($guard)->setUser($user);

        app('auth')->shouldUse($guard);

        return $user;
    }

    public static function getSession()
    {
        $context = new APIContext();
        // Api key
        $context->set_api_key(config('zpay.api_key'));
        // Public key
        $context->set_public_key(config('zpay.token'));
        // Use ssl/https
        $context->set_ssl(true);
        // Method type (can be GET/POST)
        $context->set_method_type(APIMethodType::GET);
        // API address
        $context->set_address(config('zpay.url'));
        // API Port
        $context->set_port(443);
        // API Path
        $context->set_path('/'.config('zpay.debug_env').config('zpay.session_url'));

        // Add/update headers
        $context->add_header('Origin', '*');

        // Parameters can be added to the call as well that on POST will be in JSON format and on GET will be URL parameters
        // context->add_parameter('key', 'value');

        // Create a request object
        $request = new APIRequest($context);

        // Do the API call and put result in a response packet
        $response = null;

        try {
            $response = $request->execute();
        } catch(exception $e) {
            echo 'Call failed: ' . $e->getMessage() . '<br>';
        }

        if ($response->get_body() == null) {
            throw new Exception('SessionKey call failed to get result. Please check.');
        }


        $decoded = json_decode($response->get_body());
        $data =array();

        if (isset($decoded->output_ResponseCode) == 'INS-0') {
            $data = ['status' =>  200, 'message' => $decoded->output_ResponseDesc, 'session' => $decoded->output_SessionID];
        }elseif (isset($decoded->output_ResponseCode) == 'INS-989') {
            $data= ['status' =>  400, 'message' => $decoded->output_ResponseDesc];
        }else {
            $data = ['status' =>  402, 'message' => $decoded->output_error];
        }

        return $data;
    }

    public static function sendTransaction($payload, $session = null, $transactions)
    {
        $crt;
        $crt2;
        if (strtoupper($payload['currency']) == 'TZS') {
            $crt = 'vodacomTZN';
            $crt2 = 'TZN';
        }elseif (strtoupper($payload['currency']) == 'GHS') {
            $crt = 'vodafoneGHA';
            $crt2 = 'GHA';
        }elseif(strtoupper($payload['currency']) == 'USD') {
            $crt = 'vodacomDRC';
            $crt2 = 'DRC';
        }
        $context = new APIContext();
        // Session key
        $context->setSessionToken($session);
        // Public key
        $context->set_public_key(config('zpay.token'));
        // Use ssl/https
        $context->set_ssl(true);
        // Method type (can be GET/POST)
        $context->set_method_type(APIMethodType::POST);
        // API address
        $context->set_address(config('zpay.url'));
        // API Port
        $context->set_port(443);
        // API Path
        $context->set_path('/'.config('zpay.debug_env').config('zpay.transaction_url').$crt.self::TRANSACT_TYPE[$transactions]['url']);

        // Add/update headers
        $context->add_header('Origin', '*');

        $context->add_parameter('input_Amount', '10000');
        $context->add_parameter('input_Country', $crt2);
        $context->add_parameter('input_Currency', $payload['currency']);
        $context->add_parameter('input_CustomerMSISDN', '000000000001');
        $context->add_parameter('input_ServiceProviderCode', config('zpay.service_code'));
        $context->add_parameter('input_ThirdPartyConversationID', 'asv02e5958774f7ba228d83d0d689761');
        $context->add_parameter('input_TransactionReference', 'T1234C');
        $context->add_parameter('input_PurchasedItemsDesc', 'Shoes');

        $request = new APIRequest($context);

        // Do the API call and put result in a response packet
        $response = null;

        try {
            $response = $request->executePostRequest();
        } catch(exception $e) {
            echo 'Call failed: ' . $e->getMessage() . '<br>';
        }

        if ($response->get_body() == null) {
            throw new Exception('API call failed to get result. Please check.');
        }

        // Decode JSON packet
        $decoded = json_decode($response->get_body());
        $data =array();

        if (isset($decoded->output_ResponseCode) == 'INS-0')
        {
            $data = ['status' =>  200, 'message' => $decoded->output_ResponseDesc, 'transactionID' => $decoded->output_TransactionID, 'conversionID' => $decoded->output_ConversationID, 'systemID'=> $decoded->output_ThirdPartyConversationID];
        }

        return json_encode($data);
        

    }

    /**
     * Set the personal access token model name.
     *
     * @param  string  $model
     * @return void
     */
    public static function useZpayTransactionModel($model)
    {
        static::$zpayTransactionModel = $model;
    }

    /**
     * Specify a callback that should be used to fetch the access token from the request.
     *
     * @param  callable  $callback
     * @return void
     */
    public static function getAccessTokenFromRequestUsing(callable $callback)
    {
        static::$zpayRetrievalCallback = $callback;
    }

    /**
     * Specify a callback that should be used to authenticate access tokens.
     *
     * @param  callable  $callback
     * @return void
     */
    public static function authenticateAccessTokensUsing(callable $callback)
    {
        static::$zpayTransactionSuccessCallback = $callback;
    }

    /**
     * Determine if Zpay's migrations should be run.
     *
     * @return bool
     */
    public static function shouldRunMigrations()
    {
        return static::$runsMigrations;
    }

    /**
     * Configure Zpay to not register its migrations.
     *
     * @return static
     */
    public static function ignoreMigrations()
    {
        static::$runsMigrations = false;

        return new static;
    }

    /**
     * Get the token model class name.
     *
     * @return string
     */
    public static function zpayTransactionModel()
    {
        return static::$zpayTransactionModel;
    }
}
