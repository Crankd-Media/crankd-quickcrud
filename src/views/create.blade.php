<x-guest-layout>

    <section class="deals-create py-16">
        <div class="mx-auto max-w-4xl">


            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-12">
                Create Module
            </h2>

            <form action="{{ route('quickcrud.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="block w-full shadow-sm sm:text-sm border border-gray-100 rounded-md">
                </div>

                <div class="py-10">
                    <button type="submit" class="py-2 px-6 bg-gray-800 text-white hover:bg-gray-600">Create</button>
                </div>

            </form>

        </div>
    </section>

</x-guest-layout>
