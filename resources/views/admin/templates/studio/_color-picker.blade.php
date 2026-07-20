{{-- Popover color picker in-app (ganti dialog OS). Dipakai via @include di setiap field
     warna, dibungkus x-show="cp.key === '<id unik>'" — hanya satu tampil. Semua state &
     math di studioApp.cp (openColorPicker/cpApply/dst). Warna chrome dari palet --ui-*. --}}
{{-- click.outside menutup, tapi lewati klik pada trigger warna lain: triggernya sendiri
     yang atur cp.key (buka/tutup), jangan sampai outside ini balapan menutupnya. --}}
<div class="cpick" @click.outside="$event.target.closest('.cpick-trigger') || (cp.key = null)">
    <div class="cpick-sv" @pointerdown="cpDragSV($event)" :style="cpSvBg()">
        <span class="cpick-thumb" :style="`left:${cp.s * 100}%;top:${(1 - cp.v) * 100}%`"></span>
    </div>
    <div class="cpick-hue" @pointerdown="cpDragHue($event)">
        <span class="cpick-thumb cpick-hue-thumb" :style="`left:${cp.h / 360 * 100}%`"></span>
    </div>
    <div class="cpick-inputs">
        <div class="cpick-hex">
            <span>#</span>
            <input type="text" maxlength="8" spellcheck="false"
                :value="cpHex().replace('#', '')"
                @change="if (!cpSetHex($event.target.value)) $event.target.value = cpHex().replace('#', '')">
        </div>
        <div class="cpick-rgb">
            <label><span>R</span><input :value="cpRgb()[0]" @change="cpSetRgb(0, $event.target.value)"></label>
            <label><span>G</span><input :value="cpRgb()[1]" @change="cpSetRgb(1, $event.target.value)"></label>
            <label><span>B</span><input :value="cpRgb()[2]" @change="cpSetRgb(2, $event.target.value)"></label>
        </div>
    </div>
    <div class="cpick-tokens">
        <span class="cpick-tok-h">Token tema</span>
        <div class="cpick-tok-row">
            <template x-for="(hex, tk) in theme.colors" :key="tk">
                <button type="button" class="cpick-tok" :style="`background:${hex}`" :title="tk"
                    @click="cpSetHex(hex)"></button>
            </template>
        </div>
    </div>
</div>
