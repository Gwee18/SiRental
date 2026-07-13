{{-- Desktop Auth Buttons --}}
            <div class="hidden md:flex items-center gap-3">
                @auth('web')
                    <div class="relative">
                        <button id="profileMenuButton" type="button" class="flex items-center gap-2 focus:outline-none">
                            <img
                                src="{{ auth('web')->user()->avatar_url }}"
                                alt="{{ auth('web')->user()->nama_lengkap }}"
                                class="w-9 h-9 rounded-full object-cover border-2 border-white/30"
                            >
                        </button>

                        <div id="profileMenuDropdown" class="hidden absolute right-0 mt-3 w-52 bg-white rounded-xl shadow-xl py-2 z-50 text-gray-700 border border-gray-100">
                            <div class="px-4 py-2.5 border-b border-gray-100">
                                <p class="text-sm font-semibold text-[#00372c] truncate">
                                    {{ auth('web')->user()->nama_lengkap }}
                                </p>

                                <p class="text-xs text-gray-400 truncate">
                                    {{ auth('web')->user()->email }}
                                </p>
                            </div>

                            <a
                                href="{{ route('customer.transaksi.index') }}"
                                class="block px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors"
                            >
                                Transaksi Saya
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <button
                                    type="submit"
                                    class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors text-red-600"
                                >
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-white/80 hover:text-white text-sm font-medium transition-colors">
                        Masuk
                    </a>

                    <a href="{{ route('register') }}" class="bg-[#68dbae] hover:bg-[#55c99c] text-[#00372c] text-sm font-semibold px-4 py-2 rounded-lg transition-all">
                        Daftar
                    </a>
                @endauth
            </div>