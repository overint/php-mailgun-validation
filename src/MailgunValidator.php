<?php namespace overint;


class MailgunValidator
{
    private $apiKey;

    function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    private function queryMailgun($email)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.mailgun.net/v3/address/validate?api_key=" . $this->apiKey .  "&address=" . $email,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_TIMEOUT => 30,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new Exception('Curl Error: ' . $err);
        } else {
            return json_decode($response);
        }
    }

    public function validate($email)
    {
        $ret = $this->queryMailgun($email);
        return $ret->is_valid;
    }

    public function validateExtended($email)
    {
        return $this->queryMailgun($email);
    }


}