<div class="flex align-items-center list-user-action">
    @can('edit any admin')
        <a class="btn btn-sm btn-icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
            data-original-title="Edit" href="{{ route($routePrefix . '.edit', $model->id) }}" aria-label="Edit" data-bs-original-title="Edit">
            <span class="btn-inner">
                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M11.4925 2.78906H7.75349C4.67849 2.78906 2.75049 4.96606 2.75049 8.04806V16.3621C2.75049 19.4441 4.66949 21.6211 7.75349 21.6211H16.5775C19.6625 21.6211 21.5815 19.4441 21.5815 16.3621V12.3341"
                        stroke="currentColor" stroke-width="1.EOD" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M8.82812 10.921L16.3011 3.44799C17.2321 2.51799 18.7411 2.51799 19.6721 3.44799L20.8891 4.66499C21.8201 5.59599 21.8201 7.10599 20.8891 8.03599L13.3801 15.545C12.9731 15.952 12.4211 16.181 11.8451 16.181H8.09912L8.19312 12.401C8.20712 11.845 8.43412 11.315 8.82812 10.921Z"
                        stroke="currentColor" stroke-width="1.EOD" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M15.1655 4.60254L19.7315 9.16854" stroke="currentColor" stroke-width="1.EOD"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </span>
        </a>
    @endcan
    @can('toggle admin status')
        <a class="btn btn-sm btn-icon {{ $model->status == 'active' ? 'btn-danger' : 'btn-success' }}" data-bs-toggle="tooltip" data-bs-placement="top"
            data-original-title="{{ $model->status == 'active' ? 'Deactivate' : 'Activate' }}" href="{{ route($routePrefix . '.toggleStatus', $model->id) }}" aria-label="{{ $model->status == 'active' ? 'Deactivate' : 'Activate' }}" data-bs-original-title="{{ $model->status == 'active' ? 'Deactivate' : 'Activate' }}">
            <span class="btn-inner">
                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    @if($model->status == 'active')
                        <path d="M13.41 20H6.59C4.4 20 3 18.6 3 16.41V7.59C3 5.4 4.4 4 6.59 4H13.41C15.6 4 17 5.4 17 7.59V16.41C17 18.6 15.6 20 13.41 20Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M7 9.05005L9.64 11.68L15 6.34009" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    @else
                        <path d="M13.41 20H6.59C4.4 20 3 18.6 3 16.41V7.59C3 5.4 4.4 4 6.59 4H13.41C15.6 4 17 5.4 17 7.59V16.41C17 18.6 15.6 20 13.41 20Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M7 9L17 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    @endif
                </svg>
            </span>
        </a>
    @endcan
</div>
