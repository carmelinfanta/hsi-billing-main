<?php

namespace App\Http\Controllers;

use App\Models\AccessToken;
use App\Models\HostedPageId;
use App\Models\Subscriptions;
use App\Models\Partner;
use App\Models\PaymentMethod;
use App\Models\Enterprise;
use App\Models\Plans;
use App\Models\Support;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

use App\Mail\CreateSubscription;
use App\Models\AddOn;
use App\Models\BudgetCapSettings;
use App\Models\Card;
use App\Models\PartnerUsers;
use App\Models\ProviderAvailabilityData;
use App\Models\ProviderData;
use Exception;
use Illuminate\Support\Facades\DB;

class SubscriptionsController extends Controller
{
    //Get subscription

    public function getSubscription()
    {
        if (Session::has('loginPartner')) {

            $partner = Partner::where('zoho_cust_id', Session::get('loginId'))->first();
            $subscriptionlive = null;
            $subscriptions = null;
            $paymentmethod = null;
            $subscriptionPaymentMethod = null;
            $partner_plans = $partner->selected_plans ?? null;
            $selected_plans = json_decode($partner_plans);
            if ($selected_plans === null) {
                return redirect('/select-plans');
            }
            $highest_plan = Plans::query()
                ->orderBy('price', 'desc')
                ->whereIn('plan_id', $selected_plans)
                ->first();
            $lowest_plan = Plans::query()
                ->orderBy('price', 'asc')
                ->where('price', '!=', '0')
                ->whereIn('plan_id', $selected_plans)
                ->first();

            if (Session::has('loginId')) {

                $subscriptionlive = Subscriptions::where('zoho_cust_id', '=', Session::get('loginId'))
                    ->where('status', '=', 'live')
                    ->first();

                $subscriptions = DB::table('subscriptions')
                    ->join('plans', 'subscriptions.plan_id', '=', 'plans.plan_id')
                    ->select('plans.*', 'subscriptions.*')
                    ->where('zoho_cust_id', '=', Session::get('loginId'))
                    ->get();

                $paymentmethod = PaymentMethod::where('zoho_cust_id', '=', Session::get('loginId'))->first();
                foreach ($subscriptions as $subscription) {
                    $subscriptionPaymentMethod = PaymentMethod::where('payment_method_id', '=', $subscription->payment_method_id)->first();
                }
            }

            if ($subscriptionlive && !empty($subscriptionlive->plan_id)) {

                $subscribed_id = $subscriptionlive->plan_id;
                $plan_subscribed = Plans::where('plan_id', '=', $subscribed_id)->first();
                $plan_subscribed_id = $plan_subscribed->id ?? null;
                $current_plan = Plans::where('plan_id', $subscriptionlive->plan_id)->first();
                $is_enterprise_plan = $current_plan ? stripos($current_plan->plan_code, 'enterprise') !== false : false;
                $current_plan_has_addon = $current_plan ? AddOn::where('plan_id', $current_plan->plan_id)->exists() : false;
            } else {
                $current_plan = null;
                $plan_subscribed = null;
                $plan_subscribed_id = null;
                $is_enterprise_plan = false;
                $current_plan_has_addon = false;
            }


            if ($plan_subscribed) {

                $plans_orders = Plans::query()
                    ->orderBy('price', 'desc')
                    ->where('plan_code', 'NOT LIKE', '%' . 'enterprise' . '%')
                    ->where('price', '>', $plan_subscribed->price)
                    ->whereIn('plan_id', $selected_plans)
                    ->get();

                $plans_ascs = Plans::query()
                    ->orderBy('id', 'asc')
                    ->where('price', '!=', '0')
                    ->where('price', '<', $plan_subscribed->price)
                    ->whereIn('plan_id', $selected_plans)
                    ->get();
            } else {
                $plans_orders = Plans::query()
                    ->orderBy('price', 'desc')
                    ->where('plan_code', 'NOT LIKE', '%' . 'enterprise' . '%')
                    ->whereIn('plan_id', $selected_plans)
                    ->get();

                $plans_ascs = Plans::query()
                    ->orderBy('id', 'asc')
                    ->where('price', '!=', '0')
                    ->whereIn('plan_id', $selected_plans)
                    ->get();
            }

            $plans_orders = $plans_orders->sortBy('price')->values();

            $plans_ascs = $plans_ascs->sortBy('price')->values();

            $plans = Plans::where('price', '!=', '0')->get();

            $showModal = false;

            $partner = Partner::where('zoho_cust_id', Session::get('loginId'))->first();

            $availability_data = ProviderAvailabilityData::where('zoho_cust_id', Session::get('loginId'))->first();

            $company_info = ProviderData::where('zoho_cust_id', Session::get('loginId'))->first();

            $paymentMethods = DB::table('payment_methods')->where('zoho_cust_id', Session::get('loginId'))->get();

            // Uncomment if you want to show modal based on availability_data or company_info being null
            // if ($availability_data === null || $company_info === null) {
            //     $showModal = true;
            // }

            return view('partner.subscriptions', compact(
                'subscriptions',
                'paymentmethod',
                'subscriptionPaymentMethod',
                'plan_subscribed',
                'plans',
                'highest_plan',
                'lowest_plan',
                'current_plan',
                'subscriptionlive',
                'plans_orders',
                'plans_ascs',
                'current_plan_has_addon',
                'is_enterprise_plan',
                'showModal',
                'availability_data',
                'company_info',
                'paymentMethods'
            ));
        }
    }

