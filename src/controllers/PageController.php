<?php namespace Angel\Core;

use App, View, Response;

class PageController extends AngelController {

	public function show($url = 'home', $section = null)
	{
		$Page = App::make('Page');

		$page = $Page::with('modules')->where('url', $url)->first();

		if (!$page || !$page->is_published()) App::abort(404);

		$this->data['page'] = $page;

		$method = str_replace('-', '_', $url);
		if (method_exists($this, $method)) {
			return $this->$method($section);
		}

		return View::make('core::page', $this->data);
	}

	public function page_missing()
	{
		return Response::make(View::make('core::errors.404', $this->data), 404);
	}

}