<div class="module">
	<button type="button" class="removeModule btn btn-xs btn-danger" style="float:right;">
		<span class="glyphicon glyphicon-remove"></span>
	</button>
	<button type="button" class="btn btn-xs btn-default handle" style="float:right;margin-right:10px;">
		<span class="glyphicon glyphicon-resize-vertical"></span>
	</button>
	<input type="hidden" value="{{ isset($module) ? $module->id : '' }}" class="moduleID" />
	<p><b>Module <span class="showNumber"></span></b></p>
	<p>
		<input type="text" value="{{ isset($module) ? $module->name : '' }}" class="moduleName form-control" placeholder="Name" style="width:auto;" autocomplete="off" />
	</p>
	<textarea class="ckeditor" autocomplete="off">
		{{ isset($module) ? $module->html : '' }}
	</textarea>
</div>