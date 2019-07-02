<?php defined('ABSPATH') || exit;

use wp\WidgetArea;
use wp\WPUtils;

$content = '';
$content .= WPUtils::getSidebarContent(WidgetArea::FOOTER_TOP);
$content .= WPUtils::getSidebarContent(WidgetArea::FOOTER_BOTTOM);
$siteName = get_bloginfo('name');
$currentYear = date('Y');
$contentAfterFooter = WPUtils::doAction('wp_footer');
$textCopyright = __('Copyright');
echo "</main><footer>{$content}
<div class='text-xs-center'>{$textCopyright} © {$currentYear} {$siteName}</div></footer>{$contentAfterFooter}</body></html>";
//TODO Add this Code as default in Customizer and make optional
/*
$currentLanguage = WPUtils::getLanguageShortCode();
switch ($currentLanguage) {
    case 'en':
        $currentLanguageCode = "599a97f71b1bed47ceb05bc4";
        break;
    case 'ro':
        $currentLanguageCode = "599a9926dbb01a218b4dd6c3";
        break;
    case 'ru':
    default:
        $currentLanguageCode = "599a95bcdbb01a218b4dd6bd";
        break;
}
echo '<script>
function isBot(){var re = new RegExp("(googlebot\/|Googlebot-Mobile|Googlebot-Image|Google favicon|Mediapartners-Google|Speed Insights|GTmetrix|Gecko/20100101|Pingdom|pingdom|bingbot|slurp|java|wget|curl|Commons-HttpClient|Python-urllib|libwww|httpunit|nutch|phpcrawl|msnbot|jyxobot|FAST-WebCrawler|FAST Enterprise Crawler|biglotron|teoma|convera|seekbot|gigablast|exabot|ngbot|ia_archiver|GingerCrawler|webmon |httrack|webcrawler|grub.org|UsineNouvelleCrawler|antibot|netresearchserver|speedy|fluffy|bibnum.bnf|findlink|msrbot|panscient|yacybot|AISearchBot|IOI|ips-agent|tagoobot|MJ12bot|dotbot|woriobot|yanga|buzzbot|mlbot|yandexbot|purebot|Linguee Bot|Voyager|CyberPatrol|voilabot|baiduspider|citeseerxbot|spbot|twengabot|postrank|turnitinbot|scribdbot|page2rss|sitebot|linkdex|Adidxbot|blekkobot|ezooms|dotbot|Mail.RU_Bot|discobot|heritrix|findthatfile|europarchive.org|NerdByNature.Bot|sistrix crawler|ahrefsbot|Aboundex|domaincrawler|wbsearchbot|summify|ccbot|edisterbot|seznambot|ec2linkfinder|gslfbot|aihitbot|intelium_bot|facebookexternalhit|yeti|RetrevoPageAnalyzer|lb-spider|sogou|lssbot|careerbot|wotbox|wocbot|ichiro|DuckDuckBot|lssrocketcrawler|drupact|webcompanycrawler|acoonbot|openindexspider|gnam gnam spider|web-archive-net.com.bot|backlinkcrawler|coccoc|integromedb|content crawler spider|toplistbot|seokicks-robot|it2media-domain-crawler|ip-web-crawler.com|siteexplorer.info|elisabot|proximic|changedetection|blexbot|arabot|WeSEE:Search|niki-bot|CrystalSemanticsBot|rogerbot|360Spider|psbot|InterfaxScanBot|Lipperhey SEO Service|CC Metadata Scaper|g00g1e.net|GrapeshotCrawler|urlappendbot|brainobot|fr-crawler|binlar|SimpleCrawler|Livelapbot|Twitterbot|cXensebot|smtbot|bnf.fr_bot|A6-Indexer|ADmantX|Facebot|Twitterbot|OrangeBot|memorybot|AdvBot|MegaIndex|SemanticScholarBot|ltx71|nerdybot|xovibot|BUbiNG|Qwantify|archive.org_bot|Applebot|TweetmemeBot|crawler4j|findxbot|SemrushBot|yoozBot|lipperhey|y!j-asr|Domain Re-Animator Bot|AddThis)", \'i\');return re.test(navigator.userAgent);}
if(isBot()===false){
    (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){ (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o), m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m) })(window,document,\'script\',\'https://www.google-analytics.com/analytics.js\',\'ga\');ga(\'create\', \'UA-105645814-1\', \'auto\');ga(\'send\', \'pageview\');
    //Tawk Chat
    if(wpConfig.enableChat){
        var s1 = document.createElement("script");
        s1.type = "text/javascript";
        s1.defer = true;
        s1.src = "//embed.tawk.to/"+wpConfig.currentLanguageCode+"/default";
        s1.charset = "UTF-8";
        s1.setAttribute("crossorigin", "*");
        document.body.appendChild(s1);
    }
}
</script>';*/