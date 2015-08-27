<?php

namespace Library\Helpers;

/**
 * UserAgentDetector
 * 
 * Use following to update $crawlers
 *  - http://www.robotstxt.org/db.html 
 *  - http://www.user-agents.org/index.shtml
 *  - http://www.user-agents.org/allagents.xml
 * 
 * Based on 
 *  - Exadra37/UserAgentDetector 
 *  - JayBizzle/Crawler-Detect
 */
class UserAgentDetector
{

    /**
     * $userAgent
     *     - name of the user agent if present in the global var $_SERVER
     *
     * @var mixed
     * @access protected
     */
    protected $userAgent = null;
    
    /**
     * @var type 
     */
    protected $httpHeaders = array();
    
    /**
     * @var type 
     */
    protected $matches = array();

    /**
     * $crawlers
     *     - List of Crawler, bots, spiders
     *
     * @var array
     * @access protected
     */    
    protected static $crawlers = array(
        "abot",
        "dbot",
        "ebot",
        "hbot",
        "kbot",
        "lbot",
        "mbot",
        "nbot",
        "obot",
        "pbot",
        "rbot",
        "sbot",
        "tbot",
        "vbot",
        "ybot",
        "zbot",
        "bot.",
        "bot\/",
        "_bot",
        ".bot",
        "\/bot",
        "-bot",
        ":bot",
        "\(bot",
        "crawl",
        "slurp",
        "spider",
        "seek",
        "accoona",
        "acoon",
        "adressendeutschland",
        "ah-ha.com",
        "ahoy",
        "altavista",
        "ananzi",
        "anthill",
        "appie",
        "arachnophilia",
        "arale",
        "araneo",
        "aranha",
        "architext",
        "aretha",
        "arks",
        "asterias",
        "atlocal",
        "atn",
        "atomz",
        "augurfind",
        "backrub",
        "bannana_bot",
        "baypup",
        "bdfetch",
        "big brother",
        "biglotron",
        "bjaaland",
        "blackwidow",
        "blaiz",
        "blog",
        "blo.",
        "bloodhound",
        "boitho",
        "booch",
        "bradley",
        "butterfly",
        "calif",
        "cassandra",
        "ccubee",
        "cfetch",
        "charlotte",
        "churl",
        "cienciaficcion",
        "cmc",
        "collective",
        "comagent",
        "combine",
        "computingsite",
        "csci",
        "curl",
        "cusco",
        "daumoa",
        "deepindex",
        "delorie",
        "depspid",
        "deweb",
        "die blinde kuh",
        "digger",
        "ditto",
        "dmoz",
        "docomo",
        "download express",
        "dtaagent",
        "dwcp",
        "ebiness",
        "ebingbong",
        "e-collector",
        "ejupiter",
        "emacs-w3 search engine",
        "esther",
        "evliya celebi",
        "ezresult",
        "falcon",
        "felix ide",
        "ferret",
        "fetchrover",
        "fido",
        "findlinks",
        "fireball",
        "fish search",
        "fouineur",
        "funnelweb",
        "gazz",
        "gcreep",
        "genieknows",
        "getterroboplus",
        "geturl",
        "glx",
        "goforit",
        "golem",
        "grabber",
        "grapnel",
        "gralon",
        "griffon",
        "gromit",
        "grub",
        "gulliver",
        "hamahakki",
        "harvest",
        "havindex",
        "helix",
        "heritrix",
        "hku www octopus",
        "homerweb",
        "htdig",
        "html index",
        "html_analyzer",
        "htmlgobble",
        "hubater",
        "hyper-decontextualizer",
        "ia_archiver",
        "ibm_planetwide",
        "ichiro",
        "iconsurf",
        "iltrovatore",
        "image.kapsi.net",
        "imagelock",
        "incywincy",
        "indexer",
        "infobee",
        "informant",
        "ingrid",
        "inktomisearch.com",
        "inspector web",
        "intelliagent",
        "internet shinchakubin",
        "ip3000",
        "iron33",
        "israeli-search",
        "ivia",
        "jack",
        "jakarta",
        "javabee",
        "jetbot",
        "jumpstation",
        "katipo",
        "kdd-explorer",
        "kilroy",
        "knowledge",
        "kototoi",
        "kretrieve",
        "labelgrabber",
        "lachesis",
        "larbin",
        "legs",
        "libwww",
        "linkalarm",
        "link validator",
        "linkscan",
        "lockon",
        "lwp",
        "lycos",
        "magpie",
        "mantraagent",
        "mapoftheinternet",
        "marvin\/",
        "mattie",
        "mediafox",
        "mediapartners",
        "mercator",
        "merzscope",
        "microsoft url control",
        "minirank",
        "miva",
        "mj12",
        "mnogosearch",
        "moget",
        "monster",
        "moose",
        "motor",
        "multitext",
        "muncher",
        "muscatferret",
        "mwd.search",
        "myweb",
        "najdi",
        "nameprotect",
        "nationaldirectory",
        "nazilla",
        "ncsa beta",
        "nec-meshexplorer",
        "nederland.zoek",
        "netcarta webmap engine",
        "netmechanic",
        "netresearchserver",
        "netscoop",
        "newscan-online",
        "nhse",
        "nokia6682\/",
        "nomad",
        "noyona",
        "nutch",
        "nzexplorer",
        "objectssearch",
        "occam",
        "omni",
        "open text",
        "openfind",
        "openintelligencedata",
        "orb search",
        "osis-project",
        "pack rat",
        "pageboy",
        "pagebull",
        "page_verifier",
        "panscient",
        "parasite",
        "partnersite",
        "patric",
        "pear.",
        "pegasus",
        "peregrinator",
        "pgp key agent",
        "phantom",
        "phpdig",
        "picosearch",
        "piltdownman",
        "pimptrain",
        "pinpoint",
        "pioneer",
        "piranha",
        "plumtreewebaccessor",
        "pogodak",
        "poirot",
        "pompos",
        "poppelsdorf",
        "poppi",
        "popular iconoclast",
        "psycheclone",
        "publisher",
        "python",
        "rambler",
        "raven search",
        "roach",
        "road runner",
        "roadhouse",
        "robbie",
        "robofox",
        "robozilla",
        "rules",
        "salty",
        "sbider",
        "scooter",
        "scoutjet",
        "scrubby",
        "search.",
        "searchprocess",
        "semanticdiscovery",
        "senrigan",
        "sg-scout",
        "shai'hulud",
        "shark",
        "shopwiki",
        "sidewinder",
        "sift",
        "silk",
        "simmany",
        "site searcher",
        "site valet",
        "sitetech-rover",
        "skymob.com",
        "sleek",
        "smartwit",
        "sna-",
        "snappy",
        "snooper",
        "sohu",
        "speedfind",
        "sphere",
        "sphider",
        "spinner",
        "spyder",
        "steeler\/",
        "suke",
        "suntek",
        "supersnooper",
        "surfnomore",
        "sven",
        "sygol",
        "szukacz",
        "tach black widow",
        "tarantula",
        "templeton",
        "\/teoma",
        "t-h-u-n-d-e-r-s-t-o-n-e",
        "theophrastus",
        "titan",
        "titin",
        "tkwww",
        "toutatis",
        "t-rex",
        "tutorgig",
        "twiceler",
        "twisted",
        "ucsd",
        "udmsearch",
        "url check",
        "updated",
        "vagabondo",
        "valkyrie",
        "verticrawl",
        "victoria",
        "vision-search",
        "volcano",
        "voyager\/",
        "voyager-hc",
        "w3c_validator",
        "w3m2",
        "w3mir",
        "walker",
        "wallpaper",
        "wanderer",
        "wauuu",
        "wavefire",
        "web core",
        "web hopper",
        "web wombat",
        "webbandit",
        "webcatcher",
        "webcopy",
        "webfoot",
        "weblayers",
        "weblinker",
        "weblog monitor",
        "webmirror",
        "webmonkey",
        "webquest",
        "webreaper",
        "websitepulse",
        "websnarf",
        "webstolperer",
        "webvac",
        "webwalk",
        "webwatch",
        "webwombat",
        "webzinger",
        "wget",
        "whizbang",
        "whowhere",
        "wild ferret",
        "worldlight",
        "wwwc",
        "wwwster",
        "xenu",
        "xget",
        "xift",
        "xirq",
        "yandex",
        "yanga",
        "yeti",
        "yodao",
        "zao\/",
        "zippp",
        "zyborg",
        // Based on JayBizzle/Crawler-Detect
        "007ac9 Crawler",
        "008\/",
        "360Spider",
        "A6-Indexer",
        "ABACHOBot",
        "AbiLogicBot",
        "Aboundex",
        "Accoona-AI-Agent",
        "AddSugarSpiderBot",
        "AddThis",
        "Adidxbot",
        "ADmantX",
        "AdvBot",
        "ahrefsbot",
        "aihitbot",
        "Airmail",
        "AISearchBot",
        "Anemone",
        "antibot",
        "AnyApexBot",
        "Applebot",
        "arabot",
        "Arachmo",
        "archive-com",
        "archive.org_bot",
        "B-l-i-t-z-B-O-T",
        "backlinkcrawler",
        "baiduspider",
        "BecomeBot",
        "BeslistBot",
        "bibnum\.bnf",
        "BillyBobBot",
        "Bimbot",
        "bingbot",
        "binlar",
        "blekkobot",
        "blexbot",
        "BlitzBOT",
        "bl\.uk_lddc_bot",
        "bnf\.fr_bot",
        "boitho\.com-dc",
        "boitho\.com-robot",
        "brainobot",
        "btbot",
        "BUbiNG",
        "Butterfly\/",
        "buzzbot",
        "careerbot",
        "CatchBot",
        "CC Metadata Scaper",
        "ccbot",
        "Cerberian Drtrs",
        "changedetection",
        "CloudFlare-AlwaysOnline",
        "citeseerxbot",
        "coccoc",
        "classbot",
        "Commons-HttpClient",
        "content crawler spider",
        "Content Crawler",
        "convera",
        "ConveraCrawler",
        "CoPubbot",
        "cosmos",
        "Covario-IDS",
        "CrawlBot",
        "crawler4j",
        "CrystalSemanticsBot",
        "cXensebot",
        "CyberPatrol",
        "DataparkSearch",
        "dataprovider",
        "DiamondBot",
        "Digg",
        "discobot",
        "DomainAppender",
        "domaincrawler",
        "Domain Re-Animator Bot",
        "dotbot",
        "drupact",
        "DuckDuckBot",
        "EARTHCOM",
        "EasouSpider",
        "ec2linkfinder",
        "edisterbot",
        "ElectricMonk",
        "elisabot",
        "emailmarketingrobot",
        "EmeraldShield\.com WebBot",
        "envolk\[ITS\]spider",
        "EsperanzaBot",
        "europarchive\.org",
        "exabot",
        "ezooms",
        "facebookexternalhit",
        "Facebot",
        "FAST Enteprise Crawler",
        "FAST Enterprise Crawler",
        "FAST-WebCrawler",
        "FDSE robot",
        "Feedfetcher-Google",
        "findlink",
        "findthatfile",
        "findxbot",
        "Flamingo_SearchEngine",
        "fluffy",
        "fr-crawler",
        "FRCrawler",
        "FurlBot",
        "FyberSpider",
        "g00g1e\.net",
        "GigablastOpenSource",
        "grub-client",
        "g2crawler",
        "Gaisbot",
        "GalaxyBot",
        "genieBot",
        "Genieo",
        "GermCrawler",
        "gigabot",
        "GingerCrawler",
        "Girafabot",
        "Gluten Free Crawler",
        "gnam gnam spider",
        "Googlebot-Image",
        "Googlebot-Mobile",
        "Googlebot",
        "GrapeshotCrawler",
        "gslfbot",
        "GurujiBot",
        "HappyFunBot",
        "Healthbot",
        "hl_ftien_spider",
        "Holmes",
        "httpunit",
        "httrack",
        "iaskspider",
        "iCCrawler",
        "igdeSpyder",
        "iisbot",
        "InAGist",
        "InfoWizards Reciprocal Link System PRO",
        "Insitesbot",
        "integromedb",
        "intelium_bot",
        "InterfaxScanBot",
        "IODC",
        "IOI",
        "ip-web-crawler\.com",
        "ips-agent",
        "IRLbot",
        "IssueCrawler",
        "IstellaBot",
        "it2media-domain-crawler",
        "iZSearch",
        "Jaxified Bot",
        "JOC Web Spider",
        "jyxobot",
        "KoepaBot",
        "L\.webis",
        "LapozzBot",
        "lb-spider",
        "LDSpider",
        "LexxeBot",
        "Linguee Bot",
        "Link Valet",
        "linkdex",
        "LinkExaminer",
        "LinksManager\.com_bot",
        "LinkpadBot",
        "LinksCrawler",
        "LinkWalker",
        "Lipperhey Link Explorer",
        "Lipperhey SEO Service",
        "Livelapbot",
        "lmspider",
        "lssbot",
        "lssrocketcrawler",
        "ltx71",
        "lufsbot",
        "lwp-trivial",
        "Mail\.RU_Bot",
        "MegaIndex\.ru",
        "mabontland",
        "magpie-crawler",
        "Mediapartners-Google",
        "memorybot",
        "MetaURI",
        "MJ12bot",
        "mlbot",
        "mogimogi",
        "MojeekBot",
        "Moreoverbot",
        "Morning Paper",
        "Mrcgiguy",
        "MSIECrawler",
        "msnbot",
        "msrbot",
        "MVAClient",
        "mxbot",
        "NerdByNature\.Bot",
        "NerdyBot",
        "netEstate NE Crawler",
        "NetSeer Crawler",
        "NewsGator",
        "NextGenSearchBot",
        "NG-Search",
        "ngbot",
        "nicebot",
        "niki-bot",
        "Notifixious",
        "noxtrumbot",
        "Nusearch Spider",
        "NutchCVS",
        "Nymesis",
        "oegp",
        "ocrawler",
        "omgilibot",
        "OmniExplorer_Bot",
        "online link validator",
        "Online Website Link Checker",
        "OOZBOT",
        "openindexspider",
        "OpenWebSpider",
        "OrangeBot",
        "Orbiter",
        "ow\.ly",
        "PaperLiBot",
        "Pingdom\.com_bot",
        "Ploetz \+ Zeller",
        "page2rss",
        "PageBitesHyperBot",
        "Peew",
        "PercolateCrawler",
        "phpcrawl",
        "Pizilla",
        "Plukkie",
        "polybot",
        "PostPost",
        "postrank",
        "proximic",
        "psbot",
        "purebot",
        "PycURL",
        "python-requests",
        "Python-urllib",
        "Qseero",
        "QuerySeekerSpider",
        "Qwantify",
        "Radian6",
        "RAMPyBot",
        "REL Link Checker",
        "RetrevoPageAnalyzer",
        "Riddler",
        "Robosourcer",
        "rogerbot",
        "RufusBot",
        "SandCrawler",
        "Scrapy",
        "ScreenerBot",
        "scribdbot",
        "SearchmetricsBot",
        "SearchSight",
        "seekbot",
        "SemrushBot",
        "Sensis Web Crawler",
        "SEOChat::Bot",
        "seokicks-robot",
        "SEOstats",
        "Seznam screenshot-generator",
        "seznambot",
        "Shim-Crawler",
        "Shoula robot",
        "ShowyouBot",
        "SimpleCrawler",
        "sistrix crawler",
        "SiteBar",
        "sitebot",
        "siteexplorer\.info",
        "SklikBot",
        "slider\.com",
        "smtbot",
        "sogou spider",
        "sogou",
        "Sosospider",
        "spbot",
        "Speedy Spider",
        "speedy",
        "SpiderMan",
        "Sqworm",
        "SSL-Crawler",
        "StackRambler",
        "suggybot",
        "summify",
        "SurdotlyBot",
        "SurveyBot",
        "SynooBot",
        "tagoobot",
        "teoma",
        "TerrawizBot",
        "TheSuBot",
        "Thumbnail\.CZ robot",
        "TinEye",
        "toplistbot",
        "trendictionbot",
        "TrueBot",
        "truwoGPS",
        "turnitinbot",
        "TweetedTimes Bot",
        "TweetmemeBot",
        "twengabot",
        "Twitterbot",
        "uMBot",
        "UnisterBot",
        "UnwindFetchor",
        "urlappendbot",
        "Urlfilebot",
        "urlresolver",
        "UsineNouvelleCrawler",
        "Vivante Link Checker",
        "voilabot",
        "Vortex",
        "VYU2",
        "web-archive-net\.com\.bot",
        "Websquash\.com",
        "WeSEE:Ads\/PageBot",
        "wbsearchbot",
        "webcollage",
        "webcompanycrawler",
        "webcrawler",
        "webmon ",
        "WeSEE:Search",
        "wf84",
        "wocbot",
        "WoFindeIch Robot",
        "WomlpeFactory",
        "woriobot",
        "wotbox",
        "Xaldon_WebSpider",
        "Xenu Link Sleuth",
        "xintellibot",
        "XML Sitemaps Generator",
        "XoviBot",
        "Y!J-ASR",
        "yacy",
        "yacybot",
        "Yahoo Link Preview",
        "Yahoo! Slurp China",
        "Yahoo! Slurp",
        "YahooSeeker",
        "YahooSeeker-Testing",
        "YandexBot",
        "YandexImages",
        "YandexMetrika",
        "Yasaklibot",
        "YioopBot",
        "YisouSpider",
        "YodaoBot",
        "yoogliFetchAgent",
        "yoozBot",
        "YoudaoBot",
        "Zao",
        "Zealbot",
        "zspider",
        // Based on http://www.user-agents.org/index.shtml
        "ELinks",
        "FSurf15a",
        "Firefly",
    );
    
