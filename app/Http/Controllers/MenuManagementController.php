<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MySession as MySession;
use App\WebHelper;
use DB;
class MenuManagementController extends Controller
{
    public function index(Request $request){

        if(!MySession::isSuperAdmin()){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
    	$data['title'] = 'Menu Management';
    	if(isset($request->id_menu) && $request->id_menu > 0){ // Edit
    		$data['menu_details'] = DB::table('cms_menus')->where('id',$request->id_menu)->first();
    		$data['priv_list'] = json_encode(DB::table('cms_menus_privileges')->where('id_cms_menus',$request->id_menu)->pluck('id_cms_privileges')->toArray());

    		$data['opcode'] = 1;
    	}else{
    		$data['opcode'] = 0; // Add
    	}

    	$data['id_cms_privileges'] = MySession::myPrivilegeId();
    	$data['privilege_list'] = DB::table('cms_privileges')->get();
    	$data['font_awesome_icon'] = $this->icon_list();

    	// return WebHelper::sidebarMenu();
    	// foreach($data['privileges'] as $prev){
    	// 	return $prev->id;
    	// }
    	// return $data['privileges'];
    	// $id_cms_privileges = ($id_cms_privileges) ?: CRUDBooster::myPrivilegeId();
    	$id_cms_privileges = MySession::myPrivilegeId();

        $menu_active = DB::table('cms_menus')->where('parent_id', 0)->where('is_active', 1)->orderby('sorting', 'asc')->get();
     
        foreach ($menu_active as &$menu) {
            $child = DB::table('cms_menus')->where('is_active', 1)->where('parent_id', $menu->id)->orderby('sorting', 'asc')->get();
            if (count($child)) {
                $menu->children = $child;
            }
        }

        $menu_inactive = DB::table('cms_menus')->where('parent_id', 0)->where('is_active', 0)->orderby('sorting', 'asc')->get();

        foreach ($menu_inactive as &$menu) {
            $child = DB::table('cms_menus')->where('is_active', 1)->where('parent_id', $menu->id)->orderby('sorting', 'asc')->get();
            if (count($child)) {
                $menu->children = $child;
            }
        }
        $data['menu_active'] = $menu_active;
        $data['menu_inactive'] = $menu_inactive;
        // return $data['menu_active'];
    	return view('menu_management.index',$data);
    }
    public function arrange(Request $request){
    	$post = $request->menus;
    	$isActive = $request->isActive;
    	$post = json_decode($post,true);

    	$i = 1;
    	foreach ($post[0] as $ro) {
            $pid = $ro['id'];
            if(isset($ro['children'][0])){
	            if ($ro['children'][0]) {
	                $ci = 1;
	                foreach ($ro['children'][0] as $c) {
	                    $id = $c['id'];
	                    DB::table('cms_menus')->where('id', $id)->update(['sorting' => $ci, 'parent_id' => $pid, 'is_active' => $isActive]);
	                    $ci++;
	                }
	            }
            }
            DB::table('cms_menus')->where('id', $pid)->update(['sorting' => $i, 'parent_id' => 0, 'is_active' => $isActive]);
            $i++;
        }

        return response()->json(['success' => true]);
    }
    public function delete_menu(Request $request){
    	if($request->ajax()){
    		DB::table('cms_menus')
    		->where('id',$request->id_menu)
    		->delete();


		    DB::table('cms_menus_privileges')
			->where('id_cms_menus',$request->id_menu)
			->delete();

			DB::table('credentials')
			->where('id_menu',$request->id_menu)
			->delete();

			DB::table('cms_menus')
			->where('parent_id',$request->id_menu)
			->update(['parent_id'=>0]);

    		return response(array(
    			'message' => 'success'
    		));
    	}
    }
    public function post_menu(Request $request){
    	if($request->ajax()){
			$object = array(
    			'name' => $request->name,
    			'type' => 'URL',
    			'path' => $request->link,
    			'color' => 'normal',
    			'icon' => $request->icon,
    			'is_active' => 1,
    			'is_dashboard'=>0,
    			'id_cms_privileges' => MySession::myPrivilegeId(),
    			'is_maintenance'=>$request->is_maintenance,
    			'is_report'=>$request->is_report
    		);
    		$imploded_id = implode(',', $request->privileges);
    		if($request->opcode == 0){
    			$object['parent_id'] = 0;
	    		DB::table('cms_menus')
	    		->insert($object);

	    		$menu_id = DB::table('cms_menus')
			    		->max('id');
				//Insert to Credential
	    		DB::select("INSERT INTO credentials (is_view,is_create,is_read,is_edit,is_delete,is_print,is_confirm,id_cms_privileges,id_menu)
							SELECT if(id in ($imploded_id),1,0),0,0,0,0,0,0,id,$menu_id FROM cms_privileges;");
    		}else{
    			$menu_id = $request->id_menu;
    			DB::table('cms_menus')
    			->where('id',$menu_id)
    			->update($object);

    			DB::table('cms_menus_privileges')
    			->where('id_cms_menus',$menu_id)
    			->delete();

	    		DB::select("UPDATE credentials
				SET is_view = if(id_cms_privileges in ($imploded_id),1,0)
				WHERE id_menu = $menu_id");
    		}
    		$menu_priv = array();
		    foreach($request->privileges as $priv){
		    	$menu_priv[]= [
		    		'id_cms_menus' => $menu_id,
		    		'id_cms_privileges' => $priv
		    	];
		    	// DB::table('credentials')
		    	// ->where('id_cms_privileges',$priv)
		    	// ->where('id_menu',$menu_id)
		    	// ->update(['is_view'=>1]);
		    }
		    DB::table('cms_menus_privileges')
		    ->insert($menu_priv);

    		return response(array(
    			'message' => 'success'
    			)
    		);
    	}
    }
    public function icon_list(){
    	$icons = array(
    	'arrow-alt-circle-up'=>'far fa-arrow-alt-circle-up',
		'arrows-alt'=>'fas fa-arrows-alt',
		'expand-arrows-alt'=>'fas fa-expand-arrows-alt',
		'arrows-alt-h'=>'fas fa-arrows-alt-h',
		'arrows-alt-v'=>'fas fa-arrows-alt-v',
		'american-sign-language-interpreting'=>'fas fa-american-sign-language-interpreting',
		'car'=>'fas fa-car',
		'bandcamp'=>'fab fa-bandcamp',
		'university'=>'fas fa-university',
		'chart-bar'=>'far fa-chart-bar',
		'chart-bar'=>'far fa-chart-bar',
		'bath'=>'fas fa-bath',
		'battery-full'=>'fas fa-battery-full',
		'battery-empty'=>'fas fa-battery-empty',
		'battery-quarter'=>'fas fa-battery-quarter',
		'battery-half'=>'fas fa-battery-half',
		'battery-three-quarters'=>'fas fa-battery-three-quarters',
		'battery-full'=>'fas fa-battery-full',
		'behance'=>'fab fa-behance',
		'behance-square'=>'fab fa-behance-square',
		'bell'=>'far fa-bell',
		'bell-slash'=>'far fa-bell-slash',
		'bitbucket'=>'fab fa-bitbucket',
		'bitbucket'=>'fab fa-bitbucket',
		'btc'=>'fab fa-btc',
		'black-tie'=>'fab fa-black-tie',
		'bluetooth'=>'fab fa-bluetooth',
		'bluetooth-b'=>'fab fa-bluetooth-b',
		'bookmark'=>'far fa-bookmark',
		'btc'=>'fab fa-btc',
		'building'=>'far fa-building',
		'buysellads'=>'fab fa-buysellads',
		'taxi'=>'fas fa-taxi',
		'calendar-alt'=>'fas fa-calendar-alt',
		'calendar-check'=>'far fa-calendar-check',
		'calendar-minus'=>'far fa-calendar-minus',
		'calendar'=>'far fa-calendar',
		'calendar-plus'=>'far fa-calendar-plus',
		'calendar-times'=>'far fa-calendar-times',
		'caret-square-down'=>'far fa-caret-square-down',
		'caret-square-left'=>'far fa-caret-square-left',
		'caret-square-right'=>'far fa-caret-square-right',
		'caret-square-up'=>'far fa-caret-square-up',
		'closed-captioning'=>'far fa-closed-captioning',
		'cc-amex'=>'fab fa-cc-amex',
		'cc-diners-club'=>'fab fa-cc-diners-club',
		'cc-discover'=>'fab fa-cc-discover',
		'cc-jcb'=>'fab fa-cc-jcb',
		'cc-mastercard'=>'fab fa-cc-mastercard',
		'cc-paypal'=>'fab fa-cc-paypal',
		'cc-stripe'=>'fab fa-cc-stripe',
		'cc-visa'=>'fab fa-cc-visa',
		'link'=>'fas fa-link',
		'unlink'=>'fas fa-unlink',
		'check-circle'=>'far fa-check-circle',
		'check-square'=>'far fa-check-square',
		'chrome'=>'fab fa-chrome',
		'circle'=>'far fa-circle',
		'circle-notch'=>'fas fa-circle-notch',
		'circle'=>'far fa-circle',
		'clipboard'=>'far fa-clipboard',
		'clock'=>'far fa-clock',
		'clone'=>'far fa-clone',
		'times'=>'fas fa-times',
		'cloud-download-alt'=>'fas fa-cloud-download-alt',
		'cloud-upload-alt'=>'fas fa-cloud-upload-alt',
		'yen-sign'=>'fas fa-yen-sign',
		'code-branch'=>'fas fa-code-branch',
		'codepen'=>'fab fa-codepen',
		'codiepie'=>'fab fa-codiepie',
		'comment'=>'far fa-comment',
		'comment-dots'=>'fas fa-comment-dots',
		'comment-dots'=>'far fa-comment-dots',
		'comments'=>'far fa-comments',
		'compass'=>'far fa-compass',
		'connectdevelop'=>'fab fa-connectdevelop',
		'contao'=>'fab fa-contao',
		'copyright'=>'far fa-copyright',
		'creative-commons'=>'fab fa-creative-commons',
		'credit-card'=>'far fa-credit-card',
		'credit-card'=>'fas fa-credit-card',
		'css3'=>'fab fa-css3',
		'utensils'=>'fas fa-utensils',
		'tachometer-alt'=>'fas fa-tachometer-alt',
		'dashcube'=>'fab fa-dashcube',
		'deaf'=>'fas fa-deaf',
		'outdent'=>'fas fa-outdent',
		'delicious'=>'fab fa-delicious',
		'deviantart'=>'fab fa-deviantart',
		'gem'=>'far fa-gem',
		'digg'=>'fab fa-digg',
		'dollar-sign'=>'fas fa-dollar-sign',
		'dot-circle'=>'far fa-dot-circle',
		'dribbble'=>'fab fa-dribbble',
		'id-card'=>'fas fa-id-card',
		'id-card'=>'far fa-id-card',
		'dropbox'=>'fab fa-dropbox',
		'drupal'=>'fab fa-drupal',
		'edge'=>'fab fa-edge',
		'sellcast'=>'fab fa-sellcast',
		'empire'=>'fab fa-empire',
		'envelope'=>'far fa-envelope',
		'envelope-open'=>'far fa-envelope-open',
		'envira'=>'fab fa-envira',
		'etsy'=>'fab fa-etsy',
		'euro-sign'=>'fas fa-euro-sign',
		'euro-sign'=>'fas fa-euro-sign',
		'exchange-alt'=>'fas fa-exchange-alt',
		'expeditedssl'=>'fab fa-expeditedssl',
		'external-link-alt'=>'fas fa-external-link-alt',
		'external-link-square-alt'=>'fas fa-external-link-square-alt',
		'eye'=>'far fa-eye',
		'eye-slash'=>'far fa-eye-slash',
		'eye-dropper'=>'fas fa-eye-dropper',
		'font-awesome'=>'fab fa-font-awesome',
		'facebook-f'=>'fab fa-facebook-f',
		'facebook-f'=>'fab fa-facebook-f',
		'facebook'=>'fab fa-facebook',
		'facebook-square'=>'fab fa-facebook-square',
		'rss'=>'fas fa-rss',
		'file-archive'=>'far fa-file-archive',
		'file-audio'=>'far fa-file-audio',
		'file-code'=>'far fa-file-code',
		'file-excel'=>'far fa-file-excel',
		'file-image'=>'far fa-file-image',
		'file-video'=>'far fa-file-video',
		'file'=>'far fa-file',
		'file-pdf'=>'far fa-file-pdf',
		'file-image'=>'far fa-file-image',
		'file-image'=>'far fa-file-image',
		'file-powerpoint'=>'far fa-file-powerpoint',
		'file-audio'=>'far fa-file-audio',
		'file-alt'=>'fas fa-file-alt',
		'file-alt'=>'far fa-file-alt',
		'file-video'=>'far fa-file-video',
		'file-word'=>'far fa-file-word',
		'file-archive'=>'far fa-file-archive',
		'copy'=>'far fa-copy',
		'firefox'=>'fab fa-firefox',
		'first-order'=>'fab fa-first-order',
		'flag'=>'far fa-flag',
		'bolt'=>'fas fa-bolt',
		'flickr'=>'fab fa-flickr',
		'save'=>'far fa-save',
		'folder'=>'far fa-folder',
		'folder-open'=>'far fa-folder-open',
		'font-awesome'=>'fab fa-font-awesome',
		'fonticons'=>'fab fa-fonticons',
		'fort-awesome'=>'fab fa-fort-awesome',
		'forumbee'=>'fab fa-forumbee',
		'foursquare'=>'fab fa-foursquare',
		'free-code-camp'=>'fab fa-free-code-camp',
		'frown'=>'far fa-frown',
		'futbol'=>'far fa-futbol',
		'pound-sign'=>'fas fa-pound-sign',
		'empire'=>'fab fa-empire',
		'cog'=>'fas fa-cog',
		'cogs'=>'fas fa-cogs',
		'get-pocket'=>'fab fa-get-pocket',
		'gg'=>'fab fa-gg',
		'gg-circle'=>'fab fa-gg-circle',
		'git'=>'fab fa-git',
		'git-square'=>'fab fa-git-square',
		'github'=>'fab fa-github',
		'github-alt'=>'fab fa-github-alt',
		'github-square'=>'fab fa-github-square',
		'gitlab'=>'fab fa-gitlab',
		'gratipay'=>'fab fa-gratipay',
		'glass-martini'=>'fas fa-glass-martini',
		'glide'=>'fab fa-glide',
		'glide-g'=>'fab fa-glide-g',
		'google'=>'fab fa-google',
		'google-plus-g'=>'fab fa-google-plus-g',
		'google-plus'=>'fab fa-google-plus',
		'google-plus'=>'fab fa-google-plus',
		'google-plus-square'=>'fab fa-google-plus-square',
		'google-wallet'=>'fab fa-google-wallet',
		'gratipay'=>'fab fa-gratipay',
		'grav'=>'fab fa-grav',
		'users'=>'fas fa-users',
		'hacker-news'=>'fab fa-hacker-news',
		'hand-rock'=>'far fa-hand-rock',
		'hand-lizard'=>'far fa-hand-lizard',
		'hand-point-down'=>'far fa-hand-point-down',
		'hand-point-left'=>'far fa-hand-point-left',
		'hand-point-right'=>'far fa-hand-point-right',
		'hand-point-up'=>'far fa-hand-point-up',
		'hand-paper'=>'far fa-hand-paper',
		'hand-peace'=>'far fa-hand-peace',
		'hand-pointer'=>'far fa-hand-pointer',
		'hand-rock'=>'far fa-hand-rock',
		'hand-scissors'=>'far fa-hand-scissors',
		'hand-spock'=>'far fa-hand-spock',
		'hand-paper'=>'far fa-hand-paper',
		'handshake'=>'far fa-handshake',
		'deaf'=>'fas fa-deaf',
		'hdd'=>'far fa-hdd',
		'heading'=>'fas fa-heading',
		'heart'=>'far fa-heart',
		'hospital'=>'far fa-hospital',
		'bed'=>'fas fa-bed',
		'hourglass-start'=>'fas fa-hourglass-start',
		'hourglass-half'=>'fas fa-hourglass-half',
		'hourglass-end'=>'fas fa-hourglass-end',
		'hourglass'=>'far fa-hourglass',
		'houzz'=>'fab fa-houzz',
		'html5'=>'fab fa-html5',
		'id-badge'=>'far fa-id-badge',
		'id-card'=>'far fa-id-card',
		'shekel-sign'=>'fas fa-shekel-sign',
		'image'=>'far fa-image',
		'imdb'=>'fab fa-imdb',
		'rupee-sign'=>'fas fa-rupee-sign',
		'instagram'=>'fab fa-instagram',
		'university'=>'fas fa-university',
		'internet-explorer'=>'fab fa-internet-explorer',
		'transgender'=>'fas fa-transgender',
		'ioxhost'=>'fab fa-ioxhost',
		'joomla'=>'fab fa-joomla',
		'yen-sign'=>'fas fa-yen-sign',
		'jsfiddle'=>'fab fa-jsfiddle',
		'keyboard'=>'far fa-keyboard',
		'won-sign'=>'fas fa-won-sign',
		'lastfm'=>'fab fa-lastfm',
		'lastfm-square'=>'fab fa-lastfm-square',
		'leanpub'=>'fab fa-leanpub',
		'gavel'=>'fas fa-gavel',
		'lemon'=>'far fa-lemon',
		'level-down-alt'=>'fas fa-level-down-alt',
		'level-up-alt'=>'fas fa-level-up-alt',
		'life-ring'=>'far fa-life-ring',
		'life-ring'=>'far fa-life-ring',
		'life-ring'=>'far fa-life-ring',
		'life-ring'=>'far fa-life-ring',
		'lightbulb'=>'far fa-lightbulb',
		'chart-line'=>'fas fa-chart-line',
		'linkedin-in'=>'fab fa-linkedin-in',
		'linkedin'=>'fab fa-linkedin',
		'linode'=>'fab fa-linode',
		'linux'=>'fab fa-linux',
		'list-alt'=>'far fa-list-alt',
		'long-arrow-alt-down'=>'fas fa-long-arrow-alt-down',
		'long-arrow-alt-left'=>'fas fa-long-arrow-alt-left',
		'long-arrow-alt-right'=>'fas fa-long-arrow-alt-right',
		'long-arrow-alt-up'=>'fas fa-long-arrow-alt-up',
		'share'=>'fas fa-share',
		'reply'=>'fas fa-reply',
		'reply-all'=>'fas fa-reply-all',
		'map-marker-alt'=>'fas fa-map-marker-alt',
		'map'=>'far fa-map',
		'maxcdn'=>'fab fa-maxcdn',
		'font-awesome'=>'fab fa-font-awesome',
		'medium'=>'fab fa-medium',
		'meetup'=>'fab fa-meetup',
		'meh'=>'far fa-meh',
		'minus-square'=>'far fa-minus-square',
		'mixcloud'=>'fab fa-mixcloud',
		'mobile-alt'=>'fas fa-mobile-alt',
		'mobile-alt'=>'fas fa-mobile-alt',
		'modx'=>'fab fa-modx',
		'money-bill-alt'=>'far fa-money-bill-alt',
		'moon'=>'far fa-moon',
		'graduation-cap'=>'fas fa-graduation-cap',
		'bars'=>'fas fa-bars',
		'newspaper'=>'far fa-newspaper',
		'object-group'=>'far fa-object-group',
		'object-ungroup'=>'far fa-object-ungroup',
		'odnoklassniki'=>'fab fa-odnoklassniki',
		'odnoklassniki-square'=>'fab fa-odnoklassniki-square',
		'opencart'=>'fab fa-opencart',
		'openid'=>'fab fa-openid',
		'opera'=>'fab fa-opera',
		'optin-monster'=>'fab fa-optin-monster',
		'pagelines'=>'fab fa-pagelines',
		'paper-plane'=>'far fa-paper-plane',
		'clipboard'=>'far fa-clipboard',
		'pause-circle'=>'far fa-pause-circle',
		'paypal'=>'fab fa-paypal',
		'pencil-alt'=>'fas fa-pencil-alt',
		'pen-square'=>'fas fa-pen-square',
		'edit'=>'far fa-edit',
		'image'=>'far fa-image',
		'image'=>'far fa-image',
		'chart-pie'=>'fas fa-chart-pie',
		'pied-piper'=>'fab fa-pied-piper',
		'pied-piper-alt'=>'fab fa-pied-piper-alt',
		'pied-piper-pp'=>'fab fa-pied-piper-pp',
		'pinterest'=>'fab fa-pinterest',
		'pinterest-p'=>'fab fa-pinterest-p',
		'pinterest-square'=>'fab fa-pinterest-square',
		'play-circle'=>'far fa-play-circle',
		'plus-square'=>'far fa-plus-square',
		'product-hunt'=>'fab fa-product-hunt',
		'qq'=>'fab fa-qq',
		'question-circle'=>'far fa-question-circle',
		'quora'=>'fab fa-quora',
		'rebel'=>'fab fa-rebel',
		'ravelry'=>'fab fa-ravelry',
		'rebel'=>'fab fa-rebel',
		'reddit'=>'fab fa-reddit',
		'reddit-alien'=>'fab fa-reddit-alien',
		'reddit-square'=>'fab fa-reddit-square',
		'sync'=>'fas fa-sync',
		'registered'=>'far fa-registered',
		'times'=>'fas fa-times',
		'renren'=>'fab fa-renren',
		'bars'=>'fas fa-bars',
		'redo'=>'fas fa-redo',
		'rebel'=>'fab fa-rebel',
		'yen-sign'=>'fas fa-yen-sign',
		'undo'=>'fas fa-undo',
		'redo'=>'fas fa-redo',
		'ruble-sign'=>'fas fa-ruble-sign',
		'ruble-sign'=>'fas fa-ruble-sign',
		'ruble-sign'=>'fas fa-ruble-sign',
		'rupee-sign'=>'fas fa-rupee-sign',
		'bath'=>'fas fa-bath',
		'safari'=>'fab fa-safari',
		'cut'=>'fas fa-cut',
		'scribd'=>'fab fa-scribd',
		'sellsy'=>'fab fa-sellsy',
		'paper-plane'=>'fas fa-paper-plane',
		'paper-plane'=>'far fa-paper-plane',
		'share-square'=>'far fa-share-square',
		'shekel-sign'=>'fas fa-shekel-sign',
		'shekel-sign'=>'fas fa-shekel-sign',
		'shield-alt'=>'fas fa-shield-alt',
		'shirtsinbulk'=>'fab fa-shirtsinbulk',
		'sign-in-alt'=>'fas fa-sign-in-alt',
		'sign-out-alt'=>'fas fa-sign-out-alt',
		'sign-language'=>'fas fa-sign-language',
		'simplybuilt'=>'fab fa-simplybuilt',
		'skyatlas'=>'fab fa-skyatlas',
		'skype'=>'fab fa-skype',
		'slack'=>'fab fa-slack',
		'sliders-h'=>'fas fa-sliders-h',
		'slideshare'=>'fab fa-slideshare',
		'smile'=>'far fa-smile',
		'snapchat'=>'fab fa-snapchat',
		'snapchat-ghost'=>'fab fa-snapchat-ghost',
		'snapchat-square'=>'fab fa-snapchat-square',
		'snowflake'=>'far fa-snowflake',
		'futbol'=>'far fa-futbol',
		'sort-alpha-down'=>'fas fa-sort-alpha-down',
		'sort-alpha-up'=>'fas fa-sort-alpha-up',
		'sort-amount-down'=>'fas fa-sort-amount-down',
		'sort-amount-up'=>'fas fa-sort-amount-up',
		'sort-up'=>'fas fa-sort-up',
		'sort-down'=>'fas fa-sort-down',
		'sort-numeric-down'=>'fas fa-sort-numeric-down',
		'sort-numeric-up'=>'fas fa-sort-numeric-up',
		'soundcloud'=>'fab fa-soundcloud',
		'utensil-spoon'=>'fas fa-utensil-spoon',
		'spotify'=>'fab fa-spotify',
		'square'=>'far fa-square',
		'stack-exchange'=>'fab fa-stack-exchange',
		'stack-overflow'=>'fab fa-stack-overflow',
		'star-half'=>'far fa-star-half',
		'star-half'=>'far fa-star-half',
		'star-half'=>'far fa-star-half',
		'star'=>'far fa-star',
		'steam'=>'fab fa-steam',
		'steam-square'=>'fab fa-steam-square',
		'sticky-note'=>'far fa-sticky-note',
		'stop-circle'=>'far fa-stop-circle',
		'stumbleupon'=>'fab fa-stumbleupon',
		'stumbleupon-circle'=>'fab fa-stumbleupon-circle',
		'sun'=>'far fa-sun',
		'superpowers'=>'fab fa-superpowers',
		'life-ring'=>'far fa-life-ring',
		'tablet-alt'=>'fas fa-tablet-alt',
		'tachometer-alt'=>'fas fa-tachometer-alt',
		'telegram'=>'fab fa-telegram',
		'tv'=>'fas fa-tv',
		'tencent-weibo'=>'fab fa-tencent-weibo',
		'themeisle'=>'fab fa-themeisle',
		'thermometer-full'=>'fas fa-thermometer-full',
		'thermometer-empty'=>'fas fa-thermometer-empty',
		'thermometer-quarter'=>'fas fa-thermometer-quarter',
		'thermometer-half'=>'fas fa-thermometer-half',
		'thermometer-three-quarters'=>'fas fa-thermometer-three-quarters',
		'thermometer-full'=>'fas fa-thermometer-full',
		'thumbtack'=>'fas fa-thumbtack',
		'thumbs-down'=>'far fa-thumbs-down',
		'thumbs-up'=>'far fa-thumbs-up',
		'ticket-alt'=>'fas fa-ticket-alt',
		'times-circle'=>'far fa-times-circle',
		'window-close'=>'fas fa-window-close',
		'window-close'=>'far fa-window-close',
		'caret-square-down'=>'far fa-caret-square-down',
		'caret-square-left'=>'far fa-caret-square-left',
		'caret-square-right'=>'far fa-caret-square-right',
		'caret-square-up'=>'far fa-caret-square-up',
		'trash-alt'=>'fas fa-trash-alt',
		'trash-alt'=>'far fa-trash-alt',
		'trello'=>'fab fa-trello',
		'tripadvisor'=>'fab fa-tripadvisor',
		'lira-sign'=>'fas fa-lira-sign',
		'tumblr'=>'fab fa-tumblr',
		'tumblr-square'=>'fab fa-tumblr-square',
		'lira-sign'=>'fas fa-lira-sign',
		'twitch'=>'fab fa-twitch',
		'twitter'=>'fab fa-twitter',
		'twitter-square'=>'fab fa-twitter-square',
		'sort'=>'fas fa-sort',
		'usb'=>'fab fa-usb',
		'dollar-sign'=>'fas fa-dollar-sign',
		'user-circle'=>'far fa-user-circle',
		'user'=>'far fa-user',
		'address-card'=>'fas fa-address-card',
		'address-card'=>'far fa-address-card',
		'viacoin'=>'fab fa-viacoin',
		'viadeo'=>'fab fa-viadeo',
		'viadeo-square'=>'fab fa-viadeo-square',
		'video'=>'fas fa-video',
		'vimeo-v'=>'fab fa-vimeo-v',
		'vimeo-square'=>'fab fa-vimeo-square',
		'vine'=>'fab fa-vine',
		'vk'=>'fab fa-vk',
		'phone-volume'=>'fas fa-phone-volume',
		'exclamation-triangle'=>'fas fa-exclamation-triangle',
		'weixin'=>'fab fa-weixin',
		'weibo'=>'fab fa-weibo',
		'weixin'=>'fab fa-weixin',
		'whatsapp'=>'fab fa-whatsapp',
		'accessible-icon'=>'fab fa-accessible-icon',
		'wikipedia-w'=>'fab fa-wikipedia-w',
		'window-close'=>'far fa-window-close',
		'window-maximize'=>'far fa-window-maximize',
		'window-restore'=>'far fa-window-restore',
		'windows'=>'fab fa-windows',
		'won-sign'=>'fas fa-won-sign',
		'wordpress'=>'fab fa-wordpress',
		'wpbeginner'=>'fab fa-wpbeginner',
		'wpexplorer'=>'fab fa-wpexplorer',
		'wpforms'=>'fab fa-wpforms',
		'xing'=>'fab fa-xing',
		'xing-square'=>'fab fa-xing-square',
		'y-combinator'=>'fab fa-y-combinator',
		'hacker-news'=>'fab fa-hacker-news',
		'yahoo'=>'fab fa-yahoo',
		'y-combinator'=>'fab fa-y-combinator',
		'hacker-news'=>'fab fa-hacker-news',
		'yelp'=>'fab fa-yelp',
		'yen-sign'=>'fas fa-yen-sign',
		'yoast'=>'fab fa-yoast',
		'youtube'=>'fab fa-youtube',
		'youtube'=>'fab fa-youtube',
		'youtube-square'=>'fab fa-youtube-square',
	);
	return $icons;
    }
}
