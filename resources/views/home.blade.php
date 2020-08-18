@extends('welcome')

@section('content')
    <div id="table">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card border-primary">
                        <div class="card-header border-primary card-info">
                            <h1 class="card-title">
                                To Do List
                                <button class="btn btn-success btn-sm float-right addModalBtn">Add new</button>
                            </h1>
                        </div>
                        <div class="card-body">
                            <table class="table table-dark">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Desc</th>
                                    <th scope="col">Action</th>
                                </tr>
                                </thead>
                                <tbody id="todo-list" data-name="todo-list">
                                @php($i = 1)
                                @forelse($todos as $key => $todo)
                                    <tr id="todo{{ $todo->id }}">
                                        <th scope="row">{{ $i++ }}</th>
                                        <td>{{ $todo->title }}</td>
                                        <td>
                                            {{ Str::limit($todo->desc, 30) }}
                                        </td>
                                        <td>
                                            <button class="btn btn-info btn-sm open-modal" id="edit{{ $todo->id }}"
                                                    value="{{ $todo->id }}">
                                                Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm delete-todo" id="delete{{ $todo->id }}"
                                                    value="{{ $todo->id
                                        }}">Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%">No data found!</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="linkEditorModalLabel">Todo Editor</h4>
                </div>
                <div class="modal-body">
                    <form id="modalFormData" name="modalFormData" class="form-horizontal" novalidate="">

                        <div class="form-group">
                            <label for="title" class="col-sm-2 control-label">Title</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="title" name="title"
                                       placeholder="Enter title" value="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="desc" class="col-sm-2 control-label">Description</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="desc" name="desc"
                                       placeholder="Enter Description" value="">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btn-save" value="add">Save changes
                    </button>
                    <input type="hidden" id="todo_id" name="todo_id" value="0">
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script>
        $(function () {
            $('.addModalBtn').click(function () {
                $('#btn-save').val('add');
                $('#modalFormData').trigger("reset");
                $('#modal').modal('show');
            });

            $('.open-modal').on('click', function () {
                let todo_id = $(this).val();
                console.log(todo_id);
                $.get('todos/' + todo_id, function (data) {
                    $('#todo_id').val(data.id);
                    $('#title').val(data.title);
                    $('#desc').val(data.desc);
                    $('#btn-save').val("update");
                    $('#modal').modal('show');
                });
            });

            $('#btn-save').click(function (e) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });

                e.preventDefault();
                let formData = {
                    title: $('#title').val(),
                    desc: $('#desc').val(),
                };
                let state = $('#btn-save').val();
                let type = "POST";
                let todo_id = $("#todo_id").val();
                let ajaxUrl = 'todos';
                if (state == "update") {
                    type = "PUT";
                    ajaxUrl = 'todos/' + todo_id;
                }

                $.ajax({
                    type: type,
                    url: ajaxUrl,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        if (state == "add") {
                            $('#table').load(location.href + '#table');
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                onOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            })

                            Toast.fire({
                                icon: 'success',
                                title: 'Added Successful'
                            })
                        } else {
                            $('#table').load(location.href + '#table');
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                onOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            })

                            Toast.fire({
                                icon: 'success',
                                title: 'Update successful'
                            })
                        }
                        $('#modalFormData').trigger('reset');
                        $('#modal').modal('hide');
                    },
                    error: function (data) {
                        console.log("Error: ", data);
                    }
                });
            });

            $('.delete-todo').click(function () {
                var todo_id = $(this).val();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });
                $('#delete' + todo_id).html('<i class="fa fa-refresh fa-spin" style="font-size:24px"></i>');
                $.ajax({
                    type: "DELETE",
                    url: 'todos/' + todo_id,
                    success: function (data) {
                        console.log(data);
                        $("#todo" + todo_id).remove();
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            });
            setInterval(function () {
                getUpdates();
            }, 6000);
        });


        function getUpdates() {
            $("#table").load(location.href + "#table");
        }
    </script>
@endpush
