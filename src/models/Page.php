<?php namespace Angel\Core;

use Illuminate\Database\Eloquent\Collection;
use App, Input;

class Page extends LinkableModel {

	public static function columns()
	{
		return array(
			'name',
			'url',
			'html',
			'js',
			'css',
			'title',
			'meta_description',
			'meta_keywords',
			'og_type',
			'og_image',
			'twitter_card',
			'twitter_image',
			'published',
			'published_range',
			'published_start',
			'published_end'
		);
	}
	public function validate_rules()
	{
		return array(
			'name' => 'required',
			'url'  => 'alpha_dash|unique:pages,url,' . $this->id
		);
	}
	public function validate_custom()
	{
		$errors = array();

		$published_start = Input::get('published_start');
		$published_end   = Input::get('published_end');
		if (Input::get('published_range') && $published_end && strtotime($published_start) >= strtotime($published_end)) {
			$errors[] = 'The publication end time must come after the start time.';
		}

		return $errors;
	}

	///////////////////////////////////////////////
	//                  Events                   //
	///////////////////////////////////////////////
	public static function boot()
	{
		parent::boot();

		static::saving(function($page) {
			$page->plaintext = strip_tags($page->html);
			if (!$page->published_range) {
				$page->published_start = $page->published_end = null;
			}
			$page->title = $page->title ? $page->title : $page->name;
			$page->url   = strtolower($page->url);
		});
		static::saved(function($page) {
			if ($page->skipEvents) return;

			$PageModule    = App::make('PageModule');
			$modules       = $PageModule::where('page_id', $page->id)->get();
			$input_modules = Input::get('modules');

			$input_module_ids = array();
			foreach ($input_modules as $number=>$input_module) {
				// If there's only one module and it's blank, skip it.
				if (!$input_module['name'] && !$input_module['html'] && count($input_modules) == 1) continue;

				// Create a list of module IDs so we can delete the missing modules (which must have been deleted).
				if ($input_module['id']) $input_module_ids[] = $input_module['id'];

				// Grab the existing module if it exists.
				$module_existing = $modules->find($input_module['id']);
				$module          = ($module_existing) ? $module_existing : new $PageModule;

				// If the module exists, log its changes.
				$input_module['number'] = $number;
				if ($module_existing) {
					foreach (array('number', 'html', 'name') as $column) {
						if ($input_module[$column] != $module->$column) {
							$changes['Module ID#' . $module->id . ' ' . $column] = array(
								'old' => $module->$column,
								'new' => $input_module[$column]
							);
						}
					}
				}

				// Save that bad boy.
				$module->page_id = $page->id;
				$module->number  = $number;
				$module->name    = $input_module['name'];
				$module->html    = $input_module['html'];
				$module->save();
			}

			// Delete the deleted modules.
			foreach ($modules as $module) {
				if (!in_array($module->id, $input_module_ids)) {
					$module->delete();
				}
			}
		});
	}

	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function modules()
	{
		return $this->hasMany(App::make('PageModule'))->orderBy('number');
	}

	///////////////////////////////////////////////
	//               Menu Linkable               //
	///////////////////////////////////////////////
	public function link()
	{
		$url = ($this->url == 'home') ? '/' : $this->url;

		return url($url);
	}

	public function link_edit()
	{
		return admin_url('pages/edit/' . $this->id);
	}

	public function search($terms)
	{
		$results = new Collection;

		// Keep track of the pages we add, so we don't have repeats when we
		// search the PageModules.
		$pageIDs = array();

		// Search all pages.
		static::where(function($query) use ($terms) {
			foreach ($terms as $term) {
				$query->orWhere('name',             'like', $term);
				$query->orWhere('url',              'like', $term);
				$query->orWhere('plaintext',        'like', $term);
				$query->orWhere('meta_description', 'like', $term);
				$query->orWhere('meta_keywords',    'like', $term);
			}
		})->get()->each(function($result) use ($results, $pageIDs) {
			$results->add($result);
			$pageIDs[] = $result->id;
		});

		// Search all PageModules.
		$PageModule = App::make('PageModule');
		$PageModule::with('page')->where(function($query) use ($terms) {
			foreach ($terms as $term) {
				$query->orWhere('name',      'like', $term);
				$query->orWhere('plaintext', 'like', $term);
			}
		})->get()->each(function($result) use ($results, $pageIDs) {
			if (in_array($result->page->id, $pageIDs)) return;
			$results->add($result->page);
		});

		return $results;
	}

	///////////////////////////////////////////////
	//                View-Related               //
	///////////////////////////////////////////////
	public function meta_html()
	{
		$html = '';
		if ($this->title) {
			$html .= '<meta name="og:title" content="' . $this->title . '" />' . "\n";
			$html .= '<meta name="twitter:title" content="' . $this->title . '" />' . "\n";
		}
		if ($this->meta_description) {
			$html .= '<meta name="description" content="' . $this->meta_description . '" />' . "\n";
			$html .= '<meta name="og:description" content="' . $this->meta_description . '" />' . "\n";
			$html .= '<meta name="twitter:description" content="' . $this->meta_description . '" />' . "\n";
		}
		if ($this->meta_keywords) {
			$html .= '<meta name="keywords" content="' . $this->meta_keywords . '" />' . "\n";
		}
		if ($this->url) {
			$html .= '<meta name="og:url" content="' . $this->link() . '" />' . "\n";
			$html .= '<meta name="twitter:url" content="' . $this->link() . '" />' . "\n";
		}
		if ($this->og_type) {
			$html .= '<meta name="og:type" content="' . $this->og_type . '" />' . "\n";
		}
		if ($this->og_image) {
			$html .= '<meta name="og:image" content="' . $this->og_image . '" />' . "\n";
		}
		if ($this->twitter_card) {
			$html .= '<meta name="twitter:card" content="' . $this->twitter_card . '" />' . "\n";
		}
		if ($this->twitter_image) {
			$html .= '<meta name="twitter:image" content="' . $this->twitter_image . '" />' . "\n";
		}
		return $html;
	}

	public function is_published()
	{
		if ((
				$this->published_range &&
				(strtotime($this->published_start) > time() || strtotime($this->published_end) < time())
			) || (
				!$this->published_range &&
				!$this->published
			)) return false;
		return true;
	}
}