<?php
/**
  * @package MediaWiki
  * @subpackage Language
  */
# Ossetic stub localization; default to Russian instead of English.
# See language.txt

require_once( "LanguageRu.php" );

if($wgMetaNamespace === FALSE)
        $wgMetaNamespace = str_replace( ' ', '_', $wgSitename );

/* private */ $wgNamespaceNamesOs = array(
       NS_MEDIA            => 'Media', //чтоб не писать "Мультимедия"
        NS_SPECIAL          => 'Сæрмагонд',
        NS_MAIN             => '',
        NS_TALK             => 'Дискусси',
        NS_USER             => 'Архайæг', 
        NS_USER_TALK        => 'Архайæджы_дискусси',
        NS_PROJECT          => $wgMetaNamespace,
        NS_PROJECT_TALK     => 'Дискусси_'+$wgMetaNamespace,
        NS_IMAGE            => 'Ныв',
        NS_IMAGE_TALK       => 'Нывы_тыххæй_дискусси', 
        NS_MEDIAWIKI        => 'MediaWiki',
        NS_MEDIAWIKI_TALK   => 'Дискусси_MediaWiki',
        NS_TEMPLATE         => 'Шаблон',
        NS_TEMPLATE_TALK    => 'Шаблоны_тыххæй_дискусси',
        NS_HELP             => 'Æххуыс',
        NS_HELP_TALK        => 'Æххуысы_тыххæй_дискусси', 
        NS_CATEGORY         => 'Категори',
        NS_CATEGORY_TALK    => 'Категорийы_тыххæй_дискусси',
) + $wgNamespaceNamesEn;

if(isset($wgExtraNamespaces)) {
        $wgNamespaceNamesOs=$wgNamespaceNamesOs+$wgExtraNamespaces;
}

/* private */ $wgQuickbarSettingsOs = array(
        'Ма равдис', 'Галиуырдыгæй', 'Рахизырдыгæй', 'Рахизырдыгæй ленккæнгæ'
 ); 

/* private */ $wgSkinNamesOs = array(
        'standard' => 'Стандартон',
        'nostalgia' => 'Æнкъард',
        'cologneblue' => 'Кёльны æрхæндæг',
        'davinci' => 'Да Винчи',
        'mono' => 'Моно',
        'monobook' => 'Моно-чиныг',
        'myskin' => 'Мæхи',
        'chick' => 'Карк'
 );

