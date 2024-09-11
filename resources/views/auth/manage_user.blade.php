<x-app-layout>
    <div class="container">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Manage Users') }}
            </h2>
        </x-slot>

        <div class="mt-4">
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

            <table class="table">
                <thead class="table-light border">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        @if ($user->id != 1 && $user->id != 0)
                            <tr>
                                <!-- Display user details -->
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>

                                <td>
                                    <form id="updateForm{{ $user->id }}" method="POST" action="{{ route('admin.users.verify', $user->id) }}">
                                        @csrf
                                        <select name="role" class="form-control">
                                            <option value="1" {{ $user->role == 1 ? 'selected' : '' }}>Admin</option>
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
</x-app-layout>
