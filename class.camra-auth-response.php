<?php

require_once(plugin_dir_path(__FILE__) . 'camra-auth-utilities.php');

/**
 * CAMRA authentication class.
 *
 * @author @tomblakemore Tom Blakemore
 */
class CAMRAAuth_Response
{
    /**
     * The authenticated membership number.
     *
     * @access protected
     * @var string
     */
    protected $memno;

    /**
     * The message or outcomes from trying to authenticate a member.
     *
     * @access protected
     * @var string
     */
    protected $outcome = '';

    /**
     * Call on plugin activation to add the database options.
     *
     * @return void
     * @static
     */
    public static function activate()
    {
        add_option('camra_auth', [
            'branch_code' => '',
            'key' => '',
            'timeout' => 5,
            'url' => 'https://api.camra.org.uk/index.php/api/branch/auth_1/format/json',
            'ssl_trust_certs' => '',
            'ssl_verifypeer' => false
        ]);
    }

    /**
     * Getter for whether the outcome is authentic.
     *
     * @return bool
     */
    public function authentic()
    {
        return $this->outcome === 'Authentic';
    }

    /**
     * Attempt to authenticate a user using a membership number and password.
     * Requires the url and key to be defined as options in the database.
     *
     * @param string $memno
     * @param string $pass
     * @return CAMRAAuth_Response
     * @static
     */
    public static function authenticate($memno, $pass)
    {
        $response = new CAMRAAuth_Response;
        $response->memno = $memno;

        try {

            if (!($options = get_option('camra_auth'))) {
                throw new Exception('Missing CAMRA authentication options');
            }

            if (!($key = array_get($options, 'key'))) {
                throw new Exception('Missing service key');
            }

            if (!($url = array_get($options, 'url'))) {
                throw new Exception('Missing authentication URL');
            }

            $params = [
                'KEY' => $key,
                'memno' => $memno,
                'pass' => $pass,
            ];

            $header = [
                'Content-Type: application/x-www-form-urlencoded',
                'Content-Length: ' . strlen(http_build_query($params))
            ];

            if (($blogname = get_option('blogname'))) {
                $header[] = 'X-Application: ' . $blogname;
            }

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);

            if (($timeout = array_get($options, 'timeout'))) {
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout * 1000); // Needs to be in milliseconds
            }

            // Set whether the SSL certificate on CAMRA's server should be 
            // verified - the default is normally true as of cURL 7.10 but we 
            // allow the admin user to choose whether to enable this or not.
            if (($ssl_verifypeer = array_get($options, 'ssl_verifypeer', true))) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl_verifypeer);
            }

            if ($ssl_verifypeer && ($cainfo = array_get($options, 'ssl_trust_certs'))) {
                curl_setopt($ch, CURLOPT_CAINFO, $cainfo);
            }

            $curl_response = curl_exec($ch);
            $curl_info = curl_getinfo($ch);
            curl_close($ch);

            if ($curl_info['http_code'] !== 200) {
                throw new Exception('Invalid response code');
            }

            if ($curl_response === false || mb_strlen($curl_response) == 0) {
                throw new Exception('Empty service response');
            }

            $payload = json_decode(substr($curl_response, $curl_info['header_size']), true);

            if (($message = array_get($payload, 'Error'))) {
                throw new Exception($message);
            }

            $branch_code = array_get($options, 'branch_code');

            if (!empty($branch_code) && $branch_code !== array_get($payload, 'Branch')) {
                throw new Exception('Invalid branch');   
            }

            if (!($memno = array_get($payload, 'MembershipNumber'))) {
                throw new Exception('Missing membership number from service response');
            }

            $response->memno = $memno;
            $response->outcome = 'Authentic';
        }

        catch (Exception $e) {
            $response->outcome = $e->getMessage();
        }

        return $response;
    }

    /**
     * Call on plugin deactivation to remmove the database options.
     *
     * @return void
     * @static
     */
    public static function deactivate()
    {
        delete_option('camra_auth');
    }

    /**
     * Getter for the authentic membership number.
     *
     * @return string
     */
    public function memno()
    {
        return $this->memno;
    }
}
