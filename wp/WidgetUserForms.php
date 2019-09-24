<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:32 PM
 */

namespace wp;
final class WidgetUserForms extends WidgetDialogBase
{
    const USER_NAME = "user_login";
    const USER_PASSWORD = "user_password";
    const USER_PASS = "user_pass";
    const USER_EMAIL = "user_email";
    const USER_FIRST_NAME = "first_name";
    const USER_DISPLAY_NAME = "display_name";
    const USER_NICE_NAME = "user_nicename";
    const USER_LAST_NAME = "last_name";
    const USER_REMEMBER = "remember";
    const AJAX_LOGIN = "ajaxLogin";
    const AJAX_REGISTER = "ajaxRegister";
    const AJAX_FORGOT = "ajaxForgot";
    const REDIRECT_LINK = "_wp_http_referer";

    function __construct()
    {
        parent::__construct(__('Login Form', WpApp::TEXT_DOMAIN), __('This widget displays a Login Form.', WpApp::TEXT_DOMAIN));
        //TODO Set this handler in concordance with Widget Configuration
        WPActions::addAjaxHandler([$this, self::AJAX_LOGIN]);
        WPActions::addAjaxHandler([$this, self::AJAX_REGISTER]);
        WPActions::addAjaxHandler([$this, self::AJAX_FORGOT]);
        $this->iconModalToggle = "fa-sign-in";
    }

    function enqueueScriptsTheme()
    {
        $uriToDirLibs = UtilsWp::getUriToLibsDir(__FILE__);
        wp_enqueue_script('WidgetUserForms', "{$uriToDirLibs}/WidgetUserForms.js", ['knockout'],
            false, true);
        parent::enqueueScriptsTheme();
    }


    function getResultContent($message, $valid = false, $redirectLink = "")
    {
        return json_encode([
            'message' => $message,
            'success' => $valid,
            'redirect' => $redirectLink
        ]);
    }

    function sendMailToAdmin(\WP_User $user)
    {
        $adminEmailAddress = get_option(WPOptions::ADMIN_EMAIL);
        if (is_email($adminEmailAddress)) {
            $siteName = WPOptions::getSiteName();
            $messageSubject = sprintf(__('New user registration on your site %s:', WpApp::TEXT_DOMAIN), $siteName);
            $textUserName = sprintf(__('Username: %s', WpApp::TEXT_DOMAIN), $user->user_login);
            $textUserEmail = sprintf(__('Email: %s', WpApp::TEXT_DOMAIN), $user->user_email);
            $message = "$messageSubject<br>$textUserName<br>$textUserEmail";
            wp_mail($adminEmailAddress, $messageSubject, $message, self::getHeaderContentTypeHtml());
        }
    }

    function sendMailAboutNewUser(\WP_User $user)
    {
        /** Email to Registered User*/
        if (is_email($user->user_email)) {
            $siteName = WPOptions::getSiteName();
            $messageSubject = sprintf(__('Welcome to %s', WpApp::TEXT_DOMAIN), $siteName);
            $textUserName = sprintf(__('Your username is: %s', WpApp::TEXT_DOMAIN), "<strong>$user->user_login</strong>");
            $textUserPassword = sprintf(__('Your password is: %s', WpApp::TEXT_DOMAIN), "<strong>$user->user_pass</strong>");
            $textUserInfo = __('Your User Account was sent to the site administrator for approval', WpApp::TEXT_DOMAIN);
            $message =  "$messageSubject<br> $textUserName<br> $textUserPassword<br> $textUserInfo";
            wp_mail($user->user_email, $messageSubject, $message, self::getHeaderContentTypeHtml());
        }

    }

    static function getHeaderContentTypeHtml()
    {
        return ['Content-Type: text/html; charset=UTF-8'];
    }

    function getFloatingUserMenu()
    {
        //TODO Check FORCE_SSL_ADMIN if is defined then redirect to SSL Page
        $linkOfRedirect = add_query_arg('_', false);
        $urlLogout = wp_logout_url($linkOfRedirect);
        $authorId = get_current_user_id();
        $author = get_userdata($authorId);
        $authorAvatar = get_avatar($authorId, 32, "", "", ["class" => "media-object img-circle"]);
        $authorDisplayName = $author->display_name;
        $urlAuthorPage = get_author_posts_url($authorId);
        $urlAuthorPropertyAdd = admin_url('post-new.php?post_type=property');
        $urlAuthorEditProfile = admin_url('profile.php');
        $textMyPage = __('My Page', WpApp::TEXT_DOMAIN);
        $textAddProperty = __('Add Property', WpApp::TEXT_DOMAIN);
        $textEditProfile = __('Edit Profile', WpApp::TEXT_DOMAIN);
        $textLogOut = __('Logout', WpApp::TEXT_DOMAIN);
        return "<div class='usermenu btn-group dropup'>
        <figure class='btn btn-primary dropdown-toggle clearfix' data-toggle='dropdown' aria-haspopup='true'
                aria-expanded='false'>{$authorAvatar}<figcaption>{$authorDisplayName}</figcaption>
        </figure>
        <ul class='dropdown-menu dropdown-menu-right'>
            <li><a href='{$urlAuthorPage}'><span>{$textMyPage}</span></a></li>
            <li><a href='{$urlAuthorPropertyAdd}'><span>{$textAddProperty}</span></a></li>
            <li><a href='{$urlAuthorEditProfile}'><span>{$textEditProfile}</span></a></li>
            <li><a href='{$urlLogout}'><span>{$textLogOut}</span></a></li>
        </ul></div>";
    }