/* private */ $wgAllMessagesOs = array(
'titlematches' => 'Статьяты сæргæндты æмцаутæ',
'toc' => 'Сæргæндтæ',
'addedwatch' => "Дæ цæст кæмæ дарыс, уыцы статьятæм бафтыд.",
'all' => "æппæт",
'allarticles' => "Æппæт статьятæ",
'allmessages' => "Æппæт техникон фыстытæ",
'allpages' => "Æппæт фæрстæ",
'allpagesnamespace' => "Æппæт фæрстæ ($1)",
'allpagesnext' => "дарддæр",
'allpagesprev' => "фæстæмæ",
'alphaindexline' => "$1 (уыдоны ’хсæн цы статьятæ ис, фен) $2",
'ancientpages' => "Зæронддæр фæрстæ",
'and' => "æмæ",
'articlenamespace' => "(статьятæ)",
'articlepage' => "Фен статья",
'blanknamespace' => "(Сæйраг)",
'bold_sample' => "Ацы текст бæзджын суыдзæн",
'bold_tip' => "Бæзджын текст",
'bydate' => "рæстæгмæ гæсгæ",
'byname' => "номмæ гæсгæ",
'bysize' => "асмæ гæсгæ",
'categories' => "Категоритæ",
'categoriespagetext' => "Мæнæ ахæм категоритæ ирон Википедийы ис.",
'category' => "категори",
'category_header' => "Категори \"$1\"",
'categoryarticlecount' => "Ацы категорийы мидæг $1 статьяйы ис.",
'categoryarticlecount1' => "Ацы категорийы мидæг $1 статья ис.",
'contributions' => "Йæ бавæрд",
'createaccountmail' => "адрисмæ гæсгæ",
'currentevents' => "Ног хабæрттæ",
'currentevents-url' => "Xabar",
'diff' => "хицæн.",
'edit' => "Баив æй",
'editsection' => "баив æй",
'emailpage' => "Электронон фыстæг йæм барвит",
'error' => "Рæдыд",
'errorpagetitle' => "Рæдыд",
'exblank' => "фарс афтид уыдис",
'filename' => "Файлы ном",
'go' => "Статьямæ",
'headline_sample' => "Ам сæргонды текст уæд",
'help' => "Æххуыс",
'hide' => "бамбæхс",
'hidetoc' => "бамбæхс",
'hist' => "лог",
'histlegend' => "Куыд æй æмбарын: (нырыккон) = нырыккон версийæ хъауджыдæрдзинад, (раздæры) = раздæры версийæ хъауджыдæрдзинад, Ч = чысыл ивддзинад.",
'history_short' => "Истори",
'ilsubmit' => "Агур",
'imagelist' => "Нывты номхыгъд",
'imghistory' => "Нывы ивддзинæдты лог",
'importnotext' => "Афтид у кæнæ текст дзы нæй",
'internalerror' => "Мидæг рæдыд",
'intl' => "Æндæр æвзæгтæм æрвитæнтæ",
'ipbreason' => "Аххос",
'largefile' => "Сæдæ килобайтæй стырдæр файлтæ æгæр дынджыр сты.",
'last' => "раздæры",
'lastmodified' => "<span style=\"white-space: normal;\">Кæд æмæ ацы статьяйы ссардтай рæдыд, уæд сраст æй кæн: ацы фарсы уæлæ ис æрвитæн «баив æй».
<br /> Ацы фарс фæстаг хатт ивд æрцыд: $1.</span>",
'lineno' => "Рæнхъ $1:",
'linklistsub' => "(Æрвитæнты номхыгъд)",
'linkstoimage' => "Ацы нывæй чи пайда кæны, ахæм статьятæ:",
'listform' => "номхыгъд",
'listusers' => "Архайджыты номхыгъд",
'localtime' => "Бынатон рæстæг",
'login' => "Дæхи бавдис системæйæн",
'loginpagetitle' => "Дæхи бацамон системæйæн",
'loginsuccess' => "Ныр та Википедийы архайыс $1, зæгъгæ, ахæм номæй.",
'logout' => "Номсусæг суын",
'logouttitle' => "Номсусæг суын",
'lonelypages' => "Сидзæр фæрстæ",
'longpages' => "Даргъ фæрстæ",
'mailnologintext' => "Фыстæгтæ æрвитынмæ хъуамæ [[Special:Userlogin|системæйæн дæхи бавдисай]] æмæ дæ бæлвырд электронон посты адрис [[Special:Preferences|ныффыссай]].",
'mainpage' => "Сæйраг фарс",
'makesysopname' => "Архайæджы ном:",
'minoredit' => "Ай чысыл ивддзинад у.",
'monday' => "Къуырисæр",
'move' => "Ном баив",
'movearticle' => "Статьяйы ном баив",
'movenologin' => "Системæйæн дæхи нæ бавдыстай",
'mycontris' => "Дæ бавæрд",
'mypage' => "Дæхи фарс",
'mytalk' => "Дæумæ цы дзурынц",
'navigation' => "хъæугæ æрвитæнтæ",
'nbytes' => "$1 байт(ы)",
'nchanges' => "$1 ивддзинад(ы)",
'newarticle' => "(Ног)",
'newimages' => "Ног нывты галерей",
'newmessages' => "Райстай $1.",
'newmessageslink' => "ног фыстæгтæ",
'newpage' => "Ног фарс",
'newpageletter' => "Н",
'newpages' => "Ног фæрстæ",
'newpassword' => "Новый пароль",
'newtitle' => "Ног ном",
'newusersonly' => "(æрмæст ног архайджытæн)",
'nextn' => "$1 размæ",
'nlinks' => "$1 æрвитæн(ы)",
'nowatchlist' => "Иу статьямæ дæр дæ цæст нæ дарыс.",
'nstab-image' => "Ныв",
'nstab-mediawiki' => "Фыстаг",
'nstab-special' => "Сæрмагонд фарс",
'nstab-template' => "Шаблон",
'nstab-user' => "Архайæджы фарс",
'otherlanguages' => "Æндæр æвзæгтыл",
'others' => "æндæртæ",
'portal' => "Архайджыты æхсæнад",
'prevn' => "$1 фæстæмæ",
'printableversion' => "Мыхурмæ верси",
'printsubtitle' => "(Æрмæг ист æрцыд мæнæ ацы сайтæй: {{SERVER}})",
'qbfind' => "Агур",
'qbspecialpages' => "Сæрмагонд фæрстæ",
'randompage' => "Æнæбары æвзæрст фарс",
'rclinks' => "Фæстаг $1 ивддзинæдтæ (афæстаг $2 боны дæргъы чи ’рцыдысты) равдис; 
$3",
'rcnote' => "Дæлдæр нымад сты афæстаг <strong>$2</strong> боны дæргъы конд <strong>$1</strong> ивддзинад(ы).",
'recentchanges' => "Фæстаг ивддзинæдтæ",
'recentchangeslinked' => "Баст ивддзинæдтæ",
'recentchangestext' => "Ацы фарсыл ирон Википедийы фæстаг ивддзинæдтæ фенæн ис.",
'revhistory' => "Ивддзинæдты истори",
'rights' => "Бартæ",
'saturday' => "Сабат",
'savearticle' => "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Афтæ!&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",
'savefile' => "Бавæр æй",
'search' => "агур",
'searchresults' => "Цы ссардæуы",
'shortpages' => "Цыбыр фæрстæ",
'showpreview' => "&nbsp;&nbsp;Фен уал æй&nbsp;&nbsp;",
'showtoc' => "равдис",
'sitesubtitle' => "Сæрибар энциклопеди",
'show' => "равдис",
'showhideminor' => "$1 чысыл ивддзинæдтæ | $2 роботтæ | $3 регистрацигонд архайджыты | $4 дæ цæст кæмæ дарыс, уыдон.<br />",
'showpreview' => "&nbsp;&nbsp;Фен уал æй&nbsp;&nbsp;",
'specialpage' => "Сæрмагонд фарс",
'specialpages' => "Сæрмагонд фæрстæ",
'spheading' => "Сæрмагонд фæрстæ",
'subcategories' => "Дæлкатегоритæ",
'subcategorycount' => "Ацы категорийы мидæг $1 дæлкатегорийы ис.",
'subcategorycount1' => "Ацы категорийы мидæг $1 дæлкатегори ис.",
'sunday' => "Хуыцаубон",
'tableform' => "таблицæ",
'tagline' => "Сæрибар энциклопеди Википедийы æрмæг.",
'talk' => "Дискусси",
'talkpage' => "Ацы фарсы тыххæй ныхас",
'textmatches' => "Статьяты æмцаутæ",
'thursday' => "Цыппарæм",
'timezonelegend' => "Сахаты таг",
'timezoneoffset' => "Хъауджыдæрдзинад",
'titlematches' => "Статьяты сæргæндты æмцаутæ",
'toc' => "Сæргæндтæ",
'tog-underline' => "Æрвитæнты бын хахх",
'toolbox' => "мигæнæнтæ",
'tuesday' => "Дыццæг",
'uctop' => "(уæле баззад)",
'userlogin' => "Системæйæн дæхи бавдис",
'userlogout' => "Номсусæг суын",
'userpage' => "Ацы архайæджы фарс фен",
'userstatstext' => "Регистрацигонд æрцыдысты <b>$1</b> архайджыты, уыдонæй <b>$2</b> — админтæ (кæс $3).",
'wantedpages' => "Хъæугæ фæрстæ",
'watch' => "Дæ цæст æрдар",
'watchdetails' => "($1 фæрстæм дæ цæст дарыс, дискусситы фæстæмæ; $3... [$4 Æххæст номхыгъд фен].)",
'watchlist' => "Дæ цæст кæмæ дарыс, уыцы фæрстæ",
'watchlistcontains' => "Дæ цæст $1 фæрстæм дарыс.",
'watchlistsub' => "$1, зæгъгæ, уыцы архайæгæн",
'watchnologin' => "Системæйæн дæхи нæ бавдыстай",
'watchnologintext' => "Ацы номхыгъд ивынмæ <a href=\"{{localurle:Специальные:Userlogin}}\">хъуамæ дæхи бавдисай системæйæн</a>.",
'watchthis' => "Ацы фарсмæ дæ цæст æрдар",
'watchthispage' => "Ацы фарсмæ дæ цæст æрдар",
'wednesday' => "Æртыццæг",
'welcomecreation' => "<h2>Æгас цу, $1!</h2><p>Регистрацигонд æрцыдтæ.",
'whatlinkshere' => "Цавæр æрвитæнтæ цæуынц ардæм",
'wlnote' => "Дæлæ афæстаг <b>$2</b> сахаты дæргъы цы $1 ивддзинад(ы) æрцыди, уыдон.",
'wlshowlast' => "Фæстæг $1 сахаты, $2 боны дæргъы; $3.",
'youremail' => "Дæ электронон посты адрис",
'yourlanguage' => "Техникон фыстыты æвзаг",
'yourname' => "Дæ ном кæнæ фæсномыг",
'yourrealname' => "Дæ æцæг ном*",

);

