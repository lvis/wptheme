<?php namespace wp;

use WP_Customize_Manager;
use WP_Customize_Control;

final class WpAppCookie extends WpApp
{
    protected function __construct()
    {
        parent::__construct();
        add_action(WPActions::ENQUEUE_SCRIPTS_THEME, [$this, 'enqueueScriptsTheme']);
        add_action(WPActions::CUSTOMIZER_INIT, [$this, 'handleCustomizerInit']);
        add_action(WPActions::CUSTOMIZER_REGISTER, [$this, 'handleCustomizerRegister']);
        add_action(WPActions::WP_HEAD, [$this, 'handleHeadScripts']);
        add_action(WPActions::WP_HEAD, [$this, 'handleHeadCss'], 9999);
        add_action('wp_footer', [$this, 'handleFooter'], 9999);
        add_action('wp_print_footer_scripts', [$this, 'handleFooterPrintScripts']);
    }
    /**
     * @inheritdoc
     */
    function enqueueScriptsTheme()
    {
        parent::enqueueScriptsTheme();
        //---------------------------------------------- [Cookie]
        wp_enqueue_style('cookie', $this->uriToLibs . 'cookie.css');
        $time = get_theme_mod('ocn_expiry', '1month');
        $times = [
            '1hour' => [__('An hour', WpApp::TEXT_DOMAIN), 3600],
            '1day' => [__('1 day', WpApp::TEXT_DOMAIN), 86400],
            '1week' => [__('1 week', WpApp::TEXT_DOMAIN), 604800],
            '1month' => [__('1 month', WpApp::TEXT_DOMAIN), 2592000],
            '3months' => [__('3 months', WpApp::TEXT_DOMAIN), 7862400],
            '6months' => [__('6 months', WpApp::TEXT_DOMAIN), 15811200],
            '1year' => [__('1 year', WpApp::TEXT_DOMAIN), 31536000],
            'infinity' => [__('infinity', WpApp::TEXT_DOMAIN), 2147483647]
        ];
        $get_time = sanitize_text_field(isset($times[$time]) ? $time : '1month');
        $localizeData = [
            'cookieName'=> 'ocn_accepted',
            'cookieTime'=> $times[$get_time][1],
            'cookiePath'=> (defined('COOKIEPATH') ? COOKIEPATH : ''),
            'cookieDomain'=> (defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : ''),
            'cache'=> (defined('WP_CACHE') && WP_CACHE),
            'secure'=> (int)is_ssl(),
            'reload'=> get_theme_mod('ocn_reload', 'no'),
            'overlay'=> get_theme_mod('ocn_overlay', 'no')
        ];
        wp_enqueue_script('cookie-main', $this->uriToLibs . 'cookie-main.js', ['jquery'], false, true);
        wp_localize_script('cookie-main', 'oceanwpLocalize', $localizeData);
        self::enqueueGoogleFonts([get_theme_mod(self::OPTION_COOKIE_CONTENT_FONT, '')]);
    }
    //-----------------------------------------[Cookie Functions]
    static function getGoogleFonts()
    {
        return [
            'ABeeZee' => 'ABeeZee',
            'Abel' => 'Abel',
            'Abril Fatface' => 'Abril Fatface',
            'Aclonica' => 'Aclonica',
            'Acme' => 'Acme',
            'Actor' => 'Actor',
            'Adamina' => 'Adamina',
            'Advent Pro' => 'Advent Pro',
            'Aguafina Script' => 'Aguafina Script',
            'Akronim' => 'Akronim',
            'Aladin' => 'Aladin',
            'Aldrich' => 'Aldrich',
            'Alef' => 'Alef',
            'Alegreya' => 'Alegreya',
            'Alegreya SC' => 'Alegreya SC',
            'Alegreya Sans' => 'Alegreya Sans',
            'Alegreya Sans SC' => 'Alegreya Sans SC',
            'Alex Brush' => 'Alex Brush',
            'Alfa Slab One' => 'Alfa Slab One',
            'Alice' => 'Alice',
            'Alike' => 'Alike',
            'Alike Angular' => 'Alike Angular',
            'Allan' => 'Allan',
            'Allerta' => 'Allerta',
            'Allerta Stencil' => 'Allerta Stencil',
            'Allura' => 'Allura',
            'Almendra' => 'Almendra',
            'Almendra Display' => 'Almendra Display',
            'Almendra SC' => 'Almendra SC',
            'Amarante' => 'Amarante',
            'Amaranth' => 'Amaranth',
            'Amatic SC' => 'Amatic SC',
            'Amatica SC' => 'Amatica SC',
            'Amethysta' => 'Amethysta',
            'Amiko' => 'Amiko',
            'Amiri' => 'Amiri',
            'Amita' => 'Amita',
            'Anaheim' => 'Anaheim',
            'Andada' => 'Andada',
            'Andika' => 'Andika',
            'Angkor' => 'Angkor',
            'Annie Use Your Telescope' => 'Annie Use Your Telescope',
            'Anonymous Pro' => 'Anonymous Pro',
            'Antic' => 'Antic',
            'Antic Didone' => 'Antic Didone',
            'Antic Slab' => 'Antic Slab',
            'Anton' => 'Anton',
            'Arapey' => 'Arapey',
            'Arbutus' => 'Arbutus',
            'Arbutus Slab' => 'Arbutus Slab',
            'Architects Daughter' => 'Architects Daughter',
            'Archivo Black' => 'Archivo Black',
            'Archivo Narrow' => 'Archivo Narrow',
            'Aref Ruqaa' => 'Aref Ruqaa',
            'Arima Madurai' => 'Arima Madurai',
            'Arimo' => 'Arimo',
            'Arizonia' => 'Arizonia',
            'Armata' => 'Armata',
            'Artifika' => 'Artifika',
            'Arvo' => 'Arvo',
            'Arya' => 'Arya',
            'Asap' => 'Asap',
            'Asar' => 'Asar',
            'Asset' => 'Asset',
            'Assistant' => 'Assistant',
            'Astloch' => 'Astloch',
            'Asul' => 'Asul',
            'Athiti' => 'Athiti',
            'Atma' => 'Atma',
            'Atomic Age' => 'Atomic Age',
            'Aubrey' => 'Aubrey',
            'Audiowide' => 'Audiowide',
            'Autour One' => 'Autour One',
            'Average' => 'Average',
            'Average Sans' => 'Average Sans',
            'Averia Gruesa Libre' => 'Averia Gruesa Libre',
            'Averia Libre' => 'Averia Libre',
            'Averia Sans Libre' => 'Averia Sans Libre',
            'Averia Serif Libre' => 'Averia Serif Libre',
            'Bad Script' => 'Bad Script',
            'Baloo' => 'Baloo',
            'Baloo Bhai' => 'Baloo Bhai',
            'Baloo Da' => 'Baloo Da',
            'Baloo Thambi' => 'Baloo Thambi',
            'Balthazar' => 'Balthazar',
            'Bangers' => 'Bangers',
            'Basic' => 'Basic',
            'Battambang' => 'Battambang',
            'Baumans' => 'Baumans',
            'Bayon' => 'Bayon',
            'Belgrano' => 'Belgrano',
            'Belleza' => 'Belleza',
            'BenchNine' => 'BenchNine',
            'Bentham' => 'Bentham',
            'Berkshire Swash' => 'Berkshire Swash',
            'Bevan' => 'Bevan',
            'Bigelow Rules' => 'Bigelow Rules',
            'Bigshot One' => 'Bigshot One',
            'Bilbo' => 'Bilbo',
            'Bilbo Swash Caps' => 'Bilbo Swash Caps',
            'BioRhyme' => 'BioRhyme',
            'BioRhyme Expanded' => 'BioRhyme Expanded',
            'Biryani' => 'Biryani',
            'Bitter' => 'Bitter',
            'Black Ops One' => 'Black Ops One',
            'Bokor' => 'Bokor',
            'Bonbon' => 'Bonbon',
            'Boogaloo' => 'Boogaloo',
            'Bowlby One' => 'Bowlby One',
            'Bowlby One SC' => 'Bowlby One SC',
            'Brawler' => 'Brawler',
            'Bree Serif' => 'Bree Serif',
            'Bubblegum Sans' => 'Bubblegum Sans',
            'Bubbler One' => 'Bubbler One',
            'Buda' => 'Buda',
            'Buenard' => 'Buenard',
            'Bungee' => 'Bungee',
            'Bungee Hairline' => 'Bungee Hairline',
            'Bungee Inline' => 'Bungee Inline',
            'Bungee Outline' => 'Bungee Outline',
            'Bungee Shade' => 'Bungee Shade',
            'Butcherman' => 'Butcherman',
            'Butterfly Kids' => 'Butterfly Kids',
            'Cabin' => 'Cabin',
            'Cabin Condensed' => 'Cabin Condensed',
            'Cabin Sketch' => 'Cabin Sketch',
            'Caesar Dressing' => 'Caesar Dressing',
            'Cagliostro' => 'Cagliostro',
            'Cairo' => 'Cairo',
            'Calligraffitti' => 'Calligraffitti',
            'Cambay' => 'Cambay',
            'Cambo' => 'Cambo',
            'Candal' => 'Candal',
            'Cantarell' => 'Cantarell',
            'Cantata One' => 'Cantata One',
            'Cantora One' => 'Cantora One',
            'Capriola' => 'Capriola',
            'Cardo' => 'Cardo',
            'Carme' => 'Carme',
            'Carrois Gothic' => 'Carrois Gothic',
            'Carrois Gothic SC' => 'Carrois Gothic SC',
            'Carter One' => 'Carter One',
            'Catamaran' => 'Catamaran',
            'Caudex' => 'Caudex',
            'Caveat' => 'Caveat',
            'Caveat Brush' => 'Caveat Brush',
            'Cedarville Cursive' => 'Cedarville Cursive',
            'Ceviche One' => 'Ceviche One',
            'Changa' => 'Changa',
            'Changa One' => 'Changa One',
            'Chango' => 'Chango',
            'Chathura' => 'Chathura',
            'Chau Philomene One' => 'Chau Philomene One',
            'Chela One' => 'Chela One',
            'Chelsea Market' => 'Chelsea Market',
            'Chenla' => 'Chenla',
            'Cherry Cream Soda' => 'Cherry Cream Soda',
            'Cherry Swash' => 'Cherry Swash',
            'Chewy' => 'Chewy',
            'Chicle' => 'Chicle',
            'Chivo' => 'Chivo',
            'Chonburi' => 'Chonburi',
            'Cinzel' => 'Cinzel',
            'Cinzel Decorative' => 'Cinzel Decorative',
            'Clicker Script' => 'Clicker Script',
            'Coda' => 'Coda',
            'Coda Caption' => 'Coda Caption',
            'Codystar' => 'Codystar',
            'Coiny' => 'Coiny',
            'Combo' => 'Combo',
            'Comfortaa' => 'Comfortaa',
            'Coming Soon' => 'Coming Soon',
            'Concert One' => 'Concert One',
            'Condiment' => 'Condiment',
            'Content' => 'Content',
            'Contrail One' => 'Contrail One',
            'Convergence' => 'Convergence',
            'Cookie' => 'Cookie',
            'Copse' => 'Copse',
            'Corben' => 'Corben',
            'Cormorant' => 'Cormorant',
            'Cormorant Garamond' => 'Cormorant Garamond',
            'Cormorant Infant' => 'Cormorant Infant',
            'Cormorant SC' => 'Cormorant SC',
            'Cormorant Unicase' => 'Cormorant Unicase',
            'Cormorant Upright' => 'Cormorant Upright',
            'Courgette' => 'Courgette',
            'Cousine' => 'Cousine',
            'Coustard' => 'Coustard',
            'Covered By Your Grace' => 'Covered By Your Grace',
            'Crafty Girls' => 'Crafty Girls',
            'Creepster' => 'Creepster',
            'Crete Round' => 'Crete Round',
            'Crimson Text' => 'Crimson Text',
            'Croissant One' => 'Croissant One',
            'Crushed' => 'Crushed',
            'Cuprum' => 'Cuprum',
            'Cutive' => 'Cutive',
            'Cutive Mono' => 'Cutive Mono',
            'Damion' => 'Damion',
            'Dancing Script' => 'Dancing Script',
            'Dangrek' => 'Dangrek',
            'David Libre' => 'David Libre',
            'Dawning of a New Day' => 'Dawning of a New Day',
            'Days One' => 'Days One',
            'Dekko' => 'Dekko',
            'Delius' => 'Delius',
            'Delius Swash Caps' => 'Delius Swash Caps',
            'Delius Unicase' => 'Delius Unicase',
            'Della Respira' => 'Della Respira',
            'Denk One' => 'Denk One',
            'Devonshire' => 'Devonshire',
            'Dhurjati' => 'Dhurjati',
            'Didact Gothic' => 'Didact Gothic',
            'Diplomata' => 'Diplomata',
            'Diplomata SC' => 'Diplomata SC',
            'Domine' => 'Domine',
            'Donegal One' => 'Donegal One',
            'Doppio One' => 'Doppio One',
            'Dorsa' => 'Dorsa',
            'Dosis' => 'Dosis',
            'Dr Sugiyama' => 'Dr Sugiyama',
            'Droid Sans' => 'Droid Sans',
            'Droid Sans Mono' => 'Droid Sans Mono',
            'Droid Serif' => 'Droid Serif',
            'Duru Sans' => 'Duru Sans',
            'Dynalight' => 'Dynalight',
            'EB Garamond' => 'EB Garamond',
            'Eagle Lake' => 'Eagle Lake',
            'Eater' => 'Eater',
            'Economica' => 'Economica',
            'Eczar' => 'Eczar',
            'Ek Mukta' => 'Ek Mukta',
            'El Messiri' => 'El Messiri',
            'Electrolize' => 'Electrolize',
            'Elsie' => 'Elsie',
            'Elsie Swash Caps' => 'Elsie Swash Caps',
            'Emblema One' => 'Emblema One',
            'Emilys Candy' => 'Emilys Candy',
            'Engagement' => 'Engagement',
            'Englebert' => 'Englebert',
            'Enriqueta' => 'Enriqueta',
            'Erica One' => 'Erica One',
            'Esteban' => 'Esteban',
            'Euphoria Script' => 'Euphoria Script',
            'Ewert' => 'Ewert',
            'Exo' => 'Exo',
            'Exo 2' => 'Exo 2',
            'Expletus Sans' => 'Expletus Sans',
            'Fanwood Text' => 'Fanwood Text',
            'Farsan' => 'Farsan',
            'Fascinate' => 'Fascinate',
            'Fascinate Inline' => 'Fascinate Inline',
            'Faster One' => 'Faster One',
            'Fasthand' => 'Fasthand',
            'Fauna One' => 'Fauna One',
            'Federant' => 'Federant',
            'Federo' => 'Federo',
            'Felipa' => 'Felipa',
            'Fenix' => 'Fenix',
            'Finger Paint' => 'Finger Paint',
            'Fira Mono' => 'Fira Mono',
            'Fira Sans' => 'Fira Sans',
            'Fjalla One' => 'Fjalla One',
            'Fjord One' => 'Fjord One',
            'Flamenco' => 'Flamenco',
            'Flavors' => 'Flavors',
            'Fondamento' => 'Fondamento',
            'Fontdiner Swanky' => 'Fontdiner Swanky',
            'Forum' => 'Forum',
            'Francois One' => 'Francois One',
            'Frank Ruhl Libre' => 'Frank Ruhl Libre',
            'Freckle Face' => 'Freckle Face',
            'Fredericka the Great' => 'Fredericka the Great',
            'Fredoka One' => 'Fredoka One',
            'Freehand' => 'Freehand',
            'Fresca' => 'Fresca',
            'Frijole' => 'Frijole',
            'Fruktur' => 'Fruktur',
            'Fugaz One' => 'Fugaz One',
            'GFS Didot' => 'GFS Didot',
            'GFS Neohellenic' => 'GFS Neohellenic',
            'Gabriela' => 'Gabriela',
            'Gafata' => 'Gafata',
            'Galada' => 'Galada',
            'Galdeano' => 'Galdeano',
            'Galindo' => 'Galindo',
            'Gentium Basic' => 'Gentium Basic',
            'Gentium Book Basic' => 'Gentium Book Basic',
            'Geo' => 'Geo',
            'Geostar' => 'Geostar',
            'Geostar Fill' => 'Geostar Fill',
            'Germania One' => 'Germania One',
            'Gidugu' => 'Gidugu',
            'Gilda Display' => 'Gilda Display',
            'Give You Glory' => 'Give You Glory',
            'Glass Antiqua' => 'Glass Antiqua',
            'Glegoo' => 'Glegoo',
            'Gloria Hallelujah' => 'Gloria Hallelujah',
            'Goblin One' => 'Goblin One',
            'Gochi Hand' => 'Gochi Hand',
            'Gorditas' => 'Gorditas',
            'Goudy Bookletter 1911' => 'Goudy Bookletter 1911',
            'Graduate' => 'Graduate',
            'Grand Hotel' => 'Grand Hotel',
            'Gravitas One' => 'Gravitas One',
            'Great Vibes' => 'Great Vibes',
            'Griffy' => 'Griffy',
            'Gruppo' => 'Gruppo',
            'Gudea' => 'Gudea',
            'Gurajada' => 'Gurajada',
            'Habibi' => 'Habibi',
            'Halant' => 'Halant',
            'Hammersmith One' => 'Hammersmith One',
            'Hanalei' => 'Hanalei',
            'Hanalei Fill' => 'Hanalei Fill',
            'Handlee' => 'Handlee',
            'Hanuman' => 'Hanuman',
            'Happy Monkey' => 'Happy Monkey',
            'Harmattan' => 'Harmattan',
            'Headland One' => 'Headland One',
            'Heebo' => 'Heebo',
            'Henny Penny' => 'Henny Penny',
            'Herr Von Muellerhoff' => 'Herr Von Muellerhoff',
            'Hind' => 'Hind',
            'Hind Guntur' => 'Hind Guntur',
            'Hind Madurai' => 'Hind Madurai',
            'Hind Siliguri' => 'Hind Siliguri',
            'Hind Vadodara' => 'Hind Vadodara',
            'Holtwood One SC' => 'Holtwood One SC',
            'Homemade Apple' => 'Homemade Apple',
            'Homenaje' => 'Homenaje',
            'IM Fell DW Pica' => 'IM Fell DW Pica',
            'IM Fell DW Pica SC' => 'IM Fell DW Pica SC',
            'IM Fell Double Pica' => 'IM Fell Double Pica',
            'IM Fell Double Pica SC' => 'IM Fell Double Pica SC',
            'IM Fell English' => 'IM Fell English',
            'IM Fell English SC' => 'IM Fell English SC',
            'IM Fell French Canon' => 'IM Fell French Canon',
            'IM Fell French Canon SC' => 'IM Fell French Canon SC',
            'IM Fell Great Primer' => 'IM Fell Great Primer',
            'IM Fell Great Primer SC' => 'IM Fell Great Primer SC',
            'Iceberg' => 'Iceberg',
            'Iceland' => 'Iceland',
            'Imprima' => 'Imprima',
            'Inconsolata' => 'Inconsolata',
            'Inder' => 'Inder',
            'Indie Flower' => 'Indie Flower',
            'Inika' => 'Inika',
            'Inknut Antiqua' => 'Inknut Antiqua',
            'Irish Grover' => 'Irish Grover',
            'Istok Web' => 'Istok Web',
            'Italiana' => 'Italiana',
            'Italianno' => 'Italianno',
            'Itim' => 'Itim',
            'Jacques Francois' => 'Jacques Francois',
            'Jacques Francois Shadow' => 'Jacques Francois Shadow',
            'Jaldi' => 'Jaldi',
            'Jim Nightshade' => 'Jim Nightshade',
            'Jockey One' => 'Jockey One',
            'Jolly Lodger' => 'Jolly Lodger',
            'Jomhuria' => 'Jomhuria',
            'Josefin Sans' => 'Josefin Sans',
            'Josefin Slab' => 'Josefin Slab',
            'Joti One' => 'Joti One',
            'Judson' => 'Judson',
            'Julee' => 'Julee',
            'Julius Sans One' => 'Julius Sans One',
            'Junge' => 'Junge',
            'Jura' => 'Jura',
            'Just Another Hand' => 'Just Another Hand',
            'Just Me Again Down Here' => 'Just Me Again Down Here',
            'Kadwa' => 'Kadwa',
            'Kalam' => 'Kalam',
            'Kameron' => 'Kameron',
            'Kanit' => 'Kanit',
            'Kantumruy' => 'Kantumruy',
            'Karla' => 'Karla',
            'Karma' => 'Karma',
            'Katibeh' => 'Katibeh',
            'Kaushan Script' => 'Kaushan Script',
            'Kavivanar' => 'Kavivanar',
            'Kavoon' => 'Kavoon',
            'Kdam Thmor' => 'Kdam Thmor',
            'Keania One' => 'Keania One',
            'Kelly Slab' => 'Kelly Slab',
            'Kenia' => 'Kenia',
            'Khand' => 'Khand',
            'Khmer' => 'Khmer',
            'Khula' => 'Khula',
            'Kite One' => 'Kite One',
            'Knewave' => 'Knewave',
            'Kotta One' => 'Kotta One',
            'Koulen' => 'Koulen',
            'Kranky' => 'Kranky',
            'Kreon' => 'Kreon',
            'Kristi' => 'Kristi',
            'Krona One' => 'Krona One',
            'Kumar One' => 'Kumar One',
            'Kumar One Outline' => 'Kumar One Outline',
            'Kurale' => 'Kurale',
            'La Belle Aurore' => 'La Belle Aurore',
            'Laila' => 'Laila',
            'Lakki Reddy' => 'Lakki Reddy',
            'Lalezar' => 'Lalezar',
            'Lancelot' => 'Lancelot',
            'Lateef' => 'Lateef',
            'Lato' => 'Lato',
            'League Script' => 'League Script',
            'Leckerli One' => 'Leckerli One',
            'Ledger' => 'Ledger',
            'Lekton' => 'Lekton',
            'Lemon' => 'Lemon',
            'Lemonada' => 'Lemonada',
            'Libre Baskerville' => 'Libre Baskerville',
            'Libre Franklin' => 'Libre Franklin',
            'Life Savers' => 'Life Savers',
            'Lilita One' => 'Lilita One',
            'Lily Script One' => 'Lily Script One',
            'Limelight' => 'Limelight',
            'Linden Hill' => 'Linden Hill',
            'Lobster' => 'Lobster',
            'Lobster Two' => 'Lobster Two',
            'Londrina Outline' => 'Londrina Outline',
            'Londrina Shadow' => 'Londrina Shadow',
            'Londrina Sketch' => 'Londrina Sketch',
            'Londrina Solid' => 'Londrina Solid',
            'Lora' => 'Lora',
            'Love Ya Like A Sister' => 'Love Ya Like A Sister',
            'Loved by the King' => 'Loved by the King',
            'Lovers Quarrel' => 'Lovers Quarrel',
            'Luckiest Guy' => 'Luckiest Guy',
            'Lusitana' => 'Lusitana',
            'Lustria' => 'Lustria',
            'Macondo' => 'Macondo',
            'Macondo Swash Caps' => 'Macondo Swash Caps',
            'Mada' => 'Mada',
            'Magra' => 'Magra',
            'Maiden Orange' => 'Maiden Orange',
            'Maitree' => 'Maitree',
            'Mako' => 'Mako',
            'Mallanna' => 'Mallanna',
            'Mandali' => 'Mandali',
            'Marcellus' => 'Marcellus',
            'Marcellus SC' => 'Marcellus SC',
            'Marck Script' => 'Marck Script',
            'Margarine' => 'Margarine',
            'Marko One' => 'Marko One',
            'Marmelad' => 'Marmelad',
            'Martel' => 'Martel',
            'Martel Sans' => 'Martel Sans',
            'Marvel' => 'Marvel',
            'Mate' => 'Mate',
            'Mate SC' => 'Mate SC',
            'Maven Pro' => 'Maven Pro',
            'McLaren' => 'McLaren',
            'Meddon' => 'Meddon',
            'MedievalSharp' => 'MedievalSharp',
            'Medula One' => 'Medula One',
            'Meera Inimai' => 'Meera Inimai',
            'Megrim' => 'Megrim',
            'Meie Script' => 'Meie Script',
            'Merienda' => 'Merienda',
            'Merienda One' => 'Merienda One',
            'Merriweather' => 'Merriweather',
            'Merriweather Sans' => 'Merriweather Sans',
            'Metal' => 'Metal',
            'Metal Mania' => 'Metal Mania',
            'Metamorphous' => 'Metamorphous',
            'Metrophobic' => 'Metrophobic',
            'Michroma' => 'Michroma',
            'Milonga' => 'Milonga',
            'Miltonian' => 'Miltonian',
            'Miltonian Tattoo' => 'Miltonian Tattoo',
            'Miniver' => 'Miniver',
            'Miriam Libre' => 'Miriam Libre',
            'Mirza' => 'Mirza',
            'Miss Fajardose' => 'Miss Fajardose',
            'Mitr' => 'Mitr',
            'Modak' => 'Modak',
            'Modern Antiqua' => 'Modern Antiqua',
            'Mogra' => 'Mogra',
            'Molengo' => 'Molengo',
            'Molle' => 'Molle',
            'Monda' => 'Monda',
            'Monofett' => 'Monofett',
            'Monoton' => 'Monoton',
            'Monsieur La Doulaise' => 'Monsieur La Doulaise',
            'Montaga' => 'Montaga',
            'Montez' => 'Montez',
            'Montserrat' => 'Montserrat',
            'Montserrat Alternates' => 'Montserrat Alternates',
            'Montserrat Subrayada' => 'Montserrat Subrayada',
            'Moul' => 'Moul',
            'Moulpali' => 'Moulpali',
            'Mountains of Christmas' => 'Mountains of Christmas',
            'Mouse Memoirs' => 'Mouse Memoirs',
            'Mr Bedfort' => 'Mr Bedfort',
            'Mr Dafoe' => 'Mr Dafoe',
            'Mr De Haviland' => 'Mr De Haviland',
            'Mrs Saint Delafield' => 'Mrs Saint Delafield',
            'Mrs Sheppards' => 'Mrs Sheppards',
            'Mukta Vaani' => 'Mukta Vaani',
            'Muli' => 'Muli',
            'Mystery Quest' => 'Mystery Quest',
            'NTR' => 'NTR',
            'Neucha' => 'Neucha',
            'Neuton' => 'Neuton',
            'New Rocker' => 'New Rocker',
            'News Cycle' => 'News Cycle',
            'Niconne' => 'Niconne',
            'Nixie One' => 'Nixie One',
            'Nobile' => 'Nobile',
            'Nokora' => 'Nokora',
            'Norican' => 'Norican',
            'Nosifer' => 'Nosifer',
            'Nothing You Could Do' => 'Nothing You Could Do',
            'Noticia Text' => 'Noticia Text',
            'Noto Sans' => 'Noto Sans',
            'Noto Serif' => 'Noto Serif',
            'Nova Cut' => 'Nova Cut',
            'Nova Flat' => 'Nova Flat',
            'Nova Mono' => 'Nova Mono',
            'Nova Oval' => 'Nova Oval',
            'Nova Round' => 'Nova Round',
            'Nova Script' => 'Nova Script',
            'Nova Slim' => 'Nova Slim',
            'Nova Square' => 'Nova Square',
            'Numans' => 'Numans',
            'Nunito' => 'Nunito',
            'Odor Mean Chey' => 'Odor Mean Chey',
            'Offside' => 'Offside',
            'Old Standard TT' => 'Old Standard TT',
            'Oldenburg' => 'Oldenburg',
            'Oleo Script' => 'Oleo Script',
            'Oleo Script Swash Caps' => 'Oleo Script Swash Caps',
            'Open Sans' => 'Open Sans',
            'Open Sans Condensed' => 'Open Sans Condensed',
            'Oranienbaum' => 'Oranienbaum',
            'Orbitron' => 'Orbitron',
            'Oregano' => 'Oregano',
            'Orienta' => 'Orienta',
            'Original Surfer' => 'Original Surfer',
            'Oswald' => 'Oswald',
            'Over the Rainbow' => 'Over the Rainbow',
            'Overlock' => 'Overlock',
            'Overlock SC' => 'Overlock SC',
            'Ovo' => 'Ovo',
            'Oxygen' => 'Oxygen',
            'Oxygen Mono' => 'Oxygen Mono',
            'PT Mono' => 'PT Mono',
            'PT Sans' => 'PT Sans',
            'PT Sans Caption' => 'PT Sans Caption',
            'PT Sans Narrow' => 'PT Sans Narrow',
            'PT Serif' => 'PT Serif',
            'PT Serif Caption' => 'PT Serif Caption',
            'Pacifico' => 'Pacifico',
            'Palanquin' => 'Palanquin',
            'Palanquin Dark' => 'Palanquin Dark',
            'Paprika' => 'Paprika',
            'Parisienne' => 'Parisienne',
            'Passero One' => 'Passero One',
            'Passion One' => 'Passion One',
            'Pathway Gothic One' => 'Pathway Gothic One',
            'Patrick Hand' => 'Patrick Hand',
            'Patrick Hand SC' => 'Patrick Hand SC',
            'Pattaya' => 'Pattaya',
            'Patua One' => 'Patua One',
            'Pavanam' => 'Pavanam',
            'Paytone One' => 'Paytone One',
            'Peddana' => 'Peddana',
            'Peralta' => 'Peralta',
            'Permanent Marker' => 'Permanent Marker',
            'Petit Formal Script' => 'Petit Formal Script',
            'Petrona' => 'Petrona',
            'Philosopher' => 'Philosopher',
            'Piedra' => 'Piedra',
            'Pinyon Script' => 'Pinyon Script',
            'Pirata One' => 'Pirata One',
            'Plaster' => 'Plaster',
            'Play' => 'Play',
            'Playball' => 'Playball',
            'Playfair Display' => 'Playfair Display',
            'Playfair Display SC' => 'Playfair Display SC',
            'Podkova' => 'Podkova',
            'Poiret One' => 'Poiret One',
            'Poller One' => 'Poller One',
            'Poly' => 'Poly',
            'Pompiere' => 'Pompiere',
            'Pontano Sans' => 'Pontano Sans',
            'Poppins' => 'Poppins',
            'Port Lligat Sans' => 'Port Lligat Sans',
            'Port Lligat Slab' => 'Port Lligat Slab',
            'Pragati Narrow' => 'Pragati Narrow',
            'Prata' => 'Prata',
            'Preahvihear' => 'Preahvihear',
            'Press Start 2P' => 'Press Start 2P',
            'Pridi' => 'Pridi',
            'Princess Sofia' => 'Princess Sofia',
            'Prociono' => 'Prociono',
            'Prompt' => 'Prompt',
            'Prosto One' => 'Prosto One',
            'Proza Libre' => 'Proza Libre',
            'Puritan' => 'Puritan',
            'Purple Purse' => 'Purple Purse',
            'Quando' => 'Quando',
            'Quantico' => 'Quantico',
            'Quattrocento' => 'Quattrocento',
            'Quattrocento Sans' => 'Quattrocento Sans',
            'Questrial' => 'Questrial',
            'Quicksand' => 'Quicksand',
            'Quintessential' => 'Quintessential',
            'Qwigley' => 'Qwigley',
            'Racing Sans One' => 'Racing Sans One',
            'Radley' => 'Radley',
            'Rajdhani' => 'Rajdhani',
            'Rakkas' => 'Rakkas',
            'Raleway' => 'Raleway',
            'Raleway Dots' => 'Raleway Dots',
            'Ramabhadra' => 'Ramabhadra',
            'Ramaraja' => 'Ramaraja',
            'Rambla' => 'Rambla',
            'Rammetto One' => 'Rammetto One',
            'Ranchers' => 'Ranchers',
            'Rancho' => 'Rancho',
            'Ranga' => 'Ranga',
            'Rasa' => 'Rasa',
            'Rationale' => 'Rationale',
            'Ravi Prakash' => 'Ravi Prakash',
            'Redressed' => 'Redressed',
            'Reem Kufi' => 'Reem Kufi',
            'Reenie Beanie' => 'Reenie Beanie',
            'Revalia' => 'Revalia',
            'Rhodium Libre' => 'Rhodium Libre',
            'Ribeye' => 'Ribeye',
            'Ribeye Marrow' => 'Ribeye Marrow',
            'Righteous' => 'Righteous',
            'Risque' => 'Risque',
            'Roboto' => 'Roboto',
            'Roboto Condensed' => 'Roboto Condensed',
            'Roboto Mono' => 'Roboto Mono',
            'Roboto Slab' => 'Roboto Slab',
            'Rochester' => 'Rochester',
            'Rock Salt' => 'Rock Salt',
            'Rokkitt' => 'Rokkitt',
            'Romanesco' => 'Romanesco',
            'Ropa Sans' => 'Ropa Sans',
            'Rosario' => 'Rosario',
            'Rosarivo' => 'Rosarivo',
            'Rouge Script' => 'Rouge Script',
            'Rozha One' => 'Rozha One',
            'Rubik' => 'Rubik',
            'Rubik Mono One' => 'Rubik Mono One',
            'Rubik One' => 'Rubik One',
            'Ruda' => 'Ruda',
            'Rufina' => 'Rufina',
            'Ruge Boogie' => 'Ruge Boogie',
            'Ruluko' => 'Ruluko',
            'Rum Raisin' => 'Rum Raisin',
            'Ruslan Display' => 'Ruslan Display',
            'Russo One' => 'Russo One',
            'Ruthie' => 'Ruthie',
            'Rye' => 'Rye',
            'Sacramento' => 'Sacramento',
            'Sahitya' => 'Sahitya',
            'Sail' => 'Sail',
            'Salsa' => 'Salsa',
            'Sanchez' => 'Sanchez',
            'Sancreek' => 'Sancreek',
            'Sansita One' => 'Sansita One',
            'Sarala' => 'Sarala',
            'Sarina' => 'Sarina',
            'Sarpanch' => 'Sarpanch',
            'Satisfy' => 'Satisfy',
            'Scada' => 'Scada',
            'Scheherazade' => 'Scheherazade',
            'Schoolbell' => 'Schoolbell',
            'Scope One' => 'Scope One',
            'Seaweed Script' => 'Seaweed Script',
            'Secular One' => 'Secular One',
            'Sevillana' => 'Sevillana',
            'Seymour One' => 'Seymour One',
            'Shadows Into Light' => 'Shadows Into Light',
            'Shadows Into Light Two' => 'Shadows Into Light Two',
            'Shanti' => 'Shanti',
            'Share' => 'Share',
            'Share Tech' => 'Share Tech',
            'Share Tech Mono' => 'Share Tech Mono',
            'Shojumaru' => 'Shojumaru',
            'Short Stack' => 'Short Stack',
            'Shrikhand' => 'Shrikhand',
            'Siemreap' => 'Siemreap',
            'Sigmar One' => 'Sigmar One',
            'Signika' => 'Signika',
            'Signika Negative' => 'Signika Negative',
            'Simonetta' => 'Simonetta',
            'Sintony' => 'Sintony',
            'Sirin Stencil' => 'Sirin Stencil',
            'Six Caps' => 'Six Caps',
            'Skranji' => 'Skranji',
            'Slabo 13px' => 'Slabo 13px',
            'Slabo 27px' => 'Slabo 27px',
            'Slackey' => 'Slackey',
            'Smokum' => 'Smokum',
            'Smythe' => 'Smythe',
            'Sniglet' => 'Sniglet',
            'Snippet' => 'Snippet',
            'Snowburst One' => 'Snowburst One',
            'Sofadi One' => 'Sofadi One',
            'Sofia' => 'Sofia',
            'Sonsie One' => 'Sonsie One',
            'Sorts Mill Goudy' => 'Sorts Mill Goudy',
            'Source Code Pro' => 'Source Code Pro',
            'Source Sans Pro' => 'Source Sans Pro',
            'Source Serif Pro' => 'Source Serif Pro',
            'Space Mono' => 'Space Mono',
            'Special Elite' => 'Special Elite',
            'Spicy Rice' => 'Spicy Rice',
            'Spinnaker' => 'Spinnaker',
            'Spirax' => 'Spirax',
            'Squada One' => 'Squada One',
            'Sree Krushnadevaraya' => 'Sree Krushnadevaraya',
            'Sriracha' => 'Sriracha',
            'Stalemate' => 'Stalemate',
            'Stalinist One' => 'Stalinist One',
            'Stardos Stencil' => 'Stardos Stencil',
            'Stint Ultra Condensed' => 'Stint Ultra Condensed',
            'Stint Ultra Expanded' => 'Stint Ultra Expanded',
            'Stoke' => 'Stoke',
            'Strait' => 'Strait',
            'Sue Ellen Francisco' => 'Sue Ellen Francisco',
            'Suez One' => 'Suez One',
            'Sumana' => 'Sumana',
            'Sunshiney' => 'Sunshiney',
            'Supermercado One' => 'Supermercado One',
            'Sura' => 'Sura',
            'Suranna' => 'Suranna',
            'Suravaram' => 'Suravaram',
            'Suwannaphum' => 'Suwannaphum',
            'Swanky and Moo Moo' => 'Swanky and Moo Moo',
            'Syncopate' => 'Syncopate',
            'Tangerine' => 'Tangerine',
            'Taprom' => 'Taprom',
            'Tauri' => 'Tauri',
            'Taviraj' => 'Taviraj',
            'Teko' => 'Teko',
            'Telex' => 'Telex',
            'Tenali Ramakrishna' => 'Tenali Ramakrishna',
            'Tenor Sans' => 'Tenor Sans',
            'Text Me One' => 'Text Me One',
            'The Girl Next Door' => 'The Girl Next Door',
            'Tienne' => 'Tienne',
            'Tillana' => 'Tillana',
            'Timmana' => 'Timmana',
            'Tinos' => 'Tinos',
            'Titan One' => 'Titan One',
            'Titillium Web' => 'Titillium Web',
            'Trade Winds' => 'Trade Winds',
            'Trirong' => 'Trirong',
            'Trocchi' => 'Trocchi',
            'Trochut' => 'Trochut',
            'Trykker' => 'Trykker',
            'Tulpen One' => 'Tulpen One',
            'Ubuntu' => 'Ubuntu',
            'Ubuntu Condensed' => 'Ubuntu Condensed',
            'Ubuntu Mono' => 'Ubuntu Mono',
            'Ultra' => 'Ultra',
            'Uncial Antiqua' => 'Uncial Antiqua',
            'Underdog' => 'Underdog',
            'Unica One' => 'Unica One',
            'UnifrakturCook' => 'UnifrakturCook',
            'UnifrakturMaguntia' => 'UnifrakturMaguntia',
            'Unkempt' => 'Unkempt',
            'Unlock' => 'Unlock',
            'Unna' => 'Unna',
            'VT323' => 'VT323',
            'Vampiro One' => 'Vampiro One',
            'Varela' => 'Varela',
            'Varela Round' => 'Varela Round',
            'Vast Shadow' => 'Vast Shadow',
            'Vesper Libre' => 'Vesper Libre',
            'Vibur' => 'Vibur',
            'Vidaloka' => 'Vidaloka',
            'Viga' => 'Viga',
            'Voces' => 'Voces',
            'Volkhov' => 'Volkhov',
            'Vollkorn' => 'Vollkorn',
            'Voltaire' => 'Voltaire',
            'Waiting for the Sunrise' => 'Waiting for the Sunrise',
            'Wallpoet' => 'Wallpoet',
            'Walter Turncoat' => 'Walter Turncoat',
            'Warnes' => 'Warnes',
            'Wellfleet' => 'Wellfleet',
            'Wendy One' => 'Wendy One',
            'Wire One' => 'Wire One',
            'Work Sans' => 'Work Sans',
            'Yanone Kaffeesatz' => 'Yanone Kaffeesatz',
            'Yantramanav' => 'Yantramanav',
            'Yatra One' => 'Yatra One',
            'Yellowtail' => 'Yellowtail',
            'Yeseva One' => 'Yeseva One',
            'Yesteryear' => 'Yesteryear',
            'Yrsa' => 'Yrsa',
            'Zeyada' => 'Zeyada',
        ];
    }
    const OPTION_COOKIE_CONTENT_FONT = 'ocn_content_typo_font_family';

