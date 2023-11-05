<div>
    <div class="form py-4">
        <div class="flex items-center my-4">
            <label for="name" class="w-[20%]">Ім'я</label>
            <div class="flex flex-column">
                <input type="text" name="name" id="name" wire:model="name">
                @error('name') <span class="text-red">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex items-center my-4">
            <label for="email" class="w-[20%]">Email</label>
            <div class="flex flex-column">
                <input type="text" name="email" id="email" wire:model="email">
                @error('email') <span class="text-red">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
    @if($name && $email)
        <div class="flex justify-end">
            <button id="next" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800" wire:click="showStep('checkout-delivery-step')">
                Next
            </button>
        </div>
    @endif
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            $("#next").click(function() { $("#city").select2("enable", true); });
        });
        // $('#city').on('change', function (e) {
        //     var data = $('#city').select2("val");
        // @this.set('selected', data);
        // });

    </script>
@endpush



