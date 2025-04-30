@extends('layout.default')

@section('title', 'Dashboard')



@section('content')

<!-- BOUTON Ajouter -->
        <button
        type="button"
        class="btn btn-primary mb-3"
        data-bs-toggle="modal"
        data-bs-target="#ajouter_admin"><!-- id du modal-->
        Ajouter un Administrateur
        </button>

        @if(Session::has('success'))
        <div class="alert alert-success" role="alert">
            {{ Session::get('success') }}
        </div>
         @endif
    <table class="table">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Nom</th>
        <th scope="col">Prenom</th>
        <th scope="col">Email</th>
        <th scope="col">Telephone</th>
        <th scope="col">Cin</th>
        <th scope="col">Adresse</th>
        <th scope="col">Pays</th>
        <th scope="col">Ville</th>
        <th scope="col">Role</th>
        <th scope="col">Statut</th>
        <th scope="col">Action</th>

        
      </tr>
    </thead>
    <tbody>
        @forelse ($admins as $admin)
        <tr>
            <!--<th scope="row">1</th>-->
            <td>{{ $loop->iteration }}</td>
            <td>{{$admin->nom}}</td>
            <td>{{$admin->prenom}}</td>
            <td>{{$admin->email}}</td>
            <td>{{$admin->telephone}}</td>
            <td>{{$admin->cin}}</td>
            <td>{{$admin->adresse}}</td>
            <td>{{ $admin->pays ? $admin->pays->nom : 'Pas de pays assigné' }}</td>
            <td>{{ $admin->ville ? $admin->ville->nom : 'Pas de ville assigné' }}</td>
            <td>{{ $admin->role ? $admin->role->nom : 'Pas de role assigné' }}</td>
            <td>{{ $admin->statut ? $admin->statut->nom : 'Pas de statut assigné' }}</td>
            <td >
                <div class="d-flex gap-2" >
                    <a href="{{route('admin.edit',$admin->id)}}" class="btn btn-warning btn-sm">
                        <i class="glyphicon glyphicon-pencil"></i> Edit
                     </a>
                    <form action="{{route('admin.delete',$admin->id)}}" method="post" style="display:inline;">
                        @csrf
                        @method('delete')
                        <button class="btn btn-danger" >
                            <i class="glyphicon glyphicon-trash"></i> Delete
                        </button>
                    </form>
                    </div>
                </div>
            </td>

          </tr>
        @empty
        <tr>
            <td class="text-center" colspan="6">Admin not found</td>
        </tr> 
        @endforelse
     
     
    </tbody>
  </table>
    @include('admin.partials.modal-create')
    @if(isset($editAdmin))
     @include('admin.partials.modal-edit')
    @endif
    
@endsection