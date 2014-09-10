<?php namespace Angel\Core;

use Eloquent, Auth;

class Change extends Eloquent {

	public static function log($model, $changes)
	{
		if (!count($changes)) return;

		$change = new static;
		$change->user_id = Auth::user()->id;
		$change->fmodel  = short_name($model);
		$change->fid     = $model->id;
		$change->changes = json_encode($changes);
		$change->save();
	}

	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function user()
	{
		return $this->belongsTo('User')->withTrashed();
	}

	///////////////////////////////////////////////
	//                  Mutators                 //
	///////////////////////////////////////////////
	public function setUpdatedAt($value)
	{
		// Override - do nothing.  (We don't need this column, but we're still using the built-in timestamp
		// for created_at.)
	}
}