    /**
     * This is the main $crawlersString used in this class
     * 
     * @var string
     */
    protected static $crawlersString = "(abot|dbot|ebot|hbot|kbot|lbot|mbot|nbot|obot|pbot|rbot|sbot|tbot|vbot|ybot|zbot|bot.|bot\/|_bot|.bot|\/bot|-bot|:bot|\(bot|crawl|slurp|spider|seek|accoona|acoon|adressendeutschland|ah-ha.com|ahoy|altavista|ananzi|anthill|appie|arachnophilia|arale|araneo|aranha|architext|aretha|arks|asterias|atlocal|atn|atomz|augurfind|backrub|bannana_bot|baypup|bdfetch|big brother|biglotron|bjaaland|blackwidow|blaiz|blog|blo.|bloodhound|boitho|booch|bradley|butterfly|calif|cassandra|ccubee|cfetch|charlotte|churl|cienciaficcion|cmc|collective|comagent|combine|computingsite|csci|curl|cusco|daumoa|deepindex|delorie|depspid|deweb|die blinde kuh|digger|ditto|dmoz|docomo|download express|dtaagent|dwcp|ebiness|ebingbong|e-collector|ejupiter|emacs-w3 search engine|esther|evliya celebi|ezresult|falcon|felix ide|ferret|fetchrover|fido|findlinks|fireball|fish search|fouineur|funnelweb|gazz|gcreep|genieknows|getterroboplus|geturl|glx|goforit|golem|grabber|grapnel|gralon|griffon|gromit|grub|gulliver|hamahakki|harvest|havindex|helix|heritrix|hku www octopus|homerweb|htdig|html index|html_analyzer|htmlgobble|hubater|hyper-decontextualizer|ia_archiver|ibm_planetwide|ichiro|iconsurf|iltrovatore|image.kapsi.net|imagelock|incywincy|indexer|infobee|informant|ingrid|inktomisearch.com|inspector web|intelliagent|internet shinchakubin|ip3000|iron33|israeli-search|ivia|jack|jakarta|javabee|jetbot|jumpstation|katipo|kdd-explorer|kilroy|knowledge|kototoi|kretrieve|labelgrabber|lachesis|larbin|legs|libwww|linkalarm|link validator|linkscan|lockon|lwp|lycos|magpie|mantraagent|mapoftheinternet|marvin\/|mattie|mediafox|mediapartners|mercator|merzscope|microsoft url control|minirank|miva|mj12|mnogosearch|moget|monster|moose|motor|multitext|muncher|muscatferret|mwd.search|myweb|najdi|nameprotect|nationaldirectory|nazilla|ncsa beta|nec-meshexplorer|nederland.zoek|netcarta webmap engine|netmechanic|netresearchserver|netscoop|newscan-online|nhse|nokia6682\/|nomad|noyona|nutch|nzexplorer|objectssearch|occam|omni|open text|openfind|openintelligencedata|orb search|osis-project|pack rat|pageboy|pagebull|page_verifier|panscient|parasite|partnersite|patric|pear.|pegasus|peregrinator|pgp key agent|phantom|phpdig|picosearch|piltdownman|pimptrain|pinpoint|pioneer|piranha|plumtreewebaccessor|pogodak|poirot|pompos|poppelsdorf|poppi|popular iconoclast|psycheclone|publisher|python|rambler|raven search|roach|road runner|roadhouse|robbie|robofox|robozilla|rules|salty|sbider|scooter|scoutjet|scrubby|search.|searchprocess|semanticdiscovery|senrigan|sg-scout|shai'hulud|shark|shopwiki|sidewinder|sift|silk|simmany|site searcher|site valet|sitetech-rover|skymob.com|sleek|smartwit|sna-|snappy|snooper|sohu|speedfind|sphere|sphider|spinner|spyder|steeler\/|suke|suntek|supersnooper|surfnomore|sven|sygol|szukacz|tach black widow|tarantula|templeton|\/teoma|t-h-u-n-d-e-r-s-t-o-n-e|theophrastus|titan|titin|tkwww|toutatis|t-rex|tutorgig|twiceler|twisted|ucsd|udmsearch|url check|updated|vagabondo|valkyrie|verticrawl|victoria|vision-search|volcano|voyager\/|voyager-hc|w3c_validator|w3m2|w3mir|walker|wallpaper|wanderer|wauuu|wavefire|web core|web hopper|web wombat|webbandit|webcatcher|webcopy|webfoot|weblayers|weblinker|weblog monitor|webmirror|webmonkey|webquest|webreaper|websitepulse|websnarf|webstolperer|webvac|webwalk|webwatch|webwombat|webzinger|wget|whizbang|whowhere|wild ferret|worldlight|wwwc|wwwster|xenu|xget|xift|xirq|yandex|yanga|yeti|yodao|zao\/|zippp|zyborg|007ac9 Crawler|008\/|360Spider|A6-Indexer|ABACHOBot|AbiLogicBot|Aboundex|Accoona-AI-Agent|AddSugarSpiderBot|AddThis|Adidxbot|ADmantX|AdvBot|ahrefsbot|aihitbot|Airmail|AISearchBot|Anemone|antibot|AnyApexBot|Applebot|arabot|Arachmo|archive-com|archive.org_bot|B-l-i-t-z-B-O-T|backlinkcrawler|baiduspider|BecomeBot|BeslistBot|bibnum\.bnf|BillyBobBot|Bimbot|bingbot|binlar|blekkobot|blexbot|BlitzBOT|bl\.uk_lddc_bot|bnf\.fr_bot|boitho\.com-dc|boitho\.com-robot|brainobot|btbot|BUbiNG|Butterfly\/|buzzbot|careerbot|CatchBot|CC Metadata Scaper|ccbot|Cerberian Drtrs|changedetection|CloudFlare-AlwaysOnline|citeseerxbot|coccoc|classbot|Commons-HttpClient|content crawler spider|Content Crawler|convera|ConveraCrawler|CoPubbot|cosmos|Covario-IDS|CrawlBot|crawler4j|CrystalSemanticsBot|cXensebot|CyberPatrol|DataparkSearch|dataprovider|DiamondBot|Digg|discobot|DomainAppender|domaincrawler|Domain Re-Animator Bot|dotbot|drupact|DuckDuckBot|EARTHCOM|EasouSpider|ec2linkfinder|edisterbot|ElectricMonk|elisabot|emailmarketingrobot|EmeraldShield\.com WebBot|envolk\[ITS\]spider|EsperanzaBot|europarchive\.org|exabot|ezooms|facebookexternalhit|Facebot|FAST Enteprise Crawler|FAST Enterprise Crawler|FAST-WebCrawler|FDSE robot|Feedfetcher-Google|findlink|findthatfile|findxbot|Flamingo_SearchEngine|fluffy|fr-crawler|FRCrawler|FurlBot|FyberSpider|g00g1e\.net|GigablastOpenSource|grub-client|g2crawler|Gaisbot|GalaxyBot|genieBot|Genieo|GermCrawler|gigabot|GingerCrawler|Girafabot|Gluten Free Crawler|gnam gnam spider|Googlebot-Image|Googlebot-Mobile|Googlebot|GrapeshotCrawler|gslfbot|GurujiBot|HappyFunBot|Healthbot|hl_ftien_spider|Holmes|httpunit|httrack|iaskspider|iCCrawler|igdeSpyder|iisbot|InAGist|InfoWizards Reciprocal Link System PRO|Insitesbot|integromedb|intelium_bot|InterfaxScanBot|IODC|IOI|ip-web-crawler\.com|ips-agent|IRLbot|IssueCrawler|IstellaBot|it2media-domain-crawler|iZSearch|Jaxified Bot|JOC Web Spider|jyxobot|KoepaBot|L\.webis|LapozzBot|lb-spider|LDSpider|LexxeBot|Linguee Bot|Link Valet|linkdex|LinkExaminer|LinksManager\.com_bot|LinkpadBot|LinksCrawler|LinkWalker|Lipperhey Link Explorer|Lipperhey SEO Service|Livelapbot|lmspider|lssbot|lssrocketcrawler|ltx71|lufsbot|lwp-trivial|Mail\.RU_Bot|MegaIndex\.ru|mabontland|magpie-crawler|Mediapartners-Google|memorybot|MetaURI|MJ12bot|mlbot|mogimogi|MojeekBot|Moreoverbot|Morning Paper|Mrcgiguy|MSIECrawler|msnbot|msrbot|MVAClient|mxbot|NerdByNature\.Bot|NerdyBot|netEstate NE Crawler|NetSeer Crawler|NewsGator|NextGenSearchBot|NG-Search|ngbot|nicebot|niki-bot|Notifixious|noxtrumbot|Nusearch Spider|NutchCVS|Nymesis|oegp|ocrawler|omgilibot|OmniExplorer_Bot|online link validator|Online Website Link Checker|OOZBOT|openindexspider|OpenWebSpider|OrangeBot|Orbiter|ow\.ly|PaperLiBot|Pingdom\.com_bot|Ploetz \+ Zeller|page2rss|PageBitesHyperBot|Peew|PercolateCrawler|phpcrawl|Pizilla|Plukkie|polybot|PostPost|postrank|proximic|psbot|purebot|PycURL|python-requests|Python-urllib|Qseero|QuerySeekerSpider|Qwantify|Radian6|RAMPyBot|REL Link Checker|RetrevoPageAnalyzer|Riddler|Robosourcer|rogerbot|RufusBot|SandCrawler|Scrapy|ScreenerBot|scribdbot|SearchmetricsBot|SearchSight|seekbot|SemrushBot|Sensis Web Crawler|SEOChat::Bot|seokicks-robot|SEOstats|Seznam screenshot-generator|seznambot|Shim-Crawler|Shoula robot|ShowyouBot|SimpleCrawler|sistrix crawler|SiteBar|sitebot|siteexplorer\.info|SklikBot|slider\.com|smtbot|sogou spider|sogou|Sosospider|spbot|Speedy Spider|speedy|SpiderMan|Sqworm|SSL-Crawler|StackRambler|suggybot|summify|SurdotlyBot|SurveyBot|SynooBot|tagoobot|teoma|TerrawizBot|TheSuBot|Thumbnail\.CZ robot|TinEye|toplistbot|trendictionbot|TrueBot|truwoGPS|turnitinbot|TweetedTimes Bot|TweetmemeBot|twengabot|Twitterbot|uMBot|UnisterBot|UnwindFetchor|urlappendbot|Urlfilebot|urlresolver|UsineNouvelleCrawler|Vivante Link Checker|voilabot|Vortex|VYU2|web-archive-net\.com\.bot|Websquash\.com|WeSEE:Ads\/PageBot|wbsearchbot|webcollage|webcompanycrawler|webcrawler|webmon |WeSEE:Search|wf84|wocbot|WoFindeIch Robot|WomlpeFactory|woriobot|wotbox|Xaldon_WebSpider|Xenu Link Sleuth|xintellibot|XML Sitemaps Generator|XoviBot|Y!J-ASR|yacy|yacybot|Yahoo Link Preview|Yahoo! Slurp China|Yahoo! Slurp|YahooSeeker|YahooSeeker-Testing|YandexBot|YandexImages|YandexMetrika|Yasaklibot|YioopBot|YisouSpider|YodaoBot|yoogliFetchAgent|yoozBot|YoudaoBot|Zao|Zealbot|zspider|ELinks|FSurf15a|Firefly)";
    
