<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/datepicker.min.js"></script>
        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
{{--    <button id="b">click</button>--}}
{{--    <div id="content"></div>--}}
    <script>

        //  let  x = 'HOooooooooooooooooooooooooo'
        //  let numb = (i) => i * 75;
        // // expression();
        // console.log(declaration(1,[2,4,5],6));
        // // declaration(20,29,51) ;
        // //
        // // const expression = () => console.log('function expression')
        // function declaration(...z){
        //      // arg.reduce((accum,item) => console.log(item))
        //         console.log(typeof z[0])
        //       return z.reduce((accum,item) =>  {return typeof accum === 'object' ?  Array.from(accum).reduce((i,k) => i += parseInt(k) ): accum += item})
        // }
        // let x = 10
        //  function l(){
        //     let d = 'M'
        //     console.log(x)
        // };
        //  l();
        // let b = () => {
        //     console.log(x)
        // };
        // b();
        // let pow2 = function (a){
        //     return a**2;
        // }
        // //callback
        // let arr1 = [4,5,6,7,8];
        // const res2 = arr1.map(i => i**2)
        // // res2.pop();
        // console.log(res2)
        // const res3 = arr1.filter((el,i) => i%2 === 0).filter((el,i) => i !== 2)
        // console.log(res3);
        // this.x = 9;
        // var module = {
        //     x: 81,
        //     getX: function() { return this.x; }
        // };
        //
        // console.log(module.getX()); // 81
        //
        // var getX = module.getX;
        // console.log(getX()); // 9, поскольку в этом случае this ссылается на глобальный объект
        //
        // // создаём новую функцию с this, привязанным к module
        // var boundGetX = getX.bind(module);
        // console.log(boundGetX()); // 81
        // const module2 = {
        //     x: 51,
        //     getX: function(...arg) { return this.x + arg[0] + arg[1]; }
        // };
        // const module = {
        //     x: 82,
        //     getX: function(...arg) { return this.x + arg[0] - arg[1]; }
        // };
        // console.log(module.getX(40,50))
        // console.log(module2.getX.apply(module,[40,30]))
        // const f4 = module2.getX.bind(module);
        // console.log(f4(5,4))
        // let obj = {
        //     "ivanov" : {
        //         age: "25",
        //         parent: {
        //             "ivanov-a": {
        //                 age: "35",
        //             },
        //             "ivanov-b": {
        //                 age: "45",
        //                 parent: {
        //                     "sergeev-a": {
        //                         age: "55",
        //                         parent: {
        //
        //                         }
        //                     },
        //                 }
        //             }
        //         }
        //     },
        //     "Petrov": {
        //         age: "25",
        //         parent: {
        //             "lodeev-a": {
        //                 age: "45",
        //                 parent: {
        //
        //                 }
        //             },
        //         }
        //     }
        // }
        //
        // function getName(object) {
        //     for (let item in object) {
        //     if (object[item].hasOwnProperty('parent')) {
        //         for (let i in object[item]['parent']) {
        //             this.getName(object[item]['parent'])
        //             console.log(i)
        //         }
        //     }
        //     }
        // }
        // getName(obj);
        // function fact(arg){
        //     let res = 0;
        //     for(let i = 1; i < arg; i++){
        //         res  = res + (i * (i + 1));
        //     }
        //     return res
        // }
        // let x = 5
        // let user = {
        //     firstName: 'Bob'
        // }
        // let g =  function(x) {console.log(`${x} ${this.firstName}`)};
        //  function debounce(callback,delay){
        //
        //    let  time  = null;
        //    return (a) => {
        //
        //        if(time){
        //            clearTimeout(time)
        //        }
        //       time = setTimeout(() => callback(a),delay)
        //
        //
        //    }
        //
        // }
        //
        //
        // const fetching = debounce(g.bind(user),0);
        //  fetching(1)
        //  fetching(2)
        //  fetching(3)
        // fetching(4)
        // fetching(5)
        //
        // const tree = [{
        //      value: 1,
        //     children: [
        //         {
        //             value:2,
        //             children: [
        //                 {
        //                     value:3
        //                 }
        //             ]
        //         },
        //         {
        //             value:4,
        //             children: [
        //                 {
        //                     value:5
        //                 },
        //                 {
        //                     value:6
        //                 }
        //             ]
        //         }
        //     ]
        // }]
        // let children = [
        //     {
        //         value:5
        //     },
        //     {
        //         value:6
        //     }
        // ];
        // let z = () => [...children] = children;
        // console.log(children)
        // console.log(z())
// function getTree(tree){
//    let stack = tree;
//     console.log(stack.length)
//    const result = [];
//    while(stack.length > 0 ){
//        const node = stack.pop();
//        if(node.value !== undefined){
//            result.push(node.value)
//        }
//        if(node.children?.length){
//            console.log(...node.children)
//            stack.push(...node.children)
//        }
//        console.log(stack.length)
//    }
//     return result;
// }
// console.log(getTree(tree));
//         let counter = 0
//         let body = document.getElementById('content');
//         const mutationObserver = new MutationObserver((mutations) => {
//             console.log(mutations)
//         });
//         mutationObserver.observe(body,{
//             subtree: true,
//             attributeOldValue: true,
//             childList: true
//         })
//          console.log(button)
//         button.addEventListener('click',() => {
//             counter++
//             console.log(counter)
//             content.innerText = counter;
//             console.log('After Change')
//             Promise.resolve().then(() => {
//                 console.log('Prom')
//             })
//             setTimeout(() => {
//                 console.log('Timeout')
//             },0)
//         })
//         Promise.resolve().then(() => {
//             console.log(5)
//             Promise.resolve().then(() => {
//                 console.log(7)
//             })
//             setTimeout(() => {
//                 console.log(10)
//             },0)
//         })
//         setTimeout(() => console.log(4),0)
//         setTimeout(() => {
//             queueMicrotask(() => {
//                 console.log(6)
//             })
//             queueMicrotask(() => {
//                 console.log(8)
//             })
//             queueMicrotask(() => {
//                 console.log(9)
//             })
//         },0)
//         console.log(1)
//         queueMicrotask(() => {
//             console.log(2)
//         })
//  let UkraineTowns = ['Kiev','Vinnica','Dnepr'];
//  let BritainTowns = ['London','Birmingem',[{name: 'Manchester'}]];
//  const allCities = [...UkraineTowns,BritainTowns[2]];
//  console.log(allCities)
//        body.innerText = allCities;
//     function calc(a,d){
//
//           return {
//               'a':a+d,
//               'b':a-d,
//               'c':a*d,
//               'd':a/d,
//               'g': a + a + d
//           };
//     }
//         let  {g,...a} =  calc(42,10);
//          console.log(a)
//         // console.log(b)
//         // console.log(c)
//          console.log(g)
//         let car = {
//             name: 'Mercedes',
//             wheels: 4,
//             country: 'Germany',
//             price: 12000
//         }
//         let truck = new Object({
//             name: 'Reno',
//             wheels: 4,
//             country: 'France',
//             price: 30000,
//             greet: function(){
//                  console.log(`Hello Truck`)
//             }
//         })
//         truck.greet();
//         console.log(truck)
    </script>
        @inertia
    </body>
</html>
