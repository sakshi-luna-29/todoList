<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>ToDO List</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

</head>

<body>
    <h1>ToDo List</h1>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <div class="row justify-content-center mt-5">
        <div class="col-lg-6">
            @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
            @endif

            @if ($errors->any())
            @foreach ($errors->all() as $error)
            <div class="alert alert-danger">
                {{$error}}
            </div>
            @endforeach
            @endif
        </div>
    </div>
    <div class="text-center mt-5">

        <form class="row g-3 justify-content-center" id="taskForm">
            @csrf
            <div class="row col-4">
                <input type="text" class="form-control" id="title" name="title" placeholder="Title" required>
            </div>

            <!-- <div class=" col-1">
                <label class="container">Done
                    <input type="checkbox" name="is_completed" id="is_completed" placeholder="Done"> <span class="checkmark"></span>
                </label>
            </div> -->

            <div class="row col-auto">
                <button type="submit" class="btn btn-primary mb-3">Add ToDO List</button>
            </div>


        </form>

    </div>
    <div class="text-center">
        <div class="row  col-2">
            <button id="show_all" class="btn btn-primary mb-3">Show All Task</button>
        </div>

        <h2>All Todos</h2>
        <div class="row justify-content-center">
            <div class="col-lg-6">

                <table class="table table-bordered" id="taskTable">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php $counter=1 @endphp
                        @foreach($todos as $todo)
                        <tr id="task_{{$todo->id}}">
                            <th>{{$counter}}</th>
                            <td>{{$todo->title}}</td>
                            <td>
                                @if($todo->is_completed)
                                <div class="badge bg-success">Done</div>
                                @else
                                <div class="badge bg-warning">Not Done</div>
                                @endif
                            </td>
                            <td>
                                <!-- <button class="btn btn-danger" onclick="updateTask({{$todo->id}})">Edit</button> -->
                                <input type="checkbox" id="task_status" onclick="updateTask({{$todo->id}})">
                                <button class="btn btn-danger" onclick="deleteTask({{$todo->id}})">Delete</button>

                            </td>
                        </tr>

                        @php $counter++; @endphp

                        @endforeach
                        <input type="hidden" id="counter" value="{{$counter}}">

                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
<script>
    $(document).ready(function() {
        $('#show_all').click(function() {
            $.ajax({
                url: '/task/',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                success: function(tasks) {
                    console.log(tasks.data);
                    var tas = tasks.data;
                    $('#taskTable tbody').empty(); // Remove all child elements from tbody
                    var cc = 1;
                    tas.forEach(function(task) {
                        if (task.is_completed) {
                            var comp = '<div class = "badge bg-success"> Done </div>'
                        } else {
                            var comp = '<div class = "badge bg-warning" > Not Done </div>'
                        }

                        $('#taskTable tbody').append('<tr><td>' + cc + '</td><td>' + task.title + '</td><td>' + comp + '</td><td><button class="btn btn-danger" onclick="deleteTask({{' + task.id + '}})">Edit</button><button class="btn btn-danger" onclick="deleteTask({{' + task.id + '}})">Delete</button></td></tr>');
                        cc++;

                    });
                    $('#title').val(''); // Clear the input field
                    $('#is_completed').val(''); // Clear the input field

                },
                error: function(xhr) {
                    console.log('Error adding task');
                }
            });
        });
        // });
        $('#taskForm').submit(function(event) {
            event.preventDefault(); // Prevent form submission
            var taskName = $('#title').val();
            var taskComp = $('#is_completed').val();

            addTask(taskName, taskComp);
        });

        function addTask(taskName, taskComp) {
            $.ajax({
                url: '/tasks',
                type: 'POST',
                data: {
                    title: taskName,
                    is_completed: taskComp,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                success: function(response) {
                    if (response.status == 'false') {
                        alert(response.message);

                    }

                    var count = $('#counter').val();
                    if (response.data.is_completed) {
                        var comp = '<div class = "badge bg-success"> Done </div>'
                    } else {
                        var comp = '<div class = "badge bg-warning" > Not Done </div>'
                    }

                    $('#taskTable tbody').append('<tr><td>' + count + '</td><td>' + response.data.title + '</td><td>' + comp + '</td><td><button class="btn btn-danger" onclick="deleteTask({{' + count + '}})">Edit</button><button class="btn btn-danger" onclick="deleteTask({{' + count + '}})">Delete</button></td></tr>');

                    $('#title').val(''); // Clear the input field
                    $('#is_completed').val(''); // Clear the input field

                },
                error: function(xhr) {
                    console.log('Error adding task');
                }
            });
        }
    });

    function updateTask(taskId) {
        var task_status = $('#task_status').val();
        console.log("task_status " + task_status);
        $.ajax({
            url: '/tasks/' + taskId,
            type: 'patch',
            data: {
                task_status: task_status,

            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            success: function(response) {
                console.log(response);
                $('#task_' + taskId).remove();
                alert(response.message);

            },
            error: function(xhr) {
                // Handle error
                alert('Error deleting task');
            }
        });
    }



    function deleteTask(taskId) {
        if (confirm("Are you sure you want to delete this task?")) {
            $.ajax({
                url: '/tasks/' + taskId,
                type: 'delete',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                success: function(response) {
                    console.log(response);
                    $('#task_' + taskId).remove();
                    alert(response.message);
                },
                error: function(xhr) {
                    // Handle error
                    alert('Error deleting task');
                }
            });
        }
    }
</script>

</html>
