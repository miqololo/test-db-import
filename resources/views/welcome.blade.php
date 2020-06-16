<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="/select2/dist/css/select2.min.css">
    <title>Hello, world!</title>
    <script>
        onModalOpen = function(id){
            $.ajax('/info?product_id='+id,{
                success: function(result) {
                    $('#container-body').html(result);
                }
            })
        }
    </script>
</head>
<body>
<div class="container">
    <div class="mt-5" >
        {{--<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">--}}
            {{--Add File--}}
        {{--</button>--}}
        <form class="mt-5" method="get" action="/">
            @csrf
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="code">Code</label>
                    <input type="text" class="form-control" value="{{$request->code}}" id="code" name='code' placeholder="Code">
                </div>
                <div class="form-group col-md-4">
                    <label for="model">Model</label>
                    <select id="model" name="model" class="form-control">
                        <option selected value="{{$request->model}}">{{$request->model}}</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="category">Category</label>
                    <select id="category" name="category" class="form-control">
                        <option selected value="{{$request->category}}">{{$request->category}}</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="mark">Mark</label>
                    <select id="mark" name='mark' class="form-control">
                        <option selected value="{{$request->mark}}">{{$request->mark}}</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <button type="submit" class="btn btn-primary " style="margin-top: 30px;width: 100%">Filter</button>
                </div>
            </div>

        </form>
    </div>

    <table class="table">
        <thead class="thead-dark">
        <tr>
            <th scope="col">Code</th>
            <th scope="col">Name</th>
            <th scope="col">Weight</th>
            <th scope="col">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($products as $product)
            <tr>
                <th scope="row">{{$product->code}}</th>
                <td>{{$product->name}}</td>
                <td>{{$product->weight}}</td>
                <td>

                    <input type="button" onclick="onModalOpen({{$product->id}})" class="btn btn-outline-success" data-toggle="modal" data-target="#exampleModalInfo" value="Info"/>
                    <!-- Modal -->

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="text-center">
        {{ $products->links() }}
    </div>
    <!-- Button trigger modal -->


    <!-- Modal -->
    <div class="modal fade" id="exampleModalInfo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload new file</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="container-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script
        src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
        crossorigin="anonymous"></script>
<script src="/select2/dist/js/select2.min.js" type="text/javascript"></script>


<!-- Script -->
<script type="text/javascript">

    // CSRF Token
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $(document).ready(function(){

        $( "#city" ).select2({
            ajax: {
                url: "{{route('cities')}}",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: CSRF_TOKEN,
                        search: params.term // search term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }

        });

        $( "#category" ).select2({
            ajax: {
                url: "{{route('categories')}}",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: CSRF_TOKEN,
                        search: params.term // search term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }

        });

        $( "#mark" ).select2({
            ajax: {
                url: "{{route('mark')}}",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: CSRF_TOKEN,
                        search: params.term // search term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }

        });

        $( "#model" ).select2({
            ajax: {
                url: "{{route('models')}}",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: CSRF_TOKEN,
                        search: params.term // search term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }

        });

    });
</script>
</body>
</html>