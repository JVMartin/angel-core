<?php namespace Angel\Core;

use BaseController,
	Mobile_Detect,
	Input,
	Session,
	App,
	Config;

class AngelController extends BaseController {

	protected $settings;

	protected $data = array(
		'error' => array(),
		'success' => array(),
		'mobile' => false
	);

	protected $mobile = false;

	public function __construct()
	{
		$detect = new Mobile_Detect;
		if (($detect->isMobile() || Input::get('mobile')) && !Input::get('desktop')) {
			$this->mobile = true;
			$this->data['mobile'] = true;
		}

		// Used for alerting the user
		if (Session::has('errors')) {
			foreach(Session::get('errors')->all() as $message) {
				$this->data['error'][] = $message;
			}
		}
		if (Session::has('success')) {
			$this->data['success'][] = Session::get('success');
		}

		if (Input::exists('pq')) {
			App::after(function() {
				ToolBelt::print_queries();
			});
		}

		///////////////////////////////////////////////
		//           Global View Bindings            //
		///////////////////////////////////////////////
		$this->data['menuModel'] = App::make('Menu');

		///////////////////////////////////////////////
		//                Settings                   //
		///////////////////////////////////////////////
		$settingModel = App::make('Setting');
		$this->settings = $settingModel::currentSettings();
		$this->data['settings'] = $this->settings;

		///////////////////////////////////////////////
		//                Languages                  //
		///////////////////////////////////////////////
		if (Config::get('core::languages'))  {
			$languageModel = App::make('Language');
			$this->languages = $languageModel::all();
			$language_drop = array();
			foreach ($this->languages as $language) {
				$language_drop[$language->id] = $language->name;
			}
			$this->data['language_drop'] = $language_drop;
			$this->data['all_languages'] = $this->languages;
			$this->data['single_language'] = count($this->languages) == 1 ? true : false;

			// Handle the current active language
			if (!Session::get('language')) {
				Session::put('language', $languageModel::primary()->id);
			}
			$this->data['active_language'] = $this->languages->find(Session::get('language'));
		}
	}

}