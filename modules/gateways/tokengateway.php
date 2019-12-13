<?php
/**
 * WHMCS Sample Tokenisation Gateway Module
 *
 * This sample module demonstrates how to create a merchant gateway module
 * that accepts input of pay method data locally and then exchanges it for
 * a token that is stored locally for future billing attempts.
 *
 * As with all modules, within the module itself, all functions must be
 * prefixed with the module filename, followed by an underscore, and then
 * the function name. For this example file, the filename is "tokengateway"
 * and therefore all functions begin "tokengateway_".
 *
 * For more information, please refer to the online documentation.
 *
 * @see https://developers.whmcs.com/payment-gateways/
 *
 * @copyright Copyright (c) WHMCS Limited 2019
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related capabilities and
 * settings.
 *
 * @see https://developers.whmcs.com/payment-gateways/meta-data-params/
 *
 * @return array
 */
function tokengateway_MetaData()
{
    return [
        'DisplayName' => 'Sample Tokenisation Gateway Module',
        'APIVersion' => '1.1', // Use API Version 1.1
    ];
}

/**
 * Define gateway configuration options.
 *
 * The fields you define here determine the configuration options that are
 * presented to administrator users when activating and configuring your
 * payment gateway module for use.
 *
 * Supported field types include:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * For more information, please refer to the online documentation.
 *
 * @see https://developers.whmcs.com/payment-gateways/configuration/
 *
 * @return array
 */
function tokengateway_config()
{
    return [
        // the friendly display name for a payment gateway should be
        // defined here for backwards compatibility
        'FriendlyName' => [
            'Type' => 'System',
            'Value' => 'Sample Tokenisation Gateway Module',
        ],
        // a text field type allows for single line text input
        'apiUsername' => [
            'FriendlyName' => 'API Username',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your API Username here',
        ],
        // a password field type allows for masked text input
        'apiPassword' => [
            'FriendlyName' => 'API Password',
            'Type' => 'password',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your API Password here',
        ],
        // the yesno field type displays a single checkbox option
        'testMode' => [
            'FriendlyName' => 'Test Mode',
            'Type' => 'yesno',
            'Description' => 'Tick to enable test mode',
        ],
    ];
}

/**
 * Store payment details.
 *
 * Called when a new pay method is added or an existing pay method is
 * requested to be updated or deleted.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/tokenised-remote-storage/
 *
 * @return array
 */