    /**
     * $botTypes
     *     - list of types of web sniffers like bot, crawl, spider and slurp
     *
     * @var string
     * @access public
    */
    protected static $botTypes = array(
        "bot",
        "crawl",
        "slurp",
        "spider",
    );

    /**
     * All possible HTTP headers that represent the
     * User-Agent string.
     *
     * @var array
     */
    protected static $uaHttpHeaders = array(
        // The default User-Agent string.
        "HTTP_USER_AGENT",
        // Header can occur on devices using Opera Mini.
        "HTTP_X_OPERAMINI_PHONE_UA",
        // Vodafone specific header: http://www.seoprinciple.com/mobile-web-community-still-angry-at-vodafone/24/
        "HTTP_X_DEVICE_USER_AGENT",
        "HTTP_X_ORIGINAL_USER_AGENT",
        "HTTP_X_SKYFIRE_PHONE",
        "HTTP_X_BOLT_PHONE_UA",
        "HTTP_DEVICE_STOCK_UA",
        "HTTP_X_UCBROWSER_DEVICE_UA",
    );

    /**
     * Class constructor.
     */
    public function __construct(array $headers = null, $userAgent = null)
    {
        //echo '('.implode('|', self::$crawlers).')'; exit;
        $this->setHttpHeaders($headers);
        $this->setUserAgent($userAgent);
    }

