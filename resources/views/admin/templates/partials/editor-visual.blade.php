{{-- Visual Canvas Panel --}}
<div x-show="panels.visual" class="order-1 h-full flex-1 overflow-y-auto bg-gray-100 min-w-[350px]" id="visual-workspace">
    <div class="relative transform overflow-hidden mx-auto my-4 min-h-screen max-w-[480px] bg-white font-[Lato] shadow-2xl"
        @mouseleave="hoverMenuVisible = false">
        <x-invitation.layout class="bg-gray-50" :skip-cover="true">
            <div @tab-changed.window="isOpen = ($event.detail !== 'cover')"></div>
            <x-invitation.audio :src="''" />
            <div id="visual-canvas" class="@container min-h-[500px] w-full"
                @click="inspectElement($event)" @mousemove.throttle.50ms="trackHover($event)">
                {!! $template->html_content !!}
            </div>
        </x-invitation.layout>

        {{-- Floating Hover Menu --}}
        <div x-show="hoverMenuVisible"
            class="pointer-events-none absolute z-40 border-2 border-blue-400 transition-all duration-75 ease-linear"
            :style="`top: ${hoverMenuPos.top}; left: ${hoverMenuPos.left}; width: ${hoverMenuPos.width}; height: ${hoverMenuPos.height};`"
            style="display: none;">
            <div
                class="pointer-events-auto absolute -left-3 -top-4 z-50 flex gap-1 overflow-hidden rounded border border-gray-200 bg-white shadow-sm">
                <button @click.stop="moveNodeUp()" class="p-1.5 text-gray-600 transition hover:bg-gray-100"
                    title="Move Up">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 15l7-7 7 7"></path>
                    </svg>
                </button>
                <div class="w-px bg-gray-200"></div>
                <button @click.stop="moveNodeDown()" class="p-1.5 text-gray-600 transition hover:bg-gray-100"
                    title="Move Down">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>

            <div class="pointer-events-auto absolute -right-3 -top-3 z-50 flex gap-1">
                <button @click.stop="duplicateHoveredNode()"
                    class="rounded-full bg-blue-500 p-1.5 text-white shadow-md transition hover:scale-110 hover:bg-blue-600"
                    title="Duplicate Block">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                        </path>
                    </svg>
                </button>
                <button @click.stop="deleteHoveredNode()"
                    class="rounded-full bg-red-500 p-1.5 text-white shadow-md transition hover:scale-110 hover:bg-red-600"
                    title="Delete Block">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="pointer-events-auto absolute -bottom-3 left-1/2 z-50 -translate-x-1/2 transform">
                <button @click.stop="prepareInsertBelow()"
                    class="rounded-full bg-blue-600 p-1.5 text-white shadow-md transition hover:scale-110 hover:bg-blue-700"
                    title="Add Section Below">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            </div>
        </div>

    </div>
</div>