    function generateUserName()
    {
        $authors = get_users([
            QueryUsers::ROLE => WPUserRoles::AUTHOR,
            QueryUsers::ORDER_BY => 'registered',
            QueryUsers::ORDER => WPOrder::DESC,
            QueryUsers::NUMBER => 1,
        ]);
        $userName = "User";
        $lastRegisteredAuthor = $authors[0]; // the first user from the list
        if (isset($lastRegisteredAuthor)) {
            $userName .= $lastRegisteredAuthor->ID++;
        } else {
            $users = get_users([
                QueryUsers::ORDER_BY => WPOrderBy::REGISTERED,
                QueryUsers::ORDER => WPOrder::DESC,
                QueryUsers::NUMBER => 1,
            ]);
            $lastRegisteredAuthor = $users[0];
            $userName .= $lastRegisteredAuthor->ID++;
        }

        return $userName;
    }

    /** Register */
    function ajaxRegister()
    {
        $result = json_encode(['success' => false]);
        if (check_ajax_referer(self::AJAX_REGISTER, self::AJAX_REGISTER) && isset($_POST[self::USER_EMAIL])) {
            $userdata = [];
            if (isset($_POST[self::USER_FIRST_NAME])) {
                $userdata[self::USER_FIRST_NAME] = sanitize_text_field($_POST[self::USER_FIRST_NAME]);
            }
            if (isset($_POST[self::USER_LAST_NAME])) {
                $userdata[self::USER_LAST_NAME] = sanitize_text_field($_POST[self::USER_LAST_NAME]);
            }
            $userdata[self::USER_NAME] = $this->generateUserName();

            $nameNumber = filter_var($userdata[self::USER_NAME], FILTER_SANITIZE_NUMBER_INT);
            $userdata[self::USER_NICE_NAME] = "realtor" . $nameNumber;//Риелтор

            $userdata[self::USER_EMAIL] = sanitize_email($_POST[self::USER_EMAIL]);
            $userdata[self::USER_PASS] = wp_generate_password(12);
            $userInsertResult = wp_insert_user($userdata);
            if (is_wp_error($userInsertResult)) {
                $result = $this->getResultContent($userInsertResult->get_error_message());
            } else {
                $user = get_userdata($userInsertResult);
                $this->sendMailAboutNewUser($user);
                $result = $this->getResultContent(__('Registration complete. Please check your email.'), true);
            }
        }
        echo $result;
        die();
    }

    function getFormRegister($linkOfAdmin, $formId)
    {
        $textRegister = __('Register');
        $textRegisterInfo = __('Registration confirmation will be emailed to you.');
        $textFirstName = __('First Name');
        $textLastName = __('Last Name');
        $textEmail = __('Email');
        $textRegisterOnSite = __('Register For This Site');
        $fieldUserFirstName = self::USER_FIRST_NAME;
        $fieldUserLastName = self::USER_LAST_NAME;
        $fieldUserEmail = self::USER_EMAIL;
        $fieldAjaxRegister = self::AJAX_REGISTER;
        $fieldNonce = UtilsWp::getNonceField(self::AJAX_REGISTER, self::AJAX_REGISTER, true, false);
        return "<input name='UserForm{$this->number}' type='radio' id='tabRegister{$formId}'>
        <label for='tabRegister{$formId}'>
            <h4><span>{$textRegister}</span></h4>
        </label>
        <div class='tab-content'>
        <p class='text-xs-center'>{$textRegisterInfo}</p>
        <form method='post' enctype='multipart/form-data' action='{$linkOfAdmin}'  data-bind='submit: handleOnSubmit'>
        <fieldset>
            <input id='{$fieldUserFirstName}{$formId}' name='{$fieldUserFirstName}' data-bind='textInput: userFirstName' 
                   type='text' autocomplete='given-name' required>
            <label for='{$fieldUserFirstName}{$formId}' class='label-float'>
                <span>{$textFirstName}</span>
            </label>
        </fieldset>
        <fieldset>
            <input id='{$fieldUserLastName}{$formId}' name='{$fieldUserLastName}' data-bind='textInput: userLastName' 
                   type='text' autocomplete='family-name' required>
            <label for='{$fieldUserLastName}{$formId}' class='label-float'>
                <span>{$textLastName}</span>
            </label>
        </fieldset>
        <fieldset>
            <input id='{$fieldUserEmail}{$formId}' name='{$fieldUserEmail}' data-bind='textInput: userEmail' 
                   type='email' autocomplete='email' 
                   oninput='this.setAttribute(\"value\", this.value);' value=''  required>
            <label for='{$fieldUserEmail}{$formId}' class='label-float'>
                <i class='fa fa-envelope'></i> 
                <span>{$textEmail}</span>
            </label>
        </fieldset>
        <fieldset>
            <button type='submit' data-bind='enable:hasUserData'>
                <i class='fa fa-user-plus'></i>
                <span>{$textRegisterOnSite}</span>
            </button>
            <input type='hidden' name='user-cookie' value='1'>
            <input type='hidden' name='action'      value='{$fieldAjaxRegister}'>
            {$fieldNonce}
        </fieldset></form></div>";
    }

