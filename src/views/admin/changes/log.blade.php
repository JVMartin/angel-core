@if (!count($changes))
	<p><em>No changes yet.</em></p>
@else
	<table class="table table-striped">
		<tbody>
			@foreach ($changes as $change)
				<tr>
					<td>
						<button type="button" class="btn btn-xs btn-info" data-toggle="modal" data-target="#changeModal{{ $change->id }}">
							<span class="glyphicon glyphicon-eye-open"></span>
						</button>
						<div class="modal fade" id="changeModal{{ $change->id }}" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title">Change Details</h4>
									</div>
									<div class="modal-body">
										<table class="table table-striped">
											<tbody>
												<tr>
													<td><strong>Date</strong></td>
													<td>{{ $change->created_at }}</td>
												</tr>
												<tr>
													<td><strong>User</strong></td>
													<td>{{ $change->user->full_name() }}</td>
												</tr>
												<tr>
													<td><strong>Email</strong></td>
													<td>{{ $change->user->email }}</td>
												</tr>
											</tbody>
										</table>
										@foreach (json_decode($change->changes) as $column=>$this_change)
											<div class="panel panel-info">
												<div class="panel-heading">
													<strong>{{ $column }}</strong>
												</div>
												@if (isset($this_change->old) || isset($this_change->new))
													<table class="table table-bordered">
														<thead>
															<tr>
																<th class="text-center">Before</th>
																<th class="text-center">After</th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td>
																	{{ isset($this_change->old) ? $this_change->old : '' }}
																</td>
																<td>
																	{{ isset($this_change->new) ? $this_change->new : '' }}
																</td>
															</tr>
														</tbody>
													</table>
												@endif
											</div>
										@endforeach
									</div>{{-- Modal --}}
								</div>{{-- Modal --}}
							</div>{{-- Modal --}}
						</div>{{-- Modal --}}
					</td>
					<td>
						{{ $change->created_at }}
					</td>
				</tr>{{-- Change Row --}}
			@endforeach
		</tbody>
	</table>{{-- Changes Table --}}
@endif