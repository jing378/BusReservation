				<div class="table-wrap">
					<table class="table">
						<thead>
                            <tr>
                                <th>#</th>
								<th><a href="?{!! http_build_query(array_merge(request()->query(), ['sort' => 'full_name', 'order' => $sort === 'full_name' && $order === 'ASC' ? 'DESC' : 'ASC', 'ajax' => 1])) !!}">Name <i class="fas fa-sort"></i></a></th>
								<th><a href="?{!! http_build_query(array_merge(request()->query(), ['sort' => 'username', 'order' => $sort === 'username' && $order === 'ASC' ? 'DESC' : 'ASC', 'ajax' => 1])) !!}">Username <i class="fas fa-sort"></i></a></th>
								<th><a href="?{!! http_build_query(array_merge(request()->query(), ['sort' => 'email', 'order' => $sort === 'email' && $order === 'ASC' ? 'DESC' : 'ASC', 'ajax' => 1])) !!}">Email <i class="fas fa-sort"></i></a></th>
								<th>Phone</th>
								<th><a href="?{!! http_build_query(array_merge(request()->query(), ['sort' => 'role', 'order' => $sort === 'role' && $order === 'ASC' ? 'DESC' : 'ASC', 'ajax' => 1])) !!}">Role <i class="fas fa-sort"></i></a></th>
								<th>Type</th>
								<th><a href="?{!! http_build_query(array_merge(request()->query(), ['sort' => 'is_active', 'order' => $sort === 'is_active' && $order === 'ASC' ? 'DESC' : 'ASC', 'ajax' => 1])) !!}">Status <i class="fas fa-sort"></i></a></th>
								<th><a href="?{!! http_build_query(array_merge(request()->query(), ['sort' => 'created_at', 'order' => $sort === 'created_at' && $order === 'ASC' ? 'DESC' : 'ASC', 'ajax' => 1])) !!}">Created <i class="fas fa-sort"></i></a></th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
                            @if (!$users)
							<tr><td colspan="9" style="text-align:center">No users found.</td></tr>
                            @else
                            @php($i = $offset + 1)
                            @foreach ($users as $u)
							<tr>
                                <td>{!! $i++ !!}</td>
								<td>{{ $u['full_name'] }}</td>
								<td>{{ $u['username'] }}</td>
								<td>{{ $u['email'] }}</td>
								<td>{{ $u['phone'] ?? '' }}</td>
								<td><span class="role-badge role-{{ $u['role'] }}">{{ $u['role'] }}</span></td>
								<td>{{ $u['user_type'] ?? 'Regular' }}</td>
								<td><span class="status-badge status-{!! ((int)$u['is_active'] === 1) ? 'active' : 'inactive' !!}">{!! ((int)$u['is_active'] === 1) ? 'Active' : 'Disabled' !!}</span></td>
								<td>{!! date('M j, Y', strtotime($u['created_at'])) !!}</td>
								<td class="actions">
									<button class="btn btn-sm btn-edit" onclick="editUser({!! $u['id'] !!})" title="Edit User"><i class="fas fa-edit"></i></button>
									<button class="btn btn-sm btn-danger" onclick="deleteUser({!! $u['id'] !!}, '{{ $u['full_name'] }}', '{{ $u['email'] }}', '{{ $u['role'] }}')"><i class="fas fa-trash"></i></button>
								</td>
							</tr>
							@endforeach
                            @endif
						</tbody>
					</table>
				</div>
				@if ($total_pages > 1)
				<div class="pagination">
					<div class="group left">
						@if ($page > 1)
							<a href="?{!! http_build_query(array_merge(request()->query(), ['page' => 1, 'ajax' => 1])) !!}" class="btn btn-outline" title="First"><i class="fas fa-angles-left"></i> First</a>
							<a href="?{!! http_build_query(array_merge(request()->query(), ['page' => $page - 1, 'ajax' => 1])) !!}" class="btn btn-outline" title="Previous"><i class="fas fa-chevron-left"></i> Previous</a>
						@endif
					</div>
					<div class="group center">
						<span class="page-info">Page {!! $page !!} of {!! $total_pages !!} ({!! $total_users !!} total users)</span>
					</div>
					<div class="group right">
						@if ($page < $total_pages)
							<a href="?{!! http_build_query(array_merge(request()->query(), ['page' => $page + 1, 'ajax' => 1])) !!}" class="btn btn-outline" title="Next">Next <i class="fas fa-chevron-right"></i></a>
							<a href="?{!! http_build_query(array_merge(request()->query(), ['page' => $total_pages, 'ajax' => 1])) !!}" class="btn btn-outline" title="Last">Last <i class="fas fa-angles-right"></i></a>
						@endif
					</div>
				</div>
				@endif
