<?php namespace Angel\Core;

use Mobile_Detect, ToolBelt, ReflectionClass;
use Input, Session, App, Config;

class AngelController extends \BaseController {

	protected $settings;

	protected $data = array(
		'error'   => array(),
		'success' => array(),
		'mobile'  => false
	);

	protected $mobile = false;

	public function __construct()
	{
		$reflection = new ReflectionClass(__CLASS__);
		if (method_exists($reflection->getParentClass()->name, '__construct')) {
			parent::__construct();
		}

		$detect = new Mobile_Detect;
		if (($detect->isMobile() || Input::get('mobile')) && !Input::get('desktop')) {
			$this->mobile         = true;
			$this->data['mobile'] = true;
		}

		// Used for alerting the user
		if (Session::has('errors')) {
			foreach(Session::get('errors')->all() as $message) {
				$this->data['error'][] = $message;
			}
		}
		if (Session::has('success')) {
			if (is_array(Session::get('success'))) {
				$this->data['success'] = Session::get('success');
			} else {
				$this->data['success'][] = Session::get('success');
			}
		}

		///////////////////////////////////////////////
		//               Global Input                //
		///////////////////////////////////////////////
		if (Input::exists('pq') && Auth::check() && Auth::user()->is_superadmin()) {
			App::after(function() {
				ToolBelt::print_queries();
			});
		}
		if (Input::exists('ps') && Auth::check() && Auth::user()->is_superadmin()) {
			ToolBelt::debug(Session::all());
		}
		if (Input::exists('fs')) {
			Session::flush();
		}

		///////////////////////////////////////////////
		//           Global View Bindings            //
		///////////////////////////////////////////////
		$this->data['Menu'] = App::make('Menu');

		///////////////////////////////////////////////
		//                Settings                   //
		///////////////////////////////////////////////
		$Setting                = App::make('Setting');
		$this->settings         = $Setting::currentSettings();
		$this->data['settings'] = $this->settings;

		///////////////////////////////////////////////
		//                Languages                  //
		///////////////////////////////////////////////
		if (Config::get('core::languages'))  {
			$Language        = App::make('Language');
			$this->languages = $Language::all();
			$language_drop   = array();
			foreach ($this->languages as $language) {
				$language_drop[$language->id] = $language->name;
			}
			$this->data['language_drop']   = $language_drop;
			$this->data['all_languages']   = $this->languages;
			$this->data['single_language'] = count($this->languages) == 1 ? true : false;

			// Handle the current active language
			if (!Session::get('language')) {
				Session::put('language', $Language::primary()->id);
			}
			$this->data['active_language'] = $this->languages->find(Session::get('language'));
		}
	}

}