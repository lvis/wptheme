<?php namespace wp;
/** Author: Vitali Lupu <vitaliix@gmail.com> */
class MayFair extends WpApp
{
    const COOKIE_COMPANY = 'company';
    const COMPANY_ID_DEFAULT = '30533';
    const COMPANIES = [
        '292757' => [
            'name' => '[:en]Salon on Novolesnaya[:ru]Cалон на Новолесной[:]',
            'link' => 'https://n303202.yclients.com'
        ],
        self::COMPANY_ID_DEFAULT => [
            'name' => '[:en]Salon on Petrovskiy Pereulok[:ru]Салон на Петровском Переулке[:]',
            'link' => 'https://n11510.yclients.com'
        ]];
    const VIEW_SERVICE = 'service';
    const SERVICE_SELECT = 'o';
    const CATEGORY_GENERIC = 'generic';
    const CATEGORY_MALE = 'male';
    const CATEGORY_FEMALE = 'female';
    const CATEGORY_TYPES = [self::CATEGORY_GENERIC, self::CATEGORY_MALE, self::CATEGORY_FEMALE];
    const BOOKING_SERVICES = 'bookingServices';
    const BOOKING_CATEGORIES = 'bookingCategories';
    const TOKEN_PARTNER = 'yusw3yeu6hrr4r9j3gw6';
    const USER = '79259446038';
    const USER_PASS = '7dnse7';
    /** @var ApiBooking */
    protected $api = null;

    public function __construct()
    {
        $timeCookieExpire = time() + YEAR_IN_SECONDS;
        $companiesKeys = array_keys(self::COMPANIES);
        if (isset($_GET[self::COOKIE_COMPANY]) && in_array($_GET[self::COOKIE_COMPANY], $companiesKeys)) {
            $idCompany = $_GET[self::COOKIE_COMPANY];
            setcookie(self::COOKIE_COMPANY, $idCompany, $timeCookieExpire, COOKIEPATH, COOKIE_DOMAIN, is_ssl());
            $_COOKIE[self::COOKIE_COMPANY] = $idCompany;
        } else if (empty($_COOKIE[self::COOKIE_COMPANY])) {
            setcookie(self::COOKIE_COMPANY, self::COMPANY_ID_DEFAULT, $timeCookieExpire, COOKIEPATH, COOKIE_DOMAIN, is_ssl());
            $_COOKIE[self::COOKIE_COMPANY] = self::COMPANY_ID_DEFAULT;
        }
        $_GET[self::COOKIE_COMPANY] = $_COOKIE[self::COOKIE_COMPANY];
        PostEmployee::i();
        parent::__construct();
        add_filter(WPActions::INIT, [$this, 'handleInit']);
        add_action(WPActions::FOOTER, [$this, 'handleFooter']);
    }

    public function setupTheme()
    {
        parent::setupTheme();
        $widthThumb = 320;
        update_option(WPImages::THUMB_WIDTH, $widthThumb);
        update_option(WPImages::THUMB_HEIGHT, 0);
        update_option(WPImages::THUMB_CROP, 0);
        add_image_size(WPImages::THUMB, $widthThumb);

        $widthMedium = 426;
        update_option(WPImages::MEDIUM_WIDTH, $widthMedium);
        update_option(WPImages::MEDIUM_HEIGHT, 0);
        add_image_size(WPImages::MEDIUM, $widthMedium);

        update_option(WPImages::MEDIUM_LARGE_WIDTH, 0);
        update_option(WPImages::MEDIUM_LARGE_HEIGHT, 0);
        add_image_size(WPImages::MEDIUM_LARGE);

        $widthLarge = 640;
        update_option(WPImages::LARGE_WIDTH, $widthLarge);
        update_option(WPImages::LARGE_HEIGHT, 0);
        add_image_size(WPImages::LARGE, $widthLarge);

        if (UtilsWooCommerce::isWooCommerceActive()){
            add_image_size(WPImages::WC_THUMB, $widthThumb);
            add_image_size(WPImages::WC_THUMB_GALLERY, $widthMedium);
            add_image_size(WPImages::WC_SINGLE, $widthLarge);
        }
    }

    function enqueueScriptsTheme()
    {
        parent::enqueueScriptsTheme();
        //wp_enqueue_style('general', $this->uriToLibs . 'generic.css');
        wp_enqueue_style('mayfaiclub', $this->uriToLibs . 'mayfair.css');
        wp_enqueue_style('input', $this->uriToLibs . 'input.css');
        wp_enqueue_style('modal', $this->uriToLibs . 'modal.css');
    }

    function handleInit()
    {
        $this->api = new ApiBooking(self::TOKEN_PARTNER);
        if (function_exists('add_shortcode')) {
            add_shortcode('booking_category', [$this, 'showBookingCategories']);
            add_shortcode('booking_form', [$this, 'showBookingForm']);
            add_shortcode('booking_company', [$this, 'showBookingCompany']);
        }
    }

