<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    
    <title>@yield('title') |Administration</title>

    <style>
        @layer reset{
            button{
                all: unset;
            }
        }
    </style>
   
</head>
<body>

<div class="container mt-5">
<nav class="navbar navbar-expand-lg bg-primary navbar-dark">
        <div class="container-fluid">
             <a class="navbar-brand" href="{{ route('admin.property.index') }}">Agence</a>

             <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav"  aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            @php
            $route = request()->route()->getName();
            @endphp
            <div class="collapse navbar-collapse" id="navbarNav">
                 <ul class="navbar-nav">
                    <li class="nav-item ">
                         <a href="{{route('admin.property.index')}}" @class(['nav-link', 'active' => str_contains($route,'property.')])>Gérer les biens </a>
                    </li>
                   
                    <li class="nav-item ">
                         <a href="{{route('admin.option.index')}}" @class(['nav-link', 'active' => str_contains($route,'option.')])> les options vente</a>
                    </li>
                    <li class="nav-item ">
                         <a href="{{route('admin.rental.index')}}" @class(['nav-link', 'active' => str_contains($route,'rental.')])>Gérer les biens locatif</a>
                    </li>
                   
                    <li class="nav-item ">
                         <a href="{{route('admin.feature.index')}}" @class(['nav-link', 'active' => str_contains($route,'feature.')])> les options locatif</a>
                    </li>
                 </ul>
                 <div class="ms-auto">
                    @auth
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <form action="{{ route ('logout')}}" method="post">
                                @csrf
                                @method('delete')
                                <button class="nav-link">Se déconnecter</button>
                            </form>
                        </li>
                    </ul>
                    @endauth
                 </div>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        @include('shared.flash')
        @yield('content')
    </div>
</div>
 <script>
        new TomSelect('select[multiple]', {plugins:{remove_button:{title:'Supprimer'}}})
    </script>
</body>
</html>