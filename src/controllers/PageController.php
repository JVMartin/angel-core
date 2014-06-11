<?php

class PageController extends KrakenController {

	public function show($url = 'home', $section = null)
	{
		$page = Page::where('url', $url)->first();

		if (!$page || !$page->is_published()) App::abort(404);

		$this->data['page'] = $page;

		$method = str_replace('-', '_', $url);
		if (method_exists($this, $method)) {
			return $this->$method($section);
		}

		return View::make('core::page', $this->data);
	}

	public function show_language($language_uri = 'en', $url = 'home', $section = null)
	{
		$language = $this->languages->filter(function ($language) use ($language_uri) {
			return ($language->uri == $language_uri);
		})->first();

		if (!$language) App::abort(404);

		$page = Page::where('language_id', $language->id)
			        ->where('url', $url)
					->first();

		if (!$page || !$page->is_published()) App::abort(404);

		$this->data['active_language']  = $language;
		$this->data['page']				= $page;

		$method = str_replace('-', '_', $url);
		if (method_exists($this, $method)) {
			return $this->$method($section);
		}

		return View::make('core::page', $this->data);
	}

	public function page_missing()
	{
		return View::make('core::errors.404', $this->data);
	}

}