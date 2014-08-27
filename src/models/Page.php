<?php namespace Angel\Core;

use App, Config;
use Illuminate\Database\Eloquent\Collection;

class Page extends LinkableModel {

	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function changes()
	{
		$Change = App::make('Change');

		return $Change::where('fmodel', 'Page')
				   	       ->where('fid', $this->id)
				   	       ->with('user')
				   	       ->orderBy('created_at', 'DESC')
				   	       ->get();
	}

	public function modules()
	{
		return $this->hasMany(App::make('PageModule'))->orderBy('number');
	}

	public function pre_delete()
	{
		parent::pre_delete();
		$Change = App::make('Change');
		$Change::where('fmodel', 'Page')
			        ->where('fid', $this->id)
			        ->delete();
	}

	///////////////////////////////////////////////
	//               Menu Linkable               //
	///////////////////////////////////////////////
	public function link()
	{
		$language_segment = (Config::get('core::languages')) ? $this->language->uri . '/' : '';

		$url = ($this->url == 'home') ? '' : $this->url;

		return url($language_segment . $url);
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