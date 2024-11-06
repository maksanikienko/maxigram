
<header class="bg-white md:block hidden">
    <nav class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8" aria-label="Global">

        <div class="hidden md:flex md:flex-1 md:justify-end md:mx-28">
            @if(auth()->user())

                <div class="relative">
                    <button id="userDropdown" data-dropdown-toggle="dropdownMenu" class="text-gray-700 font-semibold flex gap-3 ">
                        <img class="max-w-12 h-auto rounded-full" src="{{asset(auth()->user()->image)}}" alt="">
                         <span class="my-auto">{{ auth()->user()->name }}</span>
                    </button>
                    <div id="dropdownMenu" class="z-10 hidden divide-y divide-gray-100 rounded-lg shadow bg-gray-50">
                        <ul class="py-1 text-gray-700" aria-labelledby="userDropdown">
                            <li>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm">Profile</a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

            @else
            <a href="{{route('index')}}" class="text-sm/6 font-semibold text-gray-900">Log in <span aria-hidden="true">&rarr;</span></a>
            @endif

        </div>
    </nav>
    <!-- Mobile menu, show/hide based on menu open state. -->
{{--    <div class="lg:hidden" role="dialog" aria-modal="true">--}}
{{--        <!-- Background backdrop, show/hide based on slide-over state. -->--}}
{{--        <div class="fixed inset-0 z-10"></div>--}}

{{--    </div>--}}
</header>