function tokengateway_storeremote($params)
{
    // Gateway Configuration Parameters
    $apiUsername = $params['apiUsername'];
    $apiPassword = $params['apiPassword'];
    $testMode = $params['testMode'];

    // Store Remote Parameters
    $action = $params['action']; // One of either 'create', 'update' or 'delete'
    $remoteGatewayToken = $params['gatewayid'];
    $cardType = $params['cardtype']; // Card Type
    $cardNumber = $params['cardnum']; // Credit Card Number
    $cardExpiry = $params['cardexp']; // Card Expiry Date (format: mmyy)
    $cardStart = $params['cardstart']; // Card Start Date (format: mmyy)
    $cardIssueNum = $params['cardissuenum']; // Card Issue Number
    $cardCvv = $params['cccvv']; // Card Verification Value

    // Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];

    switch ($action) {
        case 'create':
            // Invoked when a new card is added.
            $postfields = [
                'card_type' => $cardType,
                'card_number' => $cardNumber,
                'card_expiry_month' => substr($cardExpiry, 0, 2),
                'card_expiry_year' => substr($cardExpiry, 2, 2),
                'card_cvv' => $cardCvv,
                'card_holder_name' => $firstname . ' ' . $lastname,
                'card_holder_address1' => $address1,
                'card_holder_address2' => $address2,
                'card_holder_city' => $city,
                'card_holder_state' => $state,
                'card_holder_zip' => $postcode,
                'card_holder_country' => $country,
            ];

            // Perform API call to store the provided card details and generate a token.
            // Sample response data:
            $response = [
                'success' => true,
                'token' => 'abc1111111111',
            ];

            if ($response['success']) {
                return [
                    // 'success' if successful, otherwise 'error' for failure
                    'status' => 'success',
                    // Data to be recorded in the gateway log - can be a string or array
                    'rawdata' => $response,
                    // The token that should be stored in WHMCS for recurring payments
                    'gatewayid' => $response['token'],
                ];
            }

            return [
                // 'success' if successful, otherwise 'error' for failure
                'status' => 'error',
                // Data to be recorded in the gateway log - can be a string or array
                'rawdata' => $response,
            ];

            break;
        case 'update':
            // Invoked when an existing card is updated.
            $postfields = [
                'token' => $remoteGatewayToken,
                'card_type' => $cardType,
                'card_number' => $cardNumber,
                'card_expiry_month' => substr($cardExpiry, 0, 2),
                'card_expiry_year' => substr($cardExpiry, 2, 2),
                'card_cvv' => $cardCvv,
                'card_holder_name' => $firstname . ' ' . $lastname,
                'card_holder_address1' => $address1,
                'card_holder_address2' => $address2,
                'card_holder_city' => $city,
                'card_holder_state' => $state,
                'card_holder_zip' => $postcode,
                'card_holder_country' => $country,
            ];

            // Perform API call to update the requested token.
            // Sample response data:
            $response = [
                'success' => true,
                'token' => 'abc2222222222',
            ];

            if ($response['success']) {
                return [
                    // 'success' if successful, otherwise 'error' for failure
                    'status' => 'success',
                    // Data to be recorded in the gateway log - can be a string or array
                    'rawdata' => $response,
                    // The token to be stored if it has changed
                    'gatewayid' => $response['token'],
                ];
            }

            return [
                // 'success' if successful, otherwise 'error' for failure
                'status' => 'error',
                // Data to be recorded in the gateway log - can be a string or array
                'rawdata' => $response,
            ];

            break;
        case 'delete':
            // Invoked when an existing card is requested to be deleted.
            $postfields = [
                'token' => $remoteGatewayToken,
            ];

            // Perform API call to delete the requested token.
            // Sample response data:
            $response = [
                'success' => true,
            ];

            if ($response['success']) {
                return [
                    // 'success' if successful, otherwise 'error' for failure
                    'status' => 'success',
                    // Data to be recorded in the gateway log - can be a string or array
                    'rawdata' => $response,
                ];
            }

            return [
                // 'success' if successful, otherwise 'declined', 'error' for failure
                'status' => 'error',
                // Data to be recorded in the gateway log - can be a string or array
                'rawdata' => $response,
            ];

            break;
    }
}

/**
 * Capture payment.
 *
 * Called when a payment is requested to be processed and captured.
 *
 * This function may receive pay method data instead of a token when a
 * payment is attempted using a pay method that was originally created and
 * stored locally within WHMCS using something other than this token
 * module, and therefore it should be able to accomodate captures based
 * both on a token as well as a pay method data.
 *
 * The CVV number parameter will only be present for card holder present
 * transactions. Automated recurring capture attempts will not provide it.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/merchant-gateway/
 *
 * @return array
 */
