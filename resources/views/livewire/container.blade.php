<div>
    <div class="container m-[auto] mt-8">
        <div class="grid">
            <div class="mb-8">
                @if ($isDockerRunning)
                    <a 
                        href="{{route('create-container')}}"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex float-right"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        New
                    </a>
                @endif
            </div>
            <div>
                @if ($containers)
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <tbody>
                                @foreach ($containers as $container)
                                    @if ($loop->first)
                                        <tr class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                            <th scope="col" class="px-6 py-3">
                                                {{$container->container_id}}
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                {{$container->name}}
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                {{$container->status}}
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                {{$container->ports}}
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                {{$container->label}}
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Action
                                            </th>
                                        </tr>
                                    @else
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{$container->container_id}}
                                            </th>
                                            <td class="px-6 py-4">
                                                {{$container->name}}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{$container->status}}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{$container->ports}}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{$container->label}}
                                            </td>
                                            <td class="p-4">
                                                <div class="flex">
                                                    @if($container->state == 'running')
                                                        <div 
                                                            class="contents cursor-pointer" 
                                                            wire:click="stopContainer('{{$container->container_id}}')"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                                                <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                                                            </svg>
                                                        </div>
                                                    @else
                                                        <div 
                                                            class="contents cursor-pointer" 
                                                            wire:click="startContainer('{{$container->container_id}}')"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" class="mr-1">
                                                                <path strokeLinecap="round" strokeLinejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    @if ($container->container_id != "34eed6f028fc")
                                                        <div 
                                                            class="contents cursor-pointer" 
                                                            wire:click="deleteContainer('{{$container->container_id}}')"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                                                <path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                            </svg>
                                                        </div>
                                                    @else
                                                        <div class="contents">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" style="visibility: hidden;">
                                                                <path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                    
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                <div>
                    <h1 class="text-4xl text-center">{{$containerErrorMessage}}</h1>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
