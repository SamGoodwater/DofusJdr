<script>
export default {
    data() {
        return {
            form: {
                name: '',
                public: false,
                is_editable: false,
                parent_page: ''
            }
        };
    },
    methods: {
        submitForm() {
            // Handle form submission logic here
            console.log(this.form);
        }
    }
};
</script>

<template>
    <div class="max-w-2xl mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Créer une nouvelle page</h1>
        <form @submit.prevent="submitForm">
            @csrf
            @method('PATCH') // Add this line to the form to make it a PATCH request
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                <input type="text" id="name" v-model="form.name"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    required>
            </div>
            <div class="mb-4">
                <label for="public" class="block text-sm font-medium text-gray-700">Public</label>
                <input type="checkbox" id="public" v-model="form.public" class="mt-1 block">
            </div>
            <div class="mb-4">
                <label for="is_editable" class="block text-sm font-medium text-gray-700">Éditable</label>
                <input type="checkbox" id="is_editable" v-model="form.is_editable" class="mt-1 block">
            </div>
            <div class="mb-4">
                <label for="parent_page" class="block text-sm font-medium text-gray-700">Page Parent</label>
                <input type="text" id="parent_page" v-model="form.parent_page"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <button type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Créer
                </button>
            </div>
        </form>
    </div>
</template>

<style scoped>
/* Add any additional styling here */
</style>