    public function getSubscriptionOld()
    {

        if (Session::has('loginPartner')) {
            $plans_order = Plans::query()
                ->orderBy('price', 'desc')
                ->where('plan_code', 'NOT LIKE', '%' . 'enterprise' . '%')
                ->first();


            $plans_asc = Plans::query()
                ->orderBy('id', 'asc')
                ->where('price', '!=', '0')
                ->first();


            if (Session::has('loginId')) {
                $subscriptionlive =  Subscriptions::where('zoho_cust_id', '=', Session::get('loginId'))
                    ->where('status', '=', 'live')
                    ->first();

                $subscriptions = DB::table('subscriptions')
                    ->join('plans', 'subscriptions.plan_id', '=', 'plans.plan_id')
                    ->select('plans.*', 'subscriptions.*')
                    ->where('zoho_cust_id', '=', Session::get('loginId'))
                    ->get();
                $paymentmethod = PaymentMethod::where('zoho_cust_id', '=', Session::get('loginId'))->first();
            }
            if (!empty($subscriptionlive->plan_id)) {

                $subscribed_id = $subscriptionlive->plan_id;
                $plan_subscribed = Plans::where('plan_id', '=', $subscribed_id)->first();
                $plan_subscribed_id = $plan_subscribed->id;
                $current_plan = Plans::where('plan_id', $subscriptionlive->plan_id)->first();
                $is_enterprise_plan = stripos($current_plan->plan_code, 'enterprise') !== false;
                $current_plan_has_addon = AddOn::where('plan_id', $current_plan->plan_id)->exists();
            } else {
                $current_plan = null;
                $plan_subscribed = null;
                $plan_subscribed_id = null;
                $is_enterprise_plan = false;
                $current_plan_has_addon = false;
            }
            $plans_orders = Plans::query()
                ->orderBy('price', 'desc')
                ->where('plan_code', 'NOT LIKE', '%' . 'enterprise' . '%')
                ->where('price', '>', $plan_subscribed->price)
                ->get();

            $plans_orders = $plans_orders->sortBy('price')->values();


            $plans_ascs = Plans::query()
                ->orderBy('id', 'asc')
                ->where('price', '!=', '0')
                ->where('price', '<', $plan_subscribed->price)
                ->get();


            $plans_ascs = $plans_ascs->sortBy('price')->values();

            $plans = Plans::where('price', '!=', '0')->get();

            $showModal = false;

            $partner = Partner::where('zoho_cust_id', Session::get('loginId'))->first();

            $availability_data = ProviderAvailabilityData::where('zoho_cust_id', Session::get('loginId'))->first();

            $company_info = ProviderData::where('zoho_cust_id', Session::get('loginId'))->first();

            // if ($availability_data === null || $company_info === null) {
            //     $showModal = true;
            // }

            return view('partner.subscriptions', compact('subscriptions', 'paymentmethod', 'plan_subscribed', 'plans', 'plan_subscribed_id', 'subscriptionlive',  'plans_order', 'plans_orders', 'plans_ascs', 'plans_asc', 'current_plan_has_addon', 'is_enterprise_plan', 'showModal', 'availability_data', 'company_info'));
        }
    }


