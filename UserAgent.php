<?php

namespace mrssoft\useragent;

/**
 * Identifies the platform, browser, robot, or mobile device of the browsing agent
 *
 * @author Melnikov R.S.
 *
 * Created on the basis of the library User_agent from framework CodeIgniter
 * http://codeigniter.com/user_guide/libraries/user_agent.html
 */
class UserAgent
{
    private $is_browser = false;
    private $is_robot = false;
    private $is_mobile = false;

    private $platforms = [];
    private $browsers = [];
    private $mobiles = [];
    private $robots = [];

    public $agent;
    public $platform = '';
    public $browser = '';
    public $version = '';
    public $mobile = '';
    public $robot = '';
    public $info = '';

    public function __construct()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $this->agent = trim($_SERVER['HTTP_USER_AGENT']);
        }

        if ($this->agent !== null) {

            $data = include __DIR__ . '/data.php';

            $this->platforms = $data['platforms'];
            $this->browsers = $data['browsers'];
            $this->mobiles = $data['mobiles'];
            $this->robots = $data['robots'];

            $this->compile();
        }
    }

    protected function compile()
    {
        $this->setPlatform();

        foreach (['setRobot', 'setBrowser', 'setMobile'] as $function) {
            if ($this->$function() === true) {
                break;
            }
        }

        $this->info = $this->browser . ' ' . $this->version . ', ' . $this->platform;
    }

    protected function setPlatform()
    {
        if (is_array($this->platforms) && count($this->platforms) > 0) {
            foreach ($this->platforms as $key => $val) {
                if (preg_match('|' . preg_quote($key, '|') . '|i', $this->agent)) {
                    $this->platform = $val;

                    return true;
                }
            }
        }

        $this->platform = 'Unknown Platform';

        return false;
    }

    protected function setBrowser()
    {
        if (is_array($this->browsers) && count($this->browsers) > 0) {
            foreach ($this->browsers as $key => $val) {
                if (preg_match('|' . $key . '.*?([0-9\.]+)|i', $this->agent, $match)) {
                    $this->is_browser = true;
                    $this->version = $match[1];
                    $this->browser = $val;
                    $this->setMobile();

                    return true;
                }
            }
        }

        return false;
    }

    protected function setRobot()
    {
        if (is_array($this->robots) && count($this->robots) > 0) {
            foreach ($this->robots as $key => $val) {
                if (preg_match('|' . preg_quote($key, '|') . '|i', $this->agent)) {
                    $this->is_robot = true;
                    $this->robot = $val;
                    $this->setMobile();

                    return true;
                }
            }
        }

        return false;
    }

    protected function setMobile()
    {
        if (is_array($this->mobiles) && count($this->mobiles) > 0) {
            foreach ($this->mobiles as $key => $val) {
                if (false !== stripos($this->agent, $key)) {
                    $this->is_mobile = true;
                    $this->mobile = $val;

                    return true;
                }
            }
        }

        return false;
    }

    public function isBrowser($key = null)
    {
        if (!$this->is_browser) {
            return false;
        }

        if ($key === null) {
            return true;
        }

        return (isset($this->browsers[$key]) && $this->browser === $this->browsers[$key]);
    }

    public function isRobot($key = null)
    {
        if (!$this->is_robot) {
            return false;
        }

        if ($key === null) {
            return true;
        }

        return (isset($this->robots[$key]) && $this->robot === $this->robots[$key]);
    }

    public function isMobile($key = null)
    {
        if (!$this->is_mobile) {
            return false;
        }

        if ($key === null) {
            return true;
        }

        return (isset($this->mobiles[$key]) && $this->mobile === $this->mobiles[$key]);
    }

    public function parse($string)
    {
        $this->is_browser = false;
        $this->is_robot = false;
        $this->is_mobile = false;
        $this->browser = '';
        $this->version = '';
        $this->mobile = '';
        $this->robot = '';

        $this->agent = $string;

        if (!empty($string)) {
            $this->compile();
        }
    }
}