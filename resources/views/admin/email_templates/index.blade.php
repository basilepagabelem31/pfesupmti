@extends('layout.default')

@section('title', 'Liste des Email Templates')

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-4 sm:px-6 lg:px-8"> 
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">
            <i class="fas fa-file-alt mr-3 text-emerald-600"></i> Modèles d'email 
        </h1>

        {{-- Success Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 flex items-center shadow-sm animate-fade-in">
                <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                <div>
                    <h3 class="font-semibold text-lg">Succès !</h3>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Error Messages --}}
        @if($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 shadow-sm animate-fade-in">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3 mt-1"></i>
                    <div>
                        <h3 class="font-semibold text-lg mb-1">Oups ! Il y a des erreurs :</h3>
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="mb-6 flex justify-end"> 
            <button
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition ease-in-out duration-150 transform hover:scale-105"
                onclick="document.getElementById('addModal').showModal()"
            >
                <i class="fas fa-plus-circle mr-2"></i> Nouveau modèle
            </button>
        </div>

        {{-- Table Container --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200"> 
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sujet</th>
                            <th scope="col" class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contenu</th>
                            <th scope="col" class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($templates as $template)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="py-4 px-6 whitespace-nowrap text-sm font-medium text-gray-900">{{ $template->type }}</td>
                                <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-700">{{ $template->subject }}</td>
                                <td class="py-4 px-6 text-sm text-gray-700 max-w-xs truncate">{{ $template->description }}</td> 
                                <td class="py-4 px-6 whitespace-nowrap text-sm text-center text-gray-700">
                                    <button class="text-blue-600 hover:text-blue-800 font-medium transition-colors " onclick="document.getElementById('viewModal-{{ $template->id }}').showModal()">
                                        <i class="fas fa-eye mr-1"></i> 
                                    </button>

                                    {{-- Modal View --}}
                                    <dialog id="viewModal-{{ $template->id }}" class="rounded-2xl shadow-2xl w-full max-w-2xl p-0 backdrop:bg-gray-900/70 backdrop:backdrop-blur-sm">
                                        <div class="bg-white rounded-2xl overflow-hidden">
                                            <div class="px-8 py-5 border-b border-gray-200 flex justify-between items-center bg-blue-50">
                                                <h2 class="text-2xl font-extrabold text-gray-800">
                                                    <i class="fas fa-file-alt mr-3 text-blue-600"></i> Contenu du modèle : <span class="text-blue-700">{{ $template->type }}</span>
                                                </h2>
                                                <button type="button" class="text-gray-500 hover:text-gray-700 text-3xl leading-none" onclick="document.getElementById('viewModal-{{ $template->id }}').close()">
                                                    &times;
                                                </button>
                                            </div>
                                            <div class="p-8">
                                                <h3 class="font-semibold text-lg text-gray-700 mb-2">Sujet : <span class="font-normal text-gray-900">{{ $template->subject }}</span></h3>
                                                @if($template->description)
                                                    <h3 class="font-semibold text-lg text-gray-700 mb-4">Description : <span class="font-normal text-gray-900">{{ $template->description }}</span></h3>
                                                @endif
                                                <div class="bg-gray-100 p-4 rounded-lg overflow-x-auto text-sm text-gray-800 font-mono">
                                                    <pre class="whitespace-pre-wrap">{{ $template->content }}</pre>
                                                </div>
                                            </div>
                                            <div class="px-8 py-5 bg-gray-50 border-t border-gray-200 flex justify-end">
                                                <button type="button" class="px-6 py-3 text-gray-700 hover:text-gray-900 font-semibold rounded-lg hover:bg-gray-200 transition-colors duration-200" onclick="document.getElementById('viewModal-{{ $template->id }}').close()">
                                                    Fermer
                                                </button>
                                            </div>
                                        </div>
                                    </dialog>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-center text-sm font-medium">
                                    <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors" onclick="document.getElementById('editModal-{{ $template->id }}').showModal()">
                                        <i class="fas fa-pen mr-1"></i> 
                                    </button>

                                    {{-- Modal Edit --}}
                                    <dialog id="editModal-{{ $template->id }}" class="rounded-3xl shadow-2xl w-full max-w-2xl p-0 backdrop:bg-gray-900/70 backdrop:backdrop-blur-sm">
                                        <form method="POST" action="{{ route('admin.email_templates.update', $template->id) }}" class="bg-white rounded-3xl overflow-hidden">
                                            @csrf
                                            @method('PUT')
                                            <div class="px-8 py-5 border-b border-gray-200 flex justify-between items-center bg-yellow-50">
                                                <h2 class="text-2xl font-extrabold text-gray-800">
                                                    <i class="fas fa-edit mr-3 text-yellow-600"></i> Éditer le modèle
                                                </h2>
                                                <button type="button" class="text-gray-500 hover:text-gray-700 text-3xl leading-none" onclick="document.getElementById('editModal-{{ $template->id }}').close()">
                                                    &times;
                                                </button>
                                            </div>
                                            <div class="p-8 space-y-6">
                                                <div>
                                                    <label for="edit_type_{{ $template->id }}" class="block text-sm font-semibold text-gray-700 mb-1">Type</label>
                                                    <input type="text" id="edit_type_{{ $template->id }}" name="type" value="{{ old('type', $template->type) }}" placeholder="Type du modèle" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-yellow-500 focus:border-yellow-500 p-3 text-gray-800 placeholder-gray-400 focus:outline-none transition-all duration-200 ease-in-out hover:border-yellow-400" required>
                                                    @error('type')
                                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <label for="edit_subject_{{ $template->id }}" class="block text-sm font-semibold text-gray-700 mb-1">Sujet</label>
                                                    <input type="text" id="edit_subject_{{ $template->id }}" name="subject" value="{{ old('subject', $template->subject) }}" placeholder="Sujet de l'email" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-yellow-500 focus:border-yellow-500 p-3 text-gray-800 placeholder-gray-400 focus:outline-none transition-all duration-200 ease-in-out hover:border-yellow-400" required>
                                                    @error('subject')
                                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <label for="edit_content_{{ $template->id }}" class="block text-sm font-semibold text-gray-700 mb-1">Contenu</label>
                                                    <textarea id="edit_content_{{ $template->id }}" name="content" rows="8" placeholder="Contenu HTML/texte de l'email" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-yellow-500 focus:border-yellow-500 p-3 text-gray-800 placeholder-gray-400 focus:outline-none transition-all duration-200 ease-in-out hover:border-yellow-400" required>{{ old('content', $template->content) }}</textarea>
                                                    @error('content')
                                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <label for="edit_description_{{ $template->id }}" class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                                                    <textarea id="edit_description_{{ $template->id }}" name="description" rows="3" placeholder="Description du modèle (facultatif)" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-yellow-500 focus:border-yellow-500 p-3 text-gray-800 placeholder-gray-400 focus:outline-none transition-all duration-200 ease-in-out hover:border-yellow-400">{{ old('description', $template->description) }}</textarea>
                                                    @error('description')
                                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="px-8 py-5 bg-gray-50 border-t border-gray-200 flex justify-end space-x-4">
                                                <button type="button" class="px-6 py-3 text-gray-700 hover:text-gray-900 font-semibold rounded-lg hover:bg-gray-200 transition-colors duration-200" onclick="document.getElementById('editModal-{{ $template->id }}').close()">Annuler</button>
                                                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition ease-in-out duration-150 transform hover:scale-105">
                                                    <i class="fas fa-save mr-2"></i> Enregistrer
                                                </button>
                                            </div>
                                        </form>
                                    </dialog>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500 text-lg">
                                    <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i><br>
                                    Aucun modèle d'email n'a été créé pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Create --}}
<dialog id="addModal" class="rounded-3xl shadow-2xl w-full max-w-2xl p-0 backdrop:bg-gray-900/70 backdrop:backdrop-blur-sm">
    <form method="POST" action="{{ route('admin.email_templates.store') }}" class="bg-white rounded-3xl overflow-hidden">
        @csrf
        <div class="px-8 py-5 border-b border-gray-200 flex justify-between items-center bg-emerald-50">
            <h2 class="text-2xl font-extrabold text-gray-800">
                <i class="fas fa-plus-square mr-3 text-emerald-600"></i> Créer un nouveau modèle
            </h2>
            <button type="button" class="text-gray-500 hover:text-gray-700 text-3xl leading-none" onclick="document.getElementById('addModal').close()">
                &times;
            </button>
        </div>
        <div class="p-8 space-y-6">
            <div>
                <label for="create_type" class="block text-sm font-semibold text-gray-700 mb-1">Type</label>
                <input type="text" id="create_type" name="type" value="{{ old('type') }}" placeholder="Entrez un type " class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500 p-3 text-gray-800 placeholder-gray-400 focus:outline-none transition-all duration-200 ease-in-out hover:border-emerald-400" required>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="create_subject" class="block text-sm font-semibold text-gray-700 mb-1">Sujet</label>
                <input type="text" id="create_subject" name="subject" value="{{ old('subject') }}" placeholder="Sujet de l'email" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500 p-3 text-gray-800 placeholder-gray-400 focus:outline-none transition-all duration-200 ease-in-out hover:border-emerald-400" required>
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="create_content" class="block text-sm font-semibold text-gray-700 mb-1">Contenu</label>
                <textarea id="create_content" name="content" rows="10" placeholder="Contenu du email" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500 p-3 text-gray-800 placeholder-gray-400 focus:outline-none transition-all duration-200 ease-in-out hover:border-emerald-400" required>{{ old('content') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="create_description" class="block text-sm font-semibold text-gray-700 mb-1">Description (facultatif)</label>
                <textarea id="create_description" name="description" rows="3" placeholder="Une courte description du modèle" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500 p-3 text-gray-800 placeholder-gray-400 focus:outline-none transition-all duration-200 ease-in-out hover:border-emerald-400">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="px-8 py-5 bg-gray-50 border-t border-gray-200 flex justify-end space-x-4">
            <button type="button" class="px-6 py-3 text-gray-700 hover:text-gray-900 font-semibold rounded-lg hover:bg-gray-200 transition-colors duration-200" onclick="document.getElementById('addModal').close()">Annuler</button>
            <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition ease-in-out duration-150 transform hover:scale-105">
                <i class="fas fa-check-circle mr-2"></i> Créer le modèle
            </button>
        </div>
    </form>
</dialog>
@endsection