    // Create subscription
    public function addSubscription(AccessToken $token)
    {
        $add_url = env('NEW_SUBSCRIPTION_URL');
        $app_url = env('APP_URL');
        $id = Route::getCurrentRoute()->id;
        $token1 = AccessToken::latest('created_at')->first();
        if ($token1 !== null) {
            $access_token = $token1->access_token;
        } else {
            $token->getToken();
            $token1 = AccessToken::latest('created_at')->first();
            $access_token = $token1->access_token;
        }
        $plan = Plans::where('plan_id', '=', $id)->first();

        $paymentMethod = PaymentMethod::where('zoho_cust_id', Session::get('loginId'))->first();
        $trans_fee_enable = env('CARD_TRANS_FEE');
        $setupFee = 0;

        if ($paymentMethod->type === 'card' && $trans_fee_enable) {

            $setupFee = $this->calculateTransactionFee($plan);
        }

        $is_metered_billing = false;

        $initial_discount = "";

        if (strpos($plan->plan_code, 'cpc') !== false) {

            $is_metered_billing = true;

            $initial_discount = 'cpc-initial-coupon';
        }

        $subscription = Subscriptions::where('zoho_cust_id', Session::get('loginId'))->first();

        if (Session::has('loginId')) {

            $partner =  Partner::where('zoho_cust_id', '=', Session::get('loginId'))->first();
        }
        if (!$subscription) {

            $client = new \GuzzleHttp\Client();

            // try {
            $options = [
                'headers' => [
                    'Authorization' => ('Zoho-oauthtoken ' . $access_token),
                    'X-com-zoho-subscriptions-organizationid' => env('ORGANIZATION_ID'),
                    'content-type: application/json'
                ],
                'json' => [
                    "customer_id" => $partner->zoho_cust_id,
                    "plan" => [
                        "plan_code" => $plan->plan_code,
                        "quantity" => 1,
                    ],
                    "coupon_code" => $initial_discount,
                    "is_metered_billing" => $is_metered_billing,
                    "redirect_url" => "$app_url/thankyou-create",
                    "exclude_setup_fee" => $excludeSetupFee
                ]
            ];

            $res = $client->request(
                'POST',
                $add_url,
                $options
            );

            $response = (string) $res->getBody();
            $response = json_decode($response);

            return redirect($response->hostedpage->url);
            // } catch (GuzzleException $e) {

            //     $response = $e->getResponse()->getBody(true);

            //     $response = json_decode($response);

            //     if ($response) {

            //         return redirect('/logout')->with('fail', 'You have to login first');
            //     }
            // }
        } else {
            if ($subscription->status === 'live') {
                return redirect('/subscription')->with('fail', 'You cannot make duplicate subscription');
            } elseif ($subscription->status === 'cancelled') {
                return redirect('/subscribe-update/' . $id);
            }
        }
    }


    //Update subscription
    public function updateSubscription()
    {
        $app_url = env('APP_URL');

        $update_url = env('UPDATE_SUBSCRIPTION_URL');

        $token = AccessToken::latest('created_at')->first();

        $access_token = $token->access_token;

        $id = Route::getCurrentRoute()->id;

        $plan = Plans::where('plan_id', '=', $id)->first();

        $paymentMethod = PaymentMethod::where('zoho_cust_id', Session::get('loginId'))->first();
        $trans_fee_enable = env('CARD_TRANS_FEE');
        $setupFee = 0;
        $excludeSetupFee = true;

        if ($paymentMethod->type === 'card' && $trans_fee_enable) {

            $setupFee = $this->calculateTransactionFee($plan);
            $excludeSetupFee = false;
        }

        $subscription =   Subscriptions::where('zoho_cust_id', '=', Session::get('loginId'))->first();

        if ($id !== $subscription->plan_id) {
            try {

                $client = new \GuzzleHttp\Client();

                $options = [
                    'headers' => [
                        'Authorization' => ('Zoho-oauthtoken ' . $access_token),
                        'X-com-zoho-subscriptions-organizationid' => env('ORGANIZATION_ID'),
                        'content-type: application/json'
                    ],
                    'json' => [
                        "subscription_id" => $subscription->subscription_id,
                        "plan" => [
                            "plan_code" => $plan->plan_code,
                            "quantity" => 1,
                        ],
                        "auto_collect" => true,
                        "redirect_url" =>  "$app_url/thankyou-update",
                    ]
                ];

                $res = $client->request(
                    'POST',
                    $update_url,
                    $options
                );

                $response = (string) $res->getBody();
                $response = json_decode($response);

                return redirect($response->hostedpage->url);
            } catch (GuzzleException $e) {

                $response = $e->getResponse()->getBody(true);
                $response = json_decode($response);

                if ($response) {

                    return redirect('/logout')->with('fail', 'You have to login first');
                }
            }
        } else {
            return redirect('/subscription')->with('fail', 'You cannot make duplicate subscription');
        }
    }

    public function upgradeSubscription(Request $request)
    {

        return redirect('/change-plan/' . $request->plan_id);
    }

    public function createSubscription(Request $request, AccessToken $token)

