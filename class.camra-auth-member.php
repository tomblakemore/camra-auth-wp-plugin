<?php

require_once(plugin_dir_path(__FILE__) . 'camra-auth-utilities.php');

/**
 * Class to represent and login a CAMRA member.
 *
 * @author @tomblakemore Tom Blakemore
 */
class CAMRAAuth_Member
{
    /**
     * A check to whether this member is authentic or not.
     *
     * @access protected
     * @var bool
     */
    protected $authentic;

    /**
     * The members' membership number.
     *
     * @access protected
     * @var string
     */
    protected $memno;

    /**
     * Intialise a new instance of the class.
     *
     * @param string $memno
     * @param string $password
     * @return void
     */
    public function __construct($memno = '', $authentic = false)
    {
        $this->authentic = $authentic;
        $this->memno = $memno;
    }

    /**
     * Getter for whether the outcome is authentic.
     *
     * @return bool
     */
    public function authentic()
    {
        return $this->authentic === true;
    }

    /**
     * Create a new member by checking for credentials in the session.
     *
     * @return CAMRAAuth_Member
     * @static
     */
    public static function init()
    {
        $authentic = false;

        if (($memno = array_get($_SESSION, 'camra_auth_memno', ''))) {
            $authentic = true;
        }

        return new static($memno, $authentic);
    }

    /**
     * Create a new member from a membership number and password.
     *
     * @param string $memno
     * @param string $password
     * @return CAMRAAuth_Member
     * @static
     */
    public static function login($memno, $password)
    {
        $response = CAMRAAuth_Response::authenticate($memno, $password);

        return new static($response->memno(), $response->authentic());
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
