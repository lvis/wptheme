<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:32 PM
 */

namespace wp;
final class WidgetContactForms extends WidgetDialogBase
{
    const NONCE_MESSAGE = 'ajaxMessage';
    const RECIPIENT = "recipient";
    const RECIPIENT_SITE = "recipientSite";
    const RECIPIENT_AUTHOR = "recipientAuthor";
    const RECIPIENT_AGENCY = "recipientAgency";

    function __construct()
    {
        parent::__construct(__('Contact'));
        WPActions::addAjaxHandler([$this, 'sendMessage'], WPActions::AJAX_BOTH);
        $this->nameModalToggle = __("Add an Ad", WpApp::TEXT_DOMAIN);
        $this->iconModalToggle = "fa-pencil-square-o";
    }

    function initFields()
    {
        $this->addField(new WidgetField(WidgetField::RADIO, self::RECIPIENT, __("Recipient"), [
            self::RECIPIENT_SITE => __("Site"),
            self::RECIPIENT_AUTHOR => __("Author"),
        ], self::RECIPIENT_SITE));

        parent::initFields();
    }

    /**
     * Cc means carbon copy and Bcc means blind carbon copy.
     * For emailing, you use Cc when you want to copy others publicly,
     * and Bcc when you want to do it privately.
     * Any recipients on the Bcc line of an email are not visible to others on the email. */
    function sendMessage()
    {
        //ReCaptcha::i()->validate();
        $hasEmail = isset($_POST['email']);
        $hasNonce = isset($_POST[WidgetContactForms::NONCE_MESSAGE]);
        if ($hasEmail && $hasNonce &&
            wp_verify_nonce($_POST[WidgetContactForms::NONCE_MESSAGE], WidgetContactForms::NONCE_MESSAGE)) {
            /** Email TO */
            $emailTo = sanitize_email($_POST['target']);
            if (is_email($emailTo)) {
                /** Contact Name */
                $contactName = sanitize_text_field($_POST['name']);
                /** Contact Email*/
                $contactEmail = sanitize_email($_POST['email']);
                if (is_email($contactEmail)) {
                    $contactEmail = sprintf('<p><strong>%s</strong>:%s</p>', __("Email", WpApp::TEXT_DOMAIN),
                        $contactEmail);
                }
                /** Contact Phone */
                $contactPhone = sanitize_text_field($_POST['number']);
                if (!empty($contactPhone)) {
                    $contactPhone = sprintf('<p><strong>%s</strong>:%s</p>', __("Phone Number", WpApp::TEXT_DOMAIN),
                        $contactPhone);
                }
                /** Property TITLE */
                $pageUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : home_url();
                $pageTitle = sprintf('<p><strong>%1$s</strong>:<a href="%2$s">%2$s</a></p>',
                    __("Page Title", WpApp::TEXT_DOMAIN), $pageUrl);
                /** Email SUBJECT */
                $emailSubject = sprintf('%s %s %s',
                    get_bloginfo('name'), __("message from", WpApp::TEXT_DOMAIN), $contactName);
                /** Email BODY */
                $emailMessage = stripslashes($_POST['message']);
                if (!empty($emailMessage)) {
                    $emailMessage = wpautop($emailMessage);
                }
                $emailBody = sprintf('%s<br>%s<br>%s', $emailMessage, $contactEmail . $contactPhone, $pageTitle);
                /** Email HEADERS ( Reply To and Content Type ) */
                $emailHeaders = [];
                $emailHeaders[] = "Reply-To: $contactName <$contactEmail>";
                $emailHeaders[] = "Content-Type: text/html; charset=UTF-8";
                // Send copy of message to Admin
                $sendMessageCopy = get_option(SettingsSite::EMAIL_CC_ENABLE);
                if ($sendMessageCopy == 'true') {
                    //TODO Check if admin email is not the same as the form email
                    $emailsCc = (array)get_option(SettingsSite::EMAIL_CC);
                    if (!empty($emailsCc)) {
                        foreach ($emailsCc as $emailCc) {
                            $emailCc = sanitize_email($emailCc);
                            if (is_email($emailCc) && $emailCc != $emailTo) {
                                $emailHeaders[] = "Cc: $emailCc";
                            }
                        }
                    }
                }
                //TODO Add Function to handle from Name overrited by plugin
                /*add_filter('wp_mail_from','yoursite_wp_mail_from');
                function yoursite_wp_mail_from($content_type) {
                    return 'helenyhou@example.com';
                }
                add_filter('wp_mail_from_name','yoursite_wp_mail_from_name');
                function yoursite_wp_mail_from_name($name) {
                    return 'Helen Hou-Sandi';
                }*/
                if (wp_mail($emailTo, $emailSubject, $emailBody, $emailHeaders)) {
                    echo json_encode([
                        'success' => true,
                        'message' => __("Message sent successfully", WpApp::TEXT_DOMAIN),
                    ]);
                    die;
                }
            }

        }
        echo json_encode(['success' => false, 'message' => __("Message wasn't sent", WpApp::TEXT_DOMAIN)]);
        die;
    }

