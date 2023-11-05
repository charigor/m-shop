<div class="my-5">
    <div class="relative flex">
        <div thumbsSlider="" class="swiper mySwiper !w-[70px]">
            <div class="swiper-wrapper !w-[70px] flex flex-col">
                @foreach($product->sortedMedia as $media)
                    <div class="swiper-slide !w-[100%]">
                        <img src="{{$media->getFullUrl()}}"/>
                    </div>
                @endforeach
            </div>
        </div>
        @if((int) $product->rebate)
            <span class="bg-pink-500 inline-flex self-baseline text-white ml-2 text-xs font-medium mr-2 px-3.5 py-1.5 rounded-full dark:bg-pink-900 dark:text-pink-300">
               {{ - round($product->rebate /($product->price / 100))}} <span>%</span>
            </span>
          @endif
        <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff"
             class="swiper mySwiper2  !h-[400px] w-[100%]">
            <div class="swiper-wrapper !flex  my-5 !h-[400px] ">
                @foreach($product->sortedMedia as $media)
                    <div class="swiper-slide main">
                        <img src="{{$media->getFullUrl()}}" class="p-5"/>
                    </div>
                @endforeach
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
</div>
<script>
    function init() {
        var swiper = new Swiper(".mySwiper", {

            loop: false,
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
        });
        var swiper2 = new Swiper(".mySwiper2", {
            loop: true,
            spaceBetween: 10,
            effect: "fade",
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            thumbs: {
                swiper: swiper,
            },
        });

    }

    window.addEventListener('triggero', event => {
        init();
    })
    init();
</script>

