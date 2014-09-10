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

	protected function log_relation_name($object)
	{
		$name = short_name($object) . ' ID#' . $object->id;
		if (isset($object->name) && $object->name) $name .= ' Name: ' . $object->name;
		return $name;
	}

	public function log_relation_change($object, $old_array, $columns, &$changes)
	{
		$name = $this->log_relation_name($object);
		if (!count($old_array)) {
			$changes['Created new ' . $name] = array();
			return;
		}
		foreach ($columns as $column) {
			if ($object->$column == $old_array[$column]) continue;
			$changes['Changed ' . $name . ' Column: ' . $column] = array(
				'old' => $old_array[$column],
				'new' => $object->$column
			);
		}
	}

	public function log_relation_deletion($object, &$changes)
	{
		$name = $this->log_relation_name($object);
		$changes['Deleted ' . $name] = array();
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