    function hasTargetClose()
    {
        return (get_theme_mod('ocn_target', 'button') == 'close');
    }
    function hasTargetButton()
    {
        return (get_theme_mod('ocn_target', 'button') == 'button');
    }
    /**
     * Enqueues a Google Font
     * @param array $fonts
     */
    static function enqueueGoogleFonts(array $fonts)
    {
        if (empty($fonts) == false && is_array($fonts)) {
            foreach ($fonts as $font) {
                // Make sure font is in our list of fonts
                if (isset(self::getGoogleFonts()[$font])) {
                    // Sanitize handle
                    $handle = trim($font);
                    $handle = strtolower($handle);
                    $handle = str_replace(' ', '-', $handle);
                    // Sanitize font name
                    $font = trim($font);
                    $font = str_replace(' ', '+', $font);
                    // Create Url
                    $url = '//fonts.googleapis.com/css?family=' . str_replace(' ', '%20', $font) . ':';
                    $weights = ['100', '200', '300', '400', '500', '600', '700', '800', '900'];
                    $url .= implode(',', $weights) . ',';
                    $italic_weights[] = ['100i', '200i', '300i', '400i', '500i', '600i', '700i', '800i', '900i'];
                    $url .= implode(',', $italic_weights);
                    $subsets = ['latin'];
                    $url .= '&amp;subset=' . implode(',', $subsets);
                    // Enqueue style
                    wp_enqueue_style('oceanwp-google-font-' . $handle, $url, false, false, 'all');
                }
            }
        }
    }
    public function handleCustomizerInit()
    {
        wp_enqueue_script('cookie-customizer', $this->uriToLibs . 'cookie-customizer.js',
            ['customize-preview'], false, true);
        wp_localize_script('cookie-customizer', 'ocn_cookie', [
            'googleFontsUrl' => '//fonts.googleapis.com',
            'googleFontsWeight' => '100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i',
        ]);
    }
    function handleCustomizerRegister(WP_Customize_Manager $customizer)
    {
        //Cookie: Section
        $customizer->add_section('ocn_section', [
            CustomizerSectionArgs::TITLE => __('Cookie Notice', WpApp::TEXT_DOMAIN),
            CustomizerSectionArgs::PRIORITY => 210,
        ]);
        //Cookie: Expiry
        $customizer->add_setting('ocn_expiry', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::STD => '1month',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeSelect'],
        ]);
        $customizer->add_control(new WP_Customize_Control($customizer, 'ocn_expiry', [
            CustomizerControlArgs::LABEL => __('Cookie Expiry', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::TYPE => CustomizerInputTypes::SELECT,
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_expiry',
            CustomizerControlArgs::PRIORITY => 10,
            CustomizerControlArgs::CHOICES => [
                '1hour' => __('An Hour', WpApp::TEXT_DOMAIN),
                '1day' => __('1 day', WpApp::TEXT_DOMAIN),
                '1week' => __('1 Week', WpApp::TEXT_DOMAIN),
                '1month' => __('1 Month', WpApp::TEXT_DOMAIN),
                '3months' => __('3 Months', WpApp::TEXT_DOMAIN),
                '6months' => __('6 Months', WpApp::TEXT_DOMAIN),
                '1year' => __('1 Year', WpApp::TEXT_DOMAIN),
                'infinity' => __('Infinity', WpApp::TEXT_DOMAIN),
            ],
        ]));
        //Cookie: Section Settings
        $customizer->add_setting('ocn_content', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::STD => __('By continuing to use this website, you consent to the use of cookies in accordance with our Cookie Policy.', WpApp::TEXT_DOMAIN),
            CustomizerSettingArgs::SANITITZE_CALLBACK => 'wp_kses_post',
        ]);
        //Cookie: Text Content
        $customizer->add_control(new CustomizerControlTextarea($customizer, 'ocn_content', [
            CustomizerControlArgs::LABEL => __('Content'),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_content',
            CustomizerControlArgs::PRIORITY => 10,
        ]));
    // Content Text: Style Heading
        $customizer->add_setting('ocn_styling_content_heading', [
            CustomizerSettingArgs::SANITITZE_CALLBACK => 'wp_kses',
        ]);
        $customizer->add_control(new CustomizerControlHeading($customizer, 'ocn_styling_content_heading', [
            CustomizerControlArgs::LABEL => __('Styling').':'.__('Content'),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::PRIORITY => 10,
        ]));
        // Content Text: Color
        $customizer->add_setting('ocn_text_color', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::STD => '#777777',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeColor'],
        ]);
        $customizer->add_control(new CustomizerControlColorPicker($customizer, 'ocn_text_color', [
            CustomizerControlArgs::LABEL => __('Text Color', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_text_color',
            CustomizerControlArgs::PRIORITY => 10,
        ]));
        // Content Text: Typography
        $customizer->add_setting(self::OPTION_COOKIE_CONTENT_FONT, [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::TEXT
        ]);
        $customizer->add_setting('ocn_content_typo_font_size', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::TEXT
        ]);

        $customizer->add_setting('ocn_content_typo_font_weight', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::KEY
        ]);
        $customizer->add_setting('ocn_content_typo_font_style', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::KEY
        ]);
        $customizer->add_setting('ocn_content_typo_transform', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::KEY
        ]);
        $customizer->add_setting('ocn_content_typo_line_height', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::TEXT
        ]);
        $customizer->add_setting('ocn_content_typo_spacing', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::TEXT
        ]);
        $customizer->add_control(new CustomizerControlTypography($customizer, 'ocn_content_typo', [
            CustomizerControlArgs::LABEL => __('Typography', 'ocean-portfolio'),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => [
                'family' => self::OPTION_COOKIE_CONTENT_FONT,
                'size' => 'ocn_content_typo_font_size',
                'weight' => 'ocn_content_typo_font_weight',
                'style' => 'ocn_content_typo_font_style',
                'transform' => 'ocn_content_typo_transform',
                'line_height' => 'ocn_content_typo_line_height',
                'spacing' => 'ocn_content_typo_spacing'
            ],
            CustomizerControlArgs::PRIORITY => 10,
            'l10n' => [],
        ]));
        //Cookie: Close target
        $customizer->add_setting('ocn_target', [
            CustomizerSettingArgs::STD => CustomizerInputTypes::BUTTON,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeSelect'],
        ]);
        //Cookie: Text Button
        $customizer->add_setting('ocn_button_text', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::STD => __('Accept', WpApp::TEXT_DOMAIN),
            CustomizerSettingArgs::SANITITZE_CALLBACK => 'wp_kses_post',
        ]);
        $customizer->add_control(new WP_Customize_Control($customizer, 'ocn_button_text', [
            CustomizerControlArgs::LABEL => __('Button Text', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::TYPE => CustomizerInputTypes::TEXT,
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_button_text',
            CustomizerControlArgs::PRIORITY => 10,
            'active_callback' => [$this, 'hasTargetButton'],
        ]));
        $customizer->add_control(new WP_Customize_Control($customizer, 'ocn_target', [
            CustomizerControlArgs::LABEL => __('Close Target', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::TYPE => CustomizerInputTypes::SELECT,
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_target',
            CustomizerControlArgs::PRIORITY => 10,
            CustomizerControlArgs::CHOICES => [
                'button' => __('Button'),
                'close' => __('Close Icon', WpApp::TEXT_DOMAIN),
            ],
        ]));
        // Button: Heading
        $customizer->add_setting('ocn_styling_button_heading', [
            CustomizerSettingArgs::SANITITZE_CALLBACK => 'wp_kses',
        ]);
        $customizer->add_control(new CustomizerControlHeading($customizer, 'ocn_styling_button_heading', [
            CustomizerControlArgs::LABEL => __('Styling').':'.__('Button'),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::PRIORITY => 10,
            'active_callback' => [$this, 'hasTargetButton'],
        ]));
        // Button: Background Color
        $customizer->add_setting('ocn_btn_background', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::STD => '#13aff0',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeColor'],
        ]);
        $customizer->add_control(new CustomizerControlColorPicker($customizer, 'ocn_btn_background', [
            CustomizerControlArgs::LABEL => __('Background Color', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_btn_background',
            CustomizerControlArgs::PRIORITY => 10,
            'active_callback' => [$this, 'hasTargetButton'],
        ]));
        // Button: Color
        $customizer->add_setting('ocn_btn_color', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::STD => '#ffffff',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeColor'],
        ]);
        $customizer->add_control(new CustomizerControlColorPicker($customizer, 'ocn_btn_color', [
            CustomizerControlArgs::LABEL => __('Color', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_btn_color',
            CustomizerControlArgs::PRIORITY => 10,
            'active_callback' => [$this, 'hasTargetButton'],
        ]));
        // Button: Hover Background
        $customizer->add_setting('ocn_btn_hover_background', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::STD => '#0b7cac',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeColor'],
        ]);
        $customizer->add_control(new CustomizerControlColorPicker($customizer, 'ocn_btn_hover_background', [
            CustomizerControlArgs::LABEL => __('Hover: Background Color', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_btn_hover_background',
            CustomizerControlArgs::PRIORITY => 10,
            'active_callback' => [$this, 'hasTargetButton'],
        ]));
        // Button: Hover color
        $customizer->add_setting('ocn_btn_hover_color', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::STD => '#ffffff',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeColor'],
        ]);
        $customizer->add_control(new CustomizerControlColorPicker($customizer, 'ocn_btn_hover_color', [
            CustomizerControlArgs::LABEL => __('Hover: Color', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_btn_hover_color',
            CustomizerControlArgs::PRIORITY => 10,
            'active_callback' => [$this, 'hasTargetButton'],
        ]));
        // Close Icon: Heading
        $customizer->add_setting('ocn_styling_close_heading', [
            CustomizerSettingArgs::SANITITZE_CALLBACK => 'wp_kses',
        ]);
        $customizer->add_control(new CustomizerControlHeading($customizer, 'ocn_styling_close_heading', [
            CustomizerControlArgs::LABEL => __('Styling').':'.__('Close Icon'),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::PRIORITY => 10,
            'active_callback' => [$this, 'hasTargetClose'],
        ]));
        // Close Icon: Color
        $customizer->add_setting('ocn_close_color', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::STD => '#777777',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeColor'],
        ]);
        $customizer->add_control(new CustomizerControlColorPicker($customizer, 'ocn_close_color', [
            CustomizerControlArgs::LABEL => __('Color'),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_close_color',
            CustomizerControlArgs::PRIORITY => 10,
            'active_callback' => [$this, 'hasTargetClose'],
        ]));
        // Close Icon: Hover color
        $customizer->add_setting('ocn_close_hover_color', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::STD => '#333333',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeColor'],
        ]);
        $customizer->add_control(new CustomizerControlColorPicker($customizer, 'ocn_close_hover_color', [
            CustomizerControlArgs::LABEL => __('Hover: Color', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_close_hover_color',
            CustomizerControlArgs::PRIORITY => 10,
            'active_callback' => [$this, 'hasTargetClose'],
        ]));
        //Cookie: Popup Style
        $customizer->add_setting('ocn_style', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::STD => 'flyin',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeSelect'],
        ]);
        $customizer->add_control(new WP_Customize_Control($customizer, 'ocn_style', [
            CustomizerControlArgs::LABEL => __('Container Type', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::TYPE => CustomizerInputTypes::SELECT,
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_style',
            CustomizerControlArgs::PRIORITY => 10,
            CustomizerControlArgs::CHOICES => [
                'flyin' => __('Fly-Ins', WpApp::TEXT_DOMAIN),
                'floating' => __('Floating Bar', WpApp::TEXT_DOMAIN),
            ],
        ]));
        //Popup: Style Heading
        $customizer->add_setting('ocn_styling_general_heading', [
            CustomizerSettingArgs::SANITITZE_CALLBACK => 'wp_kses',
        ]);
        $customizer->add_control(new CustomizerControlHeading($customizer, 'ocn_styling_general_heading', [
            CustomizerControlArgs::LABEL => __('Styling').':'.__('Container'),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::PRIORITY => 10,
        ]));
        //Popup: Max Width
        $customizer->add_setting('ocn_width', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_control(new WP_Customize_Control($customizer, 'ocn_width', [
            CustomizerControlArgs::LABEL => __('Max Width (px)', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::TYPE => CustomizerInputTypes::NUMBER,
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_width',
            CustomizerControlArgs::PRIORITY => 10,
            'input_attrs' => [
                'min' => 100,
                'step' => 1,
            ],
        ]));
        //Popup: Padding
        $customizer->add_setting('ocn_top_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumber'],
        ]);
        $customizer->add_setting('ocn_right_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumber'],
        ]);
        $customizer->add_setting('ocn_bottom_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumber'],
        ]);
        $customizer->add_setting('ocn_left_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumber'],
        ]);
        $customizer->add_setting('ocn_tablet_top_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_setting('ocn_tablet_right_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_setting('ocn_tablet_bottom_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_setting('ocn_tablet_left_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_setting('ocn_mobile_top_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_setting('ocn_mobile_right_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_setting('ocn_mobile_bottom_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_setting('ocn_mobile_left_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_control(new CustomizerControlDimensions($customizer, 'ocn_padding_dimensions', [
            CustomizerControlArgs::LABEL => __('Padding (px)', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => [
                'desktop_top' => 'ocn_top_padding',
                'desktop_right' => 'ocn_right_padding',
                'desktop_bottom' => 'ocn_bottom_padding',
                'desktop_left' => 'ocn_left_padding',
                'tablet_top' => 'ocn_tablet_top_padding',
                'tablet_right' => 'ocn_tablet_right_padding',
                'tablet_bottom' => 'ocn_tablet_bottom_padding',
                'tablet_left' => 'ocn_tablet_left_padding',
                'mobile_top' => 'ocn_mobile_top_padding',
                'mobile_right' => 'ocn_mobile_right_padding',
                'mobile_bottom' => 'ocn_mobile_bottom_padding',
                'mobile_left' => 'ocn_mobile_left_padding',
            ],
            CustomizerControlArgs::PRIORITY => 10,
            'input_attrs' => [
                'min' => 0,
                'max' => 300,
                'step' => 1,
            ],
        ]));
        //Popup: Background Color
        $customizer->add_setting('ocn_background', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::STD => '#ffffff',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeColor'],
        ]);
        $customizer->add_control(new CustomizerControlColorPicker($customizer, 'ocn_background', [
            CustomizerControlArgs::LABEL => __('Background Color', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_background',
            CustomizerControlArgs::PRIORITY => 10,
        ]));
        //Popup: Border Width
        $customizer->add_setting('ocn_border_width', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_control(new WP_Customize_Control($customizer, 'ocn_border_width', [
            CustomizerControlArgs::LABEL => __('Border Width (px)', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::TYPE => CustomizerInputTypes::NUMBER,
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_border_width',
            CustomizerControlArgs::PRIORITY => 10,
            'input_attrs' => [
                'min' => 100,
                'step' => 1,
            ],
        ]));
        //Popup: Border Style
        $customizer->add_setting('ocn_border_style', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::STD => 'none',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeSelect'],
        ]);
        $customizer->add_control(new WP_Customize_Control($customizer, 'ocn_border_style', [
            CustomizerControlArgs::LABEL => __('Border Style', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::TYPE => CustomizerInputTypes::SELECT,
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_border_style',
            CustomizerControlArgs::PRIORITY => 10,
            CustomizerControlArgs::CHOICES => [
                'none' => __('None', WpApp::TEXT_DOMAIN),
                'solid' => __('Solid', WpApp::TEXT_DOMAIN),
                'double' => __('Double', WpApp::TEXT_DOMAIN),
                'dotted' => __('Dotted', WpApp::TEXT_DOMAIN),
                'dashed' => __('Dashed', WpApp::TEXT_DOMAIN),
                'groove' => __('Groove', WpApp::TEXT_DOMAIN),
            ],
        ]));
        //Popup: Border Color
        $customizer->add_setting('ocn_border_color', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeColor'],
        ]);
        $customizer->add_control(new CustomizerControlColorPicker($customizer, 'ocn_border_color', [
            CustomizerControlArgs::LABEL => __('Border Color', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_border_color',
            CustomizerControlArgs::PRIORITY => 10,
        ]));

        //Cookie: Scripts Heading
        $customizer->add_setting('cookie-scripts-heading', [
            CustomizerSettingArgs::SANITITZE_CALLBACK => 'wp_kses',
        ]);
        $customizer->add_control(new CustomizerControlHeading($customizer, 'cookie-scripts-heading', [
            CustomizerControlArgs::LABEL => __('Scripts', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::PRIORITY => 10,
        ]));
        //Cookie: Scripts Content Head
        $customizer->add_setting('ocn_head_scripts', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => false,
        ]);
        $customizer->add_control(new CustomizerControlTextarea($customizer, 'ocn_head_scripts', [
            CustomizerControlArgs::LABEL => __('Head (before the closing head tag)', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::DESCRIPTION => __('Add cookies JavaScript code here, they will be used after users consent.', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_head_scripts',
            CustomizerControlArgs::PRIORITY => 10,
        ]));
        //Cookie: Scripts Content Body
        $customizer->add_setting('ocn_body_scripts', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => false,
        ]);
        $customizer->add_control(new CustomizerControlTextarea($customizer, 'ocn_body_scripts', [
            CustomizerControlArgs::LABEL => __('Body (before the closing body tag)', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::DESCRIPTION => __('Add cookies JavaScript code here, they will be used after users consent.', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_body_scripts',
            CustomizerControlArgs::PRIORITY => 10,
        ]));
        //Cookie: Reloading
        $customizer->add_setting('ocn_reload', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::STD => 'no',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeSelect'],
        ]);
        $customizer->add_control(new CustomizerControlButtonset($customizer, 'ocn_reload', [
            CustomizerControlArgs::LABEL => __('Reloading', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::DESCRIPTION => __('Reload the page after the user consent.', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_reload',
            CustomizerControlArgs::PRIORITY => 10,
            CustomizerControlArgs::CHOICES => [
                'yes' => __('Yes'),
                'no' => __('No'),
            ],
        ]));
        //Cookie: Overlay
        $customizer->add_setting('ocn_overlay', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::STD => 'no',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeSelect'],
        ]);
        $customizer->add_control(new CustomizerControlButtonset($customizer, 'ocn_overlay', [
            CustomizerControlArgs::LABEL => __('Display Overlay', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::DESCRIPTION => __('Display an overlay to force the user to consent to your cookies policy.', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_overlay',
            CustomizerControlArgs::PRIORITY => 10,
            CustomizerControlArgs::CHOICES => [
                'yes' => __('Yes'),
                'no' => __('No'),
            ],
        ]));
    }
    function isCookieAccepted()
    {
        return isset($_COOKIE['ocn_accepted']) && $_COOKIE['ocn_accepted'] === 'true';
    }
    function getAllowedHtml()
    {
        // Get allowed script blocking HTML.
        return array_merge(wp_kses_allowed_html('post'), [
            'script' => [
                'type' => [],
                'src' => [],
                'charset' => [],
                'async' => []
            ],
            'noscript' => [],
            'style' => [
                'types' => []
            ],
            'iframe' => [
                'src' => [],
                'height' => [],
                'width' => [],
                'frameborder' => [],
                'allowfullscreen' => []
            ]
        ]);
    }
    function getPaddingCss($top, $right, $bottom, $left)
    {
        $paddingTop = $paddingRight = $paddingBottom = $paddingLeft = '0';
        if (empty($top) == false) {
            $paddingTop = intval($top);
        }
        if (empty($right) == false) {
            $paddingRight = intval($right);
        }
        if (empty($bottom) == false) {
            $paddingBottom = intval($bottom);
        }
        if (empty($left) == false) {
            $paddingLeft = intval($left);
        }
        return "{$paddingTop}px {$paddingRight}px {$paddingBottom}px {$paddingLeft}px";
    }
    function handleHeadCss()
    {
        // Width
        $css = '';
        $width = get_theme_mod('ocn_width');
        if (!empty($width)) {
            $css .= '#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating #ocn-cookie-inner{width:' . $width . 'px;}';
        }
        // Padding
        $top_padding = get_theme_mod('ocn_top_padding');
        $right_padding = get_theme_mod('ocn_right_padding');
        $bottom_padding = get_theme_mod('ocn_bottom_padding');
        $left_padding = get_theme_mod('ocn_left_padding');
        if (isset($top_padding) && '' != $top_padding
            || isset($right_padding) && '' != $right_padding
            || isset($bottom_padding) && '' != $bottom_padding
            || isset($left_padding) && '' != $left_padding) {
            $css .= '#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating{padding:' . $this->getPaddingCss($top_padding, $right_padding, $bottom_padding, $left_padding) . '}';
        }
        // Tablet padding
        $tablet_top_padding = get_theme_mod('ocn_tablet_top_padding');
        $tablet_right_padding = get_theme_mod('ocn_tablet_right_padding');
        $tablet_bottom_padding = get_theme_mod('ocn_tablet_bottom_padding');
        $tablet_left_padding = get_theme_mod('ocn_tablet_left_padding');
        if (isset($tablet_top_padding) && '' != $tablet_top_padding
            || isset($tablet_right_padding) && '' != $tablet_right_padding
            || isset($tablet_bottom_padding) && '' != $tablet_bottom_padding
            || isset($tablet_left_padding) && '' != $tablet_left_padding) {
            $css .= '@media (max-width: 768px){#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating{padding:' . $this->getPaddingCss($tablet_top_padding, $tablet_right_padding, $tablet_bottom_padding, $tablet_left_padding) . '}}';
        }
        // Mobile padding
        $mobile_top_padding = get_theme_mod('ocn_mobile_top_padding');
        $mobile_right_padding = get_theme_mod('ocn_mobile_right_padding');
        $mobile_bottom_padding = get_theme_mod('ocn_mobile_bottom_padding');
        $mobile_left_padding = get_theme_mod('ocn_mobile_left_padding');
        if (isset($mobile_top_padding) && '' != $mobile_top_padding
            || isset($mobile_right_padding) && '' != $mobile_right_padding
            || isset($mobile_bottom_padding) && '' != $mobile_bottom_padding
            || isset($mobile_left_padding) && '' != $mobile_left_padding) {
            $css .= '@media (max-width: 480px){#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating{padding:' . $this->getPaddingCss($mobile_top_padding, $mobile_right_padding, $mobile_bottom_padding, $mobile_left_padding) . '}}';
        }
        // Add background
        $background = get_theme_mod('ocn_background', '#ffffff');
        if (!empty($background) && '#ffffff' != $background) {
            $css .= "#ocn-cookie-wrap{background-color: {$background};}";
        }
        // Add border width
        $border_width = get_theme_mod('ocn_border_width');
        if (!empty($border_width)) {
            $css .= "#ocn-cookie-wrap{border-width: {$border_width}px;}";
        }
        // Add border style
        $border_style = get_theme_mod('ocn_border_style', 'none');
        if (!empty($border_style) && 'none' != $border_style) {
            $css .= "#ocn-cookie-wrap{border-style: {$border_style};}";
        }
        // Add border color
        $border_color = get_theme_mod('ocn_border_color');
        if (!empty($border_color)) {
            $css .= "#ocn-cookie-wrap{border-color: {$border_color};}";
        }
        // Add color
        $text_color = get_theme_mod('ocn_text_color', '#777777');
        if (!empty($text_color) && '#777777' != $text_color) {
            $css .= "#ocn-cookie-wrap{color: {$text_color};}";
        }
        // Button: Background
        $btn_background = get_theme_mod('ocn_btn_background', '#13aff0');
        if (!empty($btn_background) && '#13aff0' != $btn_background) {
            $css .= "#ocn-cookie-wrap .ocn-btn{background-color: {$btn_background};}";
        }
        // Button: Color
        $btn_color = get_theme_mod('ocn_btn_color', '#ffffff');
        if (!empty($btn_color) && '#ffffff' != $btn_color) {
            $css .= "#ocn-cookie-wrap .ocn-btn{color: {$btn_color};}";
        }
        // Button: Hover background
        $btn_hover_background = get_theme_mod('ocn_btn_hover_background', '#0b7cac');
        if (!empty($btn_hover_background) && '#13aff0' != $btn_hover_background) {
            $css .= "#ocn-cookie-wrap .ocn-btn:hover{background-color: {$btn_hover_background};}";
        }
        // Button: Hover color
        $btn_hover_color = get_theme_mod('ocn_btn_hover_color', '#ffffff');
        if (!empty($btn_hover_color) && '#ffffff' != $btn_hover_color) {
            $css .= "#ocn-cookie-wrap .ocn-btn:hover{color: {$btn_hover_color};}";
        }
        // Close Icon: Color
        $close_color = get_theme_mod('ocn_close_color', '#777');
        if (!empty($close_color) && '#777777' != $close_color) {
            $css .= "#ocn-cookie-wrap .ocn-icon svg{fill: {$close_color} ;}";
        }
        // Close Icon: Hover color
        $close_hover_color = get_theme_mod('ocn_close_hover_color', '#333');
        if (!empty($close_hover_color) && '#333333' != $close_hover_color) {
            $css .= "#ocn-cookie-wrap .ocn-icon:hover svg{fill: {$close_hover_color} ;}";
        }
        // Content Text: Typography
        $contentTextTypography = '';
        // Content Text: Font family
        $text_font_family = get_theme_mod(self::OPTION_COOKIE_CONTENT_FONT);
        if (empty($text_font_family) == false) {
            $contentTextTypography .= "font-family: {$text_font_family};";
        }
        // Content Text: Font size
        $text_font_size = get_theme_mod('ocn_content_typo_font_size');
        if (empty($text_font_size) == false) {
            $contentTextTypography .= "font-size: {$text_font_size};";
        }
        // Content Text: Font weight
        $text_font_weight = get_theme_mod('ocn_content_typo_font_weight');
        if (empty($text_font_weight) == false) {
            $contentTextTypography .= "font-weight: {$text_font_weight};";
        }
        // Content Text: Font style
        $text_font_style = get_theme_mod('ocn_content_typo_font_style');
        if (empty($text_font_style) == false) {
            $contentTextTypography .= "font-style: {$text_font_style};";
        }
        // Content Text: Transform
        $text_text_transform = get_theme_mod('ocn_content_typo_transform');
        if (empty($text_text_transform) == false) {
            $contentTextTypography .= "text-transform: {$text_text_transform};";
        }
        // Content Text: Line height
        $text_line_height = get_theme_mod('ocn_content_typo_line_height');
        if (empty($text_line_height) == false) {
            $contentTextTypography .= "line-height: {$text_line_height};";
        }
        // Content Text: Letter spacing
        $text_letter_spacing = get_theme_mod('ocn_content_typo_spacing');
        if (empty($text_letter_spacing) == false) {
            $contentTextTypography .= "letter-spacing: {$text_letter_spacing};";
        }

        if (empty($contentTextTypography) == false) {
            $css .= "#ocn-cookie-wrap .ocn-cookie-content{ {$contentTextTypography} }";
        }
        // Minify and output CSS in the wp_head
        if (empty($css) == false) {
            // Normalize whitespace
            $css = preg_replace('/\s+/', ' ', $css);
            // Remove ; before }
            $css = preg_replace('/;(?=\s*})/', '', $css);
            // Remove space after , : ; { } */ >
            $css = preg_replace('/(,|:|;|\{|}|\*\/|>) /', '$1', $css);
            // Remove space before , ; { }
            $css = preg_replace('/ (,|;|\{|})/', '$1', $css);
            // Strips leading 0 on decimal values (converts 0.5px into .5px)
            $css = preg_replace('/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $css);
            // Strips units if value is 0 (converts 0px to 0)
            $css = preg_replace('/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $css);
            // Trim
            $css = trim($css);
            //Normalize
            $output = wp_strip_all_tags($css);
            echo "<style type='text/css' id='cookieNotice'>{$output}</style>";
        }
    }
    function handleHeadScripts()
    {
        if ($this->isCookieAccepted()) {
            $scripts = html_entity_decode(trim(wp_kses(get_theme_mod('ocn_head_scripts'), $this->getAllowedHtml())));
            if (empty($scripts) == false) {
                echo $scripts;
            }
        }
    }
    function handleFooter()
    {
        $content = get_theme_mod('ocn_content', __('By continuing to use this website, you consent to the use of cookies in accordance with our Cookie Policy.', WpApp::TEXT_DOMAIN));
        $content = do_shortcode($content);
        $style = get_theme_mod('ocn_style', 'flyin');
        if (!$style) {
            $style = 'flyin';
        }
        $contentButton = '';
        $contentButtonClose = '';
        if (get_theme_mod('ocn_target', 'button') === 'button') {
            $buttonText = get_theme_mod('ocn_button_text', __('Accept'));
            $contentButton = "<a class='button ocn-btn ocn-close' href='#'>{$buttonText}</a>";
        } else {
            $contentButtonClose = "<a class='ocn-icon ocn-close' href='#'>
        <svg version='1.1' xmlns='http://www.w3.org/2000/svg' x='0px' y='0px' viewBox='0 0 512 512' xml:space='preserve'>
            <g><g><path d='M505.943,6.058c-8.077-8.077-21.172-8.077-29.249,0L6.058,476.693c-8.077,8.077-8.077,21.172,0,29.249
                        C10.096,509.982,15.39,512,20.683,512c5.293,0,10.586-2.019,14.625-6.059L505.943,35.306
                        C514.019,27.23,514.019,14.135,505.943,6.058z'/></g></g>
            <g><g><path d='M505.942,476.694L35.306,6.059c-8.076-8.077-21.172-8.077-29.248,0c-8.077,8.076-8.077,21.171,0,29.248l470.636,470.636
                        c4.038,4.039,9.332,6.058,14.625,6.058c5.293,0,10.587-2.019,14.624-6.057C514.018,497.866,514.018,484.771,505.942,476.694z'/></g></g>
        </svg></a>";
        }
        // Overlay
        $contentOverlay = '';
        if (get_theme_mod('ocn_overlay', 'no') != 'no') {
            $contentOverlay = '<div id="ocn-cookie-overlay"></div>';
        }
        echo "{$contentOverlay}<div id='ocn-cookie-wrap' class='{$style}'>{$contentButtonClose}
        <div id='ocn-cookie-inner'><p class='ocn-cookie-content'>{$content}</p>{$contentButton}</div></div>";
    }
    function handleFooterPrintScripts()
    {
        if ($this->isCookieAccepted()) {
            $scripts = html_entity_decode(trim(wp_kses(get_theme_mod('ocn_body_scripts'), $this->getAllowedHtml())));
            if (empty($scripts) == false) {
                echo $scripts;
            }
        }
    }
}