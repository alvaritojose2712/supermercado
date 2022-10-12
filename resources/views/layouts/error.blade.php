<link rel="stylesheet" href="{{asset("css/app.css")}}">

<div class="container h-100 d-flex justify-content-center align-items-center">
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>¡Ups!</strong> No tienes permisos para esta ruta. <br>
      <ul>
          @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
          @endforeach
      </ul>
  </div>
</div>