    {
        $partner = Partner::where('zoho_cust_id', '=', $request->partner_id)->first();

        $primary_user = PartnerUsers::where('zoho_cust_id', $partner->zoho_cust_id)->where('is_primary', true)->first();

        $add_url = env('NEW_SUBSCRIPTION_URL');

        $update_url = env('UPDATE_SUBSCRIPTION_URL');

        if (empty($partner->zoho_cust_id)) {

            return back()->with('fail', 'Create a Partner!');
        }

        $subscription = Subscriptions::where('zoho_cust_id', '=', $partner->zoho_cust_id)->where('status', 'live')->first();

        if (!empty($subscription)) {

            return back()->with('fail', 'Subscription already exists for the partner');
        }

        $plan_sub = Plans::where('plan_code', '=', $request->plan_code)->first();

        $paymentMethod = PaymentMethod::where('zoho_cust_id', $partner->zoho_cust_id)->first();
        $trans_fee_enable = env('CARD_TRANS_FEE');


        if ($paymentMethod->type === 'card' && $trans_fee_enable) {

            $setupFee = $this->calculateTransactionFee($plan_sub);
        }

        $app_url = env('APP_URL');

        $token1 = AccessToken::latest('created_at')->first();

        if ($token1 !== null) {

            $access_token = $token1->access_token;
        } else {

            $token->getToken();

            $token1 = AccessToken::latest('created_at')->first();
            $access_token = $token1->access_token;
        }

        $subscription_non_renewing = Subscriptions::where('zoho_cust_id', '=', $partner->zoho_cust_id)->where('status', 'non_renewing')->first();

        if ($subscription_non_renewing) {

            $client = new \GuzzleHttp\Client();

            $plan_sub = Plans::where('plan_code', '=', $request->plan_code)->first();

            $options = [
                'headers' => [
                    'Authorization' => ('Zoho-oauthtoken ' . $access_token),
                    'X-com-zoho-subscriptions-organizationid' => env('ORGANIZATION_ID'),
                    'content-type: application/json'
                ],
                'json' => [
                    "subscription_id" => $subscription_non_renewing->subscription_id,
                    "plan" => [
                        "plan_code" => $plan_sub->plan_code,
                        "quantity" => 1,
                    ],
                    "auto_collect" => true,
                    "redirect_url" =>  "$app_url/thankyou-update",
                ]
            ];

            $res = $client->request(
                'POST',
                $update_url,
                $options
            );

            $response = (string) $res->getBody();
            $response = json_decode($response);

            $hostedpage = $response->hostedpage;

            $pay_link = $app_url . "/subscribe-custom-plan/" . $plan_sub->plan_id . "/" . $hostedpage->hostedpage_id . "/" . $partner->zoho_cust_id;


            $partner_email = $primary_user->email;

            $partner_name = $primary_user->first_name;

            $plan_name = $plan_sub->plan_name;

            $plan_price = $plan_sub->price;

            try {

                Mail::to($partner_email)->send(new CreateSubscription($partner_email, $partner_name, $plan_name, $plan_price, $pay_link));

                return redirect('/admin/view-partner/' . $partner->id . '/subscriptions')->with('success', 'Subscription Mail sent successfully!');
            } catch (\Exception $e) {

                \Log::error("Error sending subscription email: " . $e->getMessage());

                return redirect('/admin/view-partner/' . $partner->id . '/subscriptions')->with('fail', 'There was a problem sending the email. Please try again later.');
            }
        }

        try {
            $client = new \GuzzleHttp\Client();


            $plan_sub = Plans::where('plan_code', '=', $request->plan_code)->first();

            $options = [
                'headers' => [
                    'Authorization' => ('Zoho-oauthtoken ' . $access_token),
                    'X-com-zoho-subscriptions-organizationid' => env('ORGANIZATION_ID'),
                    'content-type: application/json'
                ],
                'json' => [
                    "customer_id" => $partner->zoho_cust_id,
                    "plan" => [
                        "plan_code" => $plan_sub->plan_code,
                        "quantity" => 1

                    ],
                    "redirect_url" => "$app_url/thankyou-create",
                ]
            ];

            $res = $client->request(
                'POST',
                $add_url,
                $options
            );

            $response = (string) $res->getBody();
            $response = json_decode($response);

            $hostedpage = $response->hostedpage;

            $pay_link = $app_url . "/subscribe-custom-plan/" . $plan_sub->plan_id . "/" . $hostedpage->hostedpage_id . "/" . $partner->zoho_cust_id;

            $partner_email = $primary_user->email;

            $partner_name = $primary_user->first_name;

            $plan_name = $plan_sub->plan_name;

            $plan_price = $plan_sub->price;

            try {

                Mail::to($partner_email)->send(new CreateSubscription($partner_email, $partner_name, $plan_name, $plan_price, $pay_link));

                return back()->with('success', 'Subscription Mail sent successfully!');
            } catch (\Exception $e) {

                \Log::error("Error sending subscription email: " . $e->getMessage());

                return back()->with('fail', 'There was a problem sending the email. Please try again later.');
            }
        } catch (GuzzleException $e) {

            $response = $e->getResponse()->getBody(true);
            $response = json_decode($response);

            if ($response->message === "You are not authorized to perform this operation") {
                $token->getToken();
                return redirect('/admin/support')->with('fail', 'Kindly Try Again');
            }
        }
    }
    public function switchPaymentMethod(Request $request)
    {
        $validatedData = $request->validate([
            'subs_id' => 'required|string',
            'payment_method_id' => 'required|string',
        ]);

        $responseBody = $this->updateSubscriptionAndPaymentMethod($validatedData);


        if (isset($responseBody['code']) && $responseBody['code'] === 0) {

            $subscription_id = $responseBody['subscription']['subscription_id'];


            $subscription = Subscriptions::where('subscription_id', '=', $subscription_id)->first();

            if (!$subscription) {
                return back()->with('fail', 'Subscription not found.');
            }

            $subscription->payment_method_id = $validatedData['payment_method_id'];

            $dd = $subscription->save();

            $message = $responseBody['message'] ?? 'Payment method switched successfully.';
            return back()->with('success', $message);
        }

        return back()->with('fail', 'Failed to switch payment method.');
    }

