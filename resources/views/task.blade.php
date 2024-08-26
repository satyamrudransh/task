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
                    <h5 class="modal-title" id="updateModalLabel">Update Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="update-form">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="task-id" name="task_id" />
                        <div class="form-group">
                            <label for="update-title">Title</label>
                            <input id="update-title" name="title" type="text" class="form-control" />
                            <span class="invalid-feedback" id="update-title-error" role="alert" style="display: none;"></span>
                        </div>
                        <div class="form-group mt-2">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="pending">Non completed</option>
                                <option value="complete">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Task</button>
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
                            let statusText = task.is_complete ? 'Completed' : 'Non completed';
                            let taskRow = `
                                <tr id="task-${task.id}">
                                    <td>${task.id}</td>
                                    <td>${task.title}</td>
                                    <td>${statusText}</td>
                                    <td class="text-right">
                                        <input type="checkbox" class="complete-task" data-id="${task.id}" ${task.is_complete ? 'checked disabled' : ''}>
                                        <button type="button" class="btn btn-link text-primary update-btn" data-id="${task.id}" data-title="${task.title}" data-status="${task.is_complete ? 'complete' : 'pending'}">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-link text-danger delete-btn" data-id="${task.id}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                            $('#task-list').append(taskRow);
                        });
                    }
                });
            });

            $('#new-task-form').submit(function(event) {
                event.preventDefault();
                $('#update-title-error').hide();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        let existingTask = $(`#task-${response.id}`);
                        if (existingTask.length === 0) { // Ensure no duplicate tasks
                            let newTask = `
                                <tr id="task-${response.id}">
                                    <td>${response.id}</td>
                                    <td>${response.title}</td>
                                    <td>Non completed</td>
                                    <td class="text-right">
                                        <input type="checkbox" class="complete-task" data-id="${response.id}">
                                        <button type="button" class="btn btn-link text-primary update-btn" data-id="${response.id}" data-title="${response.title}" data-status="pending">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-link text-danger delete-btn" data-id="${response.id}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                            $('#task-list').append(newTask);
                        }
                        $('#new-task-form')[0].reset();
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON.errors && xhr.responseJSON.errors.title) {
                            alert(xhr.responseJSON.errors.title[0]); // Display error
                        }
                    }
                });
            });

            $(document).on('click', '.update-btn', function() {
                var taskId = $(this).data('id');
                var taskTitle = $(this).data('title');
                var taskStatus = $(this).data('status');

                $('#task-id').val(taskId);
                $('#update-title').val(taskTitle);
                $('#status').val(taskStatus);
                $('#updateModal').modal('show');
            });

            $('#update-form').submit(function(event) {
                event.preventDefault();
                $('#update-title-error').hide(); // Hide previous error

                var form = $(this);
                var taskId = $('#task-id').val();
                var title = $('#update-title').val();
                var status = $('#status').val();

                $.ajax({
                    url: '{{ route('tasks.update', ':id') }}'.replace(':id', taskId),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'PUT',
                        title: title,
                        status: status
                    },
                    success: function(response) {
                        let row = $(`#task-${taskId}`);
                        row.find('td').eq(1).text(response.title); // Update task name in the table
                        row.find('td').eq(2).text(status === 'complete' ? 'Completed' : 'Non completed'); // Update status in the table
                        
                        // Update checkbox based on status
                        const checkbox = row.find('.complete-task');
                        if (status === 'complete') {
                            checkbox.prop('checked', true);
                            checkbox.prop('disabled', true);
                        } else {
                            checkbox.prop('checked', false);
                            checkbox.prop('disabled', false);
                        }

                        $('#updateModal').modal('hide');
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON.errors && xhr.responseJSON.errors.title) {
                            $('#update-title-error').text(xhr.responseJSON.errors.title[0]).show();
                        }
                    }
                });
            });

            $(document).on('submit', '.delete-form', function(event) {
                event.preventDefault();
                var form = $(this);
                var taskId = form.data('id');

                $.ajax({
                    url: form.attr('action').replace(':id', taskId),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $(`#task-${taskId}`).remove();
                    }
                });
            });

            $(document).on('change', '.complete-task', function() {
                var checkbox = $(this);
                var taskId = checkbox.data('id');
                var isComplete = checkbox.is(':checked');

                $.ajax({
                    url: '{{ route('tasks.update', ':id') }}'.replace(':id', taskId),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'PUT',
                        status: isComplete ? 'complete' : 'pending'
                    },
                    success: function(response) {
                        if (isComplete) {
                            $(`#task-${taskId}`).remove();
                        } else {
                            checkbox.prop('checked', false);
                            checkbox.prop('disabled', false);
                        }
                    }
                });
            });

            $(document).on('click', '.delete-btn', function() {
                var deleteBtn = $(this);
                var taskId = deleteBtn.data('id');
                
                if (confirm('Are you sure to delete this task?')) {
                    $.ajax({
                        url: '{{ route('tasks.destroy', ':id') }}'.replace(':id', taskId),
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            $(`#task-${taskId}`).remove();
                        }
                    });
                }
            });
        });
    </script>
@endsection