    function handleFooter(){
        if (is_user_logged_in() == false){
            echo '<!-- Yandex.Metrika counter -->
            <script type="text/javascript" >
               (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
               m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
               (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
               ym(55994803, "init", {clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true, trackHash:true});
            </script>
            <noscript><div><img src="https://mc.yandex.ru/watch/55994803" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
            <!-- /Yandex.Metrika counter -->';
        }
    }

    function showBookingCompany()
    {
        $company = self::COMPANIES[self::COMPANY_ID_DEFAULT];
        $companyName = $company['name'];
        if (isset(self::COMPANIES[$_COOKIE[self::COOKIE_COMPANY]])) {
            $company = self::COMPANIES[$_COOKIE[self::COOKIE_COMPANY]];
            $companyName = $company['name'];
        }
        return apply_filters('translate_text', $companyName);
    }

    function showBookingForm()
    {
        $contentJS = '';
        $idCompany = $_COOKIE[self::COOKIE_COMPANY];
        $company = self::COMPANIES[$idCompany];
        $urlToService = $company['link'];
        $urlToServiceCategory = "{$urlToService}/company:{$idCompany}";
        if (isset($_GET[self::VIEW_SERVICE])) {
            $category = intval($_GET[self::VIEW_SERVICE]);
            if ($category < 15) {
                $contentJS .= "event.source.postMessage({action: 'showCategory', parameters : {$category}}, event.origin);";
                $urlToServiceCategory .= '/idx:0/service';
            }
        }
        $urlPrefix = '?';
        if (empty($_COOKIE['language-locale']) == false) {
            $languageLocale = str_replace('_', '-', $_COOKIE['language-locale']);
            $urlToServiceCategory .= "{$urlPrefix}lang={$languageLocale}";
            $urlPrefix = '&';
        }
        if (empty($_GET[self::SERVICE_SELECT]) == false) {
            $argName = self::SERVICE_SELECT;
            $urlToServiceCategory .= "{$urlPrefix}{$argName}={$_GET[self::SERVICE_SELECT]}";
        }
        return "<iframe id='ms_booking_iframe' src='{$urlToServiceCategory}' scrolling='no' frameborder='0' allowtransparency='true'></iframe>
        <script>
        var bookFrame = document.getElementById('ms_booking_iframe');
        function resizeFrame() {        
            if (bookFrame) {
                var elHeader = document.querySelector('[data-elementor-type=\"header\"]');
                //var elFooter = document.querySelector('[data-elementor-type=\"footer\"]');
                //if (elHeader && elFooter) {
                if (elHeader) {
                   //var externalOffset = elHeader.offsetHeight + elFooter.offsetHeight;
                   var externalOffset = elHeader.offsetHeight;
                   var viewHeight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
                   var otherHeight = 5;
                   var wpadminbar = document.getElementById('wpadminbar');
                   if (wpadminbar){
                       otherHeight += wpadminbar.offsetHeight;
                   }
                   if (window.outerWidth < 769){
                       otherHeight += 42;
                   }
                   bookFrame.style.height = (viewHeight-otherHeight-externalOffset)+'px';
                }
            }
        }
        window.addEventListener('resize', resizeFrame);
        window.addEventListener('message', function(event){
            if (event.origin === '{$urlToService}'){
                if (event.data.action === 'widget_loaded'){
                    {$contentJS}
                }   
                if (event.data.action === 'booked' && typeof ym !== undefined){
                    ym(55994803, 'reachGoal', 'service_booked');
                }
            }
            resizeFrame();
        }, false);        
        </script>";
    }

    function showBookingCategories($attributes)
    {
        $result = '';
        $attributes = shortcode_atts([
            'type' => self::CATEGORY_GENERIC,
        ], $attributes);
        if (isset($attributes['type']) && in_array($attributes['type'], self::CATEGORY_TYPES)) {
            [$generic, $male, $female] = $this->getCategories();
            switch ($attributes['type']) {
                case self::CATEGORY_GENERIC:
                    $result = $generic;
                    break;
                case self::CATEGORY_MALE:
                    $result = $male;
                    break;
                case self::CATEGORY_FEMALE:
                    $result = $female;
                    break;
            }
        }
        return $this->getContentAsList($result);
    }

    function getContentAsList($items)
    {
        $urlToService = get_home_url() . '/booking/?' . self::VIEW_SERVICE;
        $result = '';
        foreach ($items as $item) {
            $result .= "<a href='{$urlToService}={$item['index']}' class='d-xs-block'>
            <h4 class='text-uppercase text-white font-weight-normal'>
                <i class='fas fa-plus'></i><span>{$item['title']}</span>
            </h4></a>";
        }
        return $result;
    }

    function getCategories()
    {
        try {
            $services = $this->api->getBookServices($_COOKIE[self::COOKIE_COMPANY]);
            $optionServices = get_option(self::BOOKING_SERVICES);
            if ($optionServices && $optionServices === $services) {
                $categoriesResult = get_option(self::BOOKING_CATEGORIES);
            }
            if (empty($categoriesResult)) {
                $categoriesResult = [[], [], []];
                $categories = $services['category'];
                $typePrefix = ' для ';
                $wordsToExclude = [
                    "{$typePrefix}Lady&Gentleman",
                    "{$typePrefix}Gentleman",
                    "{$typePrefix}Lady"
                ];
                $i = 1;
                foreach ($categories as $category) {
                    $catTitle = $category['title'];
                    $index = $category['sex'];
                    $category['title'] = str_replace($wordsToExclude[$index], '', $catTitle);
                    $category['index'] = $i;
                    array_push($categoriesResult[$index], $category);
                    $i++;
                }
                update_option(self::BOOKING_CATEGORIES, $categoriesResult);
            }
        } catch (\Exception $e) {
            $categoriesResult = [[], [], []];
        }
        return $categoriesResult;
    }
}