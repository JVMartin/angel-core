<?php namespace Angel\Core;

use App, Redirect, Session, URL;

class AdminLanguageController extends AdminCrudController {

	protected $model	= 'Language';
	protected $uri		= 'languages';
	protected $plural	= 'languages';
	protected $singular	= 'language';
	protected $package	= 'core';

	public function validate_rules($id = null)
	{
		return array(
			'name' => 'required',
			'uri' => 'required|unique:languages,uri,' . $id
		);
	}

	public function hard_delete($id)
	{
		if ($id == $this->data['active_language']->id) {
			return Redirect::to(admin_uri('languages/edit/' . $id))->withErrors('
				<p>You cannot delete the language you\'re currently editing.</p>
				<p>Switch to a different language before deleting this one.</p>
			');
		}

		$Language = App::make('Language');

		$language = $Language::find($id);
		$language->pre_hard_delete();
		$language->forceDelete();

		return Redirect::to(admin_uri('languages'))->with('success', '
			<p>Language "' . $language->name . '" (and related content) successfully deleted forever.</p>
		');
	}

	public function make_active($id)
	{
		$Language = App::make('Language');
		$language = $Language::findOrFail($id);
		Session::put('language', $language->id);
		return Redirect::to(URL::previous());
	}

}