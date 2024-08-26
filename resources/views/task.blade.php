@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <div id="enter-button-container" style="text-align: center;">
                    <button id="enter-button" class="btn btn-primary">Enter</button>
                </div>

                <div id="task-container" style="display: none;">
                    <div class="card card-new-task">
                        <div class="card-header">New Task</div>
                        <div class="card-body">
                            <form id="new-task-form" method="POST" action="{{ route('tasks.store') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input id="title" name="title" type="text" maxlength="255" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" autocomplete="off" />
                                    @if ($errors->has('title'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('title') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <button type="submit" class="btn btn-primary">Add Task</button>
                            </form>
                        </div>
                    </div>

                    <button id="show-all-tasks" class="btn btn-secondary" style="margin-top: 10px;">Show All Tasks</button>
                </div>

                <div class="card" id="task-list-container" style="display: none; margin-top: 10px;">
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Task Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="task-list">
                                <!-- Existing tasks will be dynamically added here -->
                            </tbody>
                        </table>

                        @isset($tasks)
                            {{ $tasks->links() }}
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update Task Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="update-form">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="task-id" name="task_id" />
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="pending">Pending</option>
                                <option value="complete">Done</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include jQuery, Bootstrap, and Font Awesome -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#enter-button').click(function() {
                $('#enter-button-container').hide();
                $('#task-container').show();
                $('#task-list-container').hide();
            });

            $('#show-all-tasks').click(function() {
                $('#task-container').show();
                $('#task-list-container').show();

                $.ajax({
                    url: '{{ route('tasks.index') }}',
                    method: 'GET',
                    success: function(response) {
                        $('#task-list').empty();
                        response.tasks.forEach(function(task) {
                            let statusText = task.is_complete ? task.title : task.title;
                            let completeIcon = task.is_complete ? '' : `
                                <button type="button" class="btn btn-link text-primary update-btn" data-id="${task.id}" data-title="${task.title}" data-status="${task.is_complete ? 'complete' : 'pending'}">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            `;
                            let taskRow = `
                                <tr id="task-${task.id}">
                                    <td>${task.id}</td>
                                    <td>${statusText}</td>
                                    <td>${task.is_complete ? 'Done' : 'Pending'}</td>
                                    <td class="text-right">
                                        <input type="checkbox" class="complete-task" data-id="${task.id}" ${task.is_complete ? 'checked disabled' : ''}>
                                        ${completeIcon}
                                        <form method="POST" action="{{ route('tasks.destroy', ':id') }}" class="delete-form d-inline" data-id="${task.id}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            `;
                            $('#task-list').append(taskRow.replace(/:id/g, task.id));
                        });
                    }
                });
            });

            $('#new-task-form').submit(function(event) {
                event.preventDefault();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        let newTask = `
                            <tr id="task-${response.id}">
                                <td>${response.id}</td>
                                <td>${response.title}</td>
                                <td>Pending</td>
                                <td class="text-right">
                                    <input type="checkbox" class="complete-task" data-id="${response.id}">
                                    <button type="button" class="btn btn-link text-primary update-btn" data-id="${response.id}" data-title="${response.title}" data-status="pending">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <form method="POST" action="{{ route('tasks.destroy', ':id') }}" class="delete-form d-inline" data-id="${response.id}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        `;
                        $('#task-list').append(newTask.replace(/:id/g, response.id));
                        $('#new-task-form')[0].reset();
                    }
                });
            });

            $(document).on('click', '.update-btn', function() {
                var taskId = $(this).data('id');
                var taskTitle = $(this).data('title');
                var taskStatus = $(this).data('status');

                $('#task-id').val(taskId);
                $('#status').val(taskStatus);
                $('#updateModal').modal('show');
            });

            $('#update-form').submit(function(event) {
                event.preventDefault();

                var form = $(this);
                var taskId = $('#task-id').val();
                var status = $('#status').val();

                $.ajax({
                    url: '{{ route('tasks.update', ':id') }}'.replace(':id', taskId),
                    method: 'POST',
                    data: form.serialize(),
                    success: function() {
                        let row = $(`#task-${taskId}`);
                        row.find('td').eq(2).text(status === 'complete' ? 'Done' : 'Pending');
                        $('#updateModal').modal('hide');
                    }
                });
            });

            $(document).on('submit', '.delete-form', function(event) {
                event.preventDefault();
                if (confirm('Are you sure you want to delete this task?')) {
                    let form = $(this);
                    $.ajax({
                        url: form.attr('action').replace(':id', form.data('id')),
                        method: 'DELETE',
                        data: form.serialize(),
                        success: function() {
                            form.closest('tr').remove();
                        }
                    });
                }
            });

            $(document).on('change', '.complete-task', function() {
                var taskId = $(this).data('id');
                var isChecked = $(this).is(':checked');

                $.ajax({
                    url: '{{ route('tasks.update', ':id') }}'.replace(':id', taskId),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'PUT',
                        status: isChecked ? 'complete' : 'pending'
                    },
                    success: function() {
                        let row = $(`#task-${taskId}`);
                        row.find('td').eq(2).text(isChecked ? 'Done' : 'Pending');
                        if (isChecked) {
                            row.hide(); // Hide the row if the task is completed
                        }
                    }
                });
            });
        });
    </script>
@endsection