    /** Login */
    function ajaxLogin()
    {
        $result = json_encode(['success' => false]);
        // First check the nonce, if it fails the function will break
        if (check_ajax_referer(self::AJAX_LOGIN, self::AJAX_LOGIN)) {
            $credentials = [self::USER_REMEMBER => true];
            if (isset($_POST[self::USER_NAME]) && !empty($_POST[self::USER_NAME])) {
                $credentials[self::USER_NAME] = sanitize_user($_POST[self::USER_NAME]);
            }
            if (isset($_POST[self::USER_PASSWORD]) && !empty($_POST[self::USER_PASSWORD])) {
                $credentials[self::USER_PASSWORD] = $_POST[self::USER_PASSWORD];
            }
            $user = wp_signon($credentials, is_ssl());
            if (is_wp_error($user)) {
                $result = $this->getResultContent($user->get_error_message());
            } else {
                wp_set_current_user($user->ID);
                $result = $this->getResultContent("", true, $_POST[self::REDIRECT_LINK]);
            }
        }
        echo $result;
        die();
    }

    function getFormLogin($linkOfAdmin, $formId)
    {
        $textLogin = __('Log in');
        $textLogIn = __('Log In');
        $textUserName = __('Username or Email Address');
        $textUserPassword = __('Password');
        $fieldUserName = self::USER_NAME;
        $fieldUserPassword = self::USER_PASSWORD;
        $fieldAjaxLogin = self::AJAX_LOGIN;
        $fieldNonce = UtilsWp::getNonceField(self::AJAX_LOGIN, self::AJAX_LOGIN, true, false);
        return "<input name='UserForm{$this->number}' type='radio' id='tabLogin{$formId}' checked='checked'>
        <label for='tabLogin{$formId}'>
            <h4><span>{$textLogin}</span></h4>
        </label>
        <div class='tab-content'>
        <form method='post' enctype='multipart/form-data' action='{$linkOfAdmin}' data-bind='submit: handleOnSubmit'>
        <fieldset>
            <input id='{$fieldUserName}{$formId}' name='{$fieldUserName}' data-bind='textInput: userName'
                   type='text' autocomplete='on' autofocus required>
            <label for='{$fieldUserName}{$formId}' class='label-float'>
                <i class='fa fa-user'></i> 
                <span>{$textUserName}</span>
            </label>
        </fieldset>
        <fieldset>
            <input id='{$fieldUserPassword}{$formId}' name='{$fieldUserPassword}' data-bind='textInput: userPassword'
                   type='password' autocomplete='on' required>
            <label for='{$fieldUserPassword}{$formId}' class='label-float'>
                <i class='fa fa-key'></i> 
                <span>{$textUserPassword}</span>
            </label>
        </fieldset>
        <fieldset>
            <button type='submit' data-bind='enable:hasCredentials'>
                <i class='fa fa-unlock'></i>
                <span>{$textLogIn}</span>
            </button>
            <input type='hidden' name='user-cookie' value='1'>
            <input type='hidden' name='action'      value='{$fieldAjaxLogin}'>
            {$fieldNonce}
        </fieldset></form></div>";
    }