    /**
     * 
     * @param type $httpHeaders
     */
    public function setHttpHeaders($httpHeaders = null)
    {
        // use global _SERVER if $httpHeaders aren't defined
        if (!is_array($httpHeaders) || !count($httpHeaders)) {
            $httpHeaders = $_SERVER;
        }
        // clear existing headers
        $this->httpHeaders = array();
        // Only save HTTP headers. In PHP land, that means only _SERVER vars that
        // start with HTTP_.
        foreach ($httpHeaders as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $this->httpHeaders[$key] = $value;
            }
        }
    }
    
    /**
     * 
     * @return type
     */
    public function getUaHttpHeaders()
    {
        return self::$uaHttpHeaders;
    }
    
    /**
     * 
     * @param type $userAgent
     * @return type
     */
    public function setUserAgent($userAgent = null)
    {
        if (false === empty($userAgent)) {
            return $this->userAgent = $userAgent;
        }
        
        $this->userAgent = null;
        foreach ($this->getUaHttpHeaders() as $altHeader) {
            // @todo: should use getHttpHeader(), but it would be slow. (Serban)
            if (false === empty($this->httpHeaders[$altHeader])) {
                $this->userAgent .= $this->httpHeaders[$altHeader].' ';
            }
        }

        return $this->userAgent = (!empty($this->userAgent) ? trim($this->userAgent) : null);
    }
    
    /**
     * 
     * @return type
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * 
     * @param type $userAgent
     * @return type
     */
    public function isCrawler($userAgent = null)
    {
        $agent = is_null($userAgent)? $this->userAgent : $userAgent;
        $result = preg_match('/' . self::$crawlersString . '/i', $agent, $matches);
        
        $this->matches = array();
        if ($matches) {
            $this->matches = $matches;
        }

        return (bool) $result;
    }
    
    /**
     * 
     * @return type
     */
    public function testCrawlers()
    {
        $crawlers = array(
            "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",
            "Googlebot-News",
            "Googlebot-Image/1.0",
            "Googlebot-Video/1.0",
            "AdsBot-Google (+http://www.google.com/adsbot.html)",
            "Mozilla/5.0 (compatible; 008/0.83; http://www.80legs.com/webcrawler.html) Gecko/2008032620",
            "Full Web Bot 2816B",
            "FnooleBot/2.5.2 (+http://www.fnoole.com/addurl.html)",
            "Firefly/1.0",
            "Filangy/0.01-beta (Filangy; http://www.nutch.org/docs/en/bot.html; filangy-agent@filangy.com)",
            "FAST-WebCrawler/3.3 (crawler@fast.no; http://fast.no/support.php?c=faqs/crawler)",
            "ExperimentalHenrytheMiragoRobot",
            "ELinks/0.x.x (textmode; NetBSD 1.6.2 sparc; 132x43)",
            "FSurf15a 01",
        );
        
        $result = "";
        foreach ($crawlers as $crawler) {
            $isFiltered = $this->isCrawler($crawler)? 
                '<span class="text-success">Filtered</span>' : 
                '<span class="text-danger">Untouched</span>';
            
            $matches = 'no match';
            if (null !== $this->getMatches()) {
                $matches = $this->getMatches();
            }
                
            $result .= $crawler . ": " . $isFiltered . " [" . $matches . "]<br>";
        }
        
        return $result;
    }
    
    /**
     * Simple detection to see if is a bot, spider, crawler or slurp based only on the HTTP_USER_AGENT.
     * Inspired in https://gist.github.com/Exadra37/9453909
     *
     * @author Exadra37
     * @return boolean - return true if is a bot, otherwise returns false.
    */
    public function isBot($userAgent = null)
    {
        $agent = is_null($userAgent)? $this->userAgent : $userAgent;
        return (bool) preg_match('/' . $this->getRegex(self::$botTypes) . '/i', $agent);
    }    
    
    /**
     * 
     * @return type
     */
    public function getMatches()
    {
        return isset($this->matches[0])? $this->matches[0] : null;
    }
    
    /**
     * 
     * @return type
     */
    private function getRegex($array)
    {
        return '('.implode('|', $array).')';
    }    
}