    public function updateSubscriptionAndPaymentMethod($data)
    {
        $token = AccessToken::latest('created_at')->first();

        if (!$token) {
            return ['fail' => 'Access token not found. Please check your configuration.'];
        }

        $access_token = $token->access_token;

        $switch_payment_method_url = "https://www.zohoapis.com/billing/v1/subscriptions/{$data['subs_id']}";

        $subscription = Subscriptions::where('subscription_id', $data['subs_id'])->first();
        $plan = Plans::where('plan_id', $subscription->plan_id)->first();
        $paymentMethod = PaymentMethod::where('payment_method_id', $data['payment_method_id'])->first();

        if (!$subscription || !$plan || !$paymentMethod) {
            return ['fail' => 'Subscription, Plan, or Payment Method not found.'];
        }

        $is_metered_billing = false;
        $trans_fee_enable = env('CARD_TRANS_FEE');
        $transaction_fee = 0;

        if ($paymentMethod->type === 'card' && $trans_fee_enable) {
            $transaction_fee = $this->calculateTransactionFee($plan);
        }

        $post_data = [];

        $post_data['card_id'] = '';
        $post_data['account_id'] = '';

        if ($paymentMethod->type == 'card') {

            $post_data['card_id'] = $data['payment_method_id'];
        } elseif ($paymentMethod->type == 'bank_account') {
            $post_data['card_id'] = $data['payment_method_id'];
        }

        $post_data = array_merge($post_data, [
            "plan" => [
                "plan_code" => $plan->plan_code,
                "quantity" => 1,
            ],
            "is_metered_billing" => $is_metered_billing,
            "redirect_url" => env('APP_URL') . '/thankyou-switch-payment-method',
        ]);

        if ($plan->is_cpc) {
            $post_data["is_metered_billing"] = true;
            $post_data["coupon_code"] = "cpc-initial-coupon";
        } else {

            // $post_data["addons"] = [
            //     [
            //         "addon_code" => "card-transaction-fee",
            //         "price" => $transaction_fee,
            //     ]
            // ];
        }

        $client = new \GuzzleHttp\Client();
        $options = [
            'headers' => [
                'Authorization' => 'Zoho-oauthtoken ' . $access_token,
                'X-com-zoho-subscriptions-organizationid' => env('ORGANIZATION_ID'),
                'Content-Type' => 'application/json',
            ],
            'json' => $post_data,
        ];

        $response = $client->put($switch_payment_method_url, $options);

        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }
    

