@extends('oxygen::layouts.master-dashboard')

<?php $pageTitle = 'User Invitations' ?>

@section('content')
        {{ lotus()->pageHeadline($pageTitle) }}

        {{ lotus()->breadcrumbs([
            ['Dashboard', route('dashboard')],
            ['Access Permissions', route('manage.access.index')],
            [$pageTitle, null, true]
        ]) }}

        <a href="{{ route('access.invitations.create') }}" class="btn btn-wide btn-success mb-3"><i class="fa fa-plus-circle"></i> Invite New Users</a>

        @if ($invitations->isEmpty())
            {{ lotus()->emptyStatePanel() }}
        @else
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <div class="card-header">Sent Invitations</div>
                        <div class="card-body">

                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Invited Role</th>
                                    <th>Sent</th>
                                    <th>Status</th>
                                    <th>Invite Link</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($invitations as $invitation)
                                    <tr>
                                        <td>{{ $invitation->email }}</td>
                                        <td>
                                            @if ($invitation->role)
                                                {{ $invitation->role->title }}
                                            @endif
                                        </td>
                                        <td>
											@if ($invitation->sent_at)
												{{ $invitation->sent_at->diffForHumans() }}
											@else
												Not Sent
											@endif
										</td>
                                        <td>
                                            @if (empty($invitation->claimed_at))
                                                <span class="label label-primary">Pending to Accept</span>
                                            @else
                                                Accepted {{ $invitation->claimed_at->diffForHumans() }}
                                            @endif
                                        </td>
                                        <td>
                                            @if (empty($invitation->claimed_at))
                                                <a href="{{ $invitation->invitation_code_permalink }}"
                                                   class="btn btn-warning"
                                                   data-bs-toggle="tooltip"
                                                   target="_blank"
                                                   title="Visit link">
                                                    <i class="fas fa-external-link-alt"></i> Link
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            @if (empty($invitation->claimed_at))
                                                <form class="form-inline js-confirm-delete" role="form" method="POST"
                                                	  action="/account/invitations/{{ $invitation->id }}"
                                                      data-bs-toggle="tooltip" title="Delete Invite">
                                                    @csrf
                                                    <input type="hidden" name="_method" value="delete" />
                                                    <button class="btn btn-danger"><i class="fas fa-times"></i> Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

            {{ lotus()->pageNumbers($invitations) }}
        @endif

@endsection
