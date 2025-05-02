@extends('layout.default')

@section('title', 'Dashboard')

@section('content')
<!-- BOUTON Ajouter -->
<button
    type="button"
    class="btn btn-primary mb-3"
    data-bs-toggle="modal"
    data-bs-target="#ajouter_admin">
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
            <th>#</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>CIN</th>
            <th>Adresse</th>
            <th>Pays</th>
            <th>Ville</th>
            <th>Rôle</th>
            <th>Statut</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($admins as $admin)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $admin->nom }}</td>
            <td>{{ $admin->prenom }}</td>
            <td>{{ $admin->email }}</td>
            <td>{{ $admin->telephone }}</td>
            <td>{{ $admin->cin }}</td>
            <td>{{ $admin->adresse }}</td>
            <td>{{ $admin->pays?->nom ?? 'Pas de pays assigné' }}</td>
            <td>{{ $admin->ville?->nom ?? 'Pas de ville assignée' }}</td>
            <td>{{ $admin->role?->nom ?? 'Pas de rôle assigné' }}</td>
            <td>{{ $admin->statut?->nom ?? 'Pas de statut assigné' }}</td>
            <td>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.edit', $admin->id) }}" class="btn btn-warning btn-sm">
                        <i class="glyphicon glyphicon-pencil"></i> Edit
                    </a>
                    <form action="{{ route('admin.delete', $admin->id) }}" method="post" style="display:inline;">
                        @csrf
                        @method('delete')
                        <button class="btn btn-danger">
                            <i class="glyphicon glyphicon-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td class="text-center" colspan="12">Aucun administrateur trouvé.</td>
        </tr>
        @endforelse
    </tbody>
</table>

@include('admin.partials.modal-create')
@if(isset($editAdmin))
    @include('admin.partials.modal-edit')
@endif
@endsection

@section('my_js')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha256-o88Awf4y9z9Igh1zShmvQh+LQbi1+pqKJjzY6Xr9OOA="
        crossorigin="anonymous"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-gJWV0FZB40gCkYqL+tEzQ8eUcV5J78p3F82hMRjSA3vNkFf9wso1aCqhv8F2+/97"
        crossorigin="anonymous"></script>

<script>
$(document).ready(function () {
    // Changement de pays -> chargement des villes
    $('#pays_id_create').on('change', function () {
        var paysId = $(this).val();
        $('#ville_id_create').empty().append('<option value="">Chargement...</option>');

        if (paysId) {
            $.ajax({
                url: '/villes/' + paysId,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $('#ville_id_create').empty().append('<option value="">Sélectionner une ville</option>');
                    $.each(data, function (index, ville) {
                        $('#ville_id_create').append('<option value="' + ville.id + '">' + ville.nom + '</option>');
                    });
                },
                error: function () {
                    $('#ville_id_create').empty().append('<option value="">Erreur de chargement</option>');
                }
            });
        } else {
            $('#ville_id_create').empty().append('<option value="">Sélectionner un pays d’abord</option>');
        }
    });

    // Soumission AJAX du formulaire
    $('#adminForm').on('submit', function (e) {
        e.preventDefault();

        let actionUrl = $(this).attr('action') || '/admin/store';
        let formData = new FormData(this);

        $('.invalid-feedback').text('');
        $('.form-control, .form-select').removeClass('is-invalid');

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            success: function (response) {
                $('#ajouter_admin').modal('hide');
                alert('Administrateur ajouté avec succès');
                location.reload();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        $('#' + key + '_create').addClass('is-invalid');
                        $('#error_' + key).text(value[0]);
                    });
                } else {
                    alert('Une erreur est survenue.');
                }
            }
        });
    });
});
</script>
@endsection