    public function associateNewPaymentMethod()
    {
        $app_url = env('APP_URL');

        $ADD_PAYMENT_METHOD_URL = 'https://www.zohoapis.com/billing/v1/hostedpages/addpaymentmethod';

        $token = AccessToken::latest('created_at')->first();

        if (!$token) {
            return back()->with('fail', 'Access token not found. Please check your configuration.');
        }

        $access_token = $token->access_token;

        $id = Route::getCurrentRoute()->parameter('id');

        try {
            $client = new \GuzzleHttp\Client();

            $options = [
                'headers' => [
                    'Authorization' => 'Zoho-oauthtoken ' . $access_token,
                    'X-com-zoho-subscriptions-organizationid' => env('ORGANIZATION_ID'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'customer_id' => $id,
                    'redirect_url' => "$app_url/thankyou-new-payment-method",
                    'payment_gateways' => [
                        [
                            'payment_gateway' => 'stripe',
                        ],
                    ],
                ],
            ];

            $res = $client->request('POST', $ADD_PAYMENT_METHOD_URL, $options);

            $response = json_decode($res->getBody(), true);

            if (isset($response['hostedpage']['url'])) {

                return redirect($response['hostedpage']['url']);
            }

            return back()->with('fail', 'Unexpected response from Zoho API.');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle client exceptions (4xx errors).
            $response = json_decode($e->getResponse()->getBody()->getContents(), true);

            if (isset($response['message']) && $response['message'] === 'You are not authorized to perform this operation') {
                // Refresh the token and inform the user.
                $token->getToken(); // Assumes `getToken()` method refreshes the token.
                return back()->with('fail', 'Authorization error. Please try again.');
            }

            return back()->with('fail', $response['message'] ?? 'An error occurred while processing your request.');
        } catch (\Exception $e) {
            // Handle other exceptions.
            return back()->with('fail', 'An unexpected error occurred. Please try again.');
        }
    }


    public function updatePaymentMethod()
    {
        $app_url = env('APP_URL');

        $UPDATE_PAYMENT_METHOD_URL = env('UPDATE_PAYMENT_METHOD_URL');

        $token = AccessToken::latest('created_at')->first();

        $access_token = $token->access_token;

        $id = Route::getCurrentRoute()->id;

        try {

            $client = new \GuzzleHttp\Client();

            $options = [
                'headers' => [
                    'Authorization' => ('Zoho-oauthtoken ' . $access_token),
                    'X-com-zoho-subscriptions-organizationid' => env('ORGANIZATION_ID'),
                    'content-type: application/json'
                ],
                'json' => [
                    "subscription_id" => $id,
                    "auto_collect" => true,
                    "redirect_url" =>  "$app_url/thankyou-update",
                    "payment_gateways" => [
                        [
                            "payment_gateway" => "stripe"
                        ]
                    ]
                ]
            ];

            $res = $client->request(
                'POST',
                $UPDATE_PAYMENT_METHOD_URL,
                $options
            );

            $response = (string) $res->getBody();
            $response = json_decode($response);

            return redirect($response->hostedpage->url);
        } catch (GuzzleException $e) {

            $response = $e->getResponse()->getBody(true);
            $response = json_decode($response);

            if ($response->message === "You are not authorized to perform this operation") {

                $token->getToken();

                return back()->with('fail', 'Kindly Try Again!');
            }
        }
    }

    public function cancelSubscription(AccessToken $token, Subscriptions $subscriptions)
    {
        $token1 = AccessToken::latest('created_at')->first();

        if ($token1 !== null) {

            $access_token = $token1->access_token;
        } else {

            $token->getToken();

            $token1 = AccessToken::latest('created_at')->first();
            $access_token = $token1->access_token;

            return back()->with('fail', 'Kindly Try Again');
        }
        $zoho_cust_id = Route::getCurrentRoute()->id;

        $subscription_url = env('SUBSCRIPTION_URL');

        $subscription = Subscriptions::where('status', 'live')->where('zoho_cust_id', $zoho_cust_id)->first();

        $plan = Plans::where('plan_id', $subscription->plan_id)->first();

        $subscriptions->cancelSubscription($access_token, $subscription);

        $support = Support::where('request_type', 'Cancellation')->where('status', 'open')->where('zoho_cust_id', $zoho_cust_id)->first();

        $support->status = "Completed";
        $support->comments = "Unable to revoke";
        $support->save();

        $partner = Partner::where('zoho_cust_id', $zoho_cust_id)->first();

        if (!($plan->is_cpc)) {
            $budget_cap = $this->getOrCreateBudgetCap($partner);

            $budget_cap->click_limit = 0;
            $budget_cap->cost_limit = 0;
            $budget_cap->save();
        }

        return redirect('/admin/support');
    }

    private function getOrCreateBudgetCap($partner)
    {
        $budget_cap = BudgetCapSettings::where('partner_id', $partner->id)->first();

        if (!$budget_cap) {
            $budget_cap = new BudgetCapSettings();
            $budget_cap->partner_id = $partner->id;
        }

        return $budget_cap;
    }


    public function deletePaymentMethod(AccessToken $token)
    {
        $id = Route::getCurrentRoute()->id;
        $app_url = env('APP_URL');
        $partner_url = env('PARTNER_URL');
        $partner = Partner::where('zoho_cust_id', $id)->first();
        $payment_method = PaymentMethod::where('zoho_cust_id', $id)->first();
        $token1 = AccessToken::latest('created_at')->first();
        if ($token1 !== null) {
            $access_token = $token1->access_token;
        } else {
            $token->getToken();
            $token1 = AccessToken::latest('created_at')->first();
            $access_token = $token1->access_token;
            return back()->with('fail', 'Kindly Try Again');
        }

        $subscription = Subscriptions::where('zoho_cust_id', $id)->where('status', 'live')->first();

        if ($subscription) {
            return back()->with('fail', 'You cannot remove as you have associated subscription');
        }

        $client = new \GuzzleHttp\Client();

        try {
            $options = [
                'headers' => [
                    'Authorization' => ('Zoho-oauthtoken ' . $access_token),
                    'X-com-zoho-subscriptions-organizationid' => env('ORGANIZATION_ID'),
                ]
            ];


            $res = $client->request(
                'DELETE',
                $partner_url . '/' . $partner->zoho_cust_id . '/cards/' . $payment_method->payment_method_id,
                $options
            );

            $response = (string) $res->getBody();
            $response = json_decode($response);
            $payment_method->delete();
            return redirect('/admin/view-partner/' . $partner->id)->with('success', 'Payment method deleted successfully');
        } catch (GuzzleException $e) {

            $response = $e->getResponse()->getBody(true);
            $response = json_decode($response);

            if ($response->message === "You are not authorized to perform this operation") {

                $token->getToken();

                return redirect('/')->with('fail', 'Kindly Subscribe Again!');
            }
        }
    }

    public function updateMeteredBilling(Request $request, AccessToken $token)
    {

        $token1 = AccessToken::latest('created_at')->first();
        if ($token1 === null) {
            $token->getToken();
            $token1 = AccessToken::latest('created_at')->first();
        }
        $access_token = $token1->access_token;

        $subscription = Subscriptions::where('subscription_id', $request->subscription_id)->first();

        $subscription_url = env('UPDATE_FREE_SUBSCRIPTION_URL');


        $meteredBillingEnabled = $request->metered_billing ?? "1";

        if ($meteredBillingEnabled) {
            $url = $subscription_url . $request->subscription_id . "/meteredbilling/enable";
        } else {
            $url = $subscription_url . $request->subscription_id . "/meteredbilling/disable";
        }

        $client = new \GuzzleHttp\Client();

        try {
            $options = [
                'headers' => [
                    'Authorization' => ('Zoho-oauthtoken ' . $access_token),
                    'X-com-zoho-subscriptions-organizationid' => env('ORGANIZATION_ID'),
                ],
            ];

            $res = $client->request(
                'POST',
                $url,
                $options
            );

            $response = (string) $res->getBody();

            $response = json_decode($response);

            $subscription->is_metered_billing = $response->subscription->is_metered_billing;

            $subscription->save();

            return back()->with('success', 'Metered Billing Status has been changed successfully');
        } catch (GuzzleException $e) {

            $response = $e->getResponse()->getBody(true);
            $response = json_decode($response);

            if ($response->message === "You are not authorized to perform this operation") {
                $token->getToken();
                return redirect('/admin')->with('fail', 'Kindly Try To Add Again');
            }
        }
    }

    public function chargeSubscription(AccessToken $token, Request $request)
    {
        $zohoCustId = $request->partner_id;
        $partner = Partner::where('zoho_cust_id', $zohoCustId)->first();
        $plan = Plans::where('plan_code', $request->plan_code)->first();
        $paymentMethod = PaymentMethod::where('zoho_cust_id', $zohoCustId)->first();
        $trans_fee_enable = env('CARD_TRANS_FEE');
        $transaction_fee = 0;

        if ($paymentMethod->type === 'card' && $trans_fee_enable) {

            $transaction_fee = $this->calculateTransactionFee($plan);
        }

        $response = $this->getSubscriptionsByCustomer($zohoCustId, $token);

        $subscriptionsData = $response->getData();

        if ($subscriptionsData->subs_exists_already) {

            $partner->status = 'completed';

            $partner->save();

            return back()->with('fail', $subscriptionsData->msg);
        } else {

            if ($request->save) {

                return $this->handleSaveSubscription($request, $partner, $plan);
            } else {

                $this->handleSaveSubscription($request, $partner, $plan);

                return $this->handleNewSubscription($request, $partner, $plan, $token, $transaction_fee);
            }
        }
    }

    private function calculateTransactionFee($plan)
    {
        $transactionFeePercentage = (env('TRANSACTION_FEE_PERCENTAGE') / 100);
        $plan_price = $plan->price;
        $transFeeLevel1 = (($plan_price) + ($plan_price * ($transactionFeePercentage)));
        $transFee = ($transFeeLevel1 * $transactionFeePercentage) + 0.30;
        return $transFee;
    }


    private function handleSaveSubscription($request, $partner, $plan)
    {

        if ($this->partnerIdExists($request->advertiser_id) && $partner->isp_advertiser_id !== $request->advertiser_id) {

            return back()->with('fail', 'Partner with same advertiser ID already exists');
        }

        $partner->isp_advertiser_id = $request->advertiser_id;

        $partner->save();

        $budget_cap = BudgetCapSettings::where('partner_id', $partner->id)->first();

        if ($budget_cap === null) {

            $budget_cap = new BudgetCapSettings();
        }

        $budget_cap->click_limit = $request->cost_limit / $plan->price;

        if ($request->plan_type === "flat") {

            $budget_cap->click_limit = $request->click_limit;
        }
        $budget_cap->cost_limit = $request->cost_limit;
        $budget_cap->partner_id = $partner->id;
        $budget_cap->plan_type = $request->plan_type;
        $budget_cap->save();

        $company_info = ProviderData::where('zoho_cust_id', $request->partner_id)->first();

        if ($request->tune_link) {

            if ($company_info) {

                $tuneLinks = $request->tune_link;

                $tuneLinks = array_unique($tuneLinks);

                $company_info->tune_link = json_encode(array_values($tuneLinks));

                $company_info->save();
            } else {

                return back()->with('fail', 'Partner have not uploaded the company info');
            }
        }

        return back()->with('success', 'Changes are Saved Successfully');
    }

    private function getSubscriptionsByCustomer($customerId, $token)
    {
        $tokenRecord = AccessToken::latest('created_at')->first();

        if ($tokenRecord !== null) {

            $accessToken = $tokenRecord->access_token;
        } else {
            $token->getToken();
            $tokenRecord = AccessToken::latest('created_at')->first();
            $accessToken = $tokenRecord->access_token;
        }

        $organizationId = env('ORGANIZATION_ID');
        $url = 'https://www.zohoapis.com/billing/v1/subscriptions';

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
            'X-com-zoho-subscriptions-organizationid' => $organizationId,
        ])->get($url, [
            'customer_id' => $customerId,
        ]);