    /** Forgot Password */
    function ajaxForgot()
    {
        $result = json_encode(['success' => false]);
        /**
         * TODO Only If user has Email access can change password,
         * fix case when someone will introduce agent email to change password
         * also is posible to send to user the real email
         */
        if (check_ajax_referer(self::AJAX_FORGOT, self::AJAX_FORGOT) && isset($_POST[self::USER_EMAIL])) {
            $userEmail = sanitize_email($_POST[self::USER_EMAIL]);
            $errorMessage = "";
            if (empty($userEmail)) {
                $errorMessage = __('Provide a valid username or email address!', WpApp::TEXT_DOMAIN);
            } else {
                if (is_email($userEmail) && email_exists($userEmail)) {
                    // Generate new random password
                    $generatedPassword = wp_generate_password();
                    // Get user data by field ( fields are id, slug, email or login )
                    $target_user = get_user_by('email', $userEmail);
                    $target_user->user_pass = $generatedPassword;
                    $update_user = wp_update_user($target_user);
                    // if  update_user return true then send user an email containing the new password
                    if ($update_user) {
                        $to = $target_user->user_email;
                        $subject = sprintf(__('Your New Password For %s', WpApp::TEXT_DOMAIN), WPOptions::getSiteName());
                        $message = sprintf(__('Your new password is: %s', WpApp::TEXT_DOMAIN), $generatedPassword);
                        /** Email Headers ( Reply To and Content Type )*/
                        if (wp_mail($to, $subject, $message, ["Content-Type: text/html; charset=UTF-8"])) {
                            $success = __('Check your email for new password', WpApp::TEXT_DOMAIN);
                        } else {
                            $errorMessage = __('Failed to send you new password email!', WpApp::TEXT_DOMAIN);
                        }
                    } else {
                        $errorMessage = __('Oops! Something went wrong while resetting your password!', WpApp::TEXT_DOMAIN);
                    }
                } else {
                    $errorMessage = __('No user found for given email!', WpApp::TEXT_DOMAIN);
                }
            }
            if (!empty($errorMessage)) {
                $result = $this->getResultContent($errorMessage);
            } elseif (!empty($success)) {
                $result = $this->getResultContent($success, true);
            }
        }
        echo $result;
        die();
    }

    function getFormForgot($linkOfAdmin, $formId)
    {
        $textLostPass = __('Lost your password?');
        $textGetNewPassInfo = __('Please enter your username or email address. You will receive a link to create a new password via email.');
        $textUsername = __('Username or Email Address');
        $textGetNewPass = __('Get New Password');
        $fieldUserName = self::USER_EMAIL;
        $fieldAjaxForgot = self::AJAX_FORGOT;
        $fieldNonce = UtilsWp::getNonceField(self::AJAX_FORGOT, self::AJAX_FORGOT, true, false);
        return "<input name='UserForm{$this->number}' type='radio' id='tabForgotPassword{$formId}'>
        <label for='tabForgotPassword{$formId}'>
            <h4><span>{$textLostPass}</span></h4>
        </label>
        <div class='tab-content'>
        <p class='text-xs-center'>{$textGetNewPassInfo}</p>
        <form method='post' enctype='multipart/form-data' action='{$linkOfAdmin}' data-bind='submit: handleOnSubmit'>
        <fieldset>
            <input id='{$fieldUserName}{$formId}' name='{$fieldUserName}'  data-bind='textInput: userName'
                   type='text' required>
            <label for='{$fieldUserName}{$formId}' class='label-float'>
                <i class='fa fa-envelope'></i> 
                <span>{$textUsername}</span>
            </label>
        </fieldset>
        <fieldset>
            <button type='submit' data-bind='enable:hasUserName'>
                <i class='fa fa-repeat'></i> 
                <span>{$textGetNewPass}</span>
            </button>
            <input type='hidden' name='user-cookie' value='1'>
            <input type='hidden' name='action'      value='{$fieldAjaxForgot}'>
            {$fieldNonce}
        </fieldset></form></div>";
    }

    function widget($args, $instance)
    {
        $content = "";
        if (is_user_logged_in()) {
            $textLogOut = __('Log Out');
            $linkOfRedirect = add_query_arg('_', false);
            $urlLogout = wp_logout_url($linkOfRedirect);
            $content = "<a href='{$urlLogout}'><i class='fa fa-sign-out'></i><span>{$textLogOut}</span></a>";
            $instance[self::FORM_TYPE] = self::INLINE;
        } else {
            $linkOfAdmin = admin_url('admin-ajax.php');
            $enableRegistration = get_option(SettingsSite::REGISTRATION_ENABLED);
            $content .= $this->getFormLogin($linkOfAdmin, $this->number . '1');
            if ($enableRegistration) {
                $content .= $this->getFormRegister($linkOfAdmin, $this->number . '2');
                $content .= $this->getFormForgot($linkOfAdmin, $this->number . '3');
            }
            $content = "<div class='tabs'>{$content}</div><script>
            window.addEventListener('DOMContentLoaded', function(){
                ko.applyBindings(new WidgetUserForms(),document.getElementById('{$this->id}'));
            });</script>";
        }
        $args[WPSidebar::CONTENT] = $content;
        parent::widget($args, $instance);
    }
}