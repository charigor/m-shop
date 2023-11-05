@if($attr)
    @foreach($attr as  $key => $attribute)
        <div class="my-4">
            <span class="font-semibold text-md">{{$key}}: </span>
            @foreach($attribute as $item)
                <label class="ml-2 " for="attr_{{$item['id']}}">
                    <input id="attr_{{$item['id']}}"  type="radio" name="{{$key}}" value="{{$item['id']}}" wire:model.debounce.250ms="attr.{{$item['attribute_group_id']}}">
                    <span  class="text-sm ml-1">{{$item['attribute_name']}}</span>
                </label>
            @endforeach
        </div>
    @endforeach
@endif