        if ($response->successful()) {

            $data = $response->json();

            $subscriptions = $data['subscriptions'] ?? [];
            $subsExistsAlready = !empty($subscriptions);
            $subscriptionCount = count($subscriptions);

            $msg = 'Subscription already exists for this customer - ';

            foreach ($subscriptions as $subscription) {

                $msg .= $subscription['subscription_number'] . '';
            }
            $msg = rtrim($msg, ', ');

            return response()->json([
                'subs_exists_already' => $subsExistsAlready,
                'subscription_count' => $subscriptionCount,
                'subscriptions' => $subscriptions,
                'msg' => $msg
            ]);
        }

        return response()->json([
            'error' => 'Failed to fetch subscriptions',
            'details' => $response->body(),
        ], $response->status());
    }


    private function handleNewSubscription($request, $partner, $plan, $token, $transaction_fee)
    {
        $add_url = env('NEW_SUBSCRIPTION_URL');
        $app_url = env('APP_URL');

        $token1 = AccessToken::latest('created_at')->first();

        if ($token1 !== null) {
            $access_token = $token1->access_token;
        } else {
            $token->getToken();
            $token1 = AccessToken::latest('created_at')->first();
            $access_token = $token1->access_token;
        }

        $is_metered_billing = false;
        $post_data = [
            "customer_id" => $request->partner_id,
            "plan" => [
                "plan_code" => $plan->plan_code,
                "quantity" => 1,
            ],
            "is_metered_billing" => $is_metered_billing,
            "redirect_url" => "$app_url/thankyou-charge-subscription",
        ];

        if (strpos($plan->plan_code, 'cpc') !== false) {
            $post_data["is_metered_billing"] = true;
            $post_data["coupon_code"] = "cpc-initial-coupon";
        } else {
            // $post_data["addons"] = [
            //     [
            //         "addon_code" => "card-transaction-fee",
            //         "price" => $transaction_fee,
            //     ]
            // ];
        }

        $client = new \GuzzleHttp\Client();

        $options = [
            'headers' => [
                'Authorization' => 'Zoho-oauthtoken ' . $access_token,
                'X-com-zoho-subscriptions-organizationid' => env('ORGANIZATION_ID'),
                'Content-Type' => 'application/json',
            ],
            'json' => $post_data,
        ];


        $res = $client->request('POST', $add_url, $options);

        $response = json_decode($res->getBody(), true);

        return redirect($response['hostedpage']['url']);
    }

    private function partnerIdExists($isp_advertiser_id)
    {
        return Partner::where('isp_advertiser_id', $isp_advertiser_id)
            ->exists();
    }
}
