<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Database Driver Switcher</h1>
                <p class="text-gray-300">Demonstration of multi-database support with custom PHP binary</p>
            </div>

            <!-- Alert Messages -->
            @if($successMessage)
                <div class="mb-6 bg-green-500/10 border border-green-500 text-green-400 px-4 py-3 rounded-lg" role="alert">
                    <p>{{ $successMessage }}</p>
                </div>
            @endif

            @if($errorMessage)
                <div class="mb-6 bg-red-500/10 border border-red-500 text-red-400 px-4 py-3 rounded-lg" role="alert">
                    <p>{{ $errorMessage }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Driver Selection & Connection Info -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Driver Selector -->
                    <div class="bg-white/10 backdrop-blur-lg rounded-lg shadow-xl p-6 border border-white/20">
                        <h2 class="text-xl font-semibold text-white mb-4">Select Database Driver</h2>

                        <div class="space-y-3">
                            <label class="flex items-center p-3 bg-white/5 rounded-lg cursor-pointer hover:bg-white/10 transition">
                                <input type="radio" wire:model.live="selectedDriver" value="sqlite" class="w-4 h-4 text-purple-600">
                                <span class="ml-3 text-white font-medium">SQLite</span>
                            </label>

                            <label class="flex items-center p-3 bg-white/5 rounded-lg cursor-pointer hover:bg-white/10 transition">
                                <input type="radio" wire:model.live="selectedDriver" value="mysql" class="w-4 h-4 text-purple-600">
                                <span class="ml-3 text-white font-medium">MySQL</span>
                            </label>

                            <label class="flex items-center p-3 bg-white/5 rounded-lg cursor-pointer hover:bg-white/10 transition">
                                <input type="radio" wire:model.live="selectedDriver" value="pgsql" class="w-4 h-4 text-purple-600">
                                <span class="ml-3 text-white font-medium">PostgreSQL</span>
                            </label>

                            <label class="flex items-center p-3 bg-white/5 rounded-lg cursor-pointer hover:bg-white/10 transition">
                                <input type="radio" wire:model.live="selectedDriver" value="sqlsrv" class="w-4 h-4 text-purple-600">
                                <span class="ml-3 text-white font-medium">SQL Server</span>
                            </label>
                        </div>

                        <button wire:click="switchDriver" class="w-full mt-4 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded-lg transition shadow-lg">
                            Switch Driver & Migrate
                        </button>
                    </div>

                    <!-- Connection Info -->
                    <div class="bg-white/10 backdrop-blur-lg rounded-lg shadow-xl p-6 border border-white/20">
                        <h2 class="text-xl font-semibold text-white mb-4">Connection Info</h2>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Driver:</span>
                                <span class="text-white font-mono">{{ $this->connectionInfo['driver'] }}</span>
                            </div>

                            @if(isset($this->connectionInfo['host']))
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Host:</span>
                                    <span class="text-white font-mono">{{ $this->connectionInfo['host'] }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between">
                                <span class="text-gray-400">Database:</span>
                                <span class="text-white font-mono">{{ $this->connectionInfo['database'] }}</span>
                            </div>

                            <div class="flex justify-between items-center pt-2">
                                <span class="text-gray-400">Status:</span>
                                @if($this->connectionInfo['connected'])
                                    <span class="flex items-center text-green-400">
                                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                                        Connected
                                    </span>
                                @else
                                    <span class="flex items-center text-red-400">
                                        <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                                        Disconnected
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: CRUD Operations & Records -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Add Record Form -->
                    <div class="bg-white/10 backdrop-blur-lg rounded-lg shadow-xl p-6 border border-white/20">
                        <h2 class="text-xl font-semibold text-white mb-4">Add New Record</h2>

                        <form wire:submit="addRecord" class="space-y-4">
                            <div>
                                <label class="block text-gray-300 text-sm font-medium mb-2">Name</label>
                                <input type="text" wire:model="name" class="w-full px-4 py-2 bg-white/5 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-purple-500" placeholder="Enter name" required>
                                @error('name') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-gray-300 text-sm font-medium mb-2">Description</label>
                                <textarea wire:model="description" rows="3" class="w-full px-4 py-2 bg-white/5 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-purple-500" placeholder="Enter description (optional)"></textarea>
                                @error('description') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" wire:model="isActive" id="isActive" class="w-4 h-4 text-purple-600 rounded">
                                <label for="isActive" class="ml-2 text-gray-300 text-sm">Active</label>
                            </div>

                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition shadow-lg">
                                Add Record
                            </button>
                        </form>
                    </div>

                    <!-- Records Table -->
                    <div class="bg-white/10 backdrop-blur-lg rounded-lg shadow-xl p-6 border border-white/20">
                        <h2 class="text-xl font-semibold text-white mb-4">Records ({{ $this->records->count() }})</h2>

                        @if($this->records->isEmpty())
                            <p class="text-gray-400 text-center py-8">No records found. Add your first record above!</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-white/20">
                                            <th class="text-left text-gray-300 font-medium py-3 px-4">ID</th>
                                            <th class="text-left text-gray-300 font-medium py-3 px-4">Name</th>
                                            <th class="text-left text-gray-300 font-medium py-3 px-4">Driver</th>
                                            <th class="text-left text-gray-300 font-medium py-3 px-4">Status</th>
                                            <th class="text-left text-gray-300 font-medium py-3 px-4">Created</th>
                                            <th class="text-center text-gray-300 font-medium py-3 px-4">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($this->records as $record)
                                            <tr class="border-b border-white/10 hover:bg-white/5 transition">
                                                <td class="py-3 px-4 text-white">{{ $record->id }}</td>
                                                <td class="py-3 px-4 text-white">{{ $record->name }}</td>
                                                <td class="py-3 px-4">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-500/20 text-purple-300">
                                                        {{ $record->database_driver }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4">
                                                    @if($record->is_active)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-300">
                                                            Active
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-500/20 text-gray-300">
                                                            Inactive
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4 text-gray-300">{{ $record->created_at }}</td>
                                                <td class="py-3 px-4 text-center">
                                                    <button wire:click="deleteRecord({{ $record->id }})" wire:confirm="Are you sure you want to delete this record?" class="text-red-400 hover:text-red-300 font-medium">
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
