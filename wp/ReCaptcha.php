<?php namespace wp;
final class ReCaptcha
{
    protected static $instances;
    protected $_visible = false;
    protected $_keyPublic = "";
    protected $_keyPrivate = "";
    protected $_widget = "";

    protected function __construct()
    {
        $this->_visible = get_option(SettingsSite::RECAPTCHA_ENABLED);
        $this->_keyPublic = get_option(SettingsSite::RECAPTCHA_KEY_PUBLIC);
        $this->_keyPrivate = get_option(SettingsSite::RECAPTCHA_KEY_PRIVATE);
        $this->_visible = ($this->_visible && !empty($this->_keyPublic) && !empty($this->_keyPrivate));
        if ($this->_visible) {
            $widgetId = "reCaptchaWidget";
            $this->_widget = sprintf('<script type="text/javascript">function renderReCaptcha(){grecaptcha.render("%2$s",{"sitekey":"%1$s"});}</script>
            <script src="https://www.google.com/recaptcha/api.js?onload=renderReCaptcha&render=explicit" async defer></script>
            <div id="%2$s" class="g-recaptcha" data-sitekey="%1$s"></div>',
                $this->_keyPublic, $widgetId);
        }
    }

    final public static function i()
    {
        if (!isset(static::$instances)) {
            static::$instances = new static();
        }

        return static::$instances;
    }

    public function isVisible()
    {
        return $this->_visible;
    }

    public function keyPublic()
    {
        return $this->_keyPublic;
    }

    public function widget()
    {
        return $this->_widget;
    }

    /** Calls an HTTP POST function to verify if the user's is real human*/
    function validate()
    {
        $options = [
            'http' => [
                'method' => 'POST',
                'content' => http_build_query([
                    'secret' => $this->_keyPrivate,
                    'response' => $_POST["g-recaptcha-response"],
                    'remoteip' => $_SERVER["REMOTE_ADDR"],
                ]),
            ],
        ];
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $context = stream_context_create($options);
        $verify = file_get_contents($url, false, $context);
        $captcha_success = json_decode($verify);
        if ($captcha_success->success == false) {
            echo json_encode(['success' => false, 'message' => ""]);
            die;
        }
    }
}