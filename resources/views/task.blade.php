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

                <div id="enter-button-container">
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
                                <button type="submit" class="btn btn-primary">Create</button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">Tasks</div>
                        <div class="card-body">
                            <table class="table table-striped">
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
    </div>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#enter-button').click(function() {
                $('#enter-button-container').hide();
                $('#task-container').show();

                $.ajax({
                    url: '{{ route('tasks.index') }}',
                    method: 'GET',
                    success: function(response) {
                        $('#task-list').empty();
                        response.tasks.forEach(function(task) {
                            let taskRow = `
                                <tr id="task-${task.id}">
                                    <td>${task.is_complete ? `<s>${task.title}</s>` : task.title}</td>
                                    <td class="text-right">
                                        ${task.is_complete ? '' : `
                                            <form method="POST" action="{{ route('tasks.update', ':id') }}" class="complete-form" data-id="${task.id}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-primary">Complete</button>
                                            </form>`}
                                        <form method="POST" action="{{ route('tasks.destroy', ':id') }}" class="delete-form" data-id="${task.id}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
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
                                <td>${response.title}</td>
                                <td class="text-right">
                                    <form method="POST" action="{{ route('tasks.update', ':id') }}" class="complete-form" data-id="${response.id}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-primary">Complete</button>
                                    </form>
                                    <form method="POST" action="{{ route('tasks.destroy', ':id') }}" class="delete-form" data-id="${response.id}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        `;
                        $('#task-list').append(newTask.replace(/:id/g, response.id));
                        $('#new-task-form')[0].reset();
                    }
                });
            });

            $(document).on('submit', '.complete-form', function(event) {
                event.preventDefault();
                let form = $(this);
                $.ajax({
                    url: form.attr('action').replace(':id', form.data('id')),
                    method: 'POST',
                    data: form.serialize(),
                    success: function() {
                        form.closest('tr').find('td').first().html(`<s>${form.closest('tr').find('td').first().text()}</s>`);
                        form.remove();
                    }
                });
            });

            $(document).on('submit', '.delete-form', function(event) {
                event.preventDefault();
                if (confirm('Are you sure you want to delete this task?')) {
                    let form = $(this);
                    $.ajax({
                        url: form.attr('action').replace(':id', form.data('id')),
                        method: 'POST',
                        data: form.serialize(),
                        success: function() {
                            form.closest('tr').remove();
                        }
                    });
                }
            });
        });
    </script>
@endsection