function tokengateway_capture($params)
{
    // Gateway Configuration Parameters
    $apiUsername = $params['apiUsername'];
    $apiPassword = $params['apiPassword'];
    $testMode = $params['testMode'];

    // Capture Parameters
    $remoteGatewayToken = $params['gatewayid'];
    $cardType = $params['cardtype']; // Card Type
    $cardNumber = $params['cardnum']; // Credit Card Number
    $cardExpiry = $params['cardexp']; // Card Expiry Date (format: mmyy)
    $cardStart = $params['cardstart']; // Card Start Date (format: mmyy)
    $cardIssueNum = $params['cardissuenum']; // Card Issue Number
    $cardCvv = $params['cccvv']; // Card Verification Value

    // Invoice Parameters
    $invoiceId = $params['invoiceid'];
    $description = $params['description'];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];

    // Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $returnUrl = $params['returnurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    if (!$remoteGatewayToken) {
        // If there is no token yet, it indicates this capture is being
        // attempted using an existing locally stored card. Create a new
        // token and then attempt capture.
        $postfields = [
            'card_type' => $cardType,
            'card_number' => $cardNumber,
            'card_expiry_month' => substr($cardExpiry, 0, 2),
            'card_expiry_year' => substr($cardExpiry, 2, 2),
            'card_cvv' => $cardCvv,
            'card_holder_name' => $firstname . ' ' . $lastname,
            'card_holder_address1' => $address1,
            'card_holder_address2' => $address2,
            'card_holder_city' => $city,
            'card_holder_state' => $state,
            'card_holder_zip' => $postcode,
            'card_holder_country' => $country,
        ];

        // Perform API call to store the provided card details and generate a token.
        // Sample response data:
        $response = [
            'success' => true,
            'token' => 'abc1111111111',
        ];

        if ($response['success']) {
            $remoteGatewayToken = $response['token'];
        } else {
            return [
                // 'success' if successful, otherwise 'error' for failure
                'status' => 'error',
                // Data to be recorded in the gateway log - can be a string or array
                'rawdata' => $response,
            ];
        }
    }

    $postfields = [
        'token' => $remoteGatewayToken,
        'cvv' => $cardCvv,
        'invoice_number' => $invoiceId,
        'amount' => $amount,
        'currency' => $currencyCode,
    ];

    // Perform API call to initiate capture.
    // Sample response data:
    $response = [
        'success' => true,
        'transaction_id' => 'ABC123',
        'fee' => '1.23',
        'token' => 'abc3333333333',
    ];

    if ($response['success']) {
        return [
            // 'success' if successful, otherwise 'declined', 'error' for failure
            'status' => 'success',
            // The unique transaction id for the payment
            'transid' => $response['transaction_id'],
            // Optional fee amount for the transaction
            'fee' => $response['fee'],
            // Return only if the token has updated or changed
            'gatewayid' => $response['token'],
            // Data to be recorded in the gateway log - can be a string or array
            'rawdata' => $response,
        ];
    }

    return [
        // 'success' if successful, otherwise 'declined', 'error' for failure
        'status' => 'declined',
        // For declines, a decline reason can optionally be returned
        'declinereason' => $response['decline_reason'],
        // Data to be recorded in the gateway log - can be a string or array
        'rawdata' => $response,
    ];
}

/**
 * Refund transaction.
 *
 * Called when a refund is requested for a previously successful transaction.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/refunds/
 *
 * @return array
 */
function tokengateway_refund($params)
{
    // Gateway Configuration Parameters
    $apiUsername = $params['apiUsername'];
    $apiPassword = $params['apiPassword'];
    $testMode = $params['testMode'];

    // Refund Parameters
    $transactionIdToRefund = $params['transid'];
    $refundAmount = $params['amount'];
    $currencyCode = $params['currency'];
    $remoteGatewayToken = $params['gatewayid'];

    // Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    // Perform API call to initiate a refund.
    // Sample response data:
    $response = [
        'success' => true,
        'transaction_id' => 'ABC123',
        'fee' => '1.23',
    ];

    return [
        // 'success' if successful, otherwise 'declined', 'error' for failure
        'status' => 'success',
        // Data to be recorded in the gateway log - can be a string or array
        'rawdata' => $response,
        // Unique Transaction ID for the refund transaction
        'transid' => $response['transaction_id'],
        // Optional fee amount for the fee value refunded
        'fee' => $response['fee'],
    ];
}

/**
 * Admin Status Message.
 *
 * Called when an invoice is viewed in the admin area.
 *
 * @param array $params Payment Gateway Module Parameters.
 *
 * @return array
 */
function tokengateway_adminstatusmsg($params)
{
    // Gateway Configuration Parameters
    $apiUsername = $params['apiUsername'];
    $apiPassword = $params['apiPassword'];
    $testMode = $params['testMode'];

    // Invoice Parameters
    $remoteGatewayToken = $params['gatewayid'];
    // The id of the invoice being viewed
    $invoiceId = $params['id'];
    // The id of the user the invoice belongs to
    $userId = $params['userid'];
    // The creation date of the invoice
    $date = $params['date'];
    // The due date of the invoice
    $dueDate = $params['duedate'];
    // The status of the invoice
    $status = $params['status'];

    if ($remoteGatewayToken) {
        return [
            'type' => 'info',
            'title' => 'Token Gateway Profile',
            'msg' => 'This customer has a Remote Token storing their card'
                . ' details for automated recurring billing with ID ' . $remoteGatewayToken,
        ];
    }
}
