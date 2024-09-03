
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('RFID Binding') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="md:max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white  dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto flex flex-col items-center mt-3 mb-5 px-2">
                    <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                        {{ __('Unbound RFID Tags') }}
                    </h1>
                    @if (session('status'))
                <div class="alert alert-success text-green-500">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger text-red-600">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

                    <table class="min-w-full bg-white border border-gray-300 ">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 border-b border-gray-300 bg-gray-100 text-center text-sm font-semibold text-gray-600 uppercase tracking-wider">RFID Tag</th>
                                <th class="px-6 py-3 border-b border-gray-300 bg-gray-100 text-center text-sm font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($unboundTags as $tag)
                                <tr class="hover:bg-gray-100">
                                    <td class="px-4 py-2 border-b border-gray-300 text-center">{{ $tag->tag }}</td>
                                    <td class="px-4 py-2 border-b border-gray-300 text-right">
                                        <form action="{{ route('rfid.bind.submit') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="tag" value="{{ $tag->tag }}">
                                            <select name="user_id" required class="text-center">
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-1 mt-2 mr-3 sm:mr-0 rounded text-sm w-32" type="submit">Bind</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="col-span-2 text-center px-4 py-2 border-b" unbound RFID tags found.></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
