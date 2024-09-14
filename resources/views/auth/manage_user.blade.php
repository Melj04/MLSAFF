<x-app-layout>
    <div class="container">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Manage Users') }}
            </h2>
        </x-slot>

        <div class="mt-4">
            <div>
                <div class="w-full bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto mt-3 mb-5 px-2">
                        @if (session('success'))
                            <div class="alert alert-success mb-2">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger mb-2">
                                {{ session('error') }}
                            </div>
                        @endif

                        <table class="bg-white w-full border border-gray-300">
                            <thead class="table-light border">
                                <tr>
                                    <th class="px-6 py-3 border-b border-gray-300 bg-gray-100 text-center text-sm font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 border-b border-gray-300 bg-gray-100 text-center text-sm font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 border-b border-gray-300 bg-gray-100 text-center text-sm font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 border-b border-gray-300 bg-gray-100 text-center text-sm font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 border-b border-gray-300 bg-gray-100 text-center text-sm font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    @if ($user->id != 1 && $user->id != 0)
                                        <tr>
                                            <!-- Display user details -->
                                            <td class="overflow-hidden">{{ $user->name }}</td>

                                            <!-- Handle long emails with Tailwind classes -->
                           <td class="max-w-xs lg:max-w-full overflow-hidden text-ellipsis whitespace-nowrap">{{ $user->email }}</td>

                 <td>
                                                <form id="updateForm{{ $user->id }}" method="POST" action="{{ route('admin.users.verify', $user->id) }}">
                                                    @csrf
                                                    <select name="role" class="form-control">
                                                        <option value="2" {{ $user->role == 2 ? 'selected' : '' }}>User</option>
                                                    </select>
                                            </td>

                                            <td>
                                                <select name="status" class="form-control">
                                                    <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </td>

                                            <td>
                                                <!-- Button to trigger modal -->
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#passwordModal{{ $user->id }}">
                                                    Update
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Modal for password confirmation -->
                                        <div class="modal fade @if ($errors->has('password') && session('id') == $user->id) show @endif" id="passwordModal{{ $user->id }}" tabindex="-1"
                                            aria-labelledby="passwordModalLabel" aria-hidden="true" @if ($errors->has('password') && session('id') == $user->id) style="display: block;" @endif>
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="passwordModalLabel">Confirm Your Password</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form id="passwordForm{{ $user->id }}" method="POST" action="{{ route('admin.users.verify', $user->id) }}">
                                                            @csrf
                                                            @method('POST')

                                                            <!-- Display password field -->
                                                            <div class="form-group">
                                                                <label for="password">Password</label>
                                                                <input type="password" name="password" class="form-control" required>

                                                                <!-- Display validation errors -->
                                                                @error('password')
                                                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <!-- Hidden fields to carry over role and status -->
                                                            <input type="hidden" name="role" value="{{ old('role', $user->role) }}">
                                                            <input type="hidden" name="status" value="{{ old('status', $user->status) }}">

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Confirm</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
