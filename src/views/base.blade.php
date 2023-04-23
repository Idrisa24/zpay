<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <title>Lipa na Mpesa</title>

</head>

<body>
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600"
                alt="Your Company">
            <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Complete your payment
            </h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-3" action="#" method="POST" x-data="createFormComponent()" @submit.prevent="onSubmit">
                <div>
                    <label for="currency" class="block text-sm font-medium leading-6 text-gray-900">Currency</label>
                    <div class="mt-2">
                        <select id="currency" name="currency" autocomplete="currency-name" x-model="formData.currency"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="TZS" selected>Tanzania Shilling (Tsh)</option>
                            <option value="GHS">Ghana Cedi (₵)</option>
                            <option value="USD">US Dollar ($)</option>
                        </select>
                    </div>

                </div>
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">custormer's name
                    </label>
                    <div class="mt-2">
                        <input id="name" name="name" type="text" autocomplete="name" x-model="formData.name"
                            required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>


                <div>
                    <label for="mobile" class="block text-sm font-medium leading-6 text-gray-900">mobile</label>
                    <div class="mt-2">
                        <input id="mobile" name="mobile" type="tel" x-model="formData.mobile" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">

                    </div>
                </div>



                <div>
                    <button type="submit" x-bind:disabled="loading"
                        class="flex w-full justify-center rounded-md bg-[#bf221e] px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed">
                        <p class="text-sm text-gray-300" x-text="formData.currency" x-transition.duration.500ms
                            x-transition:leave.duration.1000ms></p>&nbsp;
                        <p class="text-sm text-gray-300" x-text="formData.price" x-transition.duration.500ms></p>&nbsp;
                        <p class="text-sm"> Pay Now</p>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function() {
            "use strict";

            window.createFormComponent = function() {
                return {
                    formData: {
                        name: "",
                        mobile: "",
                        currency: 'TZS',
                        price: '15000',
                        session: @json($body).session,
                        _token: "{{ csrf_token() }}",

                    },
                    loading: false,
                    message: '',
                    onSubmit($event) {
                        this.loading = true;
                        this.nameerror = !this.name ? "You must enter customer's name" : "";
                        this.mobileerror = !this.mobile ? "You must enter customer's mobile" : "";
                        fetch('/zpay/pay', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(this.formData)
                            })
                            .then(() => {
                                this.message = 'Form sucessfully submitted!'
                            })
                            .catch(() => {
                                this.message = 'Ooops! Something went wrong!'
                            })
                            .finally(() => {
                                this.loading = false;
                                this.buttonLabel = 'Submit'
                            })
                    }
                };
            };
        })();
    </script>
</body>

</html>
