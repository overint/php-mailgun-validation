<?php namespace overint;

/**
 * Validate email address with Mailgun's validation service (Syntax checks, DNS validation, MX validation)
 */
class MailgunValidator
{
    /** @var string Mailgun API endpoint URL */
    const API_ENDPOINT = 'https://api.mailgun.net/v3/address/validate';

    /** @var string Mailgun email validation API key  */
    private $apiKey;

    /**
     * MailgunValidator constructor.
     * @param string $apiKey Mailgun email validation API key
     */
    function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }


    /**
     * Use curl to send the validation request to Mailgun
     * @param string $email
     * @return array
     */
    private function queryMailgun($email)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::API_ENDPOINT . "?api_key=" . $this->apiKey .  "&address=" . urlencode($email) . '&mailbox_verification=true',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_TIMEOUT => 30,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new \Exception('Curl Error: ' . $err);
        } else {
            return json_decode($response);
        }
    }


    /**
     * Validate an email address and return a boolean indicating validity
     * @param string $email Email adddress to be validated
     * @return boolean
     */
    public function validate($email)
    {
        $ret = $this->queryMailgun($email);
        return property_exists($ret, 'is_valid') && $ret->is_valid === true && $ret->mailbox_verification !== false;
    }

    /**
     * Validate an email address and return a detailed infomation from Mailgun
     * @param string $email Email adddress to be validated
     * @return array
     */
    public function validateExtended($email)
    {
        return $this->queryMailgun($email);
    }


}
