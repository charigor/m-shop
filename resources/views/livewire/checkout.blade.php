<div>
    <div class="form-group"  id="type_block">
        <label class="col-form-label" for="type">Тип доставки</label>
        <select class="form-control w-[100%]" id="type">
            <option value=""></option>
            @foreach($typeOptions as $key => $item)
                <option value="{{$item['id']}}" {{$item['id'] === (int)$type ? 'selected' : ''}}>{{$item['name']}}</option>
            @endforeach
        </select>
    </div>

    @if($type === "1" || $type == "2" || $type == "3")
        <div class="form-group"  id="city_block">
            <label class="col-form-label" for="city">City </label>
            <select class="form-control w-[100%]"   id="city">
                <option value=""></option>
                @foreach($cities as $key => $item)
                    <option value="{{$item}}" {{$item === $city ? 'selected' : ''}}>{{$item}}</option>
                @endforeach
            </select>
        </div>
        @if($city)
        <div class="form-group" id="warehouse_block">
            <label class="col-form-label" for="warehouse">Warehouse </label>
            <select class="form-control w-[100%]"   id="warehouse" >
                <option value=""></option>
                @foreach($warehouses as $key => $item)
                    <option value="{{$item}}" {{$item === $warehouse ? 'selected' : ''}}>{{$item}}</option>
                @endforeach
            </select>
        </div>
        @endif
    @endif

</div>
@push('scripts')
    <script>
        function init(){
            $('#type').select2({
                placeholder: 'Select an option',
                minimumResultsForSearch: -1
            });
            $('#type').on('change', function () {
                let data = $('#type').select2("val");
                @this.set('type', data);
            });
            $('#city').select2({
                placeholder: 'Select an option',
                sorter: function(results) {
                    let query = $('.select2-search__field').val().toLowerCase();
                    return results.sort(function (a, b) {
                        return a.text.toLowerCase().indexOf(query) -
                            b.text.toLowerCase().indexOf(query);
                    });
                }
            });
            $('#city').on('change', function () {
                let data = $('#city').select2("val");
                @this.set('city', data);
            });
            $('#warehouse').select2({
                placeholder: 'Select an option'
            });
            $('#warehouse').on('change', function () {
                let data = $('#warehouse').select2("val");
                @this.set('warehouse', data);
            });
        }
        $(document).ready(init);
        window.addEventListener('updated-data',init);
    </script>
@endpush
