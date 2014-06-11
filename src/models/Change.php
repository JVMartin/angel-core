<?php

class Change extends Eloquent {

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