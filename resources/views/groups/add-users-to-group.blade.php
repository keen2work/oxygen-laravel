<div class="modal fade" id="userControlModal" tabindex="-1" role="dialog" aria-labelledby="userControlModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="usersForm">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="modal-header">
                    <h5 class="modal-title" id="userControlModalLabel">Headline</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <p>Assign users to one or many groups.</p>

                    <div class="form-group multi-select-wrapper">
                        <label for="recipient-name" class="control-label">Select Groups</label>
                        <select class="form-control select2" id="selectRoles" name="selectRoles[]" multiple="multiple" style="width: 100%">
                            @foreach ($availableRoles as $availableRole)
                                <option value="{{ $availableRole['id'] }}">{{ $availableRole['title'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group multi-select-wrapper">
                        <label for="recipient-name" class="control-label">Select New Users</label>
                        <select class="form-control select2" id="selectUsers" name="selectUsers[]" multiple="multiple" style="width: 100%">
                            @foreach ($users as $selectedUser)
                                <option value="{{ $selectedUser->id }}">{{ $selectedUser->name }} - {{ $selectedUser->email }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="modalErrorMessage" class="alert alert-danger"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-lg btn-secondary text-end" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-lg btn-success" id="addUsersButton">
                        <i class="fa fa-spin fa-spinner" id="loadingIndicator"></i> Add User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
	@vite(['resources/js/add-users-to-group.js'])
@endpush