    function widget($args, $instance)
    {
        $content = "";
        $recipient = self::getInstanceValue($instance, self::RECIPIENT, $this);
        $title = self::getInstanceValue($instance, self::MODAL_TOGGLE_NAME, $this);
        if (empty($title) == false) {
            $title = __($title, WpApp::TEXT_DOMAIN);
        }
        $icon = self::getInstanceValue($instance, self::MODAL_TOGGLE_ICON, $this);
        $formEmail = "";
        if ($recipient == self::RECIPIENT_SITE) {
            $formEmail = get_option(SettingsSite::EMAIL);
        } else if ($recipient == self::RECIPIENT_AUTHOR) {
            $formEmail = get_the_author_meta('email');
        }
        if (is_email($formEmail) == false) {
            $formEmail = get_option(WPOptions::ADMIN_EMAIL);
        }

        if (is_email($formEmail)) {
            $linkOfAdmin = admin_url('admin-ajax.php');

            $fieldName = sprintf('<div class="form-group"><div class="input-group">
                    <label class="input-group-addon" for="%1$s"><i class="fa fa-user"></i></label>
                    <input id="%1$s" name="name" placeholder="%2$s" class="form-control" required>
                </div></div>',
                uniqid("name"),
                __('Your Name', WpApp::TEXT_DOMAIN));

            $fieldEmail = sprintf('<div class="form-group"><div class="input-group">
                    <label class="input-group-addon" for="%1$s"><i class="fa fa-envelope-o"></i></label>
                    <input id="%1$s" type="email" name="email" placeholder="%2$s" class="form-control">
                	</div></div>',
                uniqid("email"),
                __('Your Email', WpApp::TEXT_DOMAIN));

            $fieldMessage = sprintf('<div class="form-group"><div class="input-group">
						<label class="input-group-addon" for="%1$s"><i class="fa fa-edit"></i></label>
						<textarea id="%1$s" name="message" rows="3" class="form-control" placeholder="%2$s" required></textarea>
						</div></div>',
                uniqid("message"),
                __('Your message', WpApp::TEXT_DOMAIN));

            $fieldPhone = sprintf('<div class="form-group"><div class="input-group">
                    <label class="input-group-addon" for="%1$s"><i class="fa fa-phone"></i></label>
                    <input id="%1$s" type="tel" name="number" placeholder="%2$s" class="form-control" required>
                	</div></div>',
                uniqid("phone"),
                __('Your Phone Number', WpApp::TEXT_DOMAIN));

            $fieldNonce = UtilsWp::getNonceField(WidgetContactForms::NONCE_MESSAGE,
                                                 WidgetContactForms::NONCE_MESSAGE, true, false);

            $contentHeader = "";
            $formType = self::getInstanceValue($instance, self::FORM_TYPE, $this);
            if ($formType == self::DIALOG) {
                $contentHeader = sprintf('<div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title"><i class="fa %s"></i> <span>%s</span></h4></div>', $icon, $title);
            }
            $markup = '%s<form method="post" action="%s" class="contact-form">
		                    <div class="modal-body">
		                    	%s
		                        <input type="hidden" name="target" value="%s">
		                        <input type="hidden" name="action" value="sendMessage">
		                    </div>
		                    <div class="modal-footer">
		                    <button type="submit" name="submit" class="btn btn-primary">
		                    <i class="fa fa-paper-plane-o" aria-hidden="true"></i><span>%s</span></button></div></form>';
            $content = sprintf($markup,
                $contentHeader,
                $linkOfAdmin,
                $fieldName . $fieldEmail . $fieldPhone . $fieldMessage . $fieldNonce,
                antispambot($formEmail),
                __("Send", WpApp::TEXT_DOMAIN)); //ReCaptcha::i()->widget()
        }
        $args[WPSidebar::CONTENT] = $content;
        parent::widget($args, $instance);
    }
}