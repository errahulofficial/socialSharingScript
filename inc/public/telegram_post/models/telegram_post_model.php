<?php
class telegram_post_model extends MY_Model {
	public $tb_account_manager = "sp_account_manager";

	public function __construct(){
		parent::__construct();
		$module_path = get_module_directory(__DIR__);
		include $module_path.'libraries/vendor/autoload.php';

		//
		$this->module_name = get_module_config( $module_path, 'name' );
		$this->module_icon = get_module_config( $module_path, 'icon' );
		$this->module_color = get_module_config( $module_path, 'color' );
		//
	}

	public function block_permissions($path = ""){
		$dir = get_directory_block(__DIR__, get_class($this));
		return [
			'position' => 8500,
			'name' => $this->module_name,
			'color' => $this->module_color,
			'icon' => $this->module_icon, 
			'id' => str_replace("_model", "", get_class($this)),
			'html' => view( $dir.'pages/block_permissions', ['path' => $path], true, $this ),
		];
	}

	public function block_report($path = ""){
		$dir = get_directory_block(__DIR__, get_class($this));
		return [
			'tab' => 'telegram',
			'position' => 1000,
			'name' => $this->module_name,
			'color' => $this->module_color,
			'icon' => $this->module_icon, 
			'id' => str_replace("_model", "", get_class($this)),
			'html' => view( $dir.'pages/block_report', ['path' => $path], true, $this ),
		];
	}

	public function block_post_preview($path = ""){
		$dir = get_directory_block(__DIR__, get_class($this));
		return [
			'position' => 1100,
			'name' => $this->module_name,
			'color' => $this->module_color,
			'icon' => $this->module_icon, 
			'id' => str_replace("_model", "", get_class($this)),
			'preview' => view( $dir.'pages/preview', ['path' => $path], true, $this ),
		];
	}

	public function post( $data ){
		$post_type = $data["post_type"];
		$account = $data["account"];
		$medias = $data["medias"];
		$link = $data["link"];
		$advance = $data["advance"];
		$caption = spintax( $data["caption"] );
		$is_schedule = $data["is_schedule"];
		$response = [ "ok" => "", "description" => __("Unknown error")];
		
		if($is_schedule)
		{	
			return [
            	"status" => "success",
            	"message" => __('Success'),
            	"type" => $post_type
            ];
		}
		
		$telegram = new Telegram($account->token);

		$params = [ "chat_id" => $account->pid ];

		switch ($post_type)
		{
			case 'photo':
				$medias[0] = watermark($medias[0], $account->team_id, $account->id);
				$content = ['chat_id' => $account->pid, 'photo' => $medias[0] ];
                $response = $telegram->sendPhoto($content);

            	$params["text"] = $caption;
                $telegram->sendMessage($params);
                unlink_watermark($medias);
				break;

			case 'video':

				$content = ['chat_id' => $account->pid, 'video' => $medias[0] ];
                $response = $telegram->sendVideo($content);

            	$params["text"] = $caption;
                $telegram->sendMessage($params);
    
				break;

			case 'link':
                $params["text"] = $caption." \n\r".$link;
                $response = $telegram->sendMessage($params);
				break;

			case 'text':
				$params["text"] = $caption;
				$response = $telegram->sendMessage($params);
				break;
			
		}

		if($response['ok'] == 1){
			return [
	        	"status" => "success",
	        	"message" => __('Success'),
	        	"id" => $response['result']['message_id'],
	        	"url" => "https://web.telegram.org/#/im?p=@".$response['result']['chat']['username'],
	        	"type" => $post_type
	        ]; 
		}else{

			if($response['error_code'] == 401){
				$this->model->update($this->tb_account_manager, [ "status" => 0 ], [ "id" => $account->id ] );
			}

			return [
            	"status" => "error",
            	"message" => __( $response['description'] ),
            	"type" => $post_type
            ];
		}
	}
}
