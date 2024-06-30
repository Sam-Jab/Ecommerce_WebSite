<?php 

// namespace App\Service;

// use App\Repository\UserRepository;
// use PayPalCheckoutSdk\Core\PayPalHttpClient;
// use PayPalCheckoutSdk\Core\SandboxEnvironment;
// use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

// class PayPalService
// {
//     private $clientId;
//     private $secret;

//     public function __construct(UserRepository $client , $secret)
//     {
//         $this->clientId = $client->getId();
//         $this->secret = $secret;
//     }

//     public function captureOrder(Orders $order)
//     {
//         $environment = new SandboxEnvironment($this->clientId, $this->secret);
//         $client = new PayPalHttpClient($environment);

//         $request = new OrdersCaptureRequest($order->getId());
//         $response = $client->execute($request);

//         return $response->result;
//     }
// }
