<h3>Cities</h3>
<table class="table">
    <thead class="thead-dark">
    <tr>
        <th scope="col">#</th>
        <th scope="col">City</th>
        <th scope="col">Count</th>
        <th scope="col">Price</th>
    </tr>
    </thead>
    <tbody>
    @foreach($productCities as $key=>$city)
        <tr>
            <th scope="row">{{$city->id}}</th>
            <td>{{$city->city->name}}</td>
            <td>{{$city->count}}</td>
            <td>{{$city->price}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<h3>Relations</h3>
<table class="table">
    <thead class="thead-dark">
    <tr>
        <th scope="col">#</th>
        <th scope="col">Model</th>
        <th scope="col">Category</th>
        <th scope="col">Mark</th>
    </tr>
    </thead>
    <tbody>
    @foreach($productDetails as $key=>$product)
        <tr>
            <th scope="row">{{$product->id}}</th>
            <td>{{$product->model->name}}</td>
            <td>{{$product->category->name}}</td>
            <td>{{$product->mark->name}}</td>
        </tr>
    @endforeach
    </tbody>
</table>