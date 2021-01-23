<?php
class telegram_groups extends MY_Controller {
	
	public $tb_account_manager = "sp_account_manager";
	public $module_name;

	public function __construct(){
		parent::__construct();
        _permission("account_manager_enable");
		$this->load->model(get_class($this).'_model', 'model');
		include get_module_path($this, 'libraries/vendor/autoload.php', true);

		//
		$this->module_name = get_module_config( $this, 'name' );
		$this->module_icon = get_module_config( $this, 'icon' );
		$this->module_color = get_module_config( $this, 'color' );
		//
	}

	public function index($page = "", $ids = "")
	{
        switch ($page) {
            case 'general':
                $result = [];
                $count_profile = 0;
                $access_token = _s("telegram_access_token");
                $telegram = new Telegram($access_token);
                $profiles = $telegram->getUpdates();
                if($profiles["ok"] == 1){

                    if(!empty($profiles['result'])){

                        foreach ($profiles['result'] as $profile) {
                            if(isset($profile['message'])){
                                $result[] = (object)[
                                    'id' => $profile['message']['chat']['id'],
                                    'name' => $profile['message']['chat']['title'],
                                    'avatar' => "https://ui-avatars.com/api?name=".$profile['message']['chat']['title']."&size=128&background=0088cc&color=fff",
                                    'desc' => isset($profile['message']['chat']['username'])?$profile['message']['chat']['username']:$profile['message']['chat']['title'],
                                ];                            
                                $count_profile++;
                            }
                        }                        
                    }

                    _ss("telegram_profiles", json_encode($result));

                    if($count_profile != 0){
                        $data = [
                            "status" => "success",
                            "result" => $result
                        ];
                    }else{
                        $data = [
                            "status" => "error",
                            "message" => __('No profile to add')
                        ];
                    }

                }else{
                    $data = [
                        "status" => "error",
                        "message" => __('No profile to add')
                    ];
                }
                break;
            
            default:

                break;
        }

        $data['module_name'] = $this->module_name;
        $data['module_icon'] = $this->module_icon;
        $data['module_color'] = $this->module_color;

		$views = [
			"subheader" => view( 'main/subheader', [ 'module_name' => $this->module_name, 'module_icon' => $this->module_icon, 'module_color' => $this->module_color ], true ),
			"column_one" => page($this, "pages", "oauth", $page, $data), 
		];
		
		views( [
			"title" => $this->module_name,
			"fragment" => "fragment_one",
			"views" => $views
		] );
	}

    public function token()
    {
        $access_token = post("access_token");

        validate('empty', __('Please enter access token'), $access_token);
        
        $telegram = new Telegram($access_token);
        $profile = $telegram->getMe();

        if($profile['ok'] != 1){
            ms([
                "status" => "error",
                "message" => __("Unauthorized")
            ]);
        }

        _ss("telegram_access_token", $access_token);

        ms([
            "status" => "success",
            "message" => __("Success")
        ]);
    }

	public function oauth()
	{
        redirect(  get_module_url() );   
	}

	public function save()
    {
        $ids = post('id');
        $team_id = _t("id");

        validate('empty', __('Please select a profile to add'), $ids);

        $profiles = _s("telegram_profiles");
        $profiles = json_decode($profiles);

        $result = [];
        $count_profile = 0;
        if(!empty($profiles)){

            foreach ($profiles as $profile) {
                $profile = (object)$profile;
                if( in_array($profile->id, $ids) ){
                    $item = $this->model->get('*', $this->tb_account_manager, "social_network = 'telegram' AND team_id = '{$team_id}' AND pid = '{$profile->id}'");
                    $avatar = save_img( $profile->avatar, TMP_PATH.'avatar/' );
                    $access_token = _s("telegram_access_token");

                    if(!$item){
                        $data = [
                            'ids' => ids(),
                            'social_network' => 'telegram',
                            'category' => 'group',
                            'login_type' => 1,
                            'can_post' => 1,
                            'team_id' => $team_id,
                            'pid' => $profile->id,
                            'name' => $profile->name,
                            'username' => $profile->desc,
                            'token' => $access_token,
                            'avatar' => $avatar,
                            'url' => 'https://web.telegram.org/#/im?p=@'.$profile->desc,
                            'data' => NULL,
                            'status' => 1,
                            'changed' => now(),
                            'created' => now()
                        ];

                        check_number_account("telegram", "group");

                        $this->model->insert($this->tb_account_manager, $data);
                    }else{
                        @unlink($item->avatar);

                        $data = [
                            'social_network' => 'telegram',
                            'category' => 'group',
                            'login_type' => 1,
                            'can_post' => 1,
                            'team_id' => $team_id,
                            'pid' => $profile->id,
                            'name' => $profile->name,
                            'username' => $profile->desc,
                            'token' => $access_token,
                            'avatar' => $avatar,
                            'url' => 'https://web.telegram.org/#/im?p=@'.$profile->desc,
                            'status' => 1,
                            'changed' => now(),
                        ];

                        $this->model->update($this->tb_account_manager, $data, ['id' => $item->id]);
                    }
                }

                $count_profile++;
            }
        }

        _us('telegram_access_token');

        if($count_profile == 0){
            ms([
                "status" => "error",
                "message" => __('No profile to add')
            ]);
        }else{
            ms([
                "status" => "success",
                "message" => __("Success")
            ]);
        }
	}

	public function get($params, $accessToken){

		try {
            $response = $this->fb->get($params, $accessToken);
            return json_decode($response->getBody()); 
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            return $e->getMessage();
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            return $e->getMessage();
        }

	}

}