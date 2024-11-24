<div>
    <h3 class="font-bold mb-2">User approval required</h3>
    <p class="text-sm mb-4">The Assistant needs your approval to call the method "{{$actionData['content']}}" with the following parameters:</p>
    <table class="table-auto border-collapse border border-gray-300 w-full mb-4 text-sm">
        <thead>
        <tr>
            <th class="border border-gray-300 px-4 py-2">Parameter</th>
            <th class="border border-gray-300 px-4 py-2">Value</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($flattenedArray as $key => $value)
            <tr>
                <td class="border border-gray-300 px-4 py-2">{{ $key }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $value }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="mb-4">
        <label for="comment" class="block text-sm font-medium text-gray-700">Enter Feedback</label>
        <textarea
            id="comment"
            wire:model="userComment"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
        ></textarea>
    </div>

    <div class="flex space-x-2 text-sm">
        <button
            wire:click="confirm"
            class="px-4 py-2 bg-green-700 text-white rounded-2xl hover:bg-green-600"
        >
            Approve
        </button>
        <button
            wire:click="decline"
            class="px-4 py-2 bg-red-600 text-white rounded-2xl hover:bg-red-800"
        >
            Reject
        </button>
    </div>
</div>
