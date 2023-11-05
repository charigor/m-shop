<div>
    <div class="form-group" wire:ignore>
        <label class="col-form-label" for="city">City </label>
        <select class="form-control w-[100%]"   id="city" wire:model="city"
                    x-init="
                        $('#city').select2({
                            placeholder: 'Select an option',
                        data: {{json_encode($cities)}}
                        });
                        $('#city').trigger('change');
                        $('#city').on('change',function(){
                            $wire.set('city',$(this).val());
                        });
                        $('#city').on('select2:open',function(e){
                            $('input.select2-search__field').on('input',function(){
                                $wire.call('getCities',$(this).val());
                            });
                        });
                    "

                    @set-data-city.window = "select2 = $('#city').data('select2')"
                    >
{{--                @foreach($cities as $item)--}}
{{--                    <option value="{{$item}}" {{$city == $item ? 'selected' : ''}}>{{$item}}</option>--}}
{{--                @endforeach--}}
            </select>
    </div>
</div>

