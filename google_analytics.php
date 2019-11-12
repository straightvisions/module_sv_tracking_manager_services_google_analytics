<?php
namespace sv_tracking_manager;

/**
 * @version         1.000
 * @author			straightvisions GmbH
 * @package			sv100
 * @copyright		2019 straightvisions GmbH
 * @link			https://straightvisions.com
 * @since			1.000
 * @license			See license.txt or https://straightvisions.com
 */

class google_analytics extends modules {
	public function init() {
		// Section Info
		$this->set_section_title( __('Google Analytics', 'sv_tracking_manager' ) )
			->set_section_desc(__( sprintf('%sGoogle Analytics Login%s', '<a target="_blank" href="https://analytics.google.com/">','</a>'), 'sv_tracking_manager' ))
			->set_section_type( 'settings' )
			->set_section_template_path( $this->get_path( 'lib/backend/tpl/settings.php' ) )
			->load_settings()
			->register_scripts()
			->get_root()->add_section( $this );

		add_action('init', array($this, 'load'));
	}

	protected function load_settings(): google_analytics {
		$this->get_setting('activate')
			->set_title( __( 'Activate', 'sv_tracking_manager' ) )
			->set_description('Enable Tracking')
			->load_type( 'checkbox' );

		$this->get_setting('user_identification')
			->set_title(__('User Identification', 'sv_tracking_manager'))
			->set_description(__(sprintf('%sEnable User Identification%s', '<a target="_blank" href="https://developers.google.com/analytics/devguides/collection/analyticsjs/cookies-user-id">','</a>'), $this->get_module_name()))
			->load_type('checkbox');

		$this->get_setting('tracking_id')
			->set_title( __( 'Tracking ID', 'sv_tracking_manager' ) )
			->set_description( __( sprintf('%sHow to retrieve Tracking ID%s', '<a target="_blank" href="https://support.google.com/analytics/answer/7372977">','</a>'), 'sv_tracking_manager' ) )
			->load_type( 'text' );

		return $this;
	}
	protected function register_scripts(): google_analytics {
		if($this->is_active()) {
			$this->get_script('ga')
				->set_path('https://www.googletagmanager.com/gtag/js?id='.$this->get_setting('tracking_id')->run_type()->get_data(), true)
				->set_type('js');

			$this->get_script('default')
				->set_deps(array($this->get_script('ga')->get_handle()))
				->set_path('lib/frontend/js/default.js')
				->set_type('js');
		}

		return $this;
	}
	public function is_active(): bool{
		// activate not set
		if(!$this->get_setting('activate')->run_type()->get_data()){
			return false;
		}
		// activate not true
		if($this->get_setting('activate')->run_type()->get_data() !== '1'){
			return false;
		}
		// Tracking ID not set
		if(!$this->get_setting('tracking_id')->run_type()->get_data()){
			return false;
		}
		// Tracking ID empty
		if(strlen(trim($this->get_setting('tracking_id')->run_type()->get_data())) === 0){
			return false;
		}

		return true;
	}
	public function load(){
		if($this->is_active()){
			$this->get_script('ga')->set_is_enqueued();

			$this->get_script('default')
				->set_is_enqueued()
				->set_deps(array($this->get_script('ga')->get_handle()))
				->set_localized(array(
				'tracking_id'	=> $this->get_setting('tracking_id')->run_type()->get_data(),
				'user_id'		=> $this->get_user_id_hash()
			));
		}

		return $this;
	}
	public function is_active_user_identification(): bool{
		// activate not set
		if(!$this->get_setting('user_identification')->run_type()->get_data()){
			return false;
		}
		// activate not true
		if($this->get_setting('user_identification')->run_type()->get_data() !== '1'){
			return false;
		}

		return true;
	}
	public function get_user_id_hash(): string{
		if($this->is_active_user_identification()) {
			return md5(wp_hash_password(get_current_user_id()));
		}else{
			return '';
		}
	}
}