class LanguageOs extends LanguageRu {
        function LanguageOs() {
                global $wgNamespaceNamesOs, $wgMetaNamespace;
                LanguageUtf8::LanguageUtf8();
        }

        function getNamespaces() {
                global $wgNamespaceNamesOs;
                return $wgNamespaceNamesOs;
        }

        function getQuickbarSettings() {
                global $wgQuickbarSettingsOs;
                return $wgQuickbarSettingsOs;
        }

        function getSkinNames() {
                global $wgSkinNamesOs;
                return $wgSkinNamesOs;
        }

        function getDateFormats() {
                global $wgDateFormatsRu;
                return $wgDateFormatsRu;
        }

        function getValidSpecialPages()
        {
                global $wgValidSpecialPagesRu;
                return $wgValidSpecialPagesRu;
        }

        function getSysopSpecialPages()
        {
                global $wgSysopSpecialPagesRu;
                return $wgSysopSpecialPagesRu;
        }

        function getDeveloperSpecialPages()
        {
                global $wgDeveloperSpecialPagesRu;
                return $wgDeveloperSpecialPagesRu;
        }

        function getMessage( $key )
        {
                global $wgAllMessagesOs;
		return isset($wgAllMessagesOs[$key]) ? $wgAllMessagesOs[$key] : parent::getMessage($key);
        }

        function fallback8bitEncoding() {
                return "windows-1251";
        }

        function getMagicWords()  {
                global $wgMagicWordsRu;
                return $wgMagicWordsRu;
        }

	function formatNum( $number ) {
		global $wgTranslateNumerals;
		return $wgTranslateNumerals ? strtr($number, '.,', ', ' ) : $number;
	}
	